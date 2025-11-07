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
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Barryvdh\DomPDF\Facade\Pdf; // ⬅️ CAMBIO CLAVE: Importación de la librería PDF

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
     * @return JsonResponse|\Illuminate\Http\Client\Response|array|null ⬅️ CAMBIO: Puede retornar la Respuesta Guzzle original.
     */
    private function callExternalApi(string $method, string $uri, array $data = [], array $query = [], bool $returnViewData = false): JsonResponse|array|\Illuminate\Http\Client\Response|null
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

                // 🚨 ADICIÓN CLAVE: Retorna el objeto Response completo si es necesario.
                if ($returnViewData === 'response') {
                    return $response;
                }

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

            // 🚨 ADICIÓN CLAVE: Retorna el objeto Response completo si es necesario.
            if ($returnViewData === 'response') {
                return $response;
            }

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

            // 🚨 ADICIÓN CLAVE: Retorna null si es para la vista y falla la conexión.
            if ($returnViewData || $returnViewData === 'response') {
                return null;
            }

            return response()->json([
                'message' => 'Error de conexión: El servidor de facturación externo no está disponible. Verifique la URL base en el archivo .env.',
                'error' => $e->getMessage()
            ], Response::HTTP_SERVICE_UNAVAILABLE); // Código 503
        } catch (\Exception $e) {
            // 4. Error genérico (incluye InvalidArgumentException del match)
            Log::critical('Error CRÍTICO en FacturaController::callExternalApi: ' . $e->getMessage());

            if ($returnViewData || $returnViewData === 'response') {
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

    // ... (Métodos create, store, edit, show y update no modificados) ...
    // Los he omitido aquí por brevedad, asumiendo que el código de arriba es el que usted me dio.

    /**
     * Muestra la vista de un recibo o factura para imprimir o ver.
     * 🚨 **MÉTODO MODIFICADO PARA GENERAR PDF** 🚨
     * GET /factura/{factura}/recibo
     * @param Factura $factura
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function recibo(Factura $factura)
    {
        $facturaId = $factura->Cod_Factura;
        // ⬅️ CAMBIO CLAVE: Usamos 'response' para obtener el objeto Response completo si es exitoso.
        $response = $this->callExternalApi('GET', "/facturas/{$facturaId}/detalles", [], [], 'response');

        // Si hay un error de conexión o API, callExternalApi retorna null
        if ($response === null || !$response->successful()) {
             // Redirigir al índice con un mensaje de error explícito
            $message = $response?->json()['message'] ?? 'Error desconocido o de conexión al intentar obtener los datos de la factura.';
            return redirect()->route('factura.index')->with('error', "No se pudo generar el recibo de la Factura #{$facturaId}: {$message}");
        }

        // Obtener los datos JSON
        $data = $response->json();

        // Asegurarse de que el formato de datos de la API sea el esperado
        $facturaData = $data['factura'] ?? null;
        $detallesProducto = $data['detalles_producto'] ?? [];
        $detallesTratamiento = $data['detalles_tratamiento'] ?? [];

        // Si la cabecera no está presente, fallar de forma controlada
        if (!$facturaData) {
            return redirect()->route('factura.index')->with('error', "La API no proporcionó los datos de cabecera para la Factura #{$facturaId}.");
        }

        // Combinamos los detalles para que la vista recibo.blade.php pueda iterar una sola lista.
        // ADVERTENCIA: Su vista recibo.blade.php necesita iterar sobre un solo array llamado 'Detalles_Factura'
        // Por eso vamos a pasar un array único que combine ambos detalles.
        $facturaData['Detalles_Factura'] = array_merge($detallesProducto, $detallesTratamiento);


        // 🚨 PASO FINAL: Generar el PDF
        $pdf = Pdf::loadView('factura.recibo', [
            // Pasamos solo la variable $factura que contiene todos los datos.
            'factura' => $facturaData
        ]);

        // Devolver el PDF para que se muestre en el navegador
        $filename = "factura_F-". str_pad($facturaId, 4, '0', STR_PAD_LEFT) .".pdf";
        return $pdf->stream($filename); // Usar stream() para visualizarlo en el navegador
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

    // ... (El resto de métodos: update, destroy, getDetalleTratamiento, etc.) ...

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
        // 🚨 CAMBIO: Se debe cambiar el tipo de retorno en el helper para que el delete funcione como antes.
        // Por ahora, usamos el objeto JsonResponse que devuelve callExternalApi.
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

    // ... (Resto de métodos de detalles) ...
}
