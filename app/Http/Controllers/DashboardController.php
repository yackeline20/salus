<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener el usuario autenticado (será del modelo Correo)
        $usuario = Auth::user();

        // Verificar que el usuario esté autenticado y tenga una persona asociada
        if (!$usuario || !$usuario->persona) {
            return redirect()->route('login')->with('error', 'Sesión inválida');
        }

        // Obtener datos de la persona
        $persona = $usuario->persona;

        // Pasar datos a la vista
        return view('dashboard', [
            'usuario' => $usuario,
            'persona' => $persona,
            'nombre_completo' => $persona->getNombreCompleto(),
            'correo' => $usuario->Correo
        ]);
    }
}
