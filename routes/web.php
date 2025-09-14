<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionPersonalController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Agregar la ruta de factura AQUÍ (sin duplicar <?php)
Route::get('/factura', function () {
    return view('factura');
})->name('factura');

Route::get('/factura/crear', function () {
    return view('factura-crear');
})->name('factura.crear');


Route::get('/servicios', function () {
    return view('gestion-servicios');
})->name('servicios');

// Ruta de citas 
Route::get('/citas', [CitasController::class, 'index'])->name('citas');


// Ruta de inventario
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');

// ruta de reportes 
Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');

// Ruta del dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// Ruta de gestion de personal 
Route::get('/gestion-personal', [GestionPersonalController::class, 'index'])->name('gestion-personal');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';