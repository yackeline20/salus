<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; //
use App\Models\Persona;
use App\Models\Usuario;

class DashboardController extends Controller
{
    /**
     * Muestra la página principal (Dashboard) si el usuario tiene permiso.
     */
    public function index()
    {
        // Obtener el usuario autenticado (puede ser Persona o Usuario)
        $user = Auth::user();

        // Verificar que el usuario esté autenticado
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesión inválida o expirada.');
        }

        // Detectar el tipo de usuario y preparar los datos
        if ($user instanceof Usuario) {
            // Es un Usuario (tabla usuarios) - Procede a verificar el permiso RBAC
            return $this->dashboardForUsuario($user);
        } elseif ($user instanceof Persona) {
            // Es una Persona (tabla persona) - No requiere verificación RBAC (son clientes o externos)
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
        // ==========================================================
        // 🛡️ APLICACIÓN DE SEGURIDAD RBAC (Gate)
        // Llama al Gate 'access-dashboard' definido en AuthServiceProvider.php
        // Esto verifica si el rol del usuario tiene 'select' en el objeto 'Dashboard' o 'Pagina Principal'.
        // Si no tiene permiso, Laravel detiene la ejecución con un error 403.
        // ==========================================================
        Gate::authorize('access-dashboard');

        // NOTA: Asegúrate de que $usuario tiene la relación con Persona cargada si la vista la necesita.

        return view('dashboard', [
            // Mantenemos 'persona' para compatibilidad con la vista
            'persona' => $usuario,
            'nombre_completo' => $usuario->Nombre_Usuario,
            'correo' => 'Usuario del sistema',
            'es_usuario' => true
        ]);
    }

    /**
     * Dashboard para usuarios de la tabla 'persona'
     */
    private function dashboardForPersona(Persona $persona)
    {
        // Obtener el correo principal de la persona
        // Asumiendo que getCorreoPrincipal() está definido en tu modelo Persona
        $correo = $persona->getCorreoPrincipal();

        return view('dashboard', [
            'persona' => $persona,
            'nombre_completo' => $persona->getNombreCompleto(),
            'correo' => $correo ? $correo->Correo : 'Sin correo',
            'es_usuario' => false
        ]);
    }
}
