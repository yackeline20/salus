<?php
// En: app/Policies/CitaPolicy.php

namespace App\Policies;

// ⬇️ CORREGIDO: Usar el modelo de User de tu app, que parece ser 'Usuario'
use App\Models\Usuario; 
use App\Models\Cita;

class CitaPolicy
{
    // Nombre del Objeto tal como está en la tabla 'objetos'
    protected $objetoName = 'Citas';
    // Nombre de los roles que necesitan acceso total a Citas
    private $rolCoordinador = 'Coordinador Operativo';
    private $rolEsteticista = 'Esteticista'; 

    public function before(Usuario $user, string $ability): ?bool
    {
        // El Administrador tiene acceso total a todos los métodos.
        if ($user->Nombre_Rol === 'Administrador') {
            return true;
        }

        // ⚠️ Verificación para Coordinador y Esteticista
        if ($user->Nombre_Rol === $this->rolCoordinador || $user->Nombre_Rol === $this->rolEsteticista) {
            // Ambos roles tienen control total sobre las citas, saltamos las verificaciones de 'objetos'.
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models (Lista de Citas).
     */
    public function viewAny(Usuario $user): bool
    {
        // Si no es un rol con acceso total, debe tener el permiso 'select'
        return $user->hasPermission('select', $this->objetoName);
    }

    /**
     * Determine whether the user can create models (Crear Citas).
     */
    public function create(Usuario $user): bool
    {
        // Si no es un rol con acceso total, debe tener el permiso 'insert'
        return $user->hasPermission('insert', $this->objetoName);
    }

    /**
     * ⬇️ CORREGIDO: Determine whether the user can update the model (Editar Citas).
     * Se quitó el argumento (Cita $cita)
     */
    public function update(Usuario $user): bool
    {
        // Si no es un rol con acceso total, debe tener el permiso 'update'
        return $user->hasPermission('update', $this->objetoName);
    }

    /**
     * ⬇️ CORREGIDO: Determine whether the user can delete the model (Eliminar Citas).
     * Se quitó el argumento (Cita $cita)
     */
    public function delete(Usuario $user): bool
    {
        // Si no es un rol con acceso total, debe tener el permiso 'delete'
        return $user->hasPermission('delete', $this->objetoName);
    }
}