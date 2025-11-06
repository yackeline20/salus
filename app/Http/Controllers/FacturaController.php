<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FacturaStoreRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Factura;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Client\ConnectionException;

/**
 * FacturaController actúa como un proxy/gateway que redirige
 * todas las peticiones CRUD de facturación a una API externa (Node.js).
 * La validación de entrada (Request) y la capa de seguridad (Policy)
 * se manejan en Laravel, pero la lógica de persistencia, cálculo
 * y stock se delega completamente a la API de Node.js.
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
        // Obtiene la URL base desde el archivo .env y asegura que no termina en barra.
        $this->apiBaseUrl = rtrim(env('SALUS_API_BASE_URL', 'http://127.0.0.1:3000'), '/');

        // Autorización de recursos. Se mantiene solo para la capa de seguridad de Laravel.
        // Se asume que el modelo Factura tiene definido el primary key (Cod_Factura)
        $this->authorizeResource(Factura::class, 'factura', [
            // Excluimos la autorización automática para los métodos de detalle que no operan sobre el Modelo Factura,
            // y que deben ser autorizados manualmente con la Policy si es necesario.
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
     * @param string $uri La parte del endpoint (ej: '/facturas/100'). Debe empezar con '/'.
     * @param array $data Cuerpo de la petición (para POST, PUT, PATCH, DELETE) o datos para GET/DELETE si se requiere.
     * @param array $query Parámetros de la URL (para GET).
     * @param bool $returnViewData Si es true, retorna los datos de la API en formato array|null (para usar en vistas).
     * @return JsonResponse|array|null
     */
    private function callExternalApi(string $method, string $uri, array $data = [], array $query = [], bool $returnViewData = false): JsonResponse|array|null
    {
        // Se construye la URL: $this->apiBaseUrl (sin barra final) + $uri (con barra inicial)
        $url = $this->apiBaseUrl . $uri;

        try {
            // Se define el cliente HTTP con el timeout
            $client = Http::timeout(15);

            // Determinar el método y realizar la llamada
            $response = match (strtolower($method)) {
                // Para GET, fusionamos $query y $data como query parameters.
                'get' => $client->get($url, array_merge($query, $data)),
                'post' => $client->post($url, $data),
                'put' => $client->put($url, $data),
                'patch' => $client->patch($url, $data),
                // Para DELETE, enviamos $data como body si se requiere.
                'delete' => $client->delete($url, $data),
                default => throw new \InvalidArgumentException("Método HTTP no soportado: {$method}")
            };

            if ($response->successful()) {
                $responseData = $response->json();

                // Si se pide para la vista, retornamos solo los datos (array)
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
                $status === 404 => 'Recurso no encontrado en la API externa. Verifique la ruta: ' . $uri,
                $status >= 400 && $status < 500 => 'Error de la aplicación al procesar la solicitud en la API externa.',
                $status >= 500 => 'Error interno del servidor de la API externa.',
                default => 'Error desconocido en la API externa.',
            };

            return response()->json([
                'message' => $message,
                'api_error' => $response->json() ?? $response->body(), // Usar body si no es JSON válido
            ], $status);

        } catch (ConnectionException $e) {
             // Error de conexión: DNS, Timeout, Servidor Inaccesible
             Log::error('Error de conexión con el servidor de facturación: ' . $e->getMessage());

             // Si es para la vista y falla la conexión, retorna null
             if ($returnViewData) {
               return null;
             }

             return response()->json(['message' => 'Error de conexión con la API de Node.js. Verifique que el servidor esté corriendo.', 'error' => $e->getMessage()], 503);
        } catch (\Exception $e) {
            // Error genérico
            Log::error('Error en FacturaController::callExternalApi: ' . $e->getMessage());

            if ($returnViewData) {
                return null;
            }

            return response()->json(['message' => 'Error inesperado al procesar la solicitud.', 'error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // MÉTODOS CRUD DE CABECERA (Header)
    // =========================================================================

    /**
     * Muestra una lista de facturas. Petición GET a la API de Node.js.
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $facturas = $this->callExternalApi('GET', '/facturas', [], $request->query(), true);
        $apiError = null;

        if (is_array($facturas)) {
            // Si la API retorna un array, se pasa a la vista
            return view('factura.index', compact('facturas'));
        } else {
            // Si retorna null (error de conexión o API), se maneja como error en la vista
            $facturas = [];
            $apiError = 'No se pudo cargar el listado de facturas. Verifique la conexión o el endpoint /facturas en la API externa.';
            return view('factura.index', compact('facturas', 'apiError'));
        }
    }

    /**
     * Muestra la vista para crear una nueva factura, cargando las listas de Clientes, Productos y Tratamientos.
     * GET /factura/create
     * @return View
     */
    public function create(): View
    {
        // Se asume que las rutas singularizadas (/cliente, /producto, /tratamiento) son las correctas para la API de precarga.
        $clientes = $this->callExternalApi('GET', '/cliente', [], [], true);
        $productos = $this->callExternalApi('GET', '/producto', [], [], true);
        $tratamientos = $this->callExternalApi('GET', '/tratamiento', [], [], true);

        // Inicializar variables para manejo de errores en la vista
        $apiError = null;
        $errorDetails = [];

        // Verificar si alguna de las llamadas falló (retornó null)
        if ($clientes === null) {
            $errorDetails[] = 'Clientes (/cliente)';
        }
        if ($productos === null) {
            $errorDetails[] = 'Productos (/producto)';
        }
        if ($tratamientos === null) {
            $errorDetails[] = 'Tratamientos (/tratamiento)';
        }

        if (!empty($errorDetails)) {
            $apiError = 'Error crítico: No se pudieron cargar los datos de precarga: ' . implode(', ', $errorDetails) . '. Verifique que la API de Node.js esté corriendo y las rutas correctas.';
        }

        // Si alguna lista es null, la convertimos a un array vacío para evitar errores en la vista.
        $clientes = is_array($clientes) ? $clientes : [];
        $productos = is_array($productos) ? $productos : [];
        $tratamientos = is_array($tratamientos) ? $tratamientos : [];

        // Retornar la vista con todos los datos necesarios
        return view('factura.create', compact('clientes', 'productos', 'tratamientos', 'apiError'));
    }

    /**
     * Crea y almacena una nueva factura (Cabecera + Detalles). Envía los datos completos a la API.
     * @param FacturaStoreRequest $request
     * @return JsonResponse
     */
    public function store(FacturaStoreRequest $request): JsonResponse
    {
        // FacturaStoreRequest se encarga de la validación completa.
        return $this->callExternalApi('POST', '/facturas', $request->validated());
    }

    /**
     * Muestra el detalle completo de una factura específica.
     * @param Factura $factura
     * @return JsonResponse
     */
    public function show(Factura $factura): JsonResponse
    {
        $facturaId = $factura->Cod_Factura;
        return $this->callExternalApi('GET', "/facturas/{$facturaId}");
    }

    /**
     * Actualiza el estado, método de pago o descuento de una factura existente (Actualización parcial/PATCH).
     * @param Request $request
     * @param Factura $factura
     * @return JsonResponse
     */
    public function update(Request $request, Factura $factura): JsonResponse
    {
        $facturaId = $factura->Cod_Factura;

        try {
            // Validación específica para los campos que pueden ser actualizados.
            $validated = $request->validate([
                'Metodo_Pago' => 'sometimes|required|string|max:50',
                'Estado_Pago' => 'sometimes|required|string|in:PENDIENTE,PAGADA,ANULADA',
                'Descuento_Aplicado' => 'sometimes|required|numeric|min:0|max:100',
            ]);
            return $this->callExternalApi('PATCH', "/facturas/{$facturaId}", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Elimina (Anula) una factura y revierte los cambios de stock de productos.
     * @param Factura $factura
     * @return JsonResponse
     */
    public function destroy(Factura $factura): JsonResponse
    {
        $facturaId = $factura->Cod_Factura;
        // DELETE debe delegar a la API para anular la factura y gestionar el stock.
        return $this->callExternalApi('DELETE', "/facturas/{$facturaId}");
    }

    // =========================================================================
    // MÉTODOS CRUD DE DETALLES (Lines) - LECTURA y CREACIÓN
    // =========================================================================

    /**
     * Obtiene los detalles de tratamiento de una factura (usando Cod_Factura como query parameter).
     * @param Request $request
     * @return JsonResponse
     */
    public function getDetalleTratamiento(Request $request): JsonResponse
    {
        $validated = $request->validate(['Cod_Factura' => 'required|integer']);
        return $this->callExternalApi('GET', "/detalle_factura_tratamiento", [], $validated);
    }

    /**
     * Obtiene los detalles de producto de una factura (usando Cod_Factura como query parameter).
     * @param Request $request
     * @return JsonResponse
     */
    public function getDetalleProducto(Request $request): JsonResponse
    {
        $validated = $request->validate(['Cod_Factura' => 'required|integer']);
        return $this->callExternalApi('GET', "/detalle_factura_producto", [], $validated);
    }

    /**
     * Agrega un detalle de producto a una factura (POST).
     * @param Request $request
     * @return JsonResponse
     */
    public function storeDetalleProducto(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Cod_Factura' => 'required|integer',
                'Cod_Producto' => 'required|integer',
                'Cantidad' => 'required|integer|min:1',
                'Precio_Unitario' => 'required|numeric|min:0',
            ]);
            // El API de Node.js es responsable de actualizar el stock.
            return $this->callExternalApi('POST', "/detalle_factura_producto", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Agrega un detalle de tratamiento a una factura (POST).
     * @param Request $request
     * @return JsonResponse
     */
    public function storeDetalleTratamiento(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Cod_Factura' => 'required|integer',
                'Cod_Tratamiento' => 'required|integer',
                'Costo' => 'required|numeric|min:0',
                'Descripcion' => 'sometimes|string|max:255',
            ]);
            return $this->callExternalApi('POST', "/detalle_factura_tratamiento", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    // =========================================================================
    // MÉTODOS CRUD DE DETALLES (Lines) - ACTUALIZACIÓN y ELIMINACIÓN
    // =========================================================================

    /**
     * Actualiza un detalle de producto en la factura (Actualización parcial/PATCH).
     * El $idDetalle es el ID del detalle, no el Cod_Factura.
     * @param Request $request
     * @param int $idDetalle El ID del detalle de producto.
     * @return JsonResponse
     */
    public function updateDetalleProducto(Request $request, int $idDetalle): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Cantidad' => 'sometimes|required|integer|min:1',
                'Precio_Unitario' => 'sometimes|required|numeric|min:0',
            ]);
            // El API de Node.js es responsable de reajustar el stock.
            return $this->callExternalApi('PATCH', "/detalle_factura_producto/{$idDetalle}", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Elimina un detalle de producto de la factura, revirtiendo el stock.
     * @param int $idDetalle El ID del detalle de producto.
     * @return JsonResponse
     */
    public function destroyDetalleProducto(int $idDetalle): JsonResponse
    {
        // DELETE debe delegar a la API para revertir el stock.
        return $this->callExternalApi('DELETE', "/detalle_factura_producto/{$idDetalle}");
    }

    /**
     * Actualiza un detalle de tratamiento en la factura (Actualización parcial/PATCH).
     * El $idDetalle es el ID del detalle, no el Cod_Factura.
     * @param Request $request
     * @param int $idDetalle El ID del detalle de tratamiento.
     * @return JsonResponse
     */
    public function updateDetalleTratamiento(Request $request, int $idDetalle): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Costo' => 'sometimes|required|numeric|min:0',
                'Descripcion' => 'sometimes|string|max:255',
            ]);
            return $this->callExternalApi('PATCH', "/detalle_factura_tratamiento/{$idDetalle}", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Elimina un detalle de tratamiento de la factura.
     * @param int $idDetalle El ID del detalle de tratamiento.
     * @return JsonResponse
     */
    public function destroyDetalleTratamiento(int $idDetalle): JsonResponse
    {
        return $this->callExternalApi('DELETE', "/detalle_factura_tratamiento/{$idDetalle}");
    }
}
