<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisteredPersonaController;
use App\Http\Controllers\Auth\RegisteredUsuarioController; // NUEVO
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionPersonalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\TwoFactorVerifyController;

// Ruta raíz - Redirige según el estado de autenticación
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('welcome');
});

// ========================================
// RUTA DE PRUEBA (TEMPORAL)
// ========================================
Route::get('/test-register-success', function() {
    session()->flash('success', true);
    session()->flash('nombre', 'Test');
    session()->flash('apellido', 'Usuario');
    session()->flash('correo', 'test@test.com');

    return view('auth.register-persona');
});

// ========================================
// RUTAS DE AUTENTICACIÓN
// ========================================

// Login unificado (soporta email para personas y username para usuarios)
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ========================================
// REGISTRO DE USUARIOS (NUEVO)
// ========================================
Route::get('/register-usuario', [RegisteredUsuarioController::class, 'create'])->name('register.usuario');
Route::post('/register-usuario', [RegisteredUsuarioController::class, 'store']);

// ========================================
// REGISTRO DE PERSONAS (EXISTENTE)
// ========================================
Route::get('/register-persona', [RegisteredPersonaController::class, 'create'])->name('register.persona');
Route::post('/register-persona', [RegisteredPersonaController::class, 'store']);

// ========================================
// MANTENER RUTAS ANTIGUAS (OPCIONAL)
// ========================================

// Registro antiguo con users (mantener por compatibilidad si lo necesitas)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ========================================
// RUTA DE ADMIN (SIN CAMBIOS)
// ========================================
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

// ========================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// ========================================
Route::middleware('auth')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ========================================
    // RUTAS DE CITAS - MODIFICADAS
    // ========================================
    // Vista principal de citas
    Route::get('/citas', [CitasController::class, 'index'])->name('citas');
    
    // API para operaciones CRUD de citas
    Route::get('/api/citas', [CitasController::class, 'getCitas'])->name('api.citas.get');
    Route::post('/api/citas', [CitasController::class, 'storeCita'])->name('api.citas.store');
    Route::put('/api/citas', [CitasController::class, 'updateCita'])->name('api.citas.update');
    Route::delete('/api/citas', [CitasController::class, 'deleteCita'])->name('api.citas.delete');

    // Módulos del sistema (sin cambios)
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');
    Route::get('/gestion-personal', [GestionPersonalController::class, 'index'])->name('gestion-personal');

    // Facturación
    Route::get('/factura', function () {
        return view('factura');
    })->name('factura');
    Route::get('/factura/crear', function () {
        return view('factura-crear');
    })->name('factura.crear');

    // Servicios
    Route::get('/servicios', function () {
        return view('gestion-servicios');
    })->name('servicios');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================================
// RUTAS DE AUTENTICACIÓN PREDETERMINADAS 
// ========================================
require __DIR__ . '/auth.php';

// Rutas protegidas (usuario autenticado)
Route::middleware(['auth'])->group(function () {
    Route::get('/2fa/setup', [TwoFactorController::class, 'show'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
});

// Rutas para verificar 2FA en login
Route::get('/2fa/verify', [TwoFactorVerifyController::class, 'show'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorVerifyController::class, 'verify']);