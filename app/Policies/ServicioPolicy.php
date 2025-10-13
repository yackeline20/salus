<?php
// En: app/Policies/ServicioPolicy.php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Tratamiento;

class ServicioPolicy
{
    protected $objetoName = 'Tratamiento'; // Ajusta a 'Tratamientos' si ese es el nombre en tu tabla 'objetos'

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

    public function update(Usuario $user, Tratamiento $tratamiento): bool
    {
        return $user->hasPermission('update', $this->objetoName);
    }

    public function delete(Usuario $user, Tratamiento $tratamiento): bool
    {
        return $user->hasPermission('delete', $this->objetoName);
    }
}
