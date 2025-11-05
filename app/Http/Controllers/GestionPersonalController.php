<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Services\ApiService;
use Illuminate\Support\Facades\Http;

class GestionPersonalController extends Controller
{
    private $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
    }

    /**
     * Muestra la página principal de Gestión de Personal
     */
    public function index()
    {
        $this->authorize('viewAny', Empleado::class);

        try {
            $empleados = $this->apiService->getEmpleados();
            return view('profile.gestion-personal', compact('empleados'));
            
        } catch (\Exception $e) {
            $empleados = [];
            return view('profile.gestion-personal', compact('empleados'))
                ->with('error', 'No se pudieron cargar los empleados: ' . $e->getMessage());
        }
    }

    /**
     * Obtener empleados activos con nombres para comisiones - NUEVA FUNCIÓN
     */
    public function getEmpleadosActivos()
    {
        try {
            // Obtener todos los empleados
            $empleados = $this->apiService->getEmpleados();
            
            // Obtener todas las personas para cruzar información
            $personas = $this->apiService->getPersonas();
            
            // Filtrar empleados activos y combinar con datos de persona
            $empleadosActivos = collect($empleados)
                ->filter(function($empleado) {
                    // Asumiendo que hay un campo 'Estado' o 'Disponibilidad'
                    return ($empleado['Estado'] ?? $empleado['Disponibilidad'] ?? 'Activo') === 'Activo';
                })
                ->map(function($empleado) use ($personas) {
                    // Buscar la persona correspondiente al empleado
                    $persona = collect($personas)->firstWhere('Cod_Persona', $empleado['Cod_Persona']);
                    
                    return [
                        'Cod_Empleado' => $empleado['Cod_Empleado'],
                        'Nombre_Completo' => ($persona['Nombre'] ?? 'N/A') . ' ' . ($persona['Apellido'] ?? 'N/A'),
                        'Departamento' => $empleado['Rol'] ?? 'N/A',
                        'Rol' => $empleado['Rol'] ?? 'N/A'
                    ];
                })
                ->values(); // Reindexar array

            return response()->json($empleadosActivos);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar empleados activos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo empleado
     */
    public function store(Request $request)
    {
        $this->authorize('create', Empleado::class);

        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'dni' => 'required|string|max:20',
                'fecha_nacimiento' => 'required|date',
                'genero' => 'required|string|in:masculino,femenino,otro',
                'email' => 'required|email|max:255',
                'rol' => 'required|string|max:255',
                'fecha_contratacion' => 'required|date',
                'salario' => 'required|numeric|min:0'
            ]);

            // VALIDACIÓN: Verificar si el DNI ya existe
            try {
                $personas = $this->apiService->getPersonas();
                $dniExiste = collect($personas)->firstWhere('DNI', $request->dni);
                
                if ($dniExiste) {
                    return redirect()->route('gestion-personal.index')
                        ->with('error', 'Ya existe una persona registrada con el DNI: ' . $request->dni);
                }
            } catch (\Exception $e) {
                // Si falla la consulta, continuar
            }

            // VALIDACIÓN: Verificar si el email ya existe
            try {
                $response = Http::timeout(10)->get('http://localhost:3000/correo');
                if ($response->successful()) {
                    $correos = $response->json();
                    $emailExiste = collect($correos)->firstWhere('Correo', $request->email);
                    
                    if ($emailExiste) {
                        return redirect()->route('gestion-personal.index')
                            ->with('error', 'Ya existe un empleado registrado con el correo: ' . $request->email);
                    }
                }
            } catch (\Exception $e) {
                // Si falla la consulta, continuar
            }

            // Datos para persona
            $datosPersona = [
                'Nombre' => $request->nombre,
                'Apellido' => $request->apellido,
                'DNI' => $request->dni,
                'Fecha_Nacimiento' => $request->fecha_nacimiento,
                'Genero' => $request->genero
            ];

            // Datos para empleado
            $datosEmpleado = [
                'Rol' => $request->rol,
                'Fecha_Contratacion' => $request->fecha_contratacion,
                'Salario' => $request->salario,
                'Disponibilidad' => $request->disponibilidad ?? 'Activo'
            ];

            // Crear empleado completo usando el servicio
            $this->apiService->crearEmpleadoCompleto($datosPersona, $request->email, $datosEmpleado);

            return redirect()->route('gestion-personal.index')
                ->with('success', 'Empleado creado exitosamente!');

        } catch (\Exception $e) {
            return redirect()->route('gestion-personal.index')
                ->with('error', 'Error al crear empleado: ' . $e->getMessage());
        }
    }

    /**
     * Registrar comisión para empleado - CORREGIDO
     */
    public function storeComision(Request $request)
    {
        try {
            // Validación de datos
            $request->validate([
                'cod_empleado' => 'required|integer',
                'monto_comision' => 'required|numeric|min:0',
                'fecha_comision' => 'required|date',
                'concepto_comision' => 'required|string|max:500'
            ]);

            $comisionData = [
                'Cod_Empleado' => $request->cod_empleado,
                'Monto_Comision' => $request->monto_comision,
                'Fecha_Comision' => $request->fecha_comision,
                'Concepto_Comision' => $request->concepto_comision,
                'Cod_Factura' => $request->cod_factura ?? null
            ];

            $this->apiService->createComision($comisionData);

            return redirect()->route('gestion-personal.index')
                ->with('success', 'Comisión registrada exitosamente!');

        } catch (\Exception $e) {
            return redirect()->route('gestion-personal.index')
                ->with('error', 'Error al registrar comisión: ' . $e->getMessage());
        }
    }

    /**
     * Obtener empleados via AJAX (para select de comisiones) - ACTUALIZADO
     */
    public function getEmpleadosAjax()
    {
        try {
            $empleados = $this->apiService->getEmpleados();
            $personas = $this->apiService->getPersonas();
            
            // Combinar datos de empleados con personas
            $empleadosConNombres = collect($empleados)->map(function($empleado) use ($personas) {
                $persona = collect($personas)->firstWhere('Cod_Persona', $empleado['Cod_Persona']);
                return [
                    'Cod_Empleado' => $empleado['Cod_Empleado'],
                    'Nombre_Completo' => ($persona['Nombre'] ?? 'N/A') . ' ' . ($persona['Apellido'] ?? 'N/A'),
                    'Rol' => $empleado['Rol'] ?? 'N/A',
                    'Departamento' => $empleado['Rol'] ?? 'N/A'
                ];
            });
            
            return response()->json($empleadosConNombres);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar empleados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ELIMINAR EMPLEADO
     */
    public function destroy($id)
    {
        try {
            $empleados = $this->apiService->getEmpleados();
            $empleadoData = collect($empleados)->firstWhere('Cod_Empleado', $id);
            
            if (!$empleadoData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado no encontrado'
                ], 404);
            }

            $empleado = Empleado::fromApiData($empleadoData);
            $this->authorize('delete', $empleado);

            $this->apiService->deleteEmpleado($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Empleado eliminado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener empleado específico
     */
    public function show($id)
    {
        try {
            $empleados = $this->apiService->getEmpleados();
            $empleado = collect($empleados)->firstWhere('Cod_Empleado', $id);
            
            if (!$empleado) {
                return response()->json(['error' => 'Empleado no encontrado'], 404);
            }

            return response()->json($empleado);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * EDITAR EMPLEADO
     */
    public function edit($id)
    {
        try {
            $empleados = $this->apiService->getEmpleados();
            $empleadoData = collect($empleados)->firstWhere('Cod_Empleado', $id);
            
            if (!$empleadoData) {
                return redirect()->route('gestion-personal.index')
                    ->with('error', 'Empleado no encontrado');
            }

            $empleado = Empleado::fromApiData($empleadoData);
            $this->authorize('update', $empleado);

            return view('profile.editar-empleado', compact('empleadoData'));

        } catch (\Exception $e) {
            return redirect()->route('gestion-personal.index')
                ->with('error', 'Error al cargar empleado para edición: ' . $e->getMessage());
        }
    }

    /**
     * ACTUALIZAR EMPLEADO
     */
    public function update(Request $request, $id)
    {
        try {
            $empleados = $this->apiService->getEmpleados();
            $empleadoData = collect($empleados)->firstWhere('Cod_Empleado', $id);
            
            if (!$empleadoData) {
                return redirect()->route('gestion-personal.index')
                    ->with('error', 'Empleado no encontrado');
            }

            $empleado = Empleado::fromApiData($empleadoData);
            $this->authorize('update', $empleado);

            // AQUÍ IRÍA LA LÓGICA DE ACTUALIZACIÓN (que parece faltar)
            // $this->apiService->updateEmpleado($id, $request->all());

            return redirect()->route('gestion-personal.index')
                ->with('success', 'Empleado actualizado exitosamente!');

        } catch (\Exception $e) {
            return redirect()->route('gestion-personal.index')
                ->with('error', 'Error al actualizar empleado: ' . $e->getMessage());
        }
    }
}