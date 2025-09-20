<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionPersonalController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

Route::middleware(['auth', 'verified'])->group(function () {
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

require __DIR__.'/auth.php';
