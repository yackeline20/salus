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
     * Permitido para todos los empleados que necesiten ver los tratamientos (Administrador ya pasa por before).
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
     * ACCESO EXCLUSIVO: JEFE DE PROYECTOS.
     */
    public function create(Usuario $usuario): bool
    {
        // Solo el Jefe de Proyectos puede crear (el Administrador ya está cubierto en before).
        return $usuario->Nombre_Rol === $this->rolJefeProyectos;
    }

    /**
     * Define si un usuario puede actualizar (editar) el modelo (Tratamiento).
     * ACCESO EXCLUSIVO: JEFE DE PROYECTOS.
     */
    public function update(Usuario $usuario, Tratamiento $tratamiento): bool
    {
        // Solo el Jefe de Proyectos puede editar.
        return $usuario->Nombre_Rol === $this->rolJefeProyectos;
    }

    /**
     * Define si un usuario puede eliminar el modelo (Tratamiento).
     * ACCESO EXCLUSIVO: JEFE DE PROYECTOS.
     */
    public function delete(Usuario $usuario, Tratamiento $tratamiento): bool
    {
        // Solo el Jefe de Proyectos puede eliminar.
        return $usuario->Nombre_Rol === $this->rolJefeProyectos;
    }

    /**
     * Define si un usuario puede restaurar el modelo (Tratamiento).
     */
    public function restore(Usuario $usuario, Tratamiento $tratamiento): bool
    {
        // Si no es Administrador (cubierto en before), se deniega.
        return false;
    }

    /**
     * Define si un usuario puede eliminar permanentemente el modelo (Tratamiento).
     */
    public function forceDelete(Usuario $usuario, Tratamiento $tratamiento): bool
    {
        // Si no es Administrador (cubierto en before), se deniega.
        return false;
    }
}
