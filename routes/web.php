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
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ServicioController;

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

    // ========================================
    // 🟢 MÓDULO DE INVENTARIO - COMPLETO Y CORREGIDO
    // ========================================
    
    // Vista principal del inventario - ESTA ES LA RUTA QUE FALTABA NOMBRAR
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');

    // Rutas API del inventario
    Route::prefix('api/inventario')->group(function () {
        Route::get('/productos', [InventarioController::class, 'obtenerProductos']);
        Route::get('/productos/{id}', [InventarioController::class, 'obtenerProducto']);
        Route::post('/productos', [InventarioController::class, 'crearProducto']);
        Route::put('/productos/{id}', [InventarioController::class, 'actualizarProducto']);
        Route::delete('/productos/{id}', [InventarioController::class, 'eliminarProducto']);
        
        // Rutas para estadísticas y utilidades
        Route::get('/estadisticas', [InventarioController::class, 'obtenerEstadisticas']);
        Route::get('/proveedores', [InventarioController::class, 'obtenerProveedores']);
        Route::get('/relaciones', [InventarioController::class, 'obtenerRelaciones']);
    });

    // ==================== RUTAS API PARA PROVEEDOR ====================
    
    // Insert Proveedor
    Route::post('/api/proveedor', function () {
        $rest = request()->all();
        
        try {
            DB::statement('CALL Ins_Proveedor(?, ?, ?, ?, ?)', [
                $rest['Nombre_Proveedor'] ?? null,
                $rest['Contacto_Principal'] ?? null,
                $rest['Telefono'] ?? null,
                $rest['Email'] ?? null,
                $rest['Direccion'] ?? null
            ]);
            
            return response("Proveedor ingresado correctamente!", 200);
        } catch (\Exception $e) {
            return response("Error al insertar proveedor: " . $e->getMessage(), 500);
        }
    });

    // Select Proveedor
    Route::get('/api/proveedor', function () {
        try {
            $result = DB::select('CALL Sel_Proveedor(NULL)');
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener proveedores: ' . $e->getMessage()], 500);
        }
    });

    // Update Proveedor
    Route::put('/api/proveedor', function () {
        $rest = request()->all();
        
        try {
            DB::statement('CALL Upd_Proveedor(?, ?, ?, ?, ?, ?)', [
                $rest['Cod_Proveedor'] ?? null,
                $rest['Nombre_Proveedor'] ?? null,
                $rest['Contacto_Principal'] ?? null,
                $rest['Telefono'] ?? null,
                $rest['Email'] ?? null,
                $rest['Direccion'] ?? null
            ]);
            
            return response("Proveedor actualizado exitosamente", 200);
        } catch (\Exception $e) {
            return response("Error al actualizar proveedor: " . $e->getMessage(), 500);
        }
    });

    // Delete Proveedor
    Route::delete('/api/proveedor', function () {
        $rest = request()->all();
        
        try {
            DB::statement('CALL Del_Proveedor(?)', [
                $rest['Cod_Proveedor'] ?? null
            ]);
            
            return response("Proveedor eliminado exitosamente", 200);
        } catch (\Exception $e) {
            return response("Error al eliminar proveedor: " . $e->getMessage(), 500);
        }
    });

    // ==================== RUTAS API PARA PRODUCTO-PROVEEDOR ====================
    
    // Insert Producto-Proveedor
    Route::post('/api/producto_proveedor', function () {
        $rest = request()->all();
        
        try {
            DB::statement('CALL Ins_Producto_Proveedor(?, ?, ?, ?)', [
                $rest['Cod_Producto'] ?? null,
                $rest['Cod_Proveedor'] ?? null,
                $rest['Precio_Ultima_Compra'] ?? null,
                $rest['Fecha_Ultima_Compra'] ?? null
            ]);
            
            return response("Relación producto-proveedor ingresada correctamente!", 200);
        } catch (\Exception $e) {
            return response("Error al insertar relación: " . $e->getMessage(), 500);
        }
    });

    // Select Producto-Proveedor
    Route::get('/api/producto_proveedor', function () {
        try {
            $result = DB::select('CALL Sel_Producto_Proveedor(NULL)');
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener relaciones: ' . $e->getMessage()], 500);
        }
    });

    // Update Producto-Proveedor
    Route::put('/api/producto_proveedor', function () {
        $rest = request()->all();
        
        try {
            DB::statement('CALL Upd_Producto_Proveedor(?, ?, ?, ?, ?)', [
                $rest['Cod_Prod_Prov'] ?? null,
                $rest['Cod_Producto'] ?? null,
                $rest['Cod_Proveedor'] ?? null,
                $rest['Precio_Ultima_Compra'] ?? null,
                $rest['Fecha_Ultima_Compra'] ?? null
            ]);
            
            return response("Relación producto-proveedor actualizada exitosamente", 200);
        } catch (\Exception $e) {
            return response("Error al actualizar relación: " . $e->getMessage(), 500);
        }
    });

    // Delete Producto-Proveedor
    Route::delete('/api/producto_proveedor', function () {
        $rest = request()->all();
        
        try {
            DB::statement('CALL Del_Producto_Proveedor(?)', [
                $rest['Cod_Prod_Prov'] ?? null
            ]);
            
            return response("Relación producto-proveedor eliminada exitosamente", 200);
        } catch (\Exception $e) {
            return response("Error al eliminar relación: " . $e->getMessage(), 500);
        }
    });

    // ========================================
    // 🟢 MÓDULO DE GESTIÓN DE SERVICIOS
    // ========================================
    
    Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios')
          ->middleware('can:viewAny,App\Models\Tratamiento');

    Route::prefix('api/servicios')->name('api.servicios.')->group(function () {
        Route::get('/', [ServicioController::class, 'getTratamientos'])->name('get')
            ->middleware('can:viewAny,App\Models\Tratamiento');
        Route::post('/', [ServicioController::class, 'store'])->name('store')
            ->middleware('can:create,App\Models\Tratamiento');
        Route::get('/{id}', [ServicioController::class, 'show'])->name('show')
            ->middleware('can:viewAny,App\Models\Tratamiento');
        Route::put('/{id}', [ServicioController::class, 'update'])->name('update')
            ->middleware('can:update,App\Models\Tratamiento');
        Route::delete('/{id}', [ServicioController::class, 'destroy'])->name('destroy')
            ->middleware('can:delete,App\Models\Tratamiento');
    });

    // ========================================
    // 🟢 MÓDULO DE GESTIÓN DE PERSONAL
    // ========================================
    
    // Vista principal de gestión de personal
    Route::get('/gestion-personal', [GestionPersonalController::class, 'index'])->name('gestion-personal.index')
         ->middleware('can:viewAny,App\Models\Usuario');

    // Rutas API de gestión de personal
    Route::prefix('api/gestion-personal')->name('api.gestion-personal.')->group(function () {
        // Obtener todo el personal
        Route::get('/', [GestionPersonalController::class, 'getPersonal'])->name('get')
            ->middleware('can:viewAny,App\Models\Usuario');
        
        // Crear nuevo personal
        Route::post('/', [GestionPersonalController::class, 'store'])->name('store')
            ->middleware('can:create,App\Models\Usuario');
        
        // Obtener un personal específico
        Route::get('/{id}', [GestionPersonalController::class, 'show'])->name('show')
            ->middleware('can:view,App\Models\Usuario');
        
        // Actualizar personal
        Route::put('/{id}', [GestionPersonalController::class, 'update'])->name('update')
            ->middleware('can:update,App\Models\Usuario');
        
        // Eliminar personal
        Route::delete('/{id}', [GestionPersonalController::class, 'destroy'])->name('destroy')
            ->middleware('can:delete,App\Models\Usuario');
        
        // Obtener estadísticas del personal
        Route::get('/estadisticas/general', [GestionPersonalController::class, 'getEstadisticas'])->name('estadisticas')
            ->middleware('can:viewAny,App\Models\Usuario');
    });

    // ========================================
    // 🟢 MÓDULO DE REPORTES
    // ========================================
    
    // Vista principal de reportes
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes')
         ->middleware('can:viewAny,App\Models\Cliente');

    // Rutas API de reportes
    Route::prefix('api/reportes')->name('api.reportes.')->group(function () {
        // Obtener reportes generales
        Route::get('/general', [ReportesController::class, 'getReporteGeneral'])->name('general')
            ->middleware('can:viewAny,App\Models\Cliente');
        
        // Obtener reporte de ventas
        Route::get('/ventas', [ReportesController::class, 'getReporteVentas'])->name('ventas')
            ->middleware('can:viewAny,App\Models\Factura');
        
        // Obtener reporte de citas
        Route::get('/citas', [ReportesController::class, 'getReporteCitas'])->name('citas')
            ->middleware('can:viewAny,App\Models\Cita');
        
        // Obtener reporte de inventario
        Route::get('/inventario', [ReportesController::class, 'getReporteInventario'])->name('inventario')
            ->middleware('can:viewAny,App\Models\Product');
        
        // Exportar reporte a PDF
        Route::post('/export/pdf', [ReportesController::class, 'exportPdf'])->name('export.pdf')
            ->middleware('can:viewAny,App\Models\Cliente');
        
        // Exportar reporte a Excel
        Route::post('/export/excel', [ReportesController::class, 'exportExcel'])->name('export.excel')
            ->middleware('can:viewAny,App\Models\Cliente');
    });

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
        Route::prefix('bitacora')->name('bitacora.')->group(function () {
            
            // 1. Mostrar la tabla de la Bitácora (URL: /bitacora)
            Route::get('/', [BitacoraController::class, 'index'])->name('index');

            // 2. Exportar los datos actuales (filtrados) a PDF
            Route::get('/export/pdf', [BitacoraController::class, 'exportPdf'])->name('export.pdf');

            // 3. Mostrar los detalles de un registro
            Route::get('/{id}', [BitacoraController::class, 'show'])->name('show');
            
            // 4. Elimina un registro de la bitácora
            Route::delete('/{id}', [BitacoraController::class, 'destroy'])->name('destroy');

            // 5. Procesa la restauración de un registro
            Route::post('/restaurar/{id}', [BitacoraController::class, 'restaurar'])->name('restaurar');
        });

    }); // CIERRE DEL PREFIX 'administracion'

}); // CIERRE DEL MIDDLEWARE 'auth', 'twofactor'

// ========================================
// RUTAS DE AUTENTICACIÓN PREDETERMINADAS
// ========================================
require __DIR__ . '/auth.php';