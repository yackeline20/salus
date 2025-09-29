<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisteredPersonaController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionPersonalController;
use App\Http\Controllers\AdminController;

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
// RUTAS DE AUTENTICACIÓN CON PERSONAS
// ========================================

// Login con correo (tabla correo)
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Registro de personas
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

    // Módulos del sistema
    Route::get('/citas', [CitasController::class, 'index'])->name('citas');
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
// RUTAS DE AUTENTICACIÓN PREDETERMINADAS (SI LAS NECESITAS)
// ========================================
require __DIR__ . '/auth.php';
