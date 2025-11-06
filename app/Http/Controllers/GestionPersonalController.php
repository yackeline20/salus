<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Services\ApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GestionPersonalController extends Controller
{
    private $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
    }

    public function index()
    {
        $this->authorize('viewAny', Empleado::class);

        try {
            // Verificar API
            try {
                $apiTest = Http::timeout(5)->get('http://localhost:3000/persona');
                if (!$apiTest->successful()) {
                    throw new \Exception('API no responde');
                }
            } catch (\Exception $e) {
                Log::error('❌ API no disponible: ' . $e->getMessage());
                $empleados = [];
                return view('profile.gestion-personal', compact('empleados'))
                    ->with('error', '⚠️ No se puede conectar con el servidor API');
            }

            $empleadosRaw = $this->apiService->getEmpleados();
            
            if (empty($empleadosRaw)) {
                Log::warning('⚠️ No se encontraron empleados');
                $empleados = [];
                return view('profile.gestion-personal', compact('empleados'))
                    ->with('info', 'No hay empleados registrados');
            }
            
            $personas = $this->apiService->getPersonas();
            
            // Combinar empleados con sus datos
            $empleados = collect($empleadosRaw)->map(function($empleadoData) use ($personas) {
                $persona = collect($personas)->firstWhere('Cod_Persona', $empleadoData['Cod_Persona']);
                
                if ($persona) {
                    $empleadoData['Nombre'] = $persona['Nombre'] ?? 'N/A';
                    $empleadoData['Apellido'] = $persona['Apellido'] ?? 'N/A';
                    $empleadoData['DNI'] = $persona['DNI'] ?? 'N/A';
                    $empleadoData['Genero'] = $persona['Genero'] ?? 'N/A';
                    $empleadoData['Fecha_Nacimiento'] = $persona['Fecha_Nacimiento'] ?? 'N/A';
                } else {
                    $empleadoData['Nombre'] = 'Sin Nombre';
                    $empleadoData['Apellido'] = '';
                    $empleadoData['DNI'] = 'N/A';
                    $empleadoData['Genero'] = 'N/A';
                    $empleadoData['Fecha_Nacimiento'] = 'N/A';
                }
                
                // ✅ OBTENER CORREO
                try {
                    $correosResponse = Http::timeout(5)->get('http://localhost:3000/correo');
                    if ($correosResponse->successful()) {
                        $correos = $correosResponse->json();
                        $correoData = collect($correos)->firstWhere('Cod_Persona', $empleadoData['Cod_Persona']);
                        $empleadoData['Correo'] = $correoData['Correo'] ?? 'N/A';
                    } else {
                        $empleadoData['Correo'] = 'N/A';
                    }
                } catch (\Exception $e) {
                    Log::warning('⚠️ Error al obtener correo: ' . $e->getMessage());
                    $empleadoData['Correo'] = 'N/A';
                }
                
                // ✅ OBTENER TELÉFONO
                try {
                    $telefonosResponse = Http::timeout(5)->get('http://localhost:3000/telefono');
                    if ($telefonosResponse->successful()) {
                        $telefonos = $telefonosResponse->json();
                        $telefonoData = collect($telefonos)->firstWhere('Cod_Persona', $empleadoData['Cod_Persona']);
                        $empleadoData['Telefono'] = $telefonoData['Numero'] ?? 'N/A';
                    } else {
                        $empleadoData['Telefono'] = 'N/A';
                    }
                } catch (\Exception $e) {
                    Log::warning('⚠️ Error al obtener teléfono: ' . $e->getMessage());
                    $empleadoData['Telefono'] = 'N/A';
                }
                
                return $empleadoData;
            })->toArray();
            
            Log::info('✅ Empleados cargados', ['total' => count($empleados)]);
            
            return view('profile.gestion-personal', compact('empleados'));
            
        } catch (\Exception $e) {
            Log::error('❌ Error al cargar empleados: ' . $e->getMessage());
            $empleados = [];
            return view('profile.gestion-personal', compact('empleados'))
                ->with('error', '❌ Error al cargar empleados: ' . $e->getMessage());
        }
    }

    public function getEmpleadosActivos()
    {
        try {
            $empleados = $this->apiService->getEmpleados();
            $personas = $this->apiService->getPersonas();
            
            $empleadosActivos = collect($empleados)
                ->filter(fn($e) => ($e['Disponibilidad'] ?? 'Activo') === 'Activo')
                ->map(function($empleado) use ($personas) {
                    $persona = collect($personas)->firstWhere('Cod_Persona', $empleado['Cod_Persona']);
                    return [
                        'Cod_Empleado' => $empleado['Cod_Empleado'],
                        'Nombre_Completo' => ($persona['Nombre'] ?? 'N/A') . ' ' . ($persona['Apellido'] ?? 'N/A'),
                        'Departamento' => $empleado['Rol'] ?? 'N/A',
                        'Rol' => $empleado['Rol'] ?? 'N/A'
                    ];
                })
                ->values();

            return response()->json($empleadosActivos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create', Empleado::class);

        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'dni' => 'required|string|size:13|regex:/^[0-9]+$/',
                'telefono' => 'required|string|size:8|regex:/^[0-9]+$/',
                'fecha_nacimiento' => 'required|date|before:-18 years',
                'genero' => 'required|string|in:Masculino,Femenino,Otro',
                'email' => 'required|email|max:255',
                'rol' => 'required|string|max:255',
                'fecha_contratacion' => 'required|date|before_or_equal:today',
                'salario' => 'required|numeric|min:0'
            ], [
                'dni.size' => 'El DNI debe tener exactamente 13 dígitos',
                'dni.regex' => 'El DNI debe contener solo números',
                'telefono.size' => 'El teléfono debe tener exactamente 8 dígitos',
                'telefono.regex' => 'El teléfono debe contener solo números',
                'fecha_nacimiento.before' => 'El empleado debe ser mayor de 18 años',
                'genero.in' => 'Seleccione un género válido'
            ]);

            Log::info('🚀 Creando empleado', $validated);

            // Verificar API
            try {
                $apiTest = Http::timeout(5)->get('http://localhost:3000/persona');
                if (!$apiTest->successful()) {
                    throw new \Exception('API no responde');
                }
            } catch (\Exception $e) {
                return redirect()->route('gestion-personal.index')
                    ->withInput()
                    ->with('error', '❌ API no disponible');
            }

            // Verificar DNI duplicado
            $personas = $this->apiService->getPersonas();
            if (collect($personas)->firstWhere('DNI', $request->dni)) {
                return redirect()->route('gestion-personal.index')
                    ->withInput()
                    ->with('error', '⚠️ DNI ya existe: ' . $request->dni);
            }

            // Verificar email duplicado
            $response = Http::timeout(10)->get('http://localhost:3000/correo');
            if ($response->successful()) {
                if (collect($response->json())->firstWhere('Correo', $request->email)) {
                    return redirect()->route('gestion-personal.index')
                        ->withInput()
                        ->with('error', '⚠️ Email ya existe: ' . $request->email);
                }
            }

            // Verificar teléfono duplicado
            $telResponse = Http::timeout(10)->get('http://localhost:3000/telefono');
            if ($telResponse->successful()) {
                if (collect($telResponse->json())->firstWhere('Numero', $request->telefono)) {
                    return redirect()->route('gestion-personal.index')
                        ->withInput()
                        ->with('error', '⚠️ Teléfono ya existe: ' . $request->telefono);
                }
            }

            $datosPersona = [
                'Nombre' => trim($request->nombre),
                'Apellido' => trim($request->apellido),
                'DNI' => trim($request->dni),
                'Fecha_Nacimiento' => $request->fecha_nacimiento,
                'Genero' => $request->genero
            ];

            $datosEmpleado = [
                'Rol' => $request->rol,
                'Fecha_Contratacion' => $request->fecha_contratacion,
                'Salario' => floatval($request->salario),
                'Disponibilidad' => 'Activo'
            ];

            Log::info('📝 Datos preparados', [
                'persona' => $datosPersona,
                'empleado' => $datosEmpleado,
                'email' => $request->email,
                'telefono' => $request->telefono
            ]);

            // Crear empleado completo
            $resultado = $this->apiService->crearEmpleadoCompleto(
                $datosPersona, 
                trim($request->email),
                trim($request->telefono),
                $datosEmpleado
            );

            Log::info('✅ Empleado creado', $resultado);

            return redirect()->route('gestion-personal.index')
                ->with('success', '✅ Empleado creado: ' . $request->nombre . ' ' . $request->apellido);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('gestion-personal.index')
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', '⚠️ Corrige los errores del formulario');
        } catch (\Exception $e) {
            Log::error('❌ Error al crear empleado', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('gestion-personal.index')
                ->withInput()
                ->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    public function storeComision(Request $request)
    {
        try {
            $request->validate([
                'cod_empleado' => 'required|integer',
                'monto_comision' => 'required|numeric|min:0',
                'fecha_comision' => 'required|date|before_or_equal:today',
                'concepto_comision' => 'required|string|max:500'
            ]);

            $comisionData = [
                'Cod_Empleado' => $request->cod_empleado,
                'Monto_Comision' => floatval($request->monto_comision),
                'Fecha_Comision' => $request->fecha_comision,
                'Concepto_Comision' => $request->concepto_comision,
                'Cod_Factura' => $request->cod_factura ?? null
            ];

            Log::info('💰 Registrando comisión', $comisionData);
            $this->apiService->createComision($comisionData);
            Log::info('✅ Comisión registrada');

            return redirect()->route('gestion-personal.index')
                ->with('success', '✅ Comisión registrada!');
        } catch (\Exception $e) {
            Log::error('❌ Error comisión: ' . $e->getMessage());
            return redirect()->route('gestion-personal.index')
                ->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    public function getEmpleadosAjax()
    {
        try {
            $empleados = $this->apiService->getEmpleados();
            $personas = $this->apiService->getPersonas();
            
            $empleadosConNombres = collect($empleados)->map(function($empleado) use ($personas) {
                $persona = collect($personas)->firstWhere('Cod_Persona', $empleado['Cod_Persona']);
                return [
                    'Cod_Empleado' => $empleado['Cod_Empleado'],
                    'Nombre' => $persona['Nombre'] ?? 'N/A',
                    'Apellido' => $persona['Apellido'] ?? 'N/A',
                    'Nombre_Completo' => ($persona['Nombre'] ?? 'N/A') . ' ' . ($persona['Apellido'] ?? 'N/A'),
                    'Rol' => $empleado['Rol'] ?? 'N/A',
                    'Departamento' => $empleado['Rol'] ?? 'N/A',
                    'Disponibilidad' => $empleado['Disponibilidad'] ?? 'Activo'
                ];
            });
            
            return response()->json($empleadosConNombres);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ MÉTODO DESTROY ACTUALIZADO - SIN USAR POLICY
     * Verifica permisos directamente desde el modelo Usuario
     */
    public function destroy($id)
    {
        try {
            // ✅ Verificar permiso directamente sin Policy
            if (!auth()->user()->hasPermission('delete', 'Gestión de Personal')) {
                Log::warning('⚠️ Usuario sin permisos de eliminación', [
                    'usuario' => auth()->user()->Nombre_Usuario,
                    'rol' => auth()->user()->Nombre_Rol
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => 'No tienes permisos para eliminar empleados'
                ], 403);
            }

            // Buscar empleado
            $empleados = $this->apiService->getEmpleados();
            $empleadoData = collect($empleados)->firstWhere('Cod_Empleado', (int)$id);
            
            if (!$empleadoData) {
                Log::warning('⚠️ Empleado no encontrado', ['id' => $id]);
                return response()->json([
                    'success' => false, 
                    'message' => 'Empleado no encontrado'
                ], 404);
            }

            Log::info('🗑️ Eliminando empleado', [
                'id' => $id,
                'empleado' => $empleadoData['Rol'] ?? 'N/A',
                'usuario' => auth()->user()->Nombre_Usuario
            ]);
            
            // Eliminar en la API
            $this->apiService->deleteEmpleado($id);
            
            Log::info('✅ Empleado eliminado exitosamente', ['id' => $id]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Empleado eliminado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error al eliminar empleado', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $empleados = $this->apiService->getEmpleados();
            $empleado = collect($empleados)->firstWhere('Cod_Empleado', $id);
            
            if (!$empleado) {
                return response()->json(['error' => 'No encontrado'], 404);
            }

            return response()->json($empleado);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}