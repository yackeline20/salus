<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class ServicioPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver todos los servicios
     */
    public function viewAny(User $user)
    {
        return $this->checkPermission($user, 'select', 'Servicios');
    }

    /**
     * Determina si el usuario puede crear servicios
     */
    public function create(User $user)
    {
        return $this->checkPermission($user, 'insert', 'Servicios');
    }

    /**
     * Determina si el usuario puede actualizar servicios
     */
    public function update(User $user)
    {
        return $this->checkPermission($user, 'update', 'Servicios');
    }

    /**
     * Determina si el usuario puede eliminar servicios
     */
    public function delete(User $user)
    {
        return $this->checkPermission($user, 'delete', 'Servicios');
    }

    /**
     * Método helper para verificar permisos
     */
    private function checkPermission(User $user, string $action, string $objectName)
    {
        // ✅ 1. Permitir todo si estamos en entorno local o de desarrollo
        if (app()->environment('local')) {
            return true;
        }

        // 🔒 2. Verificar si el usuario tiene rol asignado
        $rol = $user->rol ?? null;
        if (!$rol) {
            return false;
        }

        // 🔍 3. Buscar el objeto
        $objeto = DB::table('objeto')
            ->where('Nombre_Objeto', $objectName)
            ->first();

        if (!$objeto) {
            // Si no existe, lo creamos automáticamente
            DB::table('objeto')->insert(['Nombre_Objeto' => $objectName]);
            $objeto = DB::table('objeto')
                ->where('Nombre_Objeto', $objectName)
                ->first();
        }

        // 🔍 4. Buscar el acceso correspondiente
        $acceso = DB::table('acceso')
            ->where('Cod_Rol', $rol->Cod_Rol)
            ->where('Cod_Objeto', $objeto->Cod_Objeto)
            ->first();

        if (!$acceso) {
            return false;
        }

        // ✅ 5. Validar permiso específico
        switch ($action) {
            case 'select':
                return $acceso->Permiso_Seleccionar == 1;
            case 'insert':
                return $acceso->Permiso_Insertar == 1;
            case 'update':
                return $acceso->Permiso_Actualizar == 1;
            case 'delete':
                return $acceso->Permiso_Eliminar == 1;
            default:
                return false;
        }
    }
}
