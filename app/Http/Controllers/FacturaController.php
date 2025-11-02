<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FacturaStoreRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Factura;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View; // Importar la clase View para el método index

/**
 * FacturaController actúa como un proxy/gateway que redirige
 * todas las peticiones CRUD de facturación a una API externa (Node.js).
 * La validación de entrada (Request) y la capa de seguridad (Policy)
 * se manejan en Laravel, pero la lógica de persistencia y stock
 * se delega completamente a la API de Node.js.
 */
class FacturaController extends Controller
{
    // Propiedad para almacenar la URL base de la API de Node.js
    protected string $apiBaseUrl;

    /**
     * Constructor para inyectar la Policy de autorización y configurar la URL base.
     */
    public function __construct()
    {
        // Obtiene la URL base desde el archivo .env
        $this->apiBaseUrl = env('SALUS_API_BASE_URL', 'http://127.0.0.1:8000'); // Valor por defecto para seguridad

        // Autorización de recursos. Se mantiene solo para la capa de seguridad de Laravel
        $this->authorizeResource(Factura::class, 'factura', [
            // Excluimos la autorización automática para los métodos de detalle que no operan sobre el Modelo Factura
            'except' => [
                'storeDetalleProducto', 'storeDetalleTratamiento',
                'getDetalleTratamiento', 'getDetalleProducto',
                'updateDetalleProducto', 'destroyDetalleProducto',
                'updateDetalleTratamiento', 'destroyDetalleTratamiento',
            ]
        ]);
    }

    /**
     * Método auxiliar para encapsular la lógica de llamada a la API y el manejo de errores/respuestas.
     *
     * @param string $method Método HTTP (GET, POST, PATCH, DELETE)
     * @param string $uri La parte del endpoint (ej: '/facturas/100')
     * @param array $data Cuerpo de la petición (para POST, PUT, PATCH, DELETE)
     * @param array $query Parámetros de la URL (para GET)
     * @param bool $returnViewData Si es true, retorna los datos de la API en formato array/null (para usar en vistas).
     * @return JsonResponse|array|null
     */
    private function callExternalApi(string $method, string $uri, array $data = [], array $query = [], bool $returnViewData = false): JsonResponse|array|null
    {
        $url = $this->apiBaseUrl . $uri;

        try {
            // Se define el cliente HTTP con el timeout
            $client = Http::timeout(10);

            // Determinar el método y realizar la llamada
            $response = match (strtolower($method)) {
                'get' => $client->get($url, array_merge($query, $data)),
                'post' => $client->post($url, $data),
                'put' => $client->put($url, $data),
                'patch' => $client->patch($url, $data),
                'delete' => $client->delete($url, $data),
                default => throw new \InvalidArgumentException("Método HTTP no soportado: {$method}")
            };

            if ($response->successful()) {
                $responseData = $response->json();

                // Si se pide para la vista (index), retornamos solo los datos (array)
                if ($returnViewData) {
                    return $responseData;
                }

                // Si no es para la vista, retorna el JSONResponse con el status
                $responseBody = $responseData ?: ['message' => 'Operación exitosa'];
                return response()->json($responseBody, $response->status());
            }

            // Manejo de errores de la API externa (4xx o 5xx)
            $status = $response->status();
            $body = $response->body();

            Log::error("API {$method} {$uri} falló (Status: {$status}): " . $body);

            // Si es para la vista y falla, retorna null para que la vista maneje el error
            if ($returnViewData) {
                return null;
            }

            // Si no es para la vista, retorna el JsonResponse de error
            $message = match (true) {
                $status === 404 => 'Recurso no encontrado en la API externa.',
                $status >= 400 && $status < 500 => 'Error de la aplicación al procesar la solicitud en la API externa.',
                $status >= 500 => 'Error interno del servidor de la API externa.',
                default => 'Error desconocido en la API externa.',
            };

            return response()->json([
                'message' => $message,
                'api_error' => $response->json(), // Incluir el cuerpo de error si es JSON válido
            ], $status);

        } catch (\Exception $e) {
            // Error de conexión, timeout, DNS, etc.
            Log::error('Error de conexión con el servidor de facturación: ' . $e->getMessage());

            // Si es para la vista y falla la conexión, retorna null
            if ($returnViewData) {
                return null;
            }

            return response()->json(['message' => 'Error al conectar con el servidor de facturación.', 'error' => $e->getMessage()], 503);
        }
    }

    // =========================================================================
    // MÉTODOS CRUD DE CABECERA (Header)
    // =========================================================================

