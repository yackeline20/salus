<?php
// En: app/Http/Controllers/CitasController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Cita;

class CitasController extends Controller
{
    private $apiUrl = 'http://localhost:3000';

    public function index()
    {
        // 1. 🛡️ Autorizar la visualización del listado (ViewAny)
        // Llama a CitaPolicy::viewAny()
        $this->authorize('viewAny', Cita::class);

        return view('citas');
    }

    // GET - Obtener citas (Acción de Lectura/Seleccionar)
    public function getCitas(Request $request)
    {
        // 2. 🛡️ Autorizar la lectura/obtención de datos (ViewAny)
        // Llama a CitaPolicy::viewAny()
        // Nota: Se repite la autorización por si este método es llamado directamente vía API
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
        // 3. 🛡️ Autorizar la creación de una nueva Cita (Create)
        // Llama a CitaPolicy::create()
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
        // 4. 🛡️ Autorizar la actualización de una Cita (Update)
        // Llama a CitaPolicy::update()
        // Nota: Como no tenemos el objeto Cita completo, pasamos una nueva instancia.
        // La Policy solo verificará el permiso del Rol, no la propiedad del objeto.
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
        // 5. 🛡️ Autorizar la eliminación de una Cita (Delete)
        // Llama a CitaPolicy::delete()
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
