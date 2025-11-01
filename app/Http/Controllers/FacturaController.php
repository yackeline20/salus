<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FacturaStoreRequest; // ⬅️ Nuevo Import para validación
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Product; // Asumiendo que el modelo de productos es 'Product'
use App\Models\Tratamiento; // Asumiendo que el modelo de tratamientos es 'Tratamiento'
use App\Models\DetalleFacturaProducto;
use App\Models\DetalleFacturaTratamiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Usamos Log para registrar errores en lugar de \Log
use Illuminate\Validation\ValidationException;

class FacturaController extends Controller
{
    /**
     * Usamos el constructor para inyectar la Policy de autorización.
     */
    public function __construct()
    {
        // Autorización de recursos. Asume que existe FacturaPolicy.
        // 🚀 LÍNEA DESCOMENTADA: Esto activa la autorización automática para todos los métodos.
        $this->authorizeResource(Factura::class, 'factura');
    }

    /**
     * Muestra una lista de facturas (Recientes / Búsqueda).
     * GET /api/factura/mostrar
     */
    public function index(Request $request)
    {
        // La autorización (viewAny) se realiza antes de entrar a este método
        try {
            // Carga eficiente de relaciones necesarias para el listado
            $query = Factura::with([
                'cliente.persona', // Nombre del cliente
            ])
            ->orderBy('Fecha_Factura', 'desc');

            // Lógica de búsqueda (si se proporciona un parámetro 'search')
            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('Cod_Factura', 'like', "%{$searchTerm}%")
                      ->orWhere('Metodo_Pago', 'like', "%{$searchTerm}%")
                      ->orWhereHas('cliente.persona', function ($q2) use ($searchTerm) {
                          $q2->where('Nombre', 'like', "%{$searchTerm}%")
                             ->orWhere('Apellido', 'like', "%{$searchTerm}%");
                      });
                });
            }

            $facturas = $query->paginate(10); // Paginación por defecto

            return response()->json($facturas);

        } catch (\Exception $e) {
            Log::error('Error al obtener la lista de facturas: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener la lista de facturas.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Crea y almacena una nueva factura, sus detalles y actualiza el stock.
     * POST /api/factura/registro
     *
     * @param FacturaStoreRequest $request ⬅️ Utilizamos el Form Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FacturaStoreRequest $request)
    {
        // La autorización (create) se realiza antes de entrar a este método
        // La validación se realiza automáticamente por FacturaStoreRequest
        $validated = $request->validated();

        // Usamos transacciones para garantizar que si una parte falla, todo se revierta (incluyendo el stock)
        DB::beginTransaction();
        try {
            // 1. CREAR EL ENCABEZADO DE LA FACTURA
            $factura = Factura::create([
                'Cod_Cliente' => $validated['Cod_Cliente'],
                'Fecha_Factura' => $validated['Fecha_Factura'] ?? now(), // Se registra la fecha actual
                'Total_Factura' => $validated['Total_Factura'],
                'Metodo_Pago' => $validated['Metodo_Pago'],
                'Estado_Pago' => $validated['Estado_Pago'],
                'Descuento_Aplicado' => $validated['Descuento_Aplicado'] ?? 0,
            ]);

            $total_calculado = 0;

            // 2. PROCESAR DETALLE DE PRODUCTOS
            if (!empty($validated['items_productos'])) {
                foreach ($validated['items_productos'] as $item) {
                    // Usamos lockForUpdate para evitar condiciones de carrera en el stock
                    $producto = Product::find($item['Cod_Producto'])->lockForUpdate();

                    // Revalidación de stock (aunque el Form Request lo valida, esta es la validación de concurrencia)
                    if (!$producto || $producto->Cantidad_En_Stock < $item['Cantidad_Vendida']) {
                        DB::rollBack();
                        // Asumo que tu modelo 'Product' tiene un campo 'Nombre_Producto'
                        $nombre = $producto ? $producto->Nombre_Producto : 'desconocido';
                        return response()->json(['message' => "Stock insuficiente para el producto: {$nombre}."], 400);
                    }

                    // Calcular subtotal
                    $subtotal = $item['Cantidad_Vendida'] * $item['Precio_Unitario_Venta'];
                    $total_calculado += $subtotal;

                    // Insertar detalle de producto
                    DetalleFacturaProducto::create([
                        'Cod_Factura' => $factura->Cod_Factura,
                        'Cod_Producto' => $item['Cod_Producto'],
                        'Cantidad_Vendida' => $item['Cantidad_Vendida'],
                        'Precio_Unitario_Venta' => $item['Precio_Unitario_Venta'],
                        'Subtotal' => $subtotal,
                    ]);

                    // Actualizar el stock
                    $producto->Cantidad_En_Stock -= $item['Cantidad_Vendida'];
                    $producto->save();
                }
            }

            // 3. PROCESAR DETALLE DE TRATAMIENTOS
            if (!empty($validated['items_tratamientos'])) {
                foreach ($validated['items_tratamientos'] as $item) {
                    // La existencia del tratamiento ya fue validada en FacturaStoreRequest

                    // Calcular subtotal
                    // NOTA: Usamos 'Precio_Unitario_Venta' del request, asumiendo que es el precio final
                    $subtotal = $item['Precio_Unitario_Venta'];
                    $total_calculado += $subtotal;

                    // Insertar detalle de tratamiento
                    DetalleFacturaTratamiento::create([
                        'Cod_Factura' => $factura->Cod_Factura,
                        'Cod_Tratamiento' => $item['Cod_Tratamiento'],
                        'Precio_Unitario_Venta' => $item['Precio_Unitario_Venta'],
                        'Subtotal' => $subtotal,
                    ]);
                }
            }

            // 4. VALIDACIÓN DE TOTAL (Opcional, pero recomendado por seguridad)
            $descuento_pct = $validated['Descuento_Aplicado'] ?? 0;
            // Cálculo: Total items - Descuento (asumiendo que el descuento es un porcentaje)
            $total_neto_calculado = $total_calculado - ($total_calculado * ($descuento_pct / 100));

            // Si el total calculado no coincide con el total enviado (pequeña tolerancia por coma flotante)
            if (abs($total_neto_calculado - $validated['Total_Factura']) > 0.01) {
                DB::rollBack();
                Log::warning("Discrepancia Total Factura. Calculado: {$total_neto_calculado}, Enviado: {$validated['Total_Factura']}");
                return response()->json(['message' => 'Error: El total calculado no coincide con el total enviado.', 'Calculado' => $total_neto_calculado], 400);
            }

            // 5. CONFIRMAR TRANSACCIÓN
            DB::commit();

            return response()->json([
                'message' => 'Factura creada exitosamente.',
                'factura' => $factura->load(['cliente.persona', 'detalleProductos', 'detalleTratamientos']) // Cargar detalles para respuesta
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Logear el error para debugging
            Log::error('Error al crear factura: ' . $e->getMessage() . ' en línea ' . $e->getLine());
            return response()->json(['message' => 'Error interno del servidor al procesar la factura.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Muestra el detalle completo de una factura específica.
     * GET /api/factura/mostrar_detalle/{id}
     */
    public function show($id)
    {
        // La autorización (view) se realiza antes de entrar a este método
        try {
            $factura = Factura::with([
                'cliente.persona',
                'detalleProductos.producto', // Para obtener nombre del producto
                'detalleTratamientos.tratamiento', // Para obtener nombre del tratamiento
            ])->find($id);

            if (!$factura) {
                return response()->json(['message' => 'Factura no encontrada.'], 404);
            }

            // Si FacturaPolicy no usara authorizeResource, la autorización iría aquí:
            // $this->authorize('view', $factura);

            return response()->json($factura);

        } catch (\Exception $e) {
            Log::error('Error al obtener el detalle de la factura: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener el detalle de la factura.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Actualiza el estado o método de pago de una factura existente.
     * PUT /api/factura/cambio/{factura}
     */
    public function update(Request $request, Factura $factura)
    {
        // La autorización (update) se realiza antes de entrar a este método
        try {
            // Validar solo los campos que se permiten actualizar
            $request->validate([
                'Metodo_Pago' => 'sometimes|string|max:50',
                'Estado_Pago' => 'sometimes|string|in:PENDIENTE,PAGADA,ANULADA', // Ajustado a los valores del FormRequest
                'Descuento_Aplicado' => 'sometimes|numeric|min:0',
            ]);

            // Si el estado cambia a ANULADA, lo manejaremos en el método destroy para la reversión de stock.
            // Aquí solo permitimos cambios de método de pago y descuentos simples.
            $factura->update($request->only(['Metodo_Pago', 'Estado_Pago', 'Descuento_Aplicado']));

            return response()->json(['message' => 'Factura actualizada exitosamente.', 'factura' => $factura]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la factura: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar la factura.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Elimina (Anula) una factura y revierte los cambios de stock de productos.
     * DELETE /api/factura/eliminar/{factura}
     */
    public function destroy(Factura $factura)
    {
        // La autorización (delete) se realiza antes de entrar a este método
        DB::beginTransaction();
        try {
            // Verificamos si ya está anulada o pagada para evitar anularla dos veces o una pagada recientemente.
            // NOTA: Esta es una regla de negocio adicional, opcional.
            if ($factura->Estado_Pago === 'ANULADA') {
                DB::rollBack();
                return response()->json(['message' => 'La factura ya está anulada. No se requiere reversión de stock.'], 409);
            }

            // 1. REVERTIR STOCK DE PRODUCTOS
            $factura->loadMissing('detalleProductos');

            foreach ($factura->detalleProductos as $detalle) {
                // Cargar el producto para la actualización
                $producto = Product::find($detalle->Cod_Producto);

                if ($producto) {
                    $producto->Cantidad_En_Stock += $detalle->Cantidad_Vendida;
                    $producto->save();
                }
            }

            // 2. CAMBIAR ESTADO A ANULADA EN LUGAR DE ELIMINAR EL REGISTRO
            // Es mejor mantener el registro de la factura anulada por auditoría.
            $factura->update(['Estado_Pago' => 'ANULADA']);

            // NOTA: No eliminaremos los detalles (`$factura->detalleProductos()->delete();`)
            // ni el encabezado (`$factura->delete();`) para mantener el registro histórico.
            // Solo se marca como ANULADA.

            DB::commit();
            return response()->json(['message' => 'Factura ANULADA exitosamente, stock de productos revertido.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al anular factura: ' . $e->getMessage());
            return response()->json(['message' => 'Error al anular la factura o revertir el stock.', 'error' => $e->getMessage()], 500);
        }
    }
}
