<?php
// En: app/Policies/FacturaPolicy.php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Factura;

class FacturaPolicy
{
    protected $objetoName = 'Facturas'; // Ajusta el nombre si es diferente en tu tabla 'objetos'

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

    public function update(Usuario $user, Factura $factura): bool
    {
        return $user->hasPermission('update', $this->objetoName);
    }

    public function delete(Usuario $user, Factura $factura): bool
    {
        return $user->hasPermission('delete', $this->objetoName);
    }
}
