<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Verifica si el usuario autenticado tiene permiso de 'Seleccionar' (acceso)
     * para un objeto (módulo) específico.
     *
     * @param string $objectName Nombre_Objeto como está en la tabla 'objetos'.
     * @return bool
     */
    public static function hasAccess(string $objectName): bool
    {
        // 1. Verificar si hay un usuario logueado
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $codRol = $user->Cod_Rol ?? null;

        // 2. El Administrador (Cod_Rol = 1) siempre tiene acceso total
        if ($codRol == 1) {
            return true;
        }

        // 3. Buscar el acceso en la tabla 'accesos'
        // NOTA: Asumo que la tabla de usuarios tiene una columna Cod_Rol.
        if ($codRol) {
            $hasPermission = DB::table('accesos as a')
                ->join('objetos as o', 'a.Cod_Objeto', '=', 'o.Cod_Objeto')
                ->where('a.Cod_Rol', $codRol)
                ->where('o.Nombre_Objeto', $objectName)
                ->where('a.Permiso_Seleccionar', 1)
                ->exists();

            return $hasPermission;
        }

        return false;
    }
}
