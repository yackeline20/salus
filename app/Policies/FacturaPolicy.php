<?php
// En: app/Policies/FacturaPolicy.php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Factura;

class FacturaPolicy
{
    // CRÍTICO: El nombre debe coincidir con la tabla 'objetos' (Cod_Objeto 8)
    protected $objetoName = 'Factura';
    private $rolContador = 'Contador';

    public function before(Usuario $user, string $ability): ?bool
    {
        // 1. Administrador siempre tiene acceso total.
        if ($user->Nombre_Rol === 'Administrador') {
            return true;
        }

        // 2. Contador tiene control total sobre Facturas (Permite la entrada funcional).
        if ($user->Nombre_Rol === $this->rolContador) {
            return true;
        }

        return null; // Continúa con la verificación de permisos específicos
    }

    /**
     * Determine whether the user can view any models (Lista de Facturas).
     */
    public function viewAny(Usuario $user): bool
    {
        // Pasa si tiene permiso 'select' en la BD
        return $user->hasPermission('select', $this->objetoName);
    }

    /**
     * Determine whether the user can view the model (Ver una Factura individual).
     */
    public function view(Usuario $user, Factura $factura): bool
    {
        // La política 'view' suele delegar en 'viewAny' si no hay restricciones adicionales.
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models (Crear Facturas).
     */
    public function create(Usuario $user): bool
    {
        // Pasa si tiene permiso 'insert' en la BD
        return $user->hasPermission('insert', $this->objetoName);
    }

    /**
     * Determine whether the user can update the model (Editar Facturas).
     */
    public function update(Usuario $user, Factura $factura): bool
    {
        // Pasa si tiene permiso 'update' en la BD
        return $user->hasPermission('update', $this->objetoName);
    }

    /**
     * Determine whether the user can delete the model (Eliminar Facturas).
     */
    public function delete(Usuario $user, Factura $factura): bool
    {
        // Pasa si tiene permiso 'delete' en la BD
        return $user->hasPermission('delete', $this->objetoName);
    }
}
