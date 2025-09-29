<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

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
