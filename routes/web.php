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
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\AdministracionController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BitacoraController;
use App\Services\ApiService; // Necesario para la ruta de comisiones

// ========================================
// RUTA RAÍZ
// ========================================
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
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN + VERIFICACIÓN 2FA)
// ========================================

Route::middleware(['auth', 'twofactor'])->group(function () {

    // ----------------------------------------
    // A. RUTAS COMUNES Y DASHBOARD
    // ----------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    // ----------------------------------------
    // B. MÓDULOS CON RUTAS WEB (HTML)
    // ----------------------------------------

    // MÓDULO DE FACTURACIÓN (Rutas Web)
    Route::prefix('factura')->name('factura.')->group(function () {
        // Rutas específicas y de exportación (DEBEN IR PRIMERO)
        Route::get('{factura}/pdf', [FacturaController::class, 'exportPdf'])->name('export_pdf');
        Route::get('{factura}/recibo', [FacturaController::class, 'recibo'])->name('recibo');
        Route::get('{factura}/show', [FacturaController::class, 'showFactura'])->name('show');
    });

    // Se mantiene Route::resource para las vistas web (index, create, store, edit, update, destroy)
    Route::resource('factura', FacturaController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('can:viewAny,App\Models\Factura'); // Se añade el middleware 'can' general al resource

    // MÓDULO DE CLIENTES
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index')
        ->middleware('can:viewAny,App\Models\Cliente');

    // MÓDULO DE CITAS
    Route::get('/citas', [CitasController::class, 'index'])->name('citas')
        ->middleware('can:viewAny,App\Models\Cita');

    // MÓDULO DE INVENTARIO
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario')
        ->middleware('can:viewAny,App\Models\Product');

    // MÓDULO DE SERVICIOS (TRATAMIENTOS)
    Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios')
        ->middleware('can:viewAny,App\Models\Tratamiento');
    Route::get('/servicios/export', [ServicioController::class, 'exportExcel'])->name('servicios.export')
        ->middleware('can:viewAny,App\Models\Tratamiento');

    // MÓDULO DE REPORTES (Rutas Web)
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes')
        ->middleware('can:viewAny,App\Models\Reporte');
    // Rutas para interacción de reportes
    Route::post('/reportes/consultar', [ReportesController::class, 'obtenerReporte'])->name('reportes.consultar')
        ->middleware('can:viewAny,App\Models\Reporte');
    Route::get('/reportes/exportar', [ReportesController::class, 'exportar'])
        ->name('reportes.exportar')
        ->middleware('can:viewAny,App\Models\Reporte');

    // MÓDULO DE GESTIÓN DE PERSONAL (Rutas Web)
    Route::prefix('gestion-personal')->name('gestion-personal.')->group(function () {
        // Página principal
        Route::get('/', [GestionPersonalController::class, 'index'])->name('index')
            ->middleware('can:viewAny,App\Models\Empleado');
        // Crear empleado (POST)
        Route::post('/', [GestionPersonalController::class, 'store'])->name('store')
            ->middleware('can:create,App\Models\Empleado');
        // Registrar comisión
        Route::post('/comision', [GestionPersonalController::class, 'storeComision'])->name('comision.store')
            ->middleware('can:create,App\Models\Empleado');
        // Ver empleado específico
        Route::get('/empleados/{id}', [GestionPersonalController::class, 'show'])->name('show')
            ->middleware('can:view,App\Models\Empleado');
        // ELIMINAR EMPLEADO
        Route::delete('/empleados/{id}', [GestionPersonalController::class, 'destroy'])->name('destroy');
        // RUTAS AJAX para el frontend
        Route::get('/empleados-activos', [GestionPersonalController::class, 'getEmpleadosActivos'])
            ->name('empleados.activos')
            ->middleware('can:viewAny,App\Models\Empleado');
        Route::get('/empleados-ajax', [GestionPersonalController::class, 'getEmpleadosAjax'])
            ->name('empleados.ajax')
            ->middleware('can:viewAny,App\Models\Empleado');
    });

    // MÓDULO DE ADMINISTRACIÓN
    Route::get('/administracion', [AdministracionController::class, 'index'])->name('administracion')
        ->middleware('can:viewAny,App\Models\Cliente');
    Route::prefix('administracion')->middleware('can:viewAny,App\Models\Cliente')->group(function () {
        // Backup y Restore
        Route::get('/backup', [AdministracionController::class, 'backup'])->name('administracion.backup');
        Route::post('/backup/crear', [AdministracionController::class, 'crearBackup'])->name('administracion.backup.crear');
        Route::post('/backup/restaurar', [AdministracionController::class, 'restaurarBackup'])->name('administracion.backup.restaurar');
        // Cambio de Contraseña
        Route::get('/password', [AdministracionController::class, 'password'])->name('administracion.password');
        Route::post('/password/cambiar', [AdministracionController::class, 'cambiarPassword'])->name('administracion.password.cambiar');
    });

    // MÓDULO DE BITÁCORA
    Route::prefix('bitacora')->name('bitacora.')->group(function () {
        Route::get('/', [BitacoraController::class, 'index'])->name('index');
        Route::get('/export/pdf', [BitacoraController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{id}', [BitacoraController::class, 'show'])->name('show');
        Route::delete('/{id}', [BitacoraController::class, 'destroy'])->name('destroy');
        Route::post('/restaurar/{id}', [BitacoraController::class, 'restaurar'])->name('restaurar');
    });

    // ----------------------------------------
    // C. RUTAS DE API (CRUD DE MÓDULOS)
    // ----------------------------------------
    Route::prefix('api')->name('api.')->group(function () {

        // API DE FACTURAS (Cabecera)
        Route::get('factura', [FacturaController::class, 'index'])->name('factura.index');
        Route::post('factura', [FacturaController::class, 'store'])->name('factura.store');
        Route::put('factura/{id}', [FacturaController::class, 'update'])->name('factura.update');
        Route::delete('factura/{id}', [FacturaController::class, 'destroy'])->name('factura.destroy');

        // API DE DETALLE DE FACTURA - TRATAMIENTO
        Route::get('detalle_factura_tratamiento', [FacturaController::class, 'getDetalleTratamiento'])->name('factura.detalle_tratamiento');
        Route::post('detalle_factura_tratamiento', [FacturaController::class, 'storeDetalleTratamiento'])->name('detalle_tratamiento.store');
        Route::patch('detalle_factura_tratamiento/{id}', [FacturaController::class, 'updateDetalleTratamiento'])->name('detalle_tratamiento.update');
        Route::delete('detalle_factura_tratamiento/{id}', [FacturaController::class, 'destroyDetalleTratamiento'])->name('detalle_tratamiento.destroy');

        // API DE DETALLE DE FACTURA - PRODUCTO
        Route::get('detalle_factura_producto', [FacturaController::class, 'getDetalleProducto'])->name('factura.detalle_producto');
        Route::post('detalle_factura_producto', [FacturaController::class, 'storeDetalleProducto'])->name('detalle_producto.store');
        Route::patch('detalle_factura_producto/{id}', [FacturaController::class, 'updateDetalleProducto'])->name('detalle_producto.update');
        Route::delete('detalle_factura_producto/{id}', [FacturaController::class, 'destroyDetalleProducto'])->name('detalle_producto.destroy');

        // API DE CITAS
        Route::get('citas', [CitasController::class, 'getCitas'])->name('citas.get');
        Route::post('citas', [CitasController::class, 'storeCita'])->name('citas.store');
        Route::put('citas/{id}', [CitasController::class, 'updateCita'])->name('citas.update');
        Route::delete('citas/{id}', [CitasController::class, 'deleteCita'])->name('citas.delete');
        Route::put('citas/estado/{id}', [CitasController::class, 'updateStatus'])->name('citas.update-status');
        // Búsqueda y creación de clientes para citas
        Route::get('citas/buscar-cliente', [CitasController::class, 'buscarCliente'])->name('citas.buscar-cliente');
        Route::post('citas/crear-cliente', [CitasController::class, 'crearClienteCompleto'])->name('citas.crear-cliente');
        Route::get('clientes/listado', [CitasController::class, 'listado'])
            ->name('clientes.listado')
            ->middleware('can:viewAny,App\Models\Cita');

        // API DE INVENTARIO (PRODUCTOS)
        Route::get('productos', [InventarioController::class, 'getProducts'])->name('productos.index');
        Route::post('productos', [InventarioController::class, 'storeProduct'])->name('productos.store');
        Route::put('productos/{id}', [InventarioController::class, 'updateProduct'])->name('productos.update');
        Route::delete('productos/{id}', [InventarioController::class, 'destroyProduct'])->name('productos.destroy');
        Route::get('inventario/proveedores', [InventarioController::class, 'getProveedores'])->name('inventario.proveedores');
        Route::get('inventario/categorias', [InventarioController::class, 'getCategorias'])->name('inventario.categorias');
        Route::get('inventario/estadisticas', [InventarioController::class, 'getEstadisticas'])
            ->name('inventario.estadisticas')
            ->middleware('can:viewAny,App\Models\Product');

        // APIs para PROVEEDOR (Procedimientos almacenados)
        Route::post('/proveedor', function () {
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
        Route::get('/proveedor', function () {
            try {
                $result = DB::select('CALL Sel_Proveedor(NULL)');
                return response()->json($result);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al obtener proveedores: ' . $e->getMessage()], 500);
            }
        });
        Route::put('/proveedor', function () {
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
        Route::delete('/proveedor', function () {
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

        // APIs para PRODUCTO-PROVEEDOR (Procedimientos almacenados)
        Route::post('/producto_proveedor', function () {
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
        Route::get('/producto_proveedor', function () {
            try {
                $result = DB::select('CALL Sel_Producto_Proveedor(NULL)');
                return response()->json($result);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al obtener relaciones: ' . $e->getMessage()], 500);
            }
        });
        Route::put('/producto_proveedor', function () {
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
        Route::delete('/producto_proveedor', function () {
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

        // API DE SERVICIOS (TRATAMIENTOS)
        Route::get('tratamientos', [ServicioController::class, 'getTratamientos'])->name('tratamientos.index');
        Route::get('tratamientos/{id}', [ServicioController::class, 'show'])->name('tratamientos.show')
            ->middleware('can:viewAny,App\Models\Tratamiento');
        Route::post('tratamientos', [ServicioController::class, 'storeTratamiento'])->name('tratamientos.store');
        Route::put('tratamientos/{id}', [ServicioController::class, 'updateTratamiento'])->name('tratamientos.update');
        Route::delete('tratamientos/{id}', [ServicioController::class, 'destroyTratamiento'])->name('tratamientos.destroy');

        // API DE GESTIÓN DE PERSONAL Y COMISIONES (CRUD)
        Route::get('empleados', [GestionPersonalController::class, 'getEmpleadosAjax'])->name('empleados.index');
        Route::get('empleados/activos', [GestionPersonalController::class, 'getEmpleadosActivos'])->name('empleados.activos');
        Route::post('empleados', [GestionPersonalController::class, 'store'])->name('empleados.store');
        Route::get('empleados/{id}', [GestionPersonalController::class, 'show'])->name('empleados.show');
        Route::put('empleados/{id}', [GestionPersonalController::class, 'update'])->name('empleados.update');
        Route::delete('empleados/{id}', [GestionPersonalController::class, 'destroy'])->name('empleados.destroy');

        // API DE COMISIONES (Consumo de ApiService)
        Route::get('comisiones/{cod_empleado}', function($cod_empleado) {
            $apiService = new ApiService();
            $comisiones = $apiService->getComisiones();
            return response()->json(
                collect($comisiones)->where('Cod_Empleado', (int)$cod_empleado)->values()
            );
        })->name('comisiones.empleado');

    }); // FIN DE RUTAS API

}); // FIN DE MIDDLEWARE AUTH + TWOFACTOR

require __DIR__ . '/auth.php';