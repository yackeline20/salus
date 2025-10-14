<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Persona;
use App\Models\Usuario;

class DashboardController extends Controller
{
    /**
     * Muestra la página principal (Dashboard).
     * El acceso ya está garantizado por el middleware 'auth' en web.php.
     */
    public function index()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar que el usuario esté autenticado (aunque el middleware 'auth' ya lo hace)
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesión inválida o expirada.');
        }

        // Detectar el tipo de usuario y preparar los datos
        if ($user instanceof Usuario) {
            // Es un Usuario (empleado/administrador)
            return $this->dashboardForUsuario($user);
        } elseif ($user instanceof Persona) {
            // Es una Persona (cliente/externo)
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
        // CORRECCIÓN CLAVE: Se ELIMINÓ la verificación de Gate (Gate::authorize('access-dashboard');)
        // Ahora, cualquier usuario de la tabla 'usuarios' que inicie sesión
        // correctamente tiene acceso al Dashboard.

        // La vista 'dashboard' recibirá la información del usuario
        return view('dashboard', [
            'persona' => $usuario->persona, // Asumiendo que existe la relación 'persona'
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
