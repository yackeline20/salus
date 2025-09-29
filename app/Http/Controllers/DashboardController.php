<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener el usuario autenticado (ahora será del modelo Persona)
        $persona = Auth::user();

        // Verificar que el usuario esté autenticado
        if (!$persona) {
            return redirect()->route('login')->with('error', 'Sesión inválida');
        }

        // Obtener el correo principal de la persona
        $correo = $persona->getCorreoPrincipal();

        // Pasar datos a la vista
        return view('dashboard', [
            'persona' => $persona,
            'nombre_completo' => $persona->getNombreCompleto(),
            'correo' => $correo ? $correo->Correo : 'Sin correo'
        ]);
    }
}
