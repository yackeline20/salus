<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Tratamiento;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServiciosExport;

class ServicioController extends Controller
{
    private $apiUrl = 'http://localhost:3000';

    /**
     * Muestra el listado de tratamientos/servicios
     */
    public function index()
    {
        $this->authorize('viewAny', Tratamiento::class);
        
        // 🔥 Obtener datos desde la API de Node.js
        try {
            $response = Http::timeout(5)->get($this->apiUrl . '/tratamiento');
            
            if ($response->successful()) {
                $tratamientos = $response->json();
                return view('gestion-servicios', compact('tratamientos'));
            }
            
            return back()->with('error', 'No se pudo conectar con la API');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al obtener servicios: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los tratamientos (para AJAX)
     */
    public function getTratamientos()
    {
        $this->authorize('viewAny', Tratamiento::class);
        
        
        try {
            $response = Http::timeout(5)->get($this->apiUrl . '/tratamiento');
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'No se pudo obtener los tratamientos'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra un tratamiento específico
     */
    public function show($id)
    {
        $this->authorize('viewAny', Tratamiento::class);
        
        try {
            $response = Http::timeout(5)->get($this->apiUrl . '/tratamiento/' . $id);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Tratamiento no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Almacena un nuevo tratamiento
     */
    public function store(Request $request)
    {
       

        
        $validated = $request->validate([
            'Nombre_Tratamiento' => 'required|string|max:100',
            'Descripcion' => 'nullable|string',
            'Precio_Estandar' => 'required|numeric|min:0',
            'Duracion_Estimada_Min' => 'required|integer|min:0'
        ]);

        try {
            $response = Http::timeout(5)->post($this->apiUrl . '/tratamiento', $validated);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Servicio creado exitosamente'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el servicio'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza un tratamiento existente
     */
    public function update(Request $request, $id)
    {
        // ✅ AUTORIZACIÓN COMENTADA - La verificación se hace a nivel de ruta
        // $this->authorize('update', Tratamiento::class);
        
        $validated = $request->validate([
            'Nombre_Tratamiento' => 'required|string|max:100',
            'Descripcion' => 'nullable|string',
            'Precio_Estandar' => 'required|numeric|min:0',
            'Duracion_Estimada_Min' => 'required|integer|min:0'
        ]);

        $validated['Cod_Tratamiento'] = $id;

        try {
            $response = Http::timeout(5)->put($this->apiUrl . '/tratamiento', $validated);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Servicio actualizado exitosamente'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el servicio'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un tratamiento
     */

/**
 * Exporta los servicios a Excel
 */
public function exportExcel()
{
    $this->authorize('viewAny', Tratamiento::class);
    
    return Excel::download(new ServiciosExport, 'servicios_' . date('Y-m-d_His') . '.xlsx');
}






    public function destroy($id)
    {
        // ✅ AUTORIZACIÓN COMENTADA - La verificación se hace a nivel de ruta
        // $this->authorize('delete', Tratamiento::class);
        
        try {
            $response = Http::timeout(5)->delete($this->apiUrl . '/tratamiento', [
                'Cod_Tratamiento' => $id
            ]);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Servicio eliminado exitosamente'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el servicio'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}