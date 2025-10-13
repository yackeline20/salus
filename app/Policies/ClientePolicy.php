<?php
// En: app/Policies/ClientePolicy.php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Cliente;

class ClientePolicy
{
    protected $objetoName = 'Clientes';

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

    public function update(Usuario $user, Cliente $cliente): bool
    {
        return $user->hasPermission('update', $this->objetoName);
    }

    public function delete(Usuario $user, Cliente $cliente): bool
    {
        return $user->hasPermission('delete', $this->objetoName);
    }
}
