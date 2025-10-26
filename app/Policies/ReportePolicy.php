<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Reporte;

class ReportePolicy
{
    protected $objetoName = 'Reportes';

    private $allowedRoles = [
        'Jefe de Proyectos y Desarrollo de Servicios',
        'Coordinador Operativo',
        'Contador',
        'Especialista en Marketing y Comunicaciones',
    ];

    public function before(Usuario $user, string $ability): ?bool
    {
        if ($user->Nombre_Rol === 'Administrador') {
            return true;
        }

        if (in_array($user->Nombre_Rol, $this->allowedRoles)) {
            return true;
        }

        return null;
    }

    public function viewAny(Usuario $user): bool
    {
        return $user->hasPermission('select', $this->objetoName);
    }

    // Los reportes son solo de lectura.
    public function create(Usuario $user): bool
    {
        return false;
    }

    public function update(Usuario $user, Reporte $reporte): bool
    {
        return false;
    }

    public function delete(Usuario $user, Reporte $reporte): bool
    {
        return false;
    }
}
