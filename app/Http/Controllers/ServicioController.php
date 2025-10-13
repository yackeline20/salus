<?php
// En: app/Http/Controllers/ServicioController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tratamiento; // 👈 NECESARIO: Importar el modelo Tratamiento para la Policy

class ServicioController extends Controller
{
    /**
     * Muestra el listado de tratamientos/servicios. (Leer/Seleccionar)
     */
    public function index()
    {
        // 🛡️ Autorizar la visualización del listado (viewAny)
        // Llama a ServicioPolicy::viewAny() para verificar el permiso 'select' en el objeto 'Servicios'.
        $this->authorize('viewAny', Tratamiento::class);

        $tratamientos = Tratamiento::all();
        return view('servicios.index', compact('tratamientos'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso. (Crear/Insertar)
     */
    public function create()
    {
        // 🛡️ Autorizar la visualización del formulario de creación (create)
        // Llama a ServicioPolicy::create()
        $this->authorize('create', Tratamiento::class);

        return view('servicios.create');
    }

    /**
     * Almacena un recurso recién creado en la base de datos. (Crear/Insertar)
     */
    public function store(Request $request)
    {
        // 🛡️ Autorizar la acción de guardar/insertar (create)
        // Llama a ServicioPolicy::create()
        $this->authorize('create', Tratamiento::class);

        // ... Lógica de validación y creación del Tratamiento ...
        // $tratamiento = Tratamiento::create($request->validate([...]));

        return redirect()->route('servicios.index')->with('success', 'Servicio creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar el recurso especificado. (Actualizar)
     */
    public function edit(Tratamiento $tratamiento)
    {
        // 🛡️ Autorizar la visualización del formulario de edición (update)
        // Llama a ServicioPolicy::update($user, $tratamiento)
        $this->authorize('update', $tratamiento);

        return view('servicios.edit', compact('tratamiento'));
    }

    /**
     * Actualiza el recurso especificado en la base de datos. (Actualizar)
     */
    public function update(Request $request, Tratamiento $tratamiento)
    {
        // 🛡️ Autorizar la acción de actualizar (update)
        // Llama a ServicioPolicy::update($user, $tratamiento)
        $this->authorize('update', $tratamiento);

        // ... Lógica de validación y actualización del Tratamiento ...
        // $tratamiento->update($request->validate([...]));

        return redirect()->route('servicios.index')->with('success', 'Servicio actualizado exitosamente.');
    }

    /**
     * Elimina el recurso especificado de la base de datos. (Eliminar)
     */
    public function destroy(Tratamiento $tratamiento)
    {
        // 🛡️ Autorizar la acción de eliminar (delete)
        // Llama a ServicioPolicy::delete($user, $tratamiento)
        $this->authorize('delete', $tratamiento);

        // ... Lógica de eliminación del Tratamiento ...
        // $tratamiento->delete();

        return redirect()->route('servicios.index')->with('success', 'Servicio eliminado exitosamente.');
    }
}
