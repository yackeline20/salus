<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CitasController extends Controller
{
    private $apiUrl = 'http://localhost:3000';

    public function index()
    {
        return view('citas');
    }

    // GET - Obtener citas
    public function getCitas(Request $request)
    {
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

    // POST - Crear cita
    public function storeCita(Request $request)
    {
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

    // PUT - Actualizar cita
    public function updateCita(Request $request)
    {
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

    // DELETE - Eliminar cita
    public function deleteCita(Request $request)
    {
        try {
            $cod = $request->query('cod');
            $response = Http::delete($this->apiUrl . '/cita?cod=' . $cod);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}