<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController; // ← FALTABA ESTA LÍNEA
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionPersonalController;
use App\Http\Controllers\AdminController;

// Redirigir la página principal al login
Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticación personalizada
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta de admin
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/citas', [CitasController::class, 'index'])->name('citas');
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');
    Route::get('/gestion-personal', [GestionPersonalController::class, 'index'])->name('gestion-personal');
    Route::get('/factura', function () {
        return view('factura');
    })->name('factura');
    Route::get('/factura/crear', function () {
        return view('factura-crear');
    })->name('factura.crear');
    Route::get('/servicios', function () {
        return view('gestion-servicios');
    })->name('servicios');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ruta raíz - Solo redirige si está autenticado
Route::get('/', function () {
    // Si el usuario está autenticado, va al dashboard
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    // Si NO está autenticado, muestra la página de bienvenida/login
    return view('welcome'); // o la vista que tengas
});

// Rutas de autenticación (NO las toques)
require __DIR__.'/auth.php';

// Ruta del dashboard (ya existente, NO la cambies)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');