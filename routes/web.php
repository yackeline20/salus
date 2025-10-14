<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredPersonaController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionPersonalController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Ruta raíz - Redirige según el estado de autenticación
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard'); // Usamos el nombre de la ruta para ser seguros
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
Route::get('/register-usuario', [RegisteredUsuarioController::class, 'create'])->name('register.usuario');
Route::post('/register-usuario', [RegisteredUsuarioController::class, 'store']);

Route::get('/register-persona', [RegisteredPersonaController::class, 'create'])->name('register.persona');
Route::post('/register-persona', [RegisteredPersonaController::class, 'store']);

// ========================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// ========================================

Route::middleware('auth')->group(function () {

    // ----------------------------------------
    // A. RUTAS COMUNES y DASHBOARD
    // ----------------------------------------

    // DASHBOARD: Ahora solo requiere que el usuario esté autenticado.
    // La lógica de permisos para mostrar módulos va dentro del DashboardController.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // ----------------------------------------
    // B. MÓDULOS CON RESTRICCIÓN DE PERMISOS
    // ----------------------------------------

    // Módulo de Citas (Verifica el permiso 'select' para el objeto 'Citas')
    Route::get('/citas', [CitasController::class, 'index'])->name('citas')
        ->middleware('check.permissions:Citas,select'); // Explícitamente verificar 'select'

    // Rutas API de citas: Aquí sí especificamos la acción necesaria
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
      ->middleware('check.permissions:Gestión de Servicios,select'); // Usar Nombre_Objeto correcto

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

