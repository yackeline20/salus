<?php
// En: app/Http/Controllers/ClienteController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Http; // <-- IMPORTADO PARA HACER PETICIONES HTTP
use Illuminate\Support\Facades\Log;   // <-- IMPORTADO PARA REGISTRAR ERRORES

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource. (Ver listado)
     */
    public function index()
    {
        // 🛡️ Autorizar la visualización del listado (viewAny)
        $this->authorize('viewAny', Cliente::class);

        // 1. Definir la URL de la API (Tomada de su archivo index.js)
        $apiUrl = 'http://208.73.203.238:3000/cliente';
        $clientes = []; // Inicializar variable

        try {
            // 2. Realizar la petición GET a la API de Node.js
            $response = Http::timeout(10)->get($apiUrl); // Agregamos un timeout de 10 segundos

            // 3. Verificar si la petición fue exitosa (código 200)
            if ($response->successful()) {
                // El endpoint /cliente en su API devuelve un array JSON.
                // Lo almacenamos en $clientes para pasarlo a la vista.
                $clientes = $response->json();
            } else {
                // Manejar errores HTTP (4xx o 5xx) que devuelve la API
                Log::error("Error al obtener clientes de la API: " . $response->status() . " | " . $response->body());

                // Retornar a la página anterior con un mensaje de error
                return redirect()->back()->with('error', 'Error al cargar clientes. La API devolvió el código de estado: ' . $response->status());
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Error de conexión (API caída, IP/Puerto incorrecto, Firewall)
            Log::error("Error de conexión a la API de clientes: " . $e->getMessage());

            // Retornar con un mensaje de error específico
            return redirect()->back()->with('error', 'Error al cargar clientes, verifique la conexión a las API (Conexión rechazada o timeout).');
        } catch (\Exception $e) {
             // Cualquier otro error inesperado
             Log::error("Error inesperado en ClienteController: " . $e->getMessage());
             return redirect()->back()->with('error', 'Ocurrió un error inesperado al cargar los clientes.');
        }

        // 4. Pasar los datos de la API a la vista
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Store a newly created resource in storage. (Crear/Insertar)
     */
    public function store(Request $request)
    {
        // 🛡️ Autorizar la acción de guardar/insertar (create)
        $this->authorize('create', Cliente::class);

        // NOTA: Para este método (y PUT/DELETE), la lógica también debe cambiar para usar Http::post, Http::put, etc.

        // ... Lógica para validar y guardar el nuevo cliente ...
    }

    /**
     * Display the specified resource. (Ver detalle)
     */
    public function show(Cliente $cliente)
    {
        // 🛡️ Autorizar la visualización de un detalle (view)
        $this->authorize('view', $cliente);

        return view('clientes.show', compact('cliente'));
    }

    /**
     * Update the specified resource in storage. (Actualizar)
     */
    public function update(Request $request, Cliente $cliente)
    {
        // 🛡️ Autorizar la acción de actualizar (update)
        $this->authorize('update', $cliente);

        // ... Lógica para validar y actualizar el cliente ...
    }

    /**
     * Remove the specified resource from storage. (Eliminar)
     */
    public function destroy(Cliente $cliente)
    {
        // 🛡️ Autorizar la acción de eliminar (delete)
        $this->authorize('delete', $cliente);

        // ... Lógica para eliminar el cliente ...
    }
}
