<?php
// En: app/Http/Controllers/CitasController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Cita; // Asegúrate de importar Cita
use Illuminate\Support\Facades\Log;

class CitasController extends Controller
{
    private $apiUrl = 'http://localhost:3000';

    /**
     * Muestra la vista principal del módulo de Citas.
     */
    public function index()
    {
        $this->authorize('viewAny', Cita::class);
        return view('citas');
    }

    // Buscar cliente por código
    public function buscarCliente(Request $request)
    {
        $this->authorize('viewAny', Cita::class);

        try {
            $codCliente = $request->query('cod');
            if (!$codCliente) {
                return response()->json(['error' => 'Código de cliente requerido'], 400);
            }
            $responseCliente = Http::get($this->apiUrl . '/cliente?cod=' . $codCliente);
            if (!$responseCliente->successful() || empty($responseCliente->json())) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }
            $cliente = $responseCliente->json()[0];
            $codPersona = $cliente['Cod_Persona'];
            $responsePersona = Http::get($this->apiUrl . '/persona?cod=' . $codPersona);
            $persona = $responsePersona->json()[0] ?? null;
            $responseCorreo = Http::get($this->apiUrl . '/correo');
            $correos = collect($responseCorreo->json())->where('Cod_Persona', $codPersona)->values()->all();
            
            // ⬇️ MODIFICADO: Ahora usa la API /telefonos (plural)
            $responseTelefono = Http::get($this->apiUrl . '/telefonos'); 
            $telefonos = collect($responseTelefono->json())->where('Cod_Persona', $codPersona)->values()->all();
            
            $responseDireccion = Http::get($this->apiUrl . '/direccion');
            $direcciones = collect($responseDireccion->json())->where('Cod_Persona', $codPersona)->values()->all();

            return response()->json([
                'cliente' => $cliente,
                'persona' => $persona,
                'correos' => $correos,
                'telefonos' => $telefonos,
                'direcciones' => $direcciones
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Crear cliente completo
    public function crearClienteCompleto(Request $request)
    {
        $this->authorize('create', Cita::class);

        try {
            $responsePersona = Http::post($this->apiUrl . '/persona', [
                'Nombre'           => $request->nombre,
                'Apellido'         => $request->apellido,
                'DNI'              => $request->dni,
                'Fecha_Nacimiento' => $request->fechaNacimiento,
                'Genero'           => $request->genero
            ]);

            if (!$responsePersona->successful()) {
                throw new \Exception('Error al crear persona: ' . $responsePersona->body());
            }
            $personaData = $responsePersona->json();
            $codPersona = $personaData['Cod_Persona'] ?? $personaData['cod_persona'] ?? null;
            if (!$codPersona) {
                throw new \Exception('No se pudo obtener el código de persona');
            }
            Log::info('Persona creada con Cod_Persona: ' . $codPersona);

            $responseCliente = Http::post($this->apiUrl . '/cliente', [
                'Cod_Persona'      => $codPersona,
                'Tipo_Cliente'     => 'Ocacional', 
                'Nota_Preferencia' => '',
                'Fecha_Registro'   => date('Y-m-d')
            ]);

            if (!$responseCliente->successful()) {
                throw new \Exception('Error al crear cliente: ' . $responseCliente->body());
            }

            $responseClienteGet = Http::get($this->apiUrl . '/cliente');
            if (!$responseClienteGet->successful()) {
                throw new \Exception('Cliente creado pero no se pudo recuperar');
            }
            $clientes = $responseClienteGet->json();
            $clienteEncontrado = collect($clientes)->where('Cod_Persona', $codPersona)->first();
            if (!$clienteEncontrado) {
                throw new \Exception('No se pudo encontrar el cliente creado');
            }
            $codCliente = $clienteEncontrado['Cod_Cliente'];
            Log::info('Cliente encontrado con Cod_Cliente: ' . $codCliente);

            if ($request->correo) {
                Http::post($this->apiUrl . '/correo', ['Cod_Persona' => $codPersona, 'Correo' => $request->correo, 'Tipo_Correo' => 'Personal']);
            }
            
            // ⬇️ MODIFICACIÓN CLAVE AQUÍ ⬇️
            if ($request->telefono) {
                Http::post($this->apiUrl . '/telefonos', [ // 1. Corregido a /telefonos (plural)
                    'Cod_Persona' => $codPersona, 
                    'Numero' => $request->telefono, 
                    'Cod_Pais' => '504', 
                    'Tipo' => 'Movil', // 2. Corregido a 'Movil' (para coincidir con el ENUM)
                    'Descripcion' => 'Principal'
                ]);
            }
            // ⬆️ FIN DE LA MODIFICACIÓN ⬆️

            if ($request->direccion) {
                Http::post($this->apiUrl . '/direccion', ['Cod_Persona' => $codPersona, 'Direccion' => $request->direccion, 'Descripcion' => 'Principal']);
            }

            return response()->json([
                'success' => true, 'message' => 'Cliente creado exitosamente', 'cod_cliente' => $codCliente, 'cod_persona' => $codPersona
            ]);
        } catch (\Exception $e) {
            Log::error('Error en crearClienteCompleto: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()], 500);
        }
    }

    // GET - Obtener citas
    public function getCitas(Request $request)
    {
        $this->authorize('viewAny', Cita::class);
        try {
            $url = $request->query('cod') ? $this->apiUrl . '/cita?cod=' . $request->query('cod') : $this->apiUrl . '/cita';
            $response = Http::get($url);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // POST - Crear cita
    public function storeCita(Request $request)
    {
        $this->authorize('create', Cita::class);
        try {
            $response = Http::post($this->apiUrl . '/cita', $request->all());

            if ($response->successful()) {
                return response()->json(['message' => $response->body()], $response->status());
            } else {
                return response()->json(['error' => $response->body()], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // PUT - Actualizar cita
    public function updateCita(Request $request, $id)
    {
        $this->authorize('update', Cita::class); 

        try {
            $apiData = [
                'Cod_Cita'       => $id,
                'Cod_Cliente'    => $request->codCliente,
                'Cod_Empleado'   => $request->codEmpleado,
                'Fecha_Cita'     => $request->fechaCita,
                'Hora_Inicio'    => $request->horaInicio,
                'Hora_Fin'       => $request->horaFin,
                'Estado_Cita'    => $request->estadoCita,
                'Notas_Internas' => $request->notasInternas
            ];
            
            $response = Http::put($this->apiUrl . '/cita', $apiData);

            if ($response->successful()) {
                return response()->json(['message' => $response->body()], $response->status());
            } else {
                return response()->json(['error' => $response->body()], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // DELETE - Eliminar cita
    public function deleteCita(Request $request, $id)
    {
        $this->authorize('delete', Cita::class);

        try {
            $response = Http::delete($this->apiUrl . '/cita?cod=' . $id);

            if ($response->successful()) {
                return response()->json($response->json(), $response->status());
            } else {
                return response()->json(['error' => $response->body()], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // PUT - Actualizar SOLO el estado
    public function updateStatus(Request $request, $id)
    {
        $this->authorize('update', Cita::class);

        try {
            $responseGet = Http::get($this->apiUrl . '/cita?cod=' . $id);
            if (!$responseGet->successful() || empty($responseGet->json())) {
                return response()->json(['error' => 'Cita no encontrada para actualizar estado'], 404);
            }
            $citaActual = $responseGet->json()[0];

            $nuevoEstado = $request->input('estado');
            if (!in_array($nuevoEstado, ['Programada', 'Confirmada', 'Realizada', 'Cancelada'])) {
                 return response()->json(['error' => 'Estado no válido'], 400);
            }
            
            $fechaFormateada = date('Y-m-d', strtotime($citaActual['Fecha_Cita']));
            $horaInicioFormateada = $citaActual['Hora_Inicio'] ? date('H:i:s', strtotime($citaActual['Hora_Inicio'])) : null;
            $horaFinFormateada = $citaActual['Hora_Fin'] ? date('H:i:s', strtotime($citaActual['Hora_Fin'])) : null;

            $apiData = [
                'Cod_Cita'       => $citaActual['Cod_Cita'],
                'Cod_Cliente'    => $citaActual['Cod_Cliente'],
                'Cod_Empleado'   => $citaActual['Cod_Empleado'],
                'Fecha_Cita'     => $fechaFormateada,
                'Hora_Inicio'    => $horaInicioFormateada,
                'Hora_Fin'       => $horaFinFormateada,
                'Estado_Cita'    => $nuevoEstado,
                'Notas_Internas' => $citaActual['Notas_Internas']
            ];

            $responsePut = Http::put($this->apiUrl . '/cita', $apiData);

            if ($responsePut->successful()) {
                return response()->json(['message' => $responsePut->body()], $responsePut->status());
            } else {
                Log::error('Error de API al actualizar estado', [
                    'sending' => $apiData,
                    'response' => $responsePut->body()
                ]);
                return response()->json(['error' => $responsePut->body()], $responsePut->status());
            }

        } catch (\Exception $e) {
            Log::error('Error en updateStatus: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
