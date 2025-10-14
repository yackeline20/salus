<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Maneja una petición entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Lista de nombres de roles permitidos (ej: 'admin', 'empleado')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificar si hay un usuario autenticado
        if (!Auth::check()) {
            return redirect('/login')->withErrors('Debes iniciar sesión para acceder a este módulo.');
        }

        $user = Auth::user();

        // 2. Mapear Cod_Rol (IDs de la BD) a Nombres de Rol (para el middleware)
        // **ESTOS SON LOS ROLES QUE DEFINEN LOS PERMISOS EN LAS RUTAS (web.php)**
        $roleMap = [
            1 => 'admin',
            2 => 'empleado',
            3 => 'contador',
            // Agrega más roles si tienes IDs diferentes
        ];

        // Obtener el nombre del rol del usuario a partir de su Cod_Rol
        $userRoleName = $roleMap[$user->Cod_Rol] ?? null;

        // 3. Verificar si el rol del usuario está en la lista de roles permitidos
        if ($userRoleName && in_array($userRoleName, $roles)) {
            // Rol autorizado, permite el acceso
            return $next($request);
        }

        // Acceso denegado, redirige al dashboard con un mensaje de error
        return redirect('/dashboard')->withErrors('No tienes permiso para acceder a este módulo.');
    }
}
