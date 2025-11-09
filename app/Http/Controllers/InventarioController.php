<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class InventarioController extends Controller
{
    /**
     * Display the inventory view
     */
    public function index()
    {
        return view('inventario');
    }

    /**
     * Obtener todos los productos con información de proveedores
     */
    public function obtenerProductos()
    {
        try {
            $productos = DB::select("
                SELECT 
                    p.Cod_Producto,
                    p.Nombre_Producto,
                    p.Descripcion,
                    p.Precio_Venta,
                    p.Costo_Compra,
                    p.Cantidad_En_Stock,
                    p.Fecha_Vencimiento,
                    prov.Nombre_Proveedor,
                    pp.Precio_Ultima_Compra,
                    pp.Fecha_Ultima_Compra
                FROM producto p
                LEFT JOIN producto_proveedor pp ON p.Cod_Producto = pp.Cod_Producto
                LEFT JOIN proveedor prov ON pp.Cod_Proveedor = prov.Cod_Proveedor
                ORDER BY p.Nombre_Producto
            ");

            return response()->json([
                'success' => true,
                'data' => $productos
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del inventario
     */
    public function obtenerEstadisticas()
    {
        try {
            $estadisticas = DB::select("
                SELECT 
                    COUNT(*) as total_productos,
                    SUM(CASE WHEN Cantidad_En_Stock < 10 THEN 1 ELSE 0 END) as stock_bajo,
                    SUM(Precio_Venta * Cantidad_En_Stock) as valor_total,
                    SUM(CASE WHEN Fecha_Vencimiento IS NOT NULL 
                             AND Fecha_Vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                             AND Fecha_Vencimiento >= CURDATE()
                             THEN 1 ELSE 0 END) as proximos_vencer
                FROM producto
            ");

            return response()->json([
                'success' => true,
                'data' => $estadisticas[0] ?? [
                    'total_productos' => 0,
                    'stock_bajo' => 0,
                    'valor_total' => 0,
                    'proximos_vencer' => 0
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los proveedores
     */
    public function obtenerProveedores()
    {
        try {
            $proveedores = DB::select("
                SELECT Cod_Proveedor, Nombre_Proveedor, Contacto_Principal, Telefono, Email, Direccion
                FROM proveedor 
                ORDER BY Nombre_Proveedor
            ");

            $proveedoresMap = [];
            foreach ($proveedores as $proveedor) {
                $proveedoresMap[$proveedor->Cod_Proveedor] = $proveedor->Nombre_Proveedor;
            }

            return response()->json([
                'success' => true,
                'data' => $proveedoresMap
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener proveedores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todas las relaciones producto-proveedor
     */
    public function obtenerRelaciones()
    {
        try {
            $relaciones = DB::select("
                SELECT 
                    pp.Cod_Prod_Prov,
                    pp.Cod_Producto,
                    pp.Cod_Proveedor,
                    p.Nombre_Producto,
                    prov.Nombre_Proveedor,
                    pp.Precio_Ultima_Compra,
                    pp.Fecha_Ultima_Compra
                FROM producto_proveedor pp
                INNER JOIN producto p ON pp.Cod_Producto = p.Cod_Producto
                INNER JOIN proveedor prov ON pp.Cod_Proveedor = prov.Cod_Proveedor
                ORDER BY pp.Fecha_Ultima_Compra DESC
            ");

            return response()->json([
                'success' => true,
                'data' => $relaciones
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener relaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo producto
     */
    public function crearProducto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nombre_Producto' => 'required|string|max:30',
            'Descripcion' => 'nullable|string',
            'Precio_Venta' => 'required|numeric|min:0',
            'Costo_Compra' => 'required|numeric|min:0',
            'Cantidad_En_Stock' => 'required|integer|min:0',
            'Fecha_Vencimiento' => 'nullable|date',
            'Cod_Proveedor' => 'nullable|integer|exists:proveedor,Cod_Proveedor',
            'Precio_Ultima_Compra' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Insertar producto
            DB::insert("
                INSERT INTO producto (
                    Nombre_Producto, 
                    Descripcion, 
                    Precio_Venta, 
                    Costo_Compra, 
                    Cantidad_En_Stock, 
                    Fecha_Vencimiento
                ) VALUES (?, ?, ?, ?, ?, ?)
            ", [
                $request->Nombre_Producto,
                $request->Descripcion,
                $request->Precio_Venta,
                $request->Costo_Compra,
                $request->Cantidad_En_Stock,
                $request->Fecha_Vencimiento
            ]);

            $productoId = DB::getPdo()->lastInsertId();

            // Si se especificó un proveedor, crear la relación
            if ($request->Cod_Proveedor) {
                DB::insert("
                    INSERT INTO producto_proveedor (
                        Cod_Producto, 
                        Cod_Proveedor, 
                        Precio_Ultima_Compra, 
                        Fecha_Ultima_Compra
                    ) VALUES (?, ?, ?, NOW())
                ", [
                    $productoId,
                    $request->Cod_Proveedor,
                    $request->Precio_Ultima_Compra ?? $request->Costo_Compra
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al inventario!',
                'producto_id' => $productoId
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un producto existente
     */
    public function actualizarProducto(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'Nombre_Producto' => 'required|string|max:30',
            'Descripcion' => 'nullable|string',
            'Precio_Venta' => 'required|numeric|min:0',
            'Costo_Compra' => 'required|numeric|min:0',
            'Cantidad_En_Stock' => 'required|integer|min:0',
            'Fecha_Vencimiento' => 'nullable|date',
            'Cod_Proveedor' => 'nullable|integer|exists:proveedor,Cod_Proveedor',
            'Precio_Ultima_Compra' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Verificar que el producto existe
            $producto = DB::select("SELECT * FROM producto WHERE Cod_Producto = ?", [$id]);
            if (empty($producto)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            // Actualizar producto
            DB::update("
                UPDATE producto 
                SET Nombre_Producto = ?,
                    Descripcion = ?,
                    Precio_Venta = ?,
                    Costo_Compra = ?,
                    Cantidad_En_Stock = ?,
                    Fecha_Vencimiento = ?
                WHERE Cod_Producto = ?
            ", [
                $request->Nombre_Producto,
                $request->Descripcion,
                $request->Precio_Venta,
                $request->Costo_Compra,
                $request->Cantidad_En_Stock,
                $request->Fecha_Vencimiento,
                $id
            ]);

            // Si se especificó un proveedor, actualizar o crear la relación
            if ($request->Cod_Proveedor) {
                // Verificar si existe una relación
                $relacion = DB::select("
                    SELECT * FROM producto_proveedor 
                    WHERE Cod_Producto = ?
                ", [$id]);

                if (!empty($relacion)) {
                    // Actualizar relación existente
                    DB::update("
                        UPDATE producto_proveedor 
                        SET Cod_Proveedor = ?,
                            Precio_Ultima_Compra = ?,
                            Fecha_Ultima_Compra = NOW()
                        WHERE Cod_Producto = ?
                    ", [
                        $request->Cod_Proveedor,
                        $request->Precio_Ultima_Compra ?? $request->Costo_Compra,
                        $id
                    ]);
                } else {
                    // Crear nueva relación
                    DB::insert("
                        INSERT INTO producto_proveedor (
                            Cod_Producto, 
                            Cod_Proveedor, 
                            Precio_Ultima_Compra, 
                            Fecha_Ultima_Compra
                        ) VALUES (?, ?, ?, NOW())
                    ", [
                        $id,
                        $request->Cod_Proveedor,
                        $request->Precio_Ultima_Compra ?? $request->Costo_Compra
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un producto
     */
    public function eliminarProducto($id)
    {
        DB::beginTransaction();

        try {
            // Verificar que el producto existe
            $producto = DB::select("SELECT * FROM producto WHERE Cod_Producto = ?", [$id]);
            if (empty($producto)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            // Eliminar relaciones producto-proveedor primero
            DB::delete("DELETE FROM producto_proveedor WHERE Cod_Producto = ?", [$id]);

            // Eliminar producto
            DB::delete("DELETE FROM producto WHERE Cod_Producto = ?", [$id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un producto específico
     */
    public function obtenerProducto($id)
    {
        try {
            $producto = DB::select("
                SELECT 
                    p.Cod_Producto,
                    p.Nombre_Producto,
                    p.Descripcion,
                    p.Precio_Venta,
                    p.Costo_Compra,
                    p.Cantidad_En_Stock,
                    p.Fecha_Vencimiento,
                    prov.Nombre_Proveedor,
                    pp.Cod_Proveedor,
                    pp.Precio_Ultima_Compra,
                    pp.Fecha_Ultima_Compra
                FROM producto p
                LEFT JOIN producto_proveedor pp ON p.Cod_Producto = pp.Cod_Producto
                LEFT JOIN proveedor prov ON pp.Cod_Proveedor = prov.Cod_Proveedor
                WHERE p.Cod_Producto = ?
            ", [$id]);

            if (empty($producto)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $producto[0]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener producto: ' . $e->getMessage()
            ], 500);
        }
    }
}
