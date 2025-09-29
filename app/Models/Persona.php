<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Persona extends Authenticatable
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
        'Password'
    ];

    protected $hidden = ['Password'];

    // IMPORTANTE: Laravel busca 'password' por defecto, debemos decirle que use 'Password'
    public function getAuthPassword()
    {
        return $this->Password;
    }

    // IMPORTANTE: Laravel busca 'email' por defecto para algunas operaciones
    public function getEmailForPasswordReset()
    {
        $correo = $this->getCorreoPrincipal();
        return $correo ? $correo->Correo : null;
    }

    // Relaciones
    public function correos()
    {
        return $this->hasMany(Correo::class, 'Cod_Persona', 'Cod_Persona');
    }

    public function telefonos()
    {
        return $this->hasMany(Telefono::class, 'Cod_Persona', 'Cod_Persona');
    }

    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'Cod_Persona', 'Cod_Persona');
    }

    // Relación para obtener el correo principal (el primero o de tipo 'Personal')
    public function correo()
    {
        return $this->hasOne(Correo::class, 'Cod_Persona', 'Cod_Persona');
    }

    // Método auxiliar para obtener el correo principal
    public function getCorreoPrincipal()
    {
        return $this->correos()
                    ->where('Tipo_correo', 'Personal')
                    ->first()
                ?? $this->correos()->first();
    }

    // Método auxiliar para obtener el nombre completo
    public function getNombreCompleto()
    {
        return $this->Nombre . ' ' . $this->Apellido;
    }
}
