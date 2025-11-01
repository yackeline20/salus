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
// 🟢 Importar el controlador de Facturas (Se mantiene por si se usa en otras rutas web)
use App\Http\Controllers\FacturaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


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

    // 🔴 Módulo de Facturación: RUTA ELIMINADA.
    // La funcionalidad CRUD de Facturas ahora se maneja completamente en routes/api.php
    // y usa el FacturaController que devuelve JSON. Si necesitas una vista de
    // "Listado de Facturas", agrégala manualmente:
    Route::get('/facturas', function () {
        return view('factura.index'); // O el nombre de tu vista principal de facturas
    })->name('factura.index')
      ->middleware('can:viewAny,App\Models\Factura');

    // 🟢 Módulo de Citas (CRÍTICO: Usamos Route::resource si tienes CitaController)
    // Ya que usaste rutas API, asumiremos que el index de Citas es la vista principal,
    // pero incluiremos el Route::resource si necesitas CRUD
    // NOTA: Dejo las rutas API ya que son específicas
    Route::get('/citas', [CitasController::class, 'index'])->name('citas')
        ->middleware('can:viewAny,App\Models\Cita'); // 🟢 Usamos @can y Policy

    // Rutas API de citas: Revisa si estas APIs deben seguir usando 'check.permissions' o policies
    // NOTA: Estas rutas API deberían estar idealmente en routes/api.php
    Route::get('/api/citas', [CitasController::class, 'getCitas'])->name('api.citas.get')
         ->middleware('check.permissions:Citas,select');
    Route::post('/api/citas', [CitasController::class, 'storeCita'])->name('api.citas.store')
         ->middleware('check.permissions:Citas,insert');
    Route::put('/api/citas', [CitasController::class, 'updateCita'])->name('api.citas.update')
         ->middleware('check.permissions:Citas,update');
    Route::delete('/api/citas', [CitasController::class, 'deleteCita'])->name('api.citas.delete')
         ->middleware('check.permissions:Citas,delete');

    // 🟢 Módulo de Inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario')
         ->middleware('can:viewAny,App\Models\Product'); // 🟢 Usamos @can y Policy

    // 🟢 Módulo de Gestión de Servicios
    Route::get('/servicios', function () {
        return view('gestion-servicios');
    })->name('servicios')
      ->middleware('can:viewAny,App\Models\Tratamiento'); // 🟢 Usamos @can y Policy

    // Módulo de Reportes
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes')
         ->middleware('can:viewAny,App\Models\Reporte'); // 🟢 Usamos @can y Policy

    // Módulo de Gestión de Personal
    Route::get('/gestion-personal', [GestionPersonalController::class, 'index'])->name('gestion-personal')
         ->middleware('can:viewAny,App\Models\Empleado'); // 🟢 Usamos @can y Policy


    // ========================================
    // MÓDULO DE ADMINISTRACIÓN
    // ========================================

    // Ruta principal de Administración
    Route::get('/administracion', [AdministracionController::class, 'index'])->name('administracion')
         ->middleware('can:viewAny,App\Models\Cliente'); // 🟢 Usamos @can y Policy

    // SUB-RUTAS DE ADMINISTRACIÓN
    Route::prefix('administracion')->middleware('can:viewAny,App\Models\Cliente')->group(function () {

        // Backup y Restore
        Route::get('/backup', [AdministracionController::class, 'backup'])->name('administracion.backup');
        Route::post('/backup/crear', [AdministracionController::class, 'crearBackup'])->name('administracion.backup.crear');
        Route::post('/backup/restaurar', [AdministracionController::class, 'restaurarBackup'])->name('administracion.backup.restaurar');

        // Cambio de Contraseña
        Route::get('/password', [AdministracionController::class, 'password'])->name('administracion.password');
        Route::post('/password/cambiar', [AdministracionController::class, 'cambiarPassword'])->name('administracion.password.cambiar');

        // Bitácora
        Route::get('/bitacora', [AdministracionController::class, 'bitacora'])->name('administracion.bitacora');
        Route::get('/bitacora/export-pdf', [AdministracionController::class, 'exportPdf'])->name('administracion.bitacora.export.pdf');
        Route::get('/bitacora/export-excel', [AdministracionController::class, 'exportExcel'])->name('administracion.bitacora.export.excel');
        Route::post('/bitacora', [AdministracionController::class, 'exportExcel'])->name('administracion.bitacora.insert'); // <--- PENDING: Falta definir qué hace esta ruta
    });

}); // CIERRE DEL MIDDLEWARE 'auth', 'twofactor'

// ========================================
// RUTAS DE AUTENTICACIÓN PREDETERMINADAS
// ========================================
require __DIR__ . '/auth.php';
