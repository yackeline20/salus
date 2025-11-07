<?php

declare(strict_types=1);

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
use Illuminate\Http\RedirectResponse; // ⬅️ AGREGADA ESTA IMPORTACIÓN
use Symfony\Component\HttpFoundation\Response; // Importado para usar constantes HTTP

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
    // Timeout por defecto para las peticiones en segundos
    protected int $apiTimeout = 15;

    /**
     * Constructor para inyectar la Policy de autorización y configurar la URL base.
     */
    public function __construct()
    {
        // Obtiene la URL base desde el archivo .env y asegura que no termina en barra.
        $this->apiBaseUrl = rtrim(env('SALUS_API_BASE_URL', 'http://127.0.0.1:3000'), '/');

        // Autorización de recursos.
        $this->authorizeResource(Factura::class, 'factura', [
            // Excluimos la autorización automática para los métodos de detalle y agregamos 'recibo'.
            'except' => [
                'storeDetalleProducto', 'storeDetalleTratamiento',
                'getDetalleTratamiento', 'getDetalleProducto',
                'updateDetalleProducto', 'destroyDetalleProducto',
                'updateDetalleTratamiento', 'destroyDetalleTratamiento',
                'recibo', // Excluir para manejar la autorización de forma manual si es necesario
            ]
        ]);
    }

    /**
     * Método auxiliar robusto para encapsular la lógica de llamada a la API y manejo de errores.
     *
     * @param string $method Método HTTP (GET, POST, PATCH, DELETE).
     * @param string $uri La parte del endpoint (ej: '/facturas/100'). Debe empezar con '/'.
     * @param array $data Cuerpo de la petición (para POST, PUT, PATCH, DELETE).
     * @param array $query Parámetros de la URL (para GET/DELETE).
     * @param bool $returnViewData Si es true, retorna los datos decodificados (array|null) para su uso en vistas.
     * @return JsonResponse|array|null Retorna JsonResponse en llamadas de API, o array|null si $returnViewData es true.
     */
    private function callExternalApi(string $method, string $uri, array $data = [], array $query = [], bool $returnViewData = false): JsonResponse|array|null
    {
        $url = $this->apiBaseUrl . $uri;
        $methodLower = strtolower($method);

        try {
            // Se define el cliente HTTP con timeout y solicitando JSON
            $client = Http::timeout($this->apiTimeout)
                          ->acceptJson();

            // Realizar la llamada según el método
            $response = match ($methodLower) {
                'get' => $client->get($url, $query), // Solo usamos $query para GET
                'post' => $client->post($url, $data),
                'put' => $client->put($url, $data),
                'patch' => $client->patch($url, $data),
                'delete' => $client->delete($url, $data), // DELETE puede llevar body
                default => throw new \InvalidArgumentException("Método HTTP no soportado: {$method}")
            };

            // 1. Manejo de respuesta exitosa (2xx)
            if ($response->successful()) {
                $responseData = $response->json();

                // Si se pide para la vista, retorna el array de datos
                if ($returnViewData) {
                    // Si no hay contenido, retorna un array vacío o los datos decodificados
                    return is_array($responseData) ? $responseData : ($responseData === null ? [] : $responseData);
                }

                // Para llamadas de API, retorna un JsonResponse con el status de la API.
                $responseBody = $responseData ?? ['message' => 'Operación exitosa'];
                return response()->json($responseBody, $response->status());
            }

            // 2. Manejo de errores de la API externa (4xx o 5xx)
            $status = $response->status();
            // Intenta decodificar el error de la API o usa el cuerpo sin procesar como mensaje
            $apiErrorBody = $response->json() ?? ['message' => $response->body() ?: 'Error de la API externa sin cuerpo de respuesta.'];

            Log::error("API {$method} {$uri} falló (Status: {$status}): " . json_encode($apiErrorBody));

            // Si es para la vista y falla, retorna null para manejo de error en la vista
            if ($returnViewData) {
                return null;
            }

            // Para la respuesta JSON, crea un mensaje de proxy más útil
            $message = match (true) {
                $status === Response::HTTP_NOT_FOUND => 'El recurso no fue encontrado en el sistema de facturación externo. Por favor, verifique el endpoint.',
                $status >= 400 && $status < 500 => $apiErrorBody['message'] ?? 'Error de validación o aplicación en la API externa.',
                $status >= 500 => 'Error interno en el servidor de facturación externo. Intente más tarde.',
                default => 'Error desconocido en la API externa.',
            };

            return response()->json([
                'message' => $message,
                'api_error' => $apiErrorBody,
            ], $status);

        } catch (ConnectionException $e) {
            // 3. Error de conexión (Timeout, DNS, Servidor Inaccesible, etc.)
            Log::error('Error de conexión con el servidor de facturación: ' . $e->getMessage());

            // Si es para la vista y falla la conexión, retorna null
            if ($returnViewData) {
                return null;
            }

            return response()->json([
                'message' => 'Error de conexión: El servidor de facturación externo no está disponible. Verifique la URL base en el archivo .env.',
                'error' => $e->getMessage()
            ], Response::HTTP_SERVICE_UNAVAILABLE); // Código 503
        } catch (\Exception $e) {
            // 4. Error genérico (incluye InvalidArgumentException del match)
            Log::critical('Error CRÍTICO en FacturaController::callExternalApi: ' . $e->getMessage());

            if ($returnViewData) {
                return null;
            }

            return response()->json(['message' => 'Error inesperado del proxy.', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR); // Código 500
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
        // Se llama al helper con returnViewData=true para obtener array|null
        $facturas = $this->callExternalApi('GET', '/facturas', [], $request->query(), true);
        $apiError = null;

        if ($facturas === null) {
            // Error de conexión o API
            $facturas = [];
            $apiError = 'Error: No se pudo cargar el listado de facturas. Verifique la conexión con el servidor de Node.js y la ruta `/facturas`.';
        }

        // Ya sea array de datos o array vacío con error
        return view('factura.index', compact('facturas', 'apiError'));
    }

    /**
     * Muestra la vista para crear una nueva factura, cargando las listas de Clientes, Productos y Tratamientos.
     * GET /factura/create
     * @return View
     */
    public function create(): View
    {
        // Llamadas secuenciales para precarga. Se usa returnViewData=true.
        $clientes = $this->callExternalApi('GET', '/cliente', [], [], true);
        $productos = $this->callExternalApi('GET', '/producto', [], [], true);
        $tratamientos = $this->callExternalApi('GET', '/tratamiento', [], [], true);

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

        $apiError = !empty($errorDetails)
            ? 'Error crítico: No se pudieron cargar los datos de precarga: ' . implode(', ', $errorDetails) . '. Verifique que la API de Node.js esté corriendo y las rutas correctas.'
            : null;

        // Si alguna lista es null, la convertimos a un array vacío para evitar errores en la vista.
        $clientes = is_array($clientes) ? $clientes : [];
        $productos = is_array($productos) ? $productos : [];
        $tratamientos = is_array($tratamientos) ? $tratamientos : [];

        // Retornar la vista con todos los datos necesarios
        return view('factura.create', compact('clientes', 'productos', 'tratamientos', 'apiError'));
    }

    /**
     * Crea y almacena una nueva factura (solo cabecera).
     *
     * @param FacturaStoreRequest $request
     * @return JsonResponse
     */
    public function store(FacturaStoreRequest $request): JsonResponse
    {
        // FacturaStoreRequest se encarga de la validación completa.
        return $this->callExternalApi('POST', '/facturas', $request->validated());
    }

    /**
     * Muestra el formulario para editar la factura especificada.
     * GET /facturas/{factura}/edit
     *
     * @param Factura $factura
     * @return View
     */
    public function edit(Factura $factura): View
    {
        // Su vista de edición requiere solo el Cod_Factura para que el JavaScript
        // realice el fetch de los datos. El Route Model Binding ($factura)
        // asegura que la factura exista en su DB local.
        return view('factura.edit', [
            'facturaId' => $factura->Cod_Factura
        ]);
    }

    /**
     * Muestra la vista de un recibo o factura para imprimir o ver.
     * Este es el NUEVO método para mostrar la factura completa al usuario.
     * GET /factura/{factura}/recibo
     * @param Factura $factura
     * @return View
     */
    public function recibo(Factura $factura): View
    {
        $facturaId = $factura->Cod_Factura;
        // Llama a la API de Node.js para obtener la cabecera, productos y tratamientos.
        $data = $this->callExternalApi('GET', "/facturas/{$facturaId}", [], [], true);

        $apiError = null;
        $facturaData = null;
        $detalles = [];

        // Si la llamada falla o retorna null, preparamos el mensaje de error.
        if ($data === null || empty($data) || !isset($data['factura'])) {
            $apiError = 'Error: No se pudieron obtener los detalles de la factura #' . $facturaId . '. Verifique que el endpoint `/facturas/{id}` en Node.js esté funcionando correctamente y que devuelva la cabecera y detalles.';
        } else {
            // Si la llamada fue exitosa, preparamos los datos para la vista.
            $facturaData = $data['factura'] ?? null;
            $detallesProducto = $data['detalles_producto'] ?? [];
            $detallesTratamiento = $data['detalles_tratamiento'] ?? [];

            // Combinamos los detalles para que la vista pueda iterar una sola lista.
            $detalles = array_merge($detallesProducto, $detallesTratamiento);
        }

        // Retorna la vista con los datos (factura, detalles) o solo el error.
        return view('factura.recibo', [
            'factura' => $facturaData,
            'detalles' => $detalles,
            'apiError' => $apiError
        ]);
    }

    /**
     * Muestra el detalle completo de una factura específica (JSON).
     * Este endpoint lo usa el JS de su edit.blade.php.
     * @param Factura $factura
     * @return JsonResponse
     */
    public function show(Factura $factura): JsonResponse
    {
        $facturaId = $factura->Cod_Factura;
        return $this->callExternalApi('GET', "/facturas/{$facturaId}");
    }

    /**
     * Actualiza la cabecera de una factura existente con el método PUT.
     * **ESTE MÉTODO ES EL ACTUALIZADO** para manejar la sumisión del formulario de edición completa.
     *
     * @param Request $request
     * @param Factura $factura
     * @return JsonResponse
     */
    public function update(Request $request, Factura $factura): JsonResponse
    {
        $facturaId = $factura->Cod_Factura;

        try {
            // **Validación de campos completos** del formulario edit.blade.php
            $validated = $request->validate([
                'Cod_Cliente' => 'required|integer', // Es readonly en el formulario, pero debe enviarse
                'Fecha_Factura' => 'required|date',
                'Total_Factura' => 'required|numeric|min:0',
                'Metodo_Pago' => 'required|string|max:50',
                'Estado_Pago' => 'required|string|in:Pagada,Pendiente,Cancelada', // Se ajusta a los valores de su select
                'Descuento_Aplicado' => 'required|numeric|min:0',
                // El Cod_Factura se extrae del modelo, no del request body
            ]);

            // Se utiliza el método **PUT** en la llamada a la API para actualizar el recurso completo.
            // Los campos validados son el cuerpo de la petición.
            return $this->callExternalApi('PUT', "/facturas/{$facturaId}", $validated);
        } catch (ValidationException $e) {
            // Manejo de error 422 (Unprocessable Entity) de Laravel
            return response()->json(['message' => 'Error de validación en los datos de la factura', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    /**
     * Elimina (Anula) una factura y revierte los cambios de stock de productos.
     * * 🚨 **MÉTODO CORREGIDO PARA REDIRECCIÓN** 🚨
     * * @param Factura $factura
     * @return RedirectResponse
     */
    public function destroy(Factura $factura): RedirectResponse
    {
        $facturaId = $factura->Cod_Factura;

        // 1. Llama a la API de Node.js usando el helper.
        // callExternalApi devuelve un JsonResponse (si no retorna viewData)
        $response = $this->callExternalApi('DELETE', "/facturas/{$facturaId}");

        $status = $response->getStatusCode();

        // 2. Si el código de estado es 200 (OK), asumimos éxito.
        if ($status === Response::HTTP_OK) { // Usa constante para mayor claridad
            // Redirigir a la vista principal (index) con un mensaje de éxito.
            return redirect()->route('factura.index')
                ->with('success', "Factura #F-{$facturaId} y sus dependencias han sido eliminadas correctamente.");
        }

        // 3. Si hay un error, intentar obtener el mensaje de error del JSON.
        $errorBody = json_decode($response->getContent(), true) ?? [];
        $message = $errorBody['message'] ?? 'Error desconocido al eliminar la factura desde el servidor externo.';

        // Redirigir con un mensaje de error.
        return redirect()->route('factura.index')
            ->with('error', "Error al eliminar la Factura #F-{$facturaId}: {$message}");
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
        // El Cod_Factura se pasa como parámetro de consulta ($query)
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
                // Se verifica que la factura exista localmente antes de proxying
                'Cod_Factura' => 'required|integer|exists:facturas,Cod_Factura',
                'Cod_Producto' => 'required|integer',
                'Cantidad' => 'required|integer|min:1',
                'Precio_Unitario' => 'required|numeric|min:0',
            ]);
            // El API de Node.js es responsable de actualizar el stock.
            return $this->callExternalApi('POST', "/detalle_factura_producto", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
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
                // Se verifica que la factura exista localmente antes de proxying
                'Cod_Factura' => 'required|integer|exists:facturas,Cod_Factura',
                'Cod_Tratamiento' => 'required|integer',
                'Costo' => 'required|numeric|min:0',
                'Descripcion' => 'nullable|string|max:255', // Cambiado a 'nullable'
            ]);
            return $this->callExternalApi('POST', "/detalle_factura_tratamiento", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    // =========================================================================
    // MÉTODOS CRUD DE DETALLES (Lines) - ACTUALIZACIÓN y ELIMINACIÓN
    // =========================================================================

    /**
     * Actualiza un detalle de producto en la factura (Actualización parcial/PATCH).
     * El $idDetalle es el ID del detalle de producto.
     * @param Request $request
     * @param int $idDetalle El ID del detalle de producto.
     * @return JsonResponse
     */
    public function updateDetalleProducto(Request $request, int $idDetalle): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Cantidad' => 'sometimes|integer|min:1',
                'Precio_Unitario' => 'sometimes|numeric|min:0',
            ]);

            // Validación: Si no hay campos validados, retornar error 400
            if (empty($validated)) {
                return response()->json(['message' => 'No se proporcionaron datos válidos para actualizar el detalle.'], Response::HTTP_BAD_REQUEST);
            }

            // El API de Node.js es responsable de reajustar el stock.
            return $this->callExternalApi('PATCH', "/detalle_factura_producto/{$idDetalle}", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
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
     * El $idDetalle es el ID del detalle de tratamiento.
     * @param Request $request
     * @param int $idDetalle El ID del detalle de tratamiento.
     * @return JsonResponse
     */
    public function updateDetalleTratamiento(Request $request, int $idDetalle): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Costo' => 'sometimes|numeric|min:0',
                'Descripcion' => 'sometimes|nullable|string|max:255',
            ]);

            // Validación: Si no hay campos validados, retornar error 400
            if (empty($validated)) {
                return response()->json(['message' => 'No se proporcionaron datos válidos para actualizar el detalle.'], Response::HTTP_BAD_REQUEST);
            }

            return $this->callExternalApi('PATCH', "/detalle_factura_tratamiento/{$idDetalle}", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
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
