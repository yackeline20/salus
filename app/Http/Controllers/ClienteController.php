<?php
// En: app/Http/Controllers/ClienteController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource. (Ver listado)
     */
    public function index()
    {
        // 🛡️ Autorizar la visualización del listado (viewAny)
        // Llama a ClientePolicy::viewAny() para verificar el permiso 'select' en el objeto 'Clientes'.
        $this->authorize('viewAny', Cliente::class);

        $clientes = Cliente::all(); // Obtener todos los clientes
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Store a newly created resource in storage. (Crear/Insertar)
     */
    public function store(Request $request)
    {
        // 🛡️ Autorizar la acción de guardar/insertar (create)
        // Llama a ClientePolicy::create()
        $this->authorize('create', Cliente::class);

        // ... Lógica para validar y guardar el nuevo cliente ...
    }

    /**
     * Display the specified resource. (Ver detalle)
     */
    public function show(Cliente $cliente)
    {
        // 🛡️ Autorizar la visualización de un detalle (view)
        // Llama a ClientePolicy::view()
        $this->authorize('view', $cliente);

        return view('clientes.show', compact('cliente'));
    }

    /**
     * Update the specified resource in storage. (Actualizar)
     */
    public function update(Request $request, Cliente $cliente)
    {
        // 🛡️ Autorizar la acción de actualizar (update)
        // Llama a ClientePolicy::update($user, $cliente)
        $this->authorize('update', $cliente);

        // ... Lógica para validar y actualizar el cliente ...
    }

    /**
     * Remove the specified resource from storage. (Eliminar)
     */
    public function destroy(Cliente $cliente)
    {
        // 🛡️ Autorizar la acción de eliminar (delete)
        // Llama a ClientePolicy::delete($user, $cliente)
        $this->authorize('delete', $cliente);

        // ... Lógica para eliminar el cliente ...
    }
}
