<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'Cod_Usuario';
    public $timestamps = false; // La tabla usa Fecha_Registro personalizado

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'Cod_Persona',
        'Cod_Rol',
        'Nombre_Usuario',
        'Password',
        'Indicador_Usuario_Activo',
        'Indicador_Insertado',
        'Usuario_Registro',
        'Fecha_Registro',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'Password',
        'remember_token',
    ];

    /**
     * CRÍTICO: Método para obtener el identificador único del usuario
     * Laravel lo usa internamente para la autenticación
     */
    public function getAuthIdentifier()
    {
        return $this->Cod_Usuario;
    }

    /**
     * CRÍTICO: Método para obtener el nombre de la columna del identificador
     * Esto le dice a Laravel qué columna usar como ID
     */
    public function getAuthIdentifierName()
    {
        return 'Cod_Usuario';
    }

    /**
     * CRÍTICO: Método para obtener la contraseña
     * Laravel busca 'password' por defecto, así que le indicamos usar 'Password'
     */
    public function getAuthPassword()
    {
        return $this->Password;
    }

    /**
     * CRÍTICO: Método para obtener el campo remember_token
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * CRÍTICO: Método para establecer el remember_token
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * CRÍTICO: Método para obtener el nombre de la columna remember_token
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Relación con persona (opcional, puede ser NULL)
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación con rol (opcional, puede ser NULL)
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'Cod_Rol', 'Cod_Rol');
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('Indicador_Usuario_Activo', '1');
    }

    /**
     * MÉTODOS DE COMPATIBILIDAD CON PERSONA
     * Estos métodos permiten que el Usuario funcione en vistas que esperan Persona
     */
    
    /**
     * Obtener el correo principal del usuario
     * Como Usuario no tiene correos asociados directamente, retornamos null
     */
    public function getCorreoPrincipal()
    {
        return null;
    }

    /**
     * Obtener el teléfono principal del usuario
     */
    public function getTelefonoPrincipal()
    {
        return null;
    }

    /**
     * Obtener la dirección principal del usuario
     */
    public function getDireccionPrincipal()
    {
        return null;
    }

    /**
     * Obtener nombre completo
     * Para Usuario, usamos el Nombre_Usuario
     */
    public function getNombreCompleto()
    {
        return $this->Nombre_Usuario;
    }

    /**
     * Alias para compatibilidad - Laravel a veces busca 'name'
     */
    public function getNameAttribute()
    {
        return $this->Nombre_Usuario;
    }

    /**
     * Obtener email para reset de contraseña
     * Como Usuario no tiene email asociado, retornamos null
     */
    public function getEmailForPasswordReset()
    {
        return null;
    }

    /**
     * Relaciones vacías para compatibilidad con Persona
     */
    public function correos()
    {
        return collect(); // Colección vacía
    }

    public function telefonos()
    {
        return collect(); // Colección vacía
    }

    public function direcciones()
    {
        return collect(); // Colección vacía
    }
}