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
    // B. MÓDULOS PROTEGIDOS POR POLICIES (Route::resource)
    // Usar Route::resource activa FacturaPolicy, CitaPolicy, etc.
    // ----------------------------------------

    // 🟢 Módulo de Facturación (CRÍTICO: Usamos FacturaController y Route::resource)
    Route::resource('factura', FacturaController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // ========================================
    // 🟢 MÓDULO DE CITAS - COMPLETO Y MEJORADO
    // ========================================
    
});
    // Vista principal de citas
    Route::get('/citas', [CitasController::class, 'index'])->name('citas')
        ->middleware('can:viewAny,App\Models\Cita');

    // 🆕 NUEVAS RUTAS - Búsqueda y creación de clientes
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
    
    Route::put('/api/citas', [CitasController::class, 'updateCita'])
        ->name('api.citas.update')
        ->middleware('can:update,App\Models\Cita');
    
    Route::delete('/api/citas', [CitasController::class, 'deleteCita'])
        ->name('api.citas.delete')
        ->middleware('can:delete,App\Models\Cita');

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

        // ========================================
// RUTAS DEL MÓDULO DE BITÁCORA
// ========================================

// Agrupa las rutas de bitácora bajo el prefijo '/bitacora' y el nombre 'bitacora.'
// Asume que este bloque está dentro del middleware de autenticación que uses.
Route::prefix('bitacora')->name('bitacora.')->group(function () {
    
    // 1. Mostrar la tabla de la Bitácora (URL: /bitacora)
    // Nombre: bitacora.index
    Route::get('/', [BitacoraController::class, 'index'])->name('index');

    // 2. Exportar los datos actuales (filtrados) a PDF (URL: /bitacora/export/pdf)
    // Nombre: bitacora.export.pdf (preferible sobre bitacora.pdf)
    Route::get('/export/pdf', [BitacoraController::class, 'exportPdf'])->name('export.pdf');

    // 3. Mostrar los detalles de un registro (para la función actualizarRegistro en JS)
    // Nombre: bitacora.show (URL: /bitacora/{id})
    Route::get('/{id}', [BitacoraController::class, 'show'])->name('show');
    
    // 4. Elimina un registro de la bitácora (Eliminación física del log)
    // Nombre: bitacora.destroy (URL: /bitacora/{id})
    Route::delete('/{id}', [BitacoraController::class, 'destroy'])->name('destroy');

    // 5. Procesa la restauración de un registro previamente eliminado
    // Nombre: bitacora.restaurar (URL: /bitacora/restaurar/{id})
    Route::post('/restaurar/{id}', [BitacoraController::class, 'restaurar'])->name('restaurar');

});

}); // CIERRE DEL MIDDLEWARE 'auth', 'twofactor'

// ========================================
// RUTAS DE AUTENTICACIÓN PREDETERMINADAS
// ========================================
require __DIR__ . '/auth.php';
