<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // CORRECCIÓN: Extiende de Model, no de Authenticatable
use Illuminate\Notifications\Notifiable;

class Persona extends Model // CORRECCIÓN
{
    use HasFactory, Notifiable;

    protected $table = 'persona';
    protected $primaryKey = 'Cod_Persona';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Apellido',
        'DNI',
        'Fecha_Nacimiento',
        'Genero',
        // 'Password' NO debe estar aquí si Persona ya no se usa para login
    ];

    // Si la tabla Persona no se usa para login, 'Password' y 'hidden' no son necesarios aquí
    // protected $hidden = ['Password'];


    // --- Métodos de Compatibilidad con Autenticación (Solo si son estrictamente necesarios) ---

    /* * Los siguientes métodos (getAuthPassword, getEmailForPasswordReset)
    * SOLO son necesarios si Persona fuera a usarse para login.
    * Puesto que Usuario es el que autentica, estos pueden ser eliminados
    * o corregidos para usarlos en el proceso de restablecimiento de contraseña.
    */

    // public function getAuthPassword() { return $this->Password; } // Se elimina

    /** Obtiene el email para reset de contraseña. */
    public function getEmailForPasswordReset()
    {
        $correo = $this->getCorreoPrincipal();
        // CORRECCIÓN: Asumo que el campo en la tabla 'correo' es 'Correo' (con C mayúscula)
        return $correo ? $correo->Correo : null;
    }

    // --- Relaciones ---

    /** Relación Uno a Muchos con Correo */
    public function correos()
    {
        return $this->hasMany(Correo::class, 'Cod_Persona', 'Cod_Persona');
    }

    /** Relación Uno a Muchos con Telefono */
    public function telefonos()
    {
        return $this->hasMany(Telefono::class, 'Cod_Persona', 'Cod_Persona');
    }

    /** Relación Uno a Muchos con Direccion */
    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'Cod_Persona', 'Cod_Persona');
    }

    /** Relación Uno a Uno con Correo (Solo para obtener el primero, pero 'correos' ya lo cubre) */
    public function correo()
    {
        return $this->hasOne(Correo::class, 'Cod_Persona', 'Cod_Persona');
    }

    // --- Métodos Auxiliares ---

    /** Método auxiliar para obtener el correo principal */
    public function getCorreoPrincipal()
    {
        return $this->correos()
                     // Buscamos el tipo 'Personal' (insensible a mayúsculas/minúsculas)
                    ->whereRaw('LOWER(Tipo_correo) = ?', ['personal'])
                    ->first()
                ?? $this->correos()->first(); // Si no hay Personal, devuelve el primero
    }

    /** Método auxiliar para obtener el nombre completo */
    public function getNombreCompleto()
    {
        return $this->Nombre . ' ' . $this->Apellido;
    }
}

