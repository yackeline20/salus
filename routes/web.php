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
use App\Http\Controllers\ServicioController; // Única importación mantenida
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
    // B. MÓDULOS PROTEGIDOS POR POLICIES (Route::resource)
    // ----------------------------------------

    // 🟢 Módulo de Facturación (CRÍTICO: Usamos FacturaController y Route::resource)
    Route::resource('factura', FacturaController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // ========================================
    // 🟢 MÓDULO DE CITAS - COMPLETO Y MEJORADO
    // ========================================

    // Vista principal de citas
    Route::get('/citas', [CitasController::class, 'index'])->name('citas')
        ->middleware('can:viewAny,App\Models\Cita');

    // Búsqueda y creación de clientes
    Route::get('/api/citas/buscar-cliente', [CitasController::class, 'buscarCliente'])
        ->name('api.citas.buscar-cliente')
        ->middleware('can:viewAny,App\Models\Cita');

    Route::post('/api/citas/crear-cliente', [CitasController::class, 'crearClienteCompleto'])
        ->name('api.citas.crear-cliente')
        ->middleware('can:create,App\Models\Cita');

    // Rutas API de citas - CRUD completo
    Route::get('/api/citas', [CitasController::class, 'getCitas'])
        ->name('api.citas.get')
        ->middleware('can:viewAny,App\Models\Cita');

    Route::post('/api/citas', [CitasController::class, 'storeCita'])
        ->name('api.citas.store')
        ->middleware('can:create,App\Models\Cita');

    Route::put('/api/citas/{id}', [CitasController::class, 'updateCita'])
        ->name('api.citas.update');

    Route::delete('/api/citas/{id}', [CitasController::class, 'deleteCita'])
        ->name('api.citas.delete');

    Route::put('/api/citas/estado/{id}', [CitasController::class, 'updateStatus'])
        ->name('api.citas.update-status');

    // Esta ruta es la que usa el modal "Lista de Clientes"
    Route::get('/api/clientes/listado', [CitasController::class, 'listado'])
        ->name('api.clientes.listado')
        ->middleware('can:viewAny,App\Models\Cita');

    // ========================================
    // 🟢 MÓDULO DE INVENTARIO - COMPLETO
    // ========================================

    // Vista principal del inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario')
             ->middleware('can:viewAny,App\Models\Product');

    // 🆕 RUTAS API DE INVENTARIO
    Route::prefix('api/inventario')->group(function () {
        // Obtener todos los productos
        Route::get('/productos', [InventarioController::class, 'getProductos'])
             ->name('api.inventario.productos')
             ->middleware('can:viewAny,App\Models\Product');

        // Crear nuevo producto
        Route::post('/productos', [InventarioController::class, 'store'])
             ->name('api.inventario.store')
             ->middleware('can:create,App\Models\Product');

        // Actualizar producto existente
        Route::put('/productos/{id}', [InventarioController::class, 'update'])
             ->name('api.inventario.update')
             ->middleware('can:update,App\Models\Product');

        // Eliminar producto
        Route::delete('/productos/{id}', [InventarioController::class, 'destroy'])
             ->name('api.inventario.destroy')
             ->middleware('can:delete,App\Models\Product');

        // Obtener proveedores (datos estáticos por ahora)
        Route::get('/proveedores', [InventarioController::class, 'getProveedores'])
             ->name('api.inventario.proveedores');

        // Obtener categorías (datos estáticos por ahora)
        Route::get('/categorias', [InventarioController::class, 'getCategorias'])
             ->name('api.inventario.categorias');

        // Obtener estadísticas del inventario
        Route::get('/estadisticas', [InventarioController::class, 'getEstadisticas'])
             ->name('api.inventario.estadisticas')
             ->middleware('can:viewAny,App\Models\Product');
    });


    // ========================================
    // 🟢 MÓDULO DE GESTIÓN DE SERVICIOS
    // ========================================

    // Vista principal de Servicios
    Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios')
        ->middleware('can:viewAny,App\Models\Tratamiento');

    // Rutas API de Servicios (Tratamientos)
    Route::prefix('api/servicios')->name('api.servicios.')->group(function () {
        Route::get('/', [ServicioController::class, 'getTratamientos'])->name('get')
            ->middleware('can:viewAny,App\Models\Tratamiento');
        Route::post('/', [ServicioController::class, 'store'])->name('store')
            ->middleware('can:create,App\Models\Tratamiento');
        Route::get('/{id}', [ServicioController::class, 'show'])->name('show')
            ->middleware('can:viewAny,App\Models\Tratamiento');
        Route::put('/{id}', [ServicioController::class, 'update'])->name('update')
            ->middleware('can:update,App\Models\Tratamiento');
        Route::delete('/{id}', [ServicioController::class, 'destroy'])->name('destroy')
            ->middleware('can:delete,App\Models\Tratamiento');
    });

    // ========================================
    // Módulo de Reportes
    // ========================================
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes')
                 ->middleware('can:viewAny,App\Models\Reporte');

    // ========================================
    // Módulo de Gestión de Personal
    // ========================================
    Route::get('/gestion-personal', [GestionPersonalController::class, 'index'])->name('gestion-personal')
                 ->middleware('can:viewAny,App\Models\Empleado');


    // ========================================
    // MÓDULO DE ADMINISTRACIÓN
    // ========================================

    // Ruta principal de Administración
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

        // ========================================
        // RUTAS DEL MÓDULO DE BITÁCORA
        // ========================================

        // Agrupa las rutas de bitácora bajo el prefijo '/bitacora' y el nombre 'bitacora.'
        Route::prefix('bitacora')->name('bitacora.')->group(function () {

            // 1. Mostrar la tabla de la Bitácora (URL: /bitacora)
            // Nombre: bitacora.index
            Route::get('/', [BitacoraController::class, 'index'])->name('index');

            // 2. Exportar los datos actuales (filtrados) a PDF (URL: /bitacora/export/pdf)
            // Nombre: bitacora.export.pdf
            Route::get('/export/pdf', [BitacoraController::class, 'exportPdf'])->name('export.pdf');

            // 3. Mostrar los detalles de un registro
            // Nombre: bitacora.show (URL: /bitacora/{id})
            Route::get('/{id}', [BitacoraController::class, 'show'])->name('show');

            // 4. Elimina un registro de la bitácora
            // Nombre: bitacora.destroy (URL: /bitacora/{id})
            Route::delete('/{id}', [BitacoraController::class, 'destroy'])->name('destroy');

            // 5. Procesa la restauración de un registro
            // Nombre: bitacora.restaurar (URL: /bitacora/restaurar/{id})
            Route::post('/restaurar/{id}', [BitacoraController::class, 'restaurar'])->name('restaurar');

        });

        // ----------------------------------------
        // CRUD DE GESTIÓN DE PERSONAL (EMPLEADOS)
        // ----------------------------------------
        Route::get('empleados', [GestionPersonalController::class, 'getEmpleados'])->name('api.empleados.index');
        Route::post('empleados', [GestionPersonalController::class, 'storeEmpleado'])->name('api.empleados.store');
        Route::put('empleados/{id}', [GestionPersonalController::class, 'updateEmpleado'])->name('api.empleados.update');
        Route::delete('empleados/{id}', [GestionPersonalController::class, 'destroyEmpleado'])->name('api.empleados.destroy');


    }); // CIERRE DEL PREFIX 'administracion'

    // ========================================
    // 🟠 RUTAS DE API ADICIONALES (Dentro del grupo 'api')
    // ========================================

    Route::group(['prefix' => 'api'], function () {

        // ----------------------------------------
        // CRUD DE CABECERA DE FACTURA
        // ----------------------------------------
        Route::post('factura', [FacturaController::class, 'storeCabecera'])->name('api.factura.store');
        Route::get('factura', [FacturaController::class, 'index'])->name('api.factura.index');
        Route::put('factura/{id}', [FacturaController::class, 'update'])->name('api.factura.update');
        Route::delete('factura/{factura}', [FacturaController::class, 'destroy'])->name('api.factura.destroy');


        // ----------------------------------------
        // CRUD DE DETALLE DE FACTURA (PRODUCTOS Y TRATAMIENTOS)
        // ----------------------------------------
        Route::get('detalle_factura_tratamiento', [FacturaController::class, 'getDetalleTratamiento'])->name('api.factura.detalle_tratamiento');
        Route::get('detalle_factura_producto', [FacturaController::class, 'getDetalleProducto'])->name('api.factura.detalle_producto');
        Route::post('detalle_factura_producto', [FacturaController::class, 'storeDetalleProducto'])->name('api.detalle_producto.store');
        Route::post('detalle_factura_tratamiento', [FacturaController::class, 'storeDetalleTratamiento'])->name('api.detalle_tratamiento.store');

        Route::put('detalle_factura_producto/{id}', [FacturaController::class, 'updateDetalleProducto'])->name('api.detalle_producto.update');
        Route::delete('detalle_factura_producto/{id}', [FacturaController::class, 'destroyDetalleProducto'])->name('api.detalle_producto.destroy');
        Route::put('detalle_factura_tratamiento/{id}', [FacturaController::class, 'updateDetalleTratamiento'])->name('api.detalle_tratamiento.update');
        Route::delete('detalle_factura_tratamiento/{id}', [FacturaController::class, 'destroyDetalleTratamiento'])->name('api.detalle_tratamiento.destroy');


        // ----------------------------------------
        // CRUD DE INVENTARIO (PRODUCTOS) - Rutas duplicadas movidas aquí.
        // ----------------------------------------
        Route::get('productos', [InventarioController::class, 'getProducts'])->name('api.productos.index');
        Route::post('productos', [InventarioController::class, 'storeProduct'])->name('api.productos.store');
        Route::put('productos/{id}', [InventarioController::class, 'updateProduct'])->name('api.productos.update');
        Route::delete('productos/{id}', [InventarioController::class, 'destroyProduct'])->name('api.productos.destroy');


        // ----------------------------------------
        // CRUD DE SERVICIOS (TRATAMIENTOS) - Rutas duplicadas movidas aquí.
        // ----------------------------------------
        Route::get('tratamientos', [ServicioController::class, 'getTratamientos'])->name('api.tratamientos.index');
        Route::post('tratamientos', [ServicioController::class, 'storeTratamiento'])->name('api.tratamientos.store');
        Route::put('tratamientos/{id}', [ServicioController::class, 'updateTratamiento'])->name('api.tratamientos.update');
        Route::delete('tratamientos/{id}', [ServicioController::class, 'destroyTratamiento'])->name('api.tratamientos.destroy');


        // ----------------------------------------
        // CRUD DE CITAS - Rutas duplicadas movidas aquí.
        // NOTA: Estas rutas ya estaban declaradas, pero las dejo aquí en el grupo 'api' para consistencia con el bloque conflictivo.
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
