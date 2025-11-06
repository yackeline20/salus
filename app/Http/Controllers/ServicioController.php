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

        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/tratamiento");
            
            if ($response->successful()) {
                $tratamientos = $response->json();
                return view('gestion-servicios', compact('tratamientos'));
            } else {
                return view('gestion-servicios')->with('error', 'No se pudieron cargar los servicios.');
            }
        } catch (\Exception $e) {
            \Log::error('Error al obtener tratamientos: ' . $e->getMessage());
            return view('gestion-servicios')->with('error', 'Error de conexión con el servidor.');
        }
    }

    /**
     * Obtiene todos los tratamientos (API para AJAX)
     */
    public function getTratamientos()
    {
        $this->authorize('viewAny', Tratamiento::class);

        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/tratamiento");
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener los servicios'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error en getTratamientos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con el servidor'
            ], 500);
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
        $this->authorize('create', Tratamiento::class);

        $validated = $request->validate([
            'Nombre_Tratamiento' => 'required|string|max:50',
            'Descripcion' => 'nullable|string',
            'Precio_Estandar' => 'required|numeric|min:0',
            'Duracion_Estimada_Min' => 'required|integer|min:0'
        ]);

        try {
            $response = Http::timeout(10)->post("{$this->apiUrl}/tratamiento", $validated);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Servicio creado exitosamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el servicio'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error en store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con el servidor'
            ], 500);
        }
    }

    /**
     * Actualiza un tratamiento existente
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', Tratamiento::class);

        $validated = $request->validate([
            'Nombre_Tratamiento' => 'required|string|max:50',
            'Descripcion' => 'nullable|string',
            'Precio_Estandar' => 'required|numeric|min:0',
            'Duracion_Estimada_Min' => 'required|integer|min:0'
        ]);

        try {
            $validated['Cod_Tratamiento'] = $id;

            $response = Http::timeout(10)->put("{$this->apiUrl}/tratamiento", $validated);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Servicio actualizado exitosamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el servicio'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error en update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con el servidor'
            ], 500);
        }
    }

    /**
     * Exporta los servicios a Excel
     */
    public function exportExcel()
    {
        $this->authorize('viewAny', Tratamiento::class);
        
        return Excel::download(new ServiciosExport, 'servicios_' . date('Y-m-d_His') . '.xlsx');
    }

    /**
     * Elimina un tratamiento
     */
    public function destroy($id)
    {
        $this->authorize('delete', Tratamiento::class);

        try {
            $response = Http::timeout(10)->delete("{$this->apiUrl}/tratamiento", [
                'Cod_Tratamiento' => $id
            ]);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Servicio eliminado exitosamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el servicio'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error en destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con el servidor'
            ], 500);
        }
    }

    /**
     * Buscar tratamientos por nombre o categoría
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', Tratamiento::class);

        $searchTerm = $request->input('search', '');

        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/tratamiento");
            
            if ($response->successful()) {
                $tratamientos = $response->json();
                
                // Filtrar localmente si hay término de búsqueda
                if (!empty($searchTerm)) {
                    $tratamientos = array_filter($tratamientos, function($tratamiento) use ($searchTerm) {
                        return stripos($tratamiento['Nombre_Tratamiento'], $searchTerm) !== false ||
                               stripos($tratamiento['Descripcion'], $searchTerm) !== false;
                    });
                }
                
                return response()->json([
                    'success' => true,
                    'data' => array_values($tratamientos)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al buscar servicios'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error en search: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con el servidor'
            ], 500);
        }
    }
}