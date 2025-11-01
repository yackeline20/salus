<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredPersonaController;
use App\Http\Controllers\Auth\RegisteredUsuarioController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionPersonalController;
use App\Http\Controllers\ServicioController; // <--- CORRECCIÓN: Usando el nombre correcto (Singular)
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\AdministracionController;
use App\Http\Controllers\FacturaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BitacoraController;

// Ruta raíz - SIEMPRE muestra la vista de bienvenida.
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ========================================
// RUTAS DE AUTENTICACIÓN
// ========================================

// Login de Administrador
Route::get('/admin/login', [AdminController::class, 'showAdminLoginForm'])->name('admin.login.demo');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

// Login unificado para Recepcionista/Otros
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

// Logout (Debe ser POST)
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


// ========================================
// REGISTRO DE USUARIOS Y PERSONAS
// ========================================
Route::get('/register-usuario', [RegisteredUsuarioController::class, 'create'])->name('register.usuario');
Route::post('/register-usuario', [RegisteredUsuarioController::class, 'store']);
Route::get('/register-persona', [RegisteredPersonaController::class, 'create'])->name('register.persona');
Route::post('/register-persona', [RegisteredPersonaController::class, 'store']);


// ========================================
// RUTAS DE 2FA (SIN VERIFICACIÓN 2FA)
// ========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/2fa/setup', [TwoFactorController::class, 'show'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify.show');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
});


// ========================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN + VERIFICACIÓN 2FA + POLICIES)
// ========================================

