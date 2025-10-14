<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisteredPersonaController;
use App\Http\Controllers\Auth\RegisteredUsuarioController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionPersonalController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

// Ruta raíz - Redirige según el estado de autenticación
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('welcome');
})->name('welcome');

// ========================================
// RUTAS DE AUTENTICACIÓN
// ========================================

// 1. RUTA GET (Muestra el formulario del administrador)
Route::get('/admin/login', [AdminController::class, 'showAdminLoginForm'])->name('admin.login.demo');

// 2. NUEVA RUTA POST (Procesa el formulario de login del administrador)
// Debe coincidir con el nombre de ruta que usa el formulario en admin/adminlogin.blade.php
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

// Login unificado (Probablemente para clientes/empleados si no acceden por el botón 'Administrador')
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


// ========================================
// REGISTRO DE USUARIOS
// ========================================
Route::get('/register-usuario', [RegisteredUsuarioController::class, 'create'])->name('register.usuario');
Route::post('/register-usuario', [RegisteredUsuarioController::class, 'store']);

// REGISTRO DE PERSONAS (Clientes/Externos)
Route::get('/register-persona', [RegisteredPersonaController::class, 'create'])->name('register.persona');
Route::post('/register-persona', [RegisteredPersonaController::class, 'store']);

// ========================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// ========================================

Route::middleware('auth')->group(function () {

    // ----------------------------------------
    // A. RUTAS PARA TODOS LOS USUARIOS AUTENTICADOS (Clientes, Empleados y Admin)
    // ----------------------------------------

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/citas', [CitasController::class, 'index'])->name('citas');

    // Rutas API de citas
    Route::get('/api/citas', [CitasController::class, 'getCitas'])->name('api.citas.get');
    Route::post('/api/citas', [CitasController::class, 'storeCita'])->name('api.citas.store');


    // ----------------------------------------
    // B. RUTAS EXCLUSIVAS PARA ADMINISTRADORES Y EMPLEADOS (Cod_Rol 1 o 2)
    // ----------------------------------------
    Route::middleware('role:admin,empleado')->group(function () {
        // Inventario y Servicios (usualmente manejado por personal)
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
        Route::get('/servicios', function () {
            return view('gestion-servicios');
        })->name('servicios');

        // Facturación
        Route::get('/factura', function () {
            return view('factura');
        })->name('factura');
        Route::get('/factura/crear', function () {
            return view('factura-crear');
        })->name('factura.crear');

        // CRUD de Citas (empleados tienen permisos de modificar/eliminar)
        Route::put('/api/citas', [CitasController::class, 'updateCita'])->name('api.citas.update');
        Route::delete('/api/citas', [CitasController::class, 'deleteCita'])->name('api.citas.delete');


        // ----------------------------------------
        // C. RUTAS EXCLUSIVAS PARA ADMINISTRADORES (Cod_Rol 1)
        // ----------------------------------------
        Route::middleware('role:admin')->group(function () {
            // Reportes
            Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');

            // Gestión de Personal
            Route::get('/gestion-personal', [GestionPersonalController::class, 'index'])->name('gestion-personal');
        });

    });

});

// ========================================
require __DIR__ . '/auth.php';
