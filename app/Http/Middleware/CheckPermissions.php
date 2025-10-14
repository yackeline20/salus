<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $objectName)
    {
        // 1. Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        // 2. Obtener el Cod_Rol del usuario
        $user = Auth::user();
        // NOTA: Asegúrate de que tu modelo User (o Usuarios) tiene la columna 'Cod_Rol'
        $codRol = $user->Cod_Rol;

        // 3. Lógica de Permisos: El Administrador (Cod_Rol=1) tiene acceso total
        if ($codRol == 1) {
            return $next($request);
        }

        // 4. Buscar el Objeto (Módulo) en la base de datos por el nombre que pasamos en la ruta
        $objeto = DB::table('objetos')
                    ->where('Nombre_Objeto', $objectName)
                    ->where('Indicador_Objeto_Activo', 1)
                    ->first();

        // Si el objeto no existe o está inactivo
        if (!$objeto) {
            return redirect('/dashboard')->with('error', 'Módulo no encontrado o inactivo.');
        }

        $codObjeto = $objeto->Cod_Objeto;

        // 5. Verificar el Permiso_Seleccionar (Permiso de Lectura/Acceso)
        $access = DB::table('accesos')
                    ->where('Cod_Rol', $codRol)
                    ->where('Cod_Objeto', $codObjeto)
                    ->where('Permiso_Seleccionar', 1)
                    ->first();

        if ($access) {
            // Permiso concedido
            return $next($request);
        }

        // 6. Permiso denegado: redirigir a dashboard
        return redirect('/dashboard')->with('error', 'No tienes permisos para acceder a este módulo.');
    }
}
