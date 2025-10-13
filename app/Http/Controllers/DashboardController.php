<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Persona;
use App\Models\Usuario;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener el usuario autenticado (puede ser Persona o Usuario)
        $user = Auth::user();

        // Verificar que el usuario esté autenticado
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesión inválida');
        }

        // Detectar el tipo de usuario y preparar los datos
        if ($user instanceof Usuario) {
            // Es un Usuario (tabla usuarios)
            return $this->dashboardForUsuario($user);
        } elseif ($user instanceof Persona) {
            // Es una Persona (tabla persona)
            return $this->dashboardForPersona($user);
        }

        // Fallback (no debería llegar aquí)
        return redirect()->route('login')->with('error', 'Tipo de usuario no reconocido');
    }

    /**
     * Dashboard para usuarios de la tabla 'usuarios'
     */
    private function dashboardForUsuario(Usuario $usuario)
    {
        return view('dashboard', [
            'persona' => $usuario, // Mantenemos 'persona' para compatibilidad con la vista
            'nombre_completo' => $usuario->Nombre_Usuario,
            'correo' => 'Usuario del sistema',
            'es_usuario' => true // Flag para que la vista sepa que es un Usuario
        ]);
    }

    /**
     * Dashboard para usuarios de la tabla 'persona'
     */
    private function dashboardForPersona(Persona $persona)
    {
        // Obtener el correo principal de la persona
        $correo = $persona->getCorreoPrincipal();

        return view('dashboard', [
            'persona' => $persona,
            'nombre_completo' => $persona->getNombreCompleto(),
            'correo' => $correo ? $correo->Correo : 'Sin correo',
            'es_usuario' => false // Flag para que la vista sepa que es una Persona
        ]);
    }
}
