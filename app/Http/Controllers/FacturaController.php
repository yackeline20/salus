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
// 🟢 Importar la fachada de DomPDF
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * FacturaController actúa como un proxy/gateway que redirige
 * todas las peticiones CRUD de facturación a una API externa (Node.js).
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
            // Excluimos la autorización automática para los métodos de detalle y los de vista web
            'except' => [
                'storeDetalleProducto', 'storeDetalleTratamiento',
                'getDetalleTratamiento', 'getDetalleProducto',
                'updateDetalleProducto', 'destroyDetalleProducto',
                'updateDetalleTratamiento', 'destroyDetalleTratamiento',
                'recibo',
                'showFactura',
                'exportPdf',
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
                'get' => $client->get($url, $query),
                'post' => $client->post($url, $data),
                'put' => $client->put($url, $data),
                'patch' => $client->patch($url, $data),
                'delete' => $client->delete($url, $data),
                default => throw new \InvalidArgumentException("Método HTTP no soportado: {$method}")
            };

            // 1. Manejo de respuesta exitosa (2xx)
            if ($response->successful()) {
                $responseData = $response->json();

                // Si se pide para la vista, retorna el array de datos
                if ($returnViewData) {
                    return is_array($responseData) ? $responseData : ($responseData === null ? [] : $responseData);
                }

                // Para llamadas de API, retorna un JsonResponse con el status de la API.
                $responseBody = $responseData ?? ['message' => 'Operación exitosa'];
                return response()->json($responseBody, $response->status());
            }

            // 2. Manejo de errores de la API externa (4xx o 5xx)
            $status = $response->status();
            $apiErrorBody = $response->json() ?? ['message' => $response->body() ?: 'Error de la API externa sin cuerpo de respuesta.'];

            Log::error("API {$method} {$uri} falló (Status: {$status}): " . json_encode($apiErrorBody));

            if ($returnViewData) {
                return null;
            }

            $message = match (true) {
                $status === Response::HTTP_NOT_FOUND => 'El recurso no fue encontrado en el sistema de facturación externo.',
                $status >= 400 && $status < 500 => $apiErrorBody['message'] ?? 'Error de validación o aplicación en la API externa.',
                $status >= 500 => 'Error interno en el servidor de facturación externo. Intente más tarde.',
                default => 'Error desconocido en la API externa.',
            };

            return response()->json([
                'message' => $message,
                'api_error' => $apiErrorBody,
            ], $status);

        } catch (ConnectionException $e) {
            Log::error('Error de conexión con el servidor de facturación: ' . $e->getMessage());

            if ($returnViewData) {
                return null;
            }

            return response()->json([
                'message' => 'Error de conexión: El servidor de facturación externo no está disponible.',
                'error' => $e->getMessage()
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (\Exception $e) {
            Log::critical('Error CRÍTICO en FacturaController::callExternalApi: ' . $e->getMessage());

            if ($returnViewData) {
                return null;
            }

            return response()->json(['message' => 'Error inesperado del proxy.', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // =========================================================================
    // MÉTODOS CRUD DE CABECERA (Header)
    // =========================================================================

    /**
     * Muestra una lista de facturas. Petición GET a la API de Node.js.
     */
    public function index(Request $request): JsonResponse|View
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->callExternalApi('GET', '/facturas');
        }

        return view('factura.index');
    }

    /**
     * Muestra el formulario para crear una nueva factura.
     */
    public function create(): View
    {
        $clientesData = $this->callExternalApi('GET', '/clientes', returnViewData: true);

        return view('factura.create', [
            'clientes' => $clientesData ?? [],
        ]);
    }

    /**
     * Crea una nueva factura en el sistema externo.
     */
    public function store(FacturaStoreRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        if (isset($validated['Cod_Usuario'])) {
            $validated['Cod_Usuario'] = (int) $validated['Cod_Usuario'];
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->callExternalApi('POST', '/facturas', $validated);
        }

        $responseData = $this->callExternalApi('POST', '/facturas', $validated, returnViewData: true);

        if ($responseData === null || !isset($responseData['factura'])) {
            return redirect()->route('factura.create')
                ->withInput()
                ->with('error', 'Error al crear la factura. Por favor, intente nuevamente.');
        }

        $codFactura = $responseData['factura']['Cod_Factura'] ?? null;

        if ($codFactura) {
            return redirect()->route('factura.show', ['factura' => $codFactura])
                ->with('success', "Factura #F-{$codFactura} creada exitosamente.");
        }

        return redirect()->route('factura.index')
            ->with('error', 'Factura creada pero no se pudo obtener su identificador.');
    }

    /**
     * 🟢 Muestra una factura específica para una vista web.
     * Usa el nuevo endpoint /facturas/{id}/completa
     */
    public function showFactura(Factura $factura): View
    {
        $facturaId = $factura->Cod_Factura;

        // Llamar al nuevo endpoint que retorna factura + detalles en una sola respuesta
        $facturaData = $this->callExternalApi('GET', "/facturas/{$facturaId}/completa", returnViewData: true);

        if ($facturaData === null) {
            return view('factura.show', [
                'factura' => ['Cod_Factura' => $facturaId],
                'apiError' => true,
            ]);
        }

        return view('factura.show', [
            'factura' => $facturaData['factura'] ?? [],
            'detalles' => $facturaData['detalles'] ?? [],
            'apiError' => false,
        ]);
    }

    /**
     * Muestra el formulario para editar una factura existente.
     */
    public function edit(Factura $factura): View
    {
        $facturaId = $factura->Cod_Factura;

        $facturaData = $this->callExternalApi('GET', "/facturas/{$facturaId}", returnViewData: true);
        $clientesData = $this->callExternalApi('GET', '/clientes', returnViewData: true);

        return view('factura.edit', [
            'factura' => $facturaData ?? ['Cod_Factura' => $facturaId],
            'clientes' => $clientesData ?? [],
            'apiError' => $facturaData === null,
        ]);
    }

    /**
     * Actualiza una factura en la API externa.
     */
    public function update(Request $request, Factura $factura): JsonResponse|RedirectResponse
    {
        $facturaId = $factura->Cod_Factura;

        try {
            $validated = $request->validate([
                'Cod_Cliente' => 'sometimes|integer|exists:clientes,Cod_Cliente',
                'Metodo_Pago' => 'sometimes|string|in:Efectivo,Tarjeta,Transferencia',
                'Estado_Pago' => 'sometimes|string|in:PENDIENTE,PAGADA,ANULADA',
                'Total_Factura' => 'sometimes|numeric|min:0',
                'Descuento_Aplicado' => 'sometimes|numeric|min:0',
            ]);
        } catch (ValidationException $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return redirect()->route('factura.edit', ['factura' => $facturaId])
                ->withErrors($e->validator)
                ->withInput();
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->callExternalApi('PUT', "/facturas/{$facturaId}", $validated);
        }

        $responseData = $this->callExternalApi('PUT', "/facturas/{$facturaId}", $validated, returnViewData: true);

        if ($responseData === null) {
            return redirect()->route('factura.edit', ['factura' => $facturaId])
                ->withInput()
                ->with('error', 'Error al actualizar la factura. Intente nuevamente.');
        }

        return redirect()->route('factura.show', ['factura' => $facturaId])
            ->with('success', "Factura #F-{$facturaId} actualizada exitosamente.");
    }

    /**
     * Elimina una factura en la API externa.
     */
    public function destroy(Factura $factura): RedirectResponse
    {
        $facturaId = $factura->Cod_Factura;

        $response = $this->callExternalApi('DELETE', "/facturas/{$facturaId}");

        $status = $response->getStatusCode();

        if ($status === Response::HTTP_OK) {
            return redirect()->route('factura.index')
                ->with('success', "Factura #F-{$facturaId} y sus dependencias han sido eliminadas correctamente.");
        }

        $errorBody = json_decode($response->getContent(), true) ?? [];
        $message = $errorBody['message'] ?? 'Error desconocido al eliminar la factura desde el servidor externo.';

        return redirect()->route('factura.index')
            ->with('error', "Error al eliminar la Factura #F-{$facturaId}: {$message}");
    }

    // =========================================================================
    // MÉTODOS CRUD DE DETALLES (Lines)
    // =========================================================================

    public function getDetalleTratamiento(Request $request): JsonResponse
    {
        $validated = $request->validate(['Cod_Factura' => 'required|integer']);
        return $this->callExternalApi('GET', "/detalle_factura_tratamiento", [], $validated);
    }

    public function getDetalleProducto(Request $request): JsonResponse
    {
        $validated = $request->validate(['Cod_Factura' => 'required|integer']);
        return $this->callExternalApi('GET', "/detalle_factura_producto", [], $validated);
    }

    public function storeDetalleProducto(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Cod_Factura' => 'required|integer|exists:facturas,Cod_Factura',
                'Cod_Producto' => 'required|integer',
                'Cantidad' => 'required|integer|min:1',
                'Precio_Unitario' => 'required|numeric|min:0',
            ]);
            return $this->callExternalApi('POST', "/detalle_factura_producto", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function storeDetalleTratamiento(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Cod_Factura' => 'required|integer|exists:facturas,Cod_Factura',
                'Cod_Tratamiento' => 'required|integer',
                'Costo' => 'required|numeric|min:0',
                'Descripcion' => 'nullable|string|max:255',
            ]);
            return $this->callExternalApi('POST', "/detalle_factura_tratamiento", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateDetalleProducto(Request $request, int $idDetalle): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Cantidad' => 'sometimes|integer|min:1',
                'Precio_Unitario' => 'sometimes|numeric|min:0',
            ]);

            if (empty($validated)) {
                return response()->json(['message' => 'No se proporcionaron datos válidos para actualizar el detalle.'], Response::HTTP_BAD_REQUEST);
            }

            return $this->callExternalApi('PATCH', "/detalle_factura_producto/{$idDetalle}", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroyDetalleProducto(int $idDetalle): JsonResponse
    {
        return $this->callExternalApi('DELETE', "/detalle_factura_producto/{$idDetalle}");
    }

    public function updateDetalleTratamiento(Request $request, int $idDetalle): JsonResponse
    {
        try {
            $validated = $request->validate([
                'Costo' => 'sometimes|numeric|min:0',
                'Descripcion' => 'sometimes|nullable|string|max:255',
            ]);

            if (empty($validated)) {
                return response()->json(['message' => 'No se proporcionaron datos válidos para actualizar el detalle.'], Response::HTTP_BAD_REQUEST);
            }

            return $this->callExternalApi('PATCH', "/detalle_factura_tratamiento/{$idDetalle}", $validated);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroyDetalleTratamiento(int $idDetalle): JsonResponse
    {
        return $this->callExternalApi('DELETE', "/detalle_factura_tratamiento/{$idDetalle}");
    }

    // =========================================================================
    // 🟢 MÉTODO DE EXPORTACIÓN A PDF
    // =========================================================================

    /**
     * 🟢 MÉTODO PRINCIPAL: Exporta la factura completa a PDF.
     *
     * Este método:
     * 1. Obtiene los datos completos de la factura desde la API (usando el nuevo endpoint /completa)
     * 2. Genera un PDF usando Dompdf con la plantilla pdf_template.blade.php
     * 3. Retorna el PDF para descarga o visualización en el navegador
     *
     * @param Factura $factura Modelo de factura (resolución de ruta mediante binding)
     * @return \Illuminate\Http\Response PDF para descarga/visualización
     */
    public function exportPdf(Factura $factura)
    {
        $facturaId = $factura->Cod_Factura;

        try {
            // 1️⃣ Obtener datos completos de la factura desde la API usando el nuevo endpoint
            $facturaData = $this->callExternalApi('GET', "/facturas/{$facturaId}/completa", returnViewData: true);

            if ($facturaData === null) {
                abort(500, 'No se pudo obtener la información de la factura desde la API.');
            }

            $facturaInfo = $facturaData['factura'] ?? [];
            $detallesInfo = $facturaData['detalles'] ?? [];

            // 2️⃣ Validar que tengamos datos mínimos
            if (empty($facturaInfo)) {
                abort(404, 'Factura no encontrada.');
            }

            // 3️⃣ Generar el PDF usando Dompdf
            $pdf = Pdf::loadView('factura.pdf_template', [
                'factura' => $facturaInfo,
                'detalles' => $detallesInfo,
            ]);

            // 4️⃣ Configurar opciones del PDF
            $pdf->setPaper('letter', 'portrait');

            // 5️⃣ Generar nombre del archivo
            $fileName = 'Factura_F-' . str_pad((string)$facturaId, 4, '0', STR_PAD_LEFT) . '.pdf';

            // 6️⃣ Retornar el PDF para visualización en el navegador
            // Usa ->stream() para abrir en el navegador o ->download() para forzar descarga
            return $pdf->stream($fileName);

        } catch (\Exception $e) {
            Log::error("Error al generar PDF de factura #{$facturaId}: " . $e->getMessage());
            abort(500, 'Error al generar el PDF: ' . $e->getMessage());
        }
    }
}
