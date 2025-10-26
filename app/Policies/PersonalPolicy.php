<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Empleado;

class PersonalPolicy
{
    // Nombre del objeto en la tabla 'objetos'
    protected $objetoName = 'Gestión de Personal';
    private $rolJefeProyectos = 'Jefe de Proyectos y Desarrollo de Servicios';

    public function before(Usuario $user, string $ability): ?bool
    {
        if ($user->Nombre_Rol === 'Administrador') {
            return true;
        }
        return null;
    }

    /**
     * Define si se puede ver la lista de empleados.
     * Permitido para Jefe de Proyectos (Supervisión) o si tiene permiso 'select' en la BD.
     */
    public function viewAny(Usuario $user): bool
    {
        if ($user->Nombre_Rol === $this->rolJefeProyectos) {
            return true;
        }
        return $user->hasPermission('select', $this->objetoName);
    }

    /**
     * Define si se puede ver un empleado individual.
     */
    public function view(Usuario $user, Empleado $empleado): bool
    {
        return $this->viewAny($user);
    }

    // El Jefe de Proyectos NO tiene permiso de modificación.
    public function create(Usuario $user): bool
    {
        return $user->hasPermission('insert', $this->objetoName);
    }

    public function update(Usuario $user, Empleado $empleado): bool
    {
        return $user->hasPermission('update', $this->objetoName);
    }

    public function delete(Usuario $user, Empleado $empleado): bool
    {
        return $user->hasPermission('delete', $this->objetoName);
    }
}
