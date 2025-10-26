<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Cliente;

class ClientePolicy
{
    // Nombre del objeto en la tabla 'objetos' (Confirmado como 'Clientes' Cod_Objeto 3)
    protected $objetoName = 'Clientes';

    public function before(Usuario $user, string $ability): ?bool
    {
        // 1. Usa la propiedad Nombre_Rol para consistencia con otras políticas
        if ($user->Nombre_Rol === 'Administrador') {
            return true;
        }

        // 2. Si es Esteticista o Coordinador Operativo (roles de alta interacción),
        // pueden ver la data de clientes sin necesidad de permisos extra en la tabla 'accesos'.
        // Esto simplifica la gestión de datos de solo lectura para operativos.
        if ($user->Nombre_Rol === 'Esteticista' || $user->Nombre_Rol === 'Coordinador Operativo') {
            return true;
        }

        return null; // Continuar con la verificación de permisos específicos
    }

    /**
     * Define si se puede ver la lista de clientes (viewAny)
     */
    public function viewAny(Usuario $user): bool
    {
        // Si el usuario no fue aprobado por before, debe tener el permiso 'select' en la BD.
        return $user->hasPermission('select', $this->objetoName);
    }

    // El Jefe de Proyectos y Marketing tendrán acceso solo si tienen 'select' en la tabla accesos.

    /**
     * Define si se puede crear un cliente.
     */
    public function create(Usuario $user): bool
    {
        return $user->hasPermission('insert', $this->objetoName);
    }

    /**
     * Define si se puede editar un cliente.
     */
    public function update(Usuario $user, Cliente $cliente): bool
    {
        return $user->hasPermission('update', $this->objetoName);
    }

    /**
     * Define si se puede eliminar un cliente.
     */
    public function delete(Usuario $user, Cliente $cliente): bool
    {
        return $user->hasPermission('delete', $this->objetoName);
    }
}
