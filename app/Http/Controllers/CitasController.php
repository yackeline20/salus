<?php
// En: app/Http/Controllers/CitasController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Cita;

class CitasController extends Controller
{
    private $apiUrl = 'http://localhost:3000';

    /**
     * Muestra la vista principal del módulo de Citas.
     * Requiere Permiso_Seleccionar.
     */
    public function index()
    {
        // 1. 🛡️ Autorizar la visualización del listado (ViewAny)
        $this->authorize('viewAny', Cita::class);

        return view('citas');
    }

    // 🆕 NUEVO: Buscar cliente por código con todos sus datos
    public function buscarCliente(Request $request)
    {
        $this->authorize('viewAny', Cita::class);

        try {
            $codCliente = $request->query('cod');
            
            if (!$codCliente) {
                return response()->json(['error' => 'Código de cliente requerido'], 400);
            }

            // 1. Obtener datos del cliente
            $responseCliente = Http::get($this->apiUrl . '/cliente?cod=' . $codCliente);
            
            if (!$responseCliente->successful() || empty($responseCliente->json())) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }

            $cliente = $responseCliente->json()[0];
            $codPersona = $cliente['Cod_Persona'];

            // 2. Obtener datos de la persona
            $responsePersona = Http::get($this->apiUrl . '/persona?cod=' . $codPersona);
            $persona = $responsePersona->json()[0] ?? null;

            // 3. Obtener correos
            $responseCorreo = Http::get($this->apiUrl . '/correo');
            $correos = collect($responseCorreo->json())->where('Cod_Persona', $codPersona)->values()->all();

            // 4. Obtener teléfonos
            $responseTelefono = Http::get($this->apiUrl . '/telefono');
            $telefonos = collect($responseTelefono->json())->where('Cod_Persona', $codPersona)->values()->all();

            // 5. Obtener direcciones
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

    // 🆕 NUEVO: Crear cliente completo con persona y datos de contacto - CORREGIDO
    public function crearClienteCompleto(Request $request)
    {
        $this->authorize('create', Cita::class);

        try {
            // 1. Crear persona
            $responsePersona = Http::post($this->apiUrl . '/persona', [
                'Nombre' => $request->nombre,
                'Apellido' => $request->apellido,
                'DNI' => $request->dni,
                'Fecha_Nacimiento' => $request->fechaNacimiento,
                'Genero' => $request->genero
            ]);

            if (!$responsePersona->successful()) {
                \Log::error('Error al crear persona', [
                    'status' => $responsePersona->status(),
                    'body' => $responsePersona->body()
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Error al crear persona: ' . $responsePersona->body()
                ], 500);
            }

            $personaData = $responsePersona->json();
            
            // Extraer Cod_Persona de diferentes posibles estructuras
            $codPersona = null;
            if (isset($personaData['Cod_Persona'])) {
                $codPersona = $personaData['Cod_Persona'];
            } elseif (isset($personaData['cod_persona'])) {
                $codPersona = $personaData['cod_persona'];
            } elseif (is_array($personaData) && isset($personaData[0]['Cod_Persona'])) {
                $codPersona = $personaData[0]['Cod_Persona'];
            }

            if (!$codPersona) {
                \Log::error('Respuesta de persona sin Cod_Persona', ['response' => $personaData]);
                return response()->json([
                    'success' => false,
                    'error' => 'No se pudo obtener el código de persona'
                ], 500);
            }

            \Log::info('Persona creada con Cod_Persona: ' . $codPersona);

            // 2. Crear cliente
            $responseCliente = Http::post($this->apiUrl . '/cliente', [
                'Cod_Persona' => $codPersona,
                'Tipo_Cliente' => 'Regular',
                'Nota_Preferencia' => '',
                'Fecha_Registro' => date('Y-m-d')
            ]);

            if (!$responseCliente->successful()) {
                \Log::error('Error al crear cliente', [
                    'status' => $responseCliente->status(),
                    'body' => $responseCliente->body()
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Error al crear cliente: ' . $responseCliente->body()
                ], 500);
            }

            // Esperar un momento para asegurar que se guardó
            sleep(1);

            // Obtener todos los clientes y buscar el que acabamos de crear
            $responseClienteGet = Http::get($this->apiUrl . '/cliente');
            
            if (!$responseClienteGet->successful()) {
                \Log::error('Error al obtener clientes', [
                    'status' => $responseClienteGet->status(),
                    'body' => $responseClienteGet->body()
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Cliente creado pero no se pudo recuperar'
                ], 500);
            }

            $clientes = $responseClienteGet->json();
            
            // Buscar el cliente con el Cod_Persona que acabamos de crear
            $clienteEncontrado = null;
            foreach ($clientes as $cli) {
                if (isset($cli['Cod_Persona']) && $cli['Cod_Persona'] == $codPersona) {
                    $clienteEncontrado = $cli;
                    break;
                }
            }

            if (!$clienteEncontrado) {
                \Log::error('No se encontró cliente con Cod_Persona: ' . $codPersona, [
                    'clientes' => $clientes
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'No se pudo encontrar el cliente creado'
                ], 500);
            }

            $codCliente = $clienteEncontrado['Cod_Cliente'];
            \Log::info('Cliente encontrado con Cod_Cliente: ' . $codCliente);

            // 3. Crear correo si se proporcionó
            if ($request->correo) {
                $responseCorreo = Http::post($this->apiUrl . '/correo', [
                    'Cod_Persona' => $codPersona,
                    'Correo' => $request->correo,
                    'Tipo_Correo' => 'Personal'
                ]);
                
                if (!$responseCorreo->successful()) {
                    \Log::warning('No se pudo crear correo', ['error' => $responseCorreo->body()]);
                }
            }

            // 4. Crear teléfono si se proporcionó
            if ($request->telefono) {
                $responseTelefono = Http::post($this->apiUrl . '/telefono', [
                    'Cod_Persona' => $codPersona,
                    'Numero' => $request->telefono,
                    'Cod_Pais' => '504',
                    'Tipo' => 'Celular',
                    'Descripcion' => 'Principal'
                ]);
                
                if (!$responseTelefono->successful()) {
                    \Log::warning('No se pudo crear teléfono', ['error' => $responseTelefono->body()]);
                }
            }

            // 5. Crear dirección si se proporcionó
            if ($request->direccion) {
                $responseDireccion = Http::post($this->apiUrl . '/direccion', [
                    'Cod_Persona' => $codPersona,
                    'Direccion' => $request->direccion,
                    'Descripcion' => 'Principal'
                ]);
                
                if (!$responseDireccion->successful()) {
                    \Log::warning('No se pudo crear dirección', ['error' => $responseDireccion->body()]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'cod_cliente' => $codCliente,
                'cod_persona' => $codPersona
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en crearClienteCompleto: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    // GET - Obtener citas (Acción de Lectura/Seleccionar)
    public function getCitas(Request $request)
    {
        $this->authorize('viewAny', Cita::class);

        try {
            $cod = $request->query('cod');
            $url = $this->apiUrl . '/cita';

            if ($cod) {
                $url .= '?cod=' . $cod;
            }

            $response = Http::get($url);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // POST - Crear cita (Acción de Insertar)
    public function storeCita(Request $request)
    {
        $this->authorize('create', Cita::class);

        try {
            $response = Http::post($this->apiUrl . '/cita', [
                'codCliente' => $request->codCliente,
                'codEmpleado' => $request->codEmpleado,
                'fechaCita' => $request->fechaCita,
                'horaInicio' => $request->horaInicio,
                'horaFin' => $request->horaFin,
                'estadoCita' => $request->estadoCita,
                'notasInternas' => $request->notasInternas
            ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // PUT - Actualizar cita (Acción de Actualizar)
    public function updateCita(Request $request)
    {
        $this->authorize('update', new Cita);

        try {
            $response = Http::put($this->apiUrl . '/cita', [
                'Cod_Cita' => $request->Cod_Cita,
                'Cod_Cliente' => $request->Cod_Cliente,
                'Cod_Empleado' => $request->Cod_Empleado,
                'Fecha_Cita' => $request->Fecha_Cita,
                'Hora_Inicio' => $request->Hora_Inicio,
                'Hora_Fin' => $request->Hora_Fin,
                'Estado_Cita' => $request->Estado_Cita,
                'Notas_Internas' => $request->Notas_Internas
            ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // DELETE - Eliminar cita (Acción de Eliminar)
    public function deleteCita(Request $request)
    {
        $this->authorize('delete', new Cita);

        try {
            $cod = $request->query('cod');
            $response = Http::delete($this->apiUrl . '/cita?cod=' . $cod);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}