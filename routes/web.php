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

    // 🔴 Módulo de Facturación: Se mantiene solo la vista de índice, ya que el CRUD es API.
    Route::get('/facturas', function () {
        return view('factura.index'); // O el nombre de tu vista principal de facturas
    })->name('factura.index')
      ->middleware('can:viewAny,App\Models\Factura');

    // ========================================
    // 🟢 MÓDULO DE CITAS - CON PUNTO Y COMA
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

    // API CRUD de Citas
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

    // ⬇️ --- RUTA AÑADIDA QUE FALTABA --- ⬇️
    // Esta ruta es la que usa el modal "Lista de Clientes" y causaba el 404
    Route::get('/api/clientes/listado', [CitasController::class, 'listado'])
        ->name('api.clientes.listado')
        ->middleware('can:viewAny,App\Models\Cita');


    // 🟢 Módulo de Inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario')
          ->middleware('can:viewAny,App\Models\Product');

    // 🟢 Módulo de Gestión de Servicios
    Route::get('/servicios', function () {
        return view('gestion-servicios');
    })->name('servicios')
      ->middleware('can:viewAny,App\Models\Tratamiento');

    // Módulo de Reportes
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes')
          ->middleware('can:viewAny,App\Models\Reporte');

    // Módulo de Gestión de Personal
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

    }); // Cierre de prefix 'administracion'

    // ========================================
    // RUTAS DEL MÓDULO DE BITÁCORA
    // ========================================

    // Agrupa las rutas de bitácora bajo el prefijo '/bitacora' y el nombre 'bitacora.'
    Route::prefix('bitacora')->name('bitacora.')->group(function () {

        // 1. Mostrar la tabla de la Bitácora (URL: /bitacora)
        Route::get('/', [BitacoraController::class, 'index'])->name('index');

        // 2. Exportar los datos actuales (filtrados) a PDF (URL: /bitacora/export/pdf)
        Route::get('/export/pdf', [BitacoraController::class, 'exportPdf'])->name('export.pdf');

        // 3. Mostrar los detalles de un registro
        Route::get('/{id}', [BitacoraController::class, 'show'])->name('show');

        // 4. Elimina un registro de la bitácora
        Route::delete('/{id}', [BitacoraController::class, 'destroy'])->name('destroy');

        // 5. Procesa la restauración de un registro
        Route::post('/restaurar/{id}', [BitacoraController::class, 'restaurar'])->name('restaurar');

    }); // Cierre de prefix 'bitacora'


}); // CIERRE DEL MIDDLEWARE 'auth', 'twofactor's