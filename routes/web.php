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
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Auth;

// Importación clave que estaba en conflicto (RegisteredUsuarioController)
use App\Http\Controllers\Auth\RegisteredUsuarioController;
>>>>>>> 8a3a3bcfdaa798e93ddc172e391b183e034d8f07

// Ruta raíz - Redirige según el estado de autenticación
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// ========================================
// RUTAS DE AUTENTICACIÓN
// ========================================

// Login de Administrador (Rutas separadas para el login especial)
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

// Registro de Usuario (usa RegisteredUsuarioController)
Route::get('/register-usuario', [RegisteredUsuarioController::class, 'create'])->name('register.usuario');
Route::post('/register-usuario', [RegisteredUsuarioController::class, 'store']);

// Registro de Persona (usa RegisteredPersonaController)
Route::get('/register-persona', [RegisteredPersonaController::class, 'create'])->name('register.persona');
Route::post('/register-persona', [RegisteredPersonaController::class, 'store']);


// ========================================
// RUTAS DE 2FA (SOLO REQUIEREN AUTENTICACIÓN, SIN VERIFICACIÓN 2FA)
// Estas rutas deben estar accesibles para configurar y verificar 2FA
// ========================================
Route::middleware(['auth'])->group(function () {
    // Configuración de 2FA (setup y activación)
    Route::get('/2fa/setup', [TwoFactorController::class, 'show'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    
    // Verificación de 2FA durante login
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify.show');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
});


// ========================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN + VERIFICACIÓN 2FA)
// ========================================

Route::middleware(['auth', 'twofactor'])->group(function () {

    // ----------------------------------------
    // A. RUTAS COMUNES y DASHBOARD
    // ----------------------------------------

<<<<<<< HEAD
    // DASHBOARD
=======
    // DASHBOARD: Ahora solo requiere que el usuario esté autenticado.
>>>>>>> 8a3a3bcfdaa798e93ddc172e391b183e034d8f07
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    // ----------------------------------------
    // B. GESTIÓN DE 2FA (Dentro del dashboard - Deshabilitar)
    // ----------------------------------------
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');


    // ----------------------------------------
    // C. MÓDULOS CON RESTRICCIÓN DE PERMISOS
    // ----------------------------------------

    // Módulo de Citas
    Route::get('/citas', [CitasController::class, 'index'])->name('citas')
        ->middleware('check.permissions:Citas,select');

    // Rutas API de citas
    Route::get('/api/citas', [CitasController::class, 'getCitas'])->name('api.citas.get')
        ->middleware('check.permissions:Citas,select');
    Route::post('/api/citas', [CitasController::class, 'storeCita'])->name('api.citas.store')
        ->middleware('check.permissions:Citas,insert');
    Route::put('/api/citas', [CitasController::class, 'updateCita'])->name('api.citas.update')
        ->middleware('check.permissions:Citas,update');
    Route::delete('/api/citas', [CitasController::class, 'deleteCita'])->name('api.citas.delete')
        ->middleware('check.permissions:Citas,delete');


    // Módulo de Inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario')
        ->middleware('check.permissions:Inventario,select');

    // Módulo de Gestión de Servicios
    Route::get('/servicios', function () {
        return view('gestion-servicios');
    })->name('servicios')
      ->middleware('check.permissions:Gestión de Servicios,select');

    // Módulo de Facturación
    Route::get('/factura', function () {
        return view('factura');
    })->name('factura')
      ->middleware('check.permissions:Facturación,select');

    Route::get('/factura/crear', function () {
        return view('factura-crear');
    })->name('factura.crear')
      ->middleware('check.permissions:Facturación,insert');


    // Módulo de Reportes
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes')
        ->middleware('check.permissions:Reportes,select');


    // Módulo de Gestión de Personal
    Route::get('/gestion-personal', [GestionPersonalController::class, 'index'])->name('gestion-personal')
        ->middleware('check.permissions:Gestión de Personal,select');

});

// ========================================
// RUTAS DE AUTENTICACIÓN PREDETERMINADAS 
// ========================================
require __DIR__ . '/auth.php';