    /**
     * Muestra una lista de facturas. Petición GET a la API de Node.js.
     * GET /factura -> GET http://[API_BASE_URL]/facturas
     *
     * Este método ha sido modificado para DEVOLVER una VISTA (View) en lugar de un JSONResponse.
     * @return View
     */
    public function index(Request $request): View
    {
        // 1. Llama a la API con el método GET para obtener los datos de las facturas.
        // Se añade 'true' al final para indicar que queremos que retorne los datos puros (array)
        // en lugar de un JsonResponse.
        $facturas = $this->callExternalApi('GET', '/facturas', [], $request->query(), true);

        // Se inicializa la variable de error para la vista
        $apiError = null;

        // 2. Manejo de la respuesta para la vista.
        if (is_array($facturas)) {
            // Si $facturas es un array, la API respondió correctamente.
            return view('factura.index', compact('facturas'));
        } else {
            // Si $facturas es null, ocurrió un error de conexión o de la API.
            // Asignamos un array vacío y un mensaje de error explícito.
            $facturas = [];

            // FIX: Corregido el manejo de errores. En lugar de usar withErrors() para errores no de validación,
            // pasamos un mensaje de error explícito a la vista a través de la variable $apiError.
            $apiError = 'No se pudo cargar el listado de facturas. Verifique la conexión con la API externa.';

            // Pasamos ambas variables a la vista. El desarrollador de la vista deberá verificar $apiError
            // para mostrar el mensaje.
            return view('factura.index', compact('facturas', 'apiError'));
        }
    }

    /**
     * Muestra la vista para crear una nueva factura.
     */
    public function create(): View
    {
        // Asumiendo que tienes una ruta para esta vista
        return view('factura.create');
    }

    /**
     * Crea y almacena una nueva factura (Cabecera + Detalles). Envía los datos completos a la API.
     * POST /api/factura -> POST http://[API_BASE_URL]/facturas
     * @param FacturaStoreRequest $request
     * @return JsonResponse
     */
    public function store(FacturaStoreRequest $request): JsonResponse
    {
        // La validación se realizó en FacturaStoreRequest.
        // La API de Node.js es responsable de manejar la transacción completa (Cabecera, Detalles, Stock)
        return $this->callExternalApi('POST', '/facturas', $request->validated());
    }

    /**
     * Muestra el detalle completo de una factura específica.
     * GET /api/factura/{factura} -> GET http://[API_BASE_URL]/facturas/{id}
     */
    public function show(Factura $factura): JsonResponse
    {
        // Se asume que Factura model tiene un Cod_Factura que mapea al ID real de la API externa.
        $facturaId = $factura->Cod_Factura;
        return $this->callExternalApi('GET', "/facturas/{$facturaId}");
    }

    /**
     * Actualiza el estado, método de pago o descuento de una factura existente (Actualización parcial).
     * PATCH /api/factura/{factura} -> PATCH http://[API_BASE_URL]/facturas/{id}
     */
    public function update(Request $request, Factura $factura): JsonResponse
    {
        $facturaId = $factura->Cod_Factura;

        try {
            // 1. Validar solo los campos que se permiten actualizar (localmente)
            $validated = $request->validate([
                'Metodo_Pago' => 'sometimes|required|string|max:50',
                'Estado_Pago' => 'sometimes|required|string|in:PENDIENTE,PAGADA,ANULADA',
                'Descuento_Aplicado' => 'sometimes|required|numeric|min:0|max:100', // Asumiendo porcentaje o monto
            ]);

            // 2. Usamos PATCH ya que es una actualización parcial de la cabecera
            return $this->callExternalApi('PATCH', "/facturas/{$facturaId}", $validated);

        } catch (ValidationException $e) {
            // Error de validación local
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Elimina (Anula) una factura y revierte los cambios de stock de productos.
     * DELETE /api/factura/{factura} -> DELETE http://[API_BASE_URL]/facturas/{id}
     */
    public function destroy(Factura $factura): JsonResponse
    {
        $facturaId = $factura->Cod_Factura;
        // La lógica de anulación y reversión de stock ocurre en la API de Node.js
        return $this->callExternalApi('DELETE', "/facturas/{$facturaId}");
    }

    // =========================================================================
    // MÉTODOS CRUD DE DETALLES (Lines) - LECTURA y CREACIÓN
    // =========================================================================

    /**
     * Obtiene los detalles de tratamiento de una factura.
     * GET /api/detalle_factura_tratamiento?Cod_Factura=1 -> GET http://[API_BASE_URL]/detalle_factura_tratamiento?Cod_Factura=1
     */
    public function getDetalleTratamiento(Request $request): JsonResponse
    {
        // Validación mínima del parámetro Cod_Factura
        $validated = $request->validate(['Cod_Factura' => 'required|integer']);

        // Pasa Cod_Factura como query parameter
        return $this->callExternalApi('GET', "/detalle_factura_tratamiento", [], $validated);
    }

    /**
     * Obtiene los detalles de producto de una factura.
     * GET /api/detalle_factura_producto?Cod_Factura=1 -> GET http://[API_BASE_URL]/detalle_factura_producto?Cod_Factura=1
     */
    public function getDetalleProducto(Request $request): JsonResponse
    {
        // Validación mínima del parámetro Cod_Factura
        $validated = $request->validate(['Cod_Factura' => 'required|integer']);

        // Pasa Cod_Factura como query parameter
        return $this->callExternalApi('GET', "/detalle_factura_producto", [], $validated);
    }

    /**
     * Agrega un detalle de producto a una factura (POST).
     * POST /api/detalle_factura_producto -> POST http://[API_BASE_URL]/detalle_factura_producto
     */
    public function storeDetalleProducto(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Cod_Factura' => 'required|integer',
                'Cod_Producto' => 'required|integer',
                'Cantidad' => 'required|integer|min:1',
                'Precio_Unitario' => 'required|numeric|min:0', // Aseguramos que se envía el precio
            ]);

            // La API de Node.js se encarga de descontar el stock y recalcular el total de la factura.
            return $this->callExternalApi('POST', "/detalle_factura_producto", $validated);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Agrega un detalle de tratamiento a una factura (POST).
     * POST /api/detalle_factura_tratamiento -> POST http://[API_BASE_URL]/detalle_factura_tratamiento
     */
    public function storeDetalleTratamiento(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Cod_Factura' => 'required|integer',
                'Cod_Tratamiento' => 'required|integer',
                'Costo' => 'required|numeric|min:0',
                'Descripcion' => 'sometimes|string|max:255', // Permite que la descripción sea opcional
            ]);

