<?php
// En: app/Policies/ReportePolicy.php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Reporte;

class ReportePolicy
{
    protected $objetoName = 'Reportes'; // Ajusta el nombre si es diferente en tu tabla 'objetos'

    public function before(Usuario $user, string $ability): ?bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return null;
    }

    public function viewAny(Usuario $user): bool
    {
        // Solo necesitamos la capacidad de 'ver' o acceder al módulo de Reportes
        return $user->hasPermission('select', $this->objetoName);
    }

    // No se necesitan métodos create, update o delete para un módulo de Reportes.
}
