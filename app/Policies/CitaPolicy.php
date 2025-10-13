<?php
// En: app/Policies/CitaPolicy.php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Cita;

class CitaPolicy
{
    // Nombre del Objeto tal como está en la tabla 'objetos'
    protected $objetoName = 'Citas';

    public function before(Usuario $user, string $ability): ?bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return null;
    }

    public function viewAny(Usuario $user): bool
    {
        return $user->hasPermission('select', $this->objetoName);
    }

    public function create(Usuario $user): bool
    {
        return $user->hasPermission('insert', $this->objetoName);
    }

    public function update(Usuario $user, Cita $cita): bool
    {
        return $user->hasPermission('update', $this->objetoName);
    }

    public function delete(Usuario $user, Cita $cita): bool
    {
        return $user->hasPermission('delete', $this->objetoName);
    }
}