            // La API de Node.js se encarga de recalcular el total de la factura.
            return $this->callExternalApi('POST', "/detalle_factura_tratamiento", $validated);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    // =========================================================================
    // MÉTODOS CRUD DE DETALLES (Lines) - ACTUALIZACIÓN y ELIMINACIÓN
    // =========================================================================

    /**
     * Actualiza un detalle de producto en la factura (Actualización parcial).
     * PATCH /api/detalle_factura_producto/{idDetalle} -> PATCH http://[API_BASE_URL]/detalle_factura_producto/{idDetalle}
     */
    public function updateDetalleProducto(Request $request, $idDetalle): JsonResponse
    {
        // Validar los campos que se desean actualizar (Cantidad es clave para stock)
        try {
            $validated = $request->validate([
                'Cantidad' => 'sometimes|required|integer|min:1',
                'Precio_Unitario' => 'sometimes|required|numeric|min:0',
            ]);

            // Usamos PATCH ya que es una actualización parcial de la línea
            // La API es responsable de validar si el stock es suficiente y reversar/ajustar la diferencia de stock.
            return $this->callExternalApi('PATCH', "/detalle_factura_producto/{$idDetalle}", $validated);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Elimina un detalle de producto de la factura, revirtiendo el stock.
     * DELETE /api/detalle_factura_producto/{idDetalle} -> DELETE http://[API_BASE_URL]/detalle_factura_producto/{idDetalle}
     */
    public function destroyDetalleProducto($idDetalle): JsonResponse
    {
        // La API de Node.js es responsable de revertir el stock y recalcular el total.
        return $this->callExternalApi('DELETE', "/detalle_factura_producto/{$idDetalle}");
    }

    /**
     * Actualiza un detalle de tratamiento en la factura (Actualización parcial).
     * PATCH /api/detalle_factura_tratamiento/{idDetalle} -> PATCH http://[API_BASE_URL]/detalle_factura_tratamiento/{idDetalle}
     */
    public function updateDetalleTratamiento(Request $request, $idDetalle): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Costo' => 'sometimes|required|numeric|min:0',
                'Descripcion' => 'sometimes|string|max:255',
            ]);

            // Usamos PATCH ya que es una actualización parcial de la línea
            return $this->callExternalApi('PATCH', "/detalle_factura_tratamiento/{$idDetalle}", $validated);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Elimina un detalle de tratamiento de la factura.
     * DELETE /api/detalle_factura_tratamiento/{idDetalle} -> DELETE http://[API_BASE_URL]/detalle_factura_tratamiento/{idDetalle}
     */
    public function destroyDetalleTratamiento($idDetalle): JsonResponse
    {
        // La API de Node.js es responsable de recalcular el total.
        return $this->callExternalApi('DELETE', "/detalle_factura_tratamiento/{$idDetalle}");
    }
}
