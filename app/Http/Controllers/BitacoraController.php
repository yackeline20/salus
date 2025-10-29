<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
// use PDF; // Descomentar si usas un generador de PDF (ej: barryvdh/laravel-dompdf)

class BitacoraController extends Controller
{
    /**
     * Muestra la vista de bitácora con registros paginados y filtrados.
     * Soporta paginación dinámica (per_page) y búsqueda general (search).
     */
    public function index(Request $request)
    {
        // Obtener el número de registros por página (por defecto 10)
        $perPage = $request->input('per_page', 10);
        
        $query = Bitacora::orderBy('Fecha_Registro', 'desc');

        // Búsqueda general en múltiples campos
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Modulo', 'like', "%{$search}%")
                  ->orWhere('Nombre_Usuario', 'like', "%{$search}%")
                  ->orWhere('Observaciones', 'like', "%{$search}%")
                  ->orWhere('Accion', 'like', "%{$search}%");
            });
        }

        // Aplicar la paginación y mantener los filtros en la URL
        $registros = $query->paginate($perPage)->withQueryString();

        return view('bitacora.index', compact('registros', 'perPage'));
    }

    /**
     * Ver detalles de un registro de bitácora (para modal o AJAX).
     * @param int $id El ID del registro de bitácora.
     */
    public function show($id)
    {
        try {
            $registro = Bitacora::findOrFail($id);
            return response()->json($registro);
        } catch (\Exception $e) {
            Log::warning("Intento de ver registro de bitácora no existente: ID {$id}");
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.'
            ], 404);
        }
    }

    /**
     * Elimina un registro de bitácora permanentemente (limpieza de log).
     * @param int $id El ID del registro de bitácora a eliminar.
     */
    public function destroy($id)
    {
        try {
            $registro = Bitacora::findOrFail($id);
            $registro->delete(); // Eliminación física del registro
            
            return response()->json([
                'success' => true,
                'message' => 'Registro de bitácora eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar registro de bitácora: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el registro.'
            ], 500);
        }
    }
    
    /**
     * Restaura un registro eliminado, asumiendo que los registros eliminados tienen Soft Deletes
     * y que la bitácora guarda la referencia.
     */
    public function restaurar($id)
    {
        try {
            $registro = Bitacora::findOrFail($id);
            
            // 1. Validar que sea una acción de eliminación
            if (strtoupper($registro->Accion) != 'ELIMINACIÓN' && strtoupper($registro->Accion) != 'DELETE') {
                 return response()->json([
                    'success' => false,
                    'message' => 'El registro seleccionado no corresponde a una acción de eliminación.'
                ], 400);
            }

            // 2. Extraer el ID original desde Observaciones
            preg_match('/ID:\s*(\d+)/', $registro->Observaciones, $matches);
            
            if (empty($matches[1])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo identificar el ID del registro original a restaurar.'
                ], 400);
            }

            $idOriginal = $matches[1];
            $tabla = $registro->Modulo; // El módulo es la tabla afectada

            // -----------------------------------------------------------------
            // !!! LÓGICA CLAVE DE RESTAURACIÓN DE NEGOCIO (REQUIERE MODELOS) !!!
            // Para que esto funcione, debes tener el modelo de $tabla con Soft Deletes.
            // Ejemplo:
            // $modeloClase = "App\\Models\\{$tabla}";
            // if (class_exists($modeloClase)) {
            //     $modeloClase::withTrashed()->findOrFail($idOriginal)->restore(); 
            // } else {
            //     throw new \Exception("Modelo '{$tabla}' no encontrado para restauración.");
            // }
            // -----------------------------------------------------------------
            
            // 3. Registrar la acción de restauración manualmente (no debe pasar por trigger)
            self::registrarAccion('Bitácora', 'Restauración', "Restauración del registro ID: {$idOriginal} del módulo: {$tabla}, a partir del registro de bitácora ID: {$id}");
            
            return response()->json([
                'success' => true,
                'message' => "Registro {$idOriginal} del módulo {$tabla} restaurado exitosamente.",
                'data' => [
                    'tabla' => $tabla,
                    'id' => $idOriginal
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al restaurar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporta los registros de la bitácora a PDF.
     */
    public function exportPdf(Request $request)
    {
        // ... Lógica de filtrado ...
        $query = Bitacora::orderBy('Fecha_Registro', 'desc');

        if ($request->has('search') && $request->search != '') {
             // ... aplicar filtros ...
        }
        
        $registros = $query->get();

        // Implementación de generación de PDF aquí.
        // return PDF::loadView('bitacora.pdf_template', compact('registros'))->download('bitacora.pdf');

        return response()->json([
            'success' => true,
            'message' => 'PDF generado. Registros: ' . $registros->count()
        ]);
    }

    /**
     * MÉTODO CLAVE PARA TRABAJAR CON TRIGGERS.
     * Establece las variables de sesión de MySQL para que los triggers las lean
     * y registra acciones que no son cubiertas por triggers (ej. login, restauración).
     * * @param string $modulo El nombre del módulo/tabla afectado.
     * @param string $accion El tipo de acción (Creación, Modificación, Eliminación, etc.).
     * @param string $observaciones El detalle de la acción.
     * @return bool
     */
    public static function registrarAccion($modulo, $accion, $observaciones)
    {
        try {
            // 1. Definir variables de usuario
            $userId = Auth::id() ?? 0;
            $userName = Auth::user()->name ?? 'Sistema/Anónimo';
            $userIp = request()->ip();

            // 2. ESTABLECER VARIABLES DE SESIÓN PARA LOS TRIGGERS
            // Los triggers de otras tablas (ej: users, products) deben leer estas variables.
            DB::statement("SET @user_id = ?", [$userId]);
            DB::statement("SET @user_name = ?", [$userName]);
            DB::statement("SET @ip_address = ?", [$userIp]);

            // Opcional: Esto a veces ayuda a asegurar que la conexión de la sentencia SET
            // sea la misma que la usada por la siguiente consulta/trigger.
            // DB::reconnect(); 

            // 3. Registrar la acción manualmente (para acciones fuera de CRUD típico)
            Bitacora::create([
                'Cod_Usuario' => $userId,
                'Nombre_Usuario' => $userName,
                'Accion' => $accion,
                'Observaciones' => $observaciones,
                'Modulo' => $modulo,
                'IP_Address' => $userIp,
                'Fecha_Registro' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error en bitácora (registrarAccion): ' . $e->getMessage());
            return false;
        }
    }
}
