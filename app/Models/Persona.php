<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\BitacoraTrait;
// Importamos los modelos relacionados que usaremos
use App\Models\Correo;
use App\Models\Telefono;
use App\Models\Direccion;
use App\Models\Usuario;

class Persona extends Model
{
    use HasFactory;
use BitacoraTrait;
    protected $table = 'persona';
    protected $primaryKey = 'Cod_Persona';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Apellido',
        'DNI',
        'Fecha_Nacimiento',
        'Genero',
    ];

    // --- Relaciones ---

    /**
     * Relación UNO a MUCHOS con Correo.
     * CRÍTICA para el funcionamiento de scopeWhereEmail en Usuario.php
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function correos(): HasMany
    {
        return $this->hasMany(Correo::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a MUCHOS con Telefono
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function telefonos(): HasMany
    {
        return $this->hasMany(Telefono::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a MUCHOS con Direccion
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a UNO con Usuario
     * IMPORTANTE: Una persona puede tener un usuario en el sistema
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function usuario(): HasOne
    {
        return $this->hasOne(Usuario::class, 'Cod_Persona', 'Cod_Persona');
    }

    // --- Métodos Auxiliares ---

    /**
     * Método auxiliar para obtener el modelo Correo principal (Personal o el primero).
     * IMPORTANTE: Este método devuelve el MODELO Correo, no el string del correo.
     * @return \App\Models\Correo|null
     */
    public function getCorreoPrincipal()
    {
        // Buscamos el tipo 'Personal' (insensible a mayúsculas/minúsculas)
        $personal = $this->correos()
                         ->whereRaw('LOWER(Tipo_correo) = ?', ['personal'])
                         ->first();

        // Si no hay Personal, devuelve el primero que encuentre la relación
        return $personal ?? $this->correos()->first();
    }

    /**
     * Método auxiliar para obtener el nombre completo
     * @return string
     */
    public function getNombreCompleto(): string
    {
        return trim($this->Nombre . ' ' . $this->Apellido);
    }
}