Route::middleware(['auth', 'twofactor'])->group(function () {

    // ----------------------------------------
    // A. RUTAS COMUNES y DASHBOARD
    // ----------------------------------------

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    // ----------------------------------------
    // B. MÓDULOS PROTEGIDOS POR POLICIES (Rutas Web)
    // ----------------------------------------

    // Módulo de Facturación (Ruta Web de la Vista Principal)
    Route::get('/facturas', [FacturaController::class, 'index'])->name('factura.index')
        ->middleware('can:viewAny,App\Models\Factura');

    // RUTA AÑADIDA: Vista del formulario para crear una nueva factura
    Route::get('/facturas/create', [FacturaController::class, 'create'])->name('factura.create')
        ->middleware('can:create,App\Models\Factura');


    // MÓDULO DE CITAS
    // Vista principal de citas
    Route::get('/citas', [CitasController::class, 'index'])->name('citas')
        ->middleware('can:viewAny,App\Models\Cita');

    // Módulo de Inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario')
             ->middleware('can:viewAny,App\Models\Product');

    // Módulo de Gestión de Servicios (Ajustado para usar el controller)
    Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios') // <--- CORRECCIÓN A SERVICIOCONTROLLER
      ->middleware('can:viewAny,App\Models\Tratamiento');

    // Módulo de Reportes
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes')
             ->middleware('can:viewAny,App\Models\Reporte');

    // Módulo de Gestión de Personal
    Route::get('/gestion-personal', [GestionPersonalController::class, 'index'])->name('gestion-personal')
             ->middleware('can:viewAny,App\Models\Empleado');


    // MÓDULO DE ADMINISTRACIÓN
    Route::get('/administracion', [AdministracionController::class, 'index'])->name('administracion')
             ->middleware('can:viewAny,App\Models\Cliente');

    // SUB-RUTAS DE ADMINISTRACIÓN
    Route::prefix('administracion')->middleware('can:viewAny,App\Models\Cliente')->group(function () {

        // Backup y Restore
        Route::get('/backup', [AdministracionController::class, 'backup'])->name('administracion.backup');
        Route::post('/backup/crear', [AdministracionController::class, 'crearBackup'])->name('administracion.backup.crear');
        Route::post('/backup/restaurar', [AdministracionController::class, 'restaurarBackup'])->name('administracion.backup.restaurar');

        // Cambio de Contraseña
        Route::get('/password', [AdministracionController::class, 'password'])->name('administracion.password');
        Route::post('/password/cambiar', [AdministracionController::class, 'cambiarPassword'])->name('administracion.password.cambiar');

    }); // Cierre de prefix 'administracion'

    // RUTAS DEL MÓDULO DE BITÁCORA
    Route::prefix('bitacora')->name('bitacora.')->group(function () {
        Route::get('/', [BitacoraController::class, 'index'])->name('index');
        Route::get('/export/pdf', [BitacoraController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{id}', [BitacoraController::class, 'show'])->name('show');
        Route::delete('/{id}', [BitacoraController::class, 'destroy'])->name('destroy');
        Route::post('/restaurar/{id}', [BitacoraController::class, 'restaurar'])->name('restaurar');
    }); // Cierre de prefix 'bitacora'


    // ========================================
    // 🟠 RUTAS DE API (CRUD de Facturación, Citas, Inventario, Servicios, Personal)
    // Estas rutas actúan como un intermediario (proxy) para las APIs de Node.js (puerto 3000).
    // ========================================

    Route::group(['prefix' => 'api'], function () {

        // ----------------------------------------
        // CRUD DE CABECERA DE FACTURA
        // ----------------------------------------
        // POST /api/factura (Creación)
        Route::post('factura', [FacturaController::class, 'storeCabecera'])->name('api.factura.store');
        // GET /api/factura (Listado/Búsqueda por Cod_Factura)
        Route::get('factura', [FacturaController::class, 'index'])->name('api.factura.index');
        // PUT /api/factura (Actualización/Cambio de estado/pago)
        Route::put('factura', [FacturaController::class, 'update'])->name('api.factura.update');
        // DELETE /api/factura/{factura} (Eliminación/Anulación)
        Route::delete('factura/{factura}', [FacturaController::class, 'destroy'])->name('api.factura.destroy');


        // ----------------------------------------
        // CRUD DE DETALLE DE FACTURA (PRODUCTOS Y TRATAMIENTOS)
        // ----------------------------------------

        // GET y POST
        Route::get('detalle_factura_tratamiento', [FacturaController::class, 'getDetalleTratamiento'])->name('api.factura.detalle_tratamiento');
        Route::get('detalle_factura_producto', [FacturaController::class, 'getDetalleProducto'])->name('api.factura.detalle_producto');
        Route::post('detalle_factura_producto', [FacturaController::class, 'storeDetalleProducto'])->name('api.detalle_producto.store');
        Route::post('detalle_factura_tratamiento', [FacturaController::class, 'storeDetalleTratamiento'])->name('api.detalle_tratamiento.store');

        // PUT y DELETE AÑADIDOS para Detalle de Factura
        Route::put('detalle_factura_producto/{id}', [FacturaController::class, 'updateDetalleProducto'])->name('api.detalle_producto.update');
        Route::delete('detalle_factura_producto/{id}', [FacturaController::class, 'destroyDetalleProducto'])->name('api.detalle_producto.destroy');
        Route::put('detalle_factura_tratamiento/{id}', [FacturaController::class, 'updateDetalleTratamiento'])->name('api.detalle_tratamiento.update');
        Route::delete('detalle_factura_tratamiento/{id}', [FacturaController::class, 'destroyDetalleTratamiento'])->name('api.detalle_tratamiento.destroy');


        // ----------------------------------------
        // CRUD DE INVENTARIO (PRODUCTOS)
        // ----------------------------------------
        Route::get('productos', [InventarioController::class, 'getProducts'])->name('api.productos.index');
        Route::post('productos', [InventarioController::class, 'storeProduct'])->name('api.productos.store');
        Route::put('productos/{id}', [InventarioController::class, 'updateProduct'])->name('api.productos.update');
        Route::delete('productos/{id}', [InventarioController::class, 'destroyProduct'])->name('api.productos.destroy');


        // ----------------------------------------
        // CRUD DE SERVICIOS (TRATAMIENTOS)
        // ----------------------------------------
        Route::get('tratamientos', [ServicioController::class, 'getTratamientos'])->name('api.tratamientos.index'); // <--- CORRECCIÓN A SERVICIOCONTROLLER
        Route::post('tratamientos', [ServicioController::class, 'storeTratamiento'])->name('api.tratamientos.store'); // <--- CORRECCIÓN A SERVICIOCONTROLLER
        Route::put('tratamientos/{id}', [ServicioController::class, 'updateTratamiento'])->name('api.tratamientos.update'); // <--- CORRECCIÓN A SERVICIOCONTROLLER
        Route::delete('tratamientos/{id}', [ServicioController::class, 'destroyTratamiento'])->name('api.tratamientos.destroy'); // <--- CORRECCIÓN A SERVICIOCONTROLLER


        // ----------------------------------------
        // CRUD DE GESTIÓN DE PERSONAL (EMPLEADOS)
        // ----------------------------------------
        Route::get('empleados', [GestionPersonalController::class, 'getEmpleados'])->name('api.empleados.index');
        Route::post('empleados', [GestionPersonalController::class, 'storeEmpleado'])->name('api.empleados.store');
        Route::put('empleados/{id}', [GestionPersonalController::class, 'updateEmpleado'])->name('api.empleados.update');
        Route::delete('empleados/{id}', [GestionPersonalController::class, 'destroyEmpleado'])->name('api.empleados.destroy');


        // ----------------------------------------
        // CRUD DE CITAS (Mantengo las suyas originales)
        // ----------------------------------------
        Route::get('/citas/buscar-cliente', [CitasController::class, 'buscarCliente'])
            ->name('api.citas.buscar-cliente');

        Route::post('/citas/crear-cliente', [CitasController::class, 'crearClienteCompleto'])
            ->name('api.citas.crear-cliente')
            ->middleware('can:create,App\Models\Cita');

        Route::get('/citas', [CitasController::class, 'getCitas'])
            ->name('api.citas.get');

        Route::post('/citas', [CitasController::class, 'storeCita'])
            ->name('api.citas.store');

        Route::put('/citas/{id}', [CitasController::class, 'updateCita'])
            ->name('api.citas.update');

        Route::delete('/citas/{id}', [CitasController::class, 'deleteCita'])
            ->name('api.citas.delete');

        Route::put('/citas/estado/{id}', [CitasController::class, 'updateStatus'])
            ->name('api.citas.update-status');

    }); // CIERRE DEL GRUPO 'api'


}); // CIERRE DEL MIDDLEWARE 'auth', 'twofactor'

// ========================================
// RUTAS DE AUTENTICACIÓN PREDETERMINADAS
// ========================================
require __DIR__ . '/auth.php';
