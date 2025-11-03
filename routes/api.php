<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredPersonaController;
use App\Http\Controllers\FacturaController; // Importamos el controlador de facturas

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí registras las rutas API. Estas rutas se cargan automáticamente
| con el prefijo '/api' y el middleware 'api' (que en Laravel 10/11
| es gestionado por el RouteServiceProvider).
|
*/

// =========================================================================
// 1. RUTAS DE AUTENTICACIÓN / REGISTRO
// =========================================================================

// La ruta para obtener el formulario de registro (GET) no es necesaria en una API pura,
// pero se mantiene si se usa para obtener datos de inicialización.
Route::get('/register-persona', [RegisteredPersonaController::class, 'create']);

// RUTA ESTÁNDAR PARA REGISTRAR (Crear una nueva persona/usuario).
// Convención REST: usar el plural /api/personas.
Route::post('/personas', [RegisteredPersonaController::class, 'store']);

// Ruta de prueba para el usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// =========================================================================
// 2. RUTAS DE FACTURACIÓN (Requieren autenticación)
// =========================================================================

// Usamos el middleware 'auth:sanctum' para proteger todos los endpoints de facturación.
Route::middleware('auth:sanctum')->group(function () {

    // A. Rutas del Recurso Principal (Factura Cabecera)
    // FacturaController::index() -> GET /api/facturas (Lista)
    // FacturaController::show() -> GET /api/facturas/{factura}
    // FacturaController::update() -> PUT/PATCH /api/facturas/{factura}
    // FacturaController::destroy() -> DELETE /api/facturas/{factura}
    Route::apiResource('facturas', FacturaController::class)
        ->only(['index', 'show', 'update', 'destroy'])
        ->parameters(['facturas' => 'factura']); // Asegura que se use {factura} para el Route Model Binding

    // Ruta específica para la creación (POST).
    // Nota: Aunque no es la convención estándar (sería 'store' en el apiResource),
    // se respeta el nombre 'storeCabecera' para esta ruta.
    Route::post('facturas', [FacturaController::class, 'storeCabecera']); // POST /api/facturas

    // B. Rutas para los Detalles de Factura (Productos y Tratamientos)
    // Se utiliza un esquema de rutas planas para la gestión de detalles.

    // Rutas para Detalle Producto
    Route::post('detalle_factura_producto', [FacturaController::class, 'storeDetalleProducto']);
    // Lectura por Cod_Factura (se espera Cod_Factura como Query String: /api/detalle_factura_producto?Cod_Factura=X)
    Route::get('detalle_factura_producto', [FacturaController::class, 'getDetalleProducto']);
    Route::put('detalle_factura_producto/{idDetalle}', [FacturaController::class, 'updateDetalleProducto']);
    Route::delete('detalle_factura_producto/{idDetalle}', [FacturaController::class, 'destroyDetalleProducto']);

    // Rutas para Detalle Tratamiento
    Route::post('detalle_factura_tratamiento', [FacturaController::class, 'storeDetalleTratamiento']);
    // Lectura por Cod_Factura (se espera Cod_Factura como Query String: /api/detalle_factura_tratamiento?Cod_Factura=X)
    Route::get('detalle_factura_tratamiento', [FacturaController::class, 'getDetalleTratamiento']);
    Route::put('detalle_factura_tratamiento/{idDetalle}', [FacturaController::class, 'updateDetalleTratamiento']);
    Route::delete('detalle_factura_tratamiento/{idDetalle}', [FacturaController::class, 'destroyDetalleTratamiento']);
});
