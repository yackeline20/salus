<?php
// En: app/Policies/TratamientoPolicy.php

namespace App\Policies;

use App\Models\Tratamiento;
use App\Models\Usuario; // Usamos su modelo Usuario
use Illuminate\Auth\Access\Response;

class TratamientoPolicy
{
    private $rolJefeProyectos = 'Jefe de Proyectos y Desarrollo de Servicios';

    /**
     * MÉTODO DE PRE-AUTORIZACIÓN: Permite al Administrador saltarse todas las verificaciones.
     * Esto simplifica la lógica en los métodos siguientes.
     */
    public function before(Usuario $usuario, string $ability): ?bool
    {
        if ($usuario->Nombre_Rol === 'Administrador') {
            return true; // Acceso total si es Administrador
        }
        return null; // Continuar con la lógica específica del método
    }

    /**
     * Define si un usuario puede ver cualquier modelo (lista de tratamientos).
     */
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    /**
     * Define si un usuario puede ver un tratamiento específico.
     */
    public function view(Usuario $usuario, Tratamiento $tratamiento): bool
    {
        return true;
    }

    /**
     * Define si un usuario puede crear nuevos modelos (Tratamientos).
     * Permitido para roles seleccionados.
     */
    public function create(Usuario $usuario): bool
    {
        $rolesPermitidos = [
            'Administrador',
            'Jefe de Proyectos y Desarrollo de Servicios',
            'Recepcionista',
            'Esteticista'
        ];

        return in_array($usuario->Nombre_Rol, $rolesPermitidos);
    }

    /**
     * Define si un usuario puede actualizar (editar) un tratamiento.
     * Permitido para roles seleccionados.
     */
    public function update(Usuario $usuario, Tratamiento $tratamiento): bool
    {
        $rolesPermitidos = [
            'Administrador',
            'Jefe de Proyectos y Desarrollo de Servicios',
            'Recepcionista',
            'Esteticista'
        ];

        return in_array($usuario->Nombre_Rol, $rolesPermitidos);
    }

    /**
     * Define si un usuario puede eliminar un tratamiento.
     * Permitido para roles seleccionados.
     */
    public function delete(Usuario $usuario, Tratamiento $tratamiento): bool
    {
        $rolesPermitidos = [
            'Administrador',
            'Jefe de Proyectos y Desarrollo de Servicios',
            'Recepcionista'
        ];

        return in_array($usuario->Nombre_Rol, $rolesPermitidos);
    }

    /**
     * Define si un usuario puede restaurar un tratamiento.
     */
    public function restore(Usuario $usuario, Tratamiento $tratamiento): bool
    {
        return false;
    }

    /**
     * Define si un usuario puede eliminar permanentemente un tratamiento.
     */
    public function forceDelete(Usuario $usuario, Tratamiento $tratamiento): bool
    {
        return false;
    }
}
