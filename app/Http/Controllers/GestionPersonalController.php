<?php
// En: app/Http/Controllers/GestionPersonalController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado; // 👈 NECESARIO: Importar el modelo Empleado para la Policy

class GestionPersonalController extends Controller
{
    /**
     * Muestra la página principal de Gestión de Personal. (Leer/Seleccionar)
     */
    public function index()
    {
        // 🛡️ Autorizar la visualización del listado (viewAny)
        // Llama a PersonalPolicy::viewAny() para verificar el permiso 'select' en el objeto 'Personal'.
        $this->authorize('viewAny', Empleado::class);

        // Aquí podrías cargar la lista de empleados si fuera necesario
        // $empleados = Empleado::all();

        return view('profile.gestion-personal');
    }
}
