<?php
// En: app/Http/Controllers/FacturaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource. (Leer/Seleccionar)
     */
    public function index()
    {
        // 🛡️ Autorizar la visualización del listado (viewAny)
        // Llama a FacturaPolicy::viewAny()
        $this->authorize('viewAny', Factura::class);

        // ... Lógica para obtener y listar facturas ...
    }

    /**
     * Show the form for creating a new resource. (Crear/Insertar)
     */
    public function create()
    {
        // 🛡️ Autorizar la visualización del formulario de creación (create)
        // Llama a FacturaPolicy::create()
        $this->authorize('create', Factura::class);

        // ... Lógica para mostrar el formulario ...
    }

    /**
     * Store a newly created resource in storage. (Crear/Insertar)
     */
    public function store(Request $request)
    {
        // 🛡️ Autorizar la acción de guardar/insertar (create)
        // Llama a FacturaPolicy::create()
        $this->authorize('create', Factura::class);

        // ... Lógica para validar y guardar la nueva factura ...
    }

    /**
     * Display the specified resource. (Leer/Seleccionar detalle)
     */
    public function show(string $id)
    {
        // 🛡️ Autorizar la visualización de un detalle (view)
        // Llama a FacturaPolicy::view(). Usamos new Factura si $id no es un modelo inyectado.
        $this->authorize('view', new Factura);

        // ... Lógica para buscar la factura por $id y mostrarla ...
    }

    /**
     * Show the form for editing the specified resource. (Actualizar)
     */
    public function edit(string $id)
    {
        // 🛡️ Autorizar la visualización del formulario de edición (update)
        // Llama a FacturaPolicy::update()
        $this->authorize('update', new Factura);

        // ... Lógica para buscar la factura por $id y mostrar el formulario de edición ...
    }

    /**
     * Update the specified resource in storage. (Actualizar)
     */
    public function update(Request $request, string $id)
    {
        // 🛡️ Autorizar la acción de actualizar (update)
        // Llama a FacturaPolicy::update()
        $this->authorize('update', new Factura);

        // ... Lógica para validar y actualizar la factura por $id ...
    }

    /**
     * Remove the specified resource from storage. (Eliminar)
     */
    public function destroy(string $id)
    {
        // 🛡️ Autorizar la acción de eliminar (delete)
        // Llama a FacturaPolicy::delete()
        $this->authorize('delete', new Factura);

        // ... Lógica para buscar y eliminar la factura por $id ...
    }
}
