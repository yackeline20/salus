<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Correo extends Authenticatable
{
    use HasFactory;

    protected $table = 'correo';
    protected $primaryKey = 'Cod_Correo';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Persona',
        'Correo',
        'Tipo_correo'
    ];

    // Relación con persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona');
    }

    // Métodos requeridos para la autenticación de Laravel

    /**
     * Obtiene la contraseña del usuario para autenticación
     */
    public function getAuthPassword()
    {
        return $this->persona->Password;
    }

    /**
     * Obtiene el nombre del campo que Laravel usará como identificador único
     */
    public function getAuthIdentifierName()
    {
        return 'Correo';
    }

    /**
     * Obtiene el valor del identificador único
     */
    public function getAuthIdentifier()
    {
        return $this->Correo;
    }

    /**
     * Método auxiliar para obtener el nombre completo del usuario
     */
    public function getNombreCompleto()
    {
        return $this->persona ? $this->persona->getNombreCompleto() : '';
    }

    /**
     * Método auxiliar para verificar si es correo personal
     */
    public function esPersonal()
    {
        return $this->Tipo_correo === 'Personal';
    }

    /**
     * Scope para obtener solo correos personales
     */
    public function scopePersonales($query)
    {
        return $query->where('Tipo_correo', 'Personal');
    }
}
