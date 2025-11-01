<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Importamos el trait para el sistema de Bitácora
use App\Traits\BitacoraTrait;

// Importamos los modelos relacionados para las relaciones
use App\Models\Cliente;
use App\Models\Correo;
use App\Models\Telefono;
use App\Models\Direccion;
use App\Models\Usuario;

class Persona extends Model
{
    // Usamos los Traits HasFactory y BitacoraTrait
    use HasFactory, BitacoraTrait;

    // Nombre de la tabla y clave primaria
    protected $table = 'persona';
    protected $primaryKey = 'Cod_Persona';
    public $timestamps = false; // Deshabilitar timestamps

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'Nombre',
        'Apellido',
        'DNI',
        'Fecha_Nacimiento',
        'Genero',
    ];

    // --- Relaciones Eloquent ---

    /**
     * Relación UNO a UNO con Cliente.
     */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a UNO con Usuario.
     */
    public function usuario(): HasOne
    {
        return $this->hasOne(Usuario::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a MUCHOS con Correo.
     */
    public function correos(): HasMany
    {
        return $this->hasMany(Correo::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a MUCHOS con Telefono
     */
    public function telefonos(): HasMany
    {
        return $this->hasMany(Telefono::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a MUCHOS con Direccion
     */
    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'Cod_Persona', 'Cod_Persona');
    }


    // --- ACCESORES ---
    // Permiten acceder a un valor calculado como una propiedad ($persona->nombre_completo)

    /**
     * Accesor para obtener el nombre completo (Forma idiomática de Laravel).
     * @return string
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim($this->Nombre . ' ' . $this->Apellido);
    }

    // --- CORRECCIÓN DE LEGADO ---
    // Método para soportar la llamada a función que podría existir en alguna vista
    // ($persona->getnombreCompleto())

    /**
     * Método de compatibilidad para llamadas tipo función.
     * @return string
     */
    public function getnombreCompleto(): string
    {
        // Llama al Accesor real para centralizar la lógica
        return $this->getNombreCompletoAttribute();
    }


    // --- Métodos Auxiliares/Funciones ---

    /**
     * Método auxiliar para obtener el modelo Correo principal (Personal o el primero).
     * @return \App\Models\Correo|null
     */
    public function getCorreoPrincipal()
    {
        // Busca el correo tipo 'Personal' o el primer correo disponible.
        return $this->correos()->where('Tipo_correo', 'Personal')->first() ??
               $this->correos()->first();
    }

    // --- Scopes ---
    // Permiten construir consultas sencillas (Persona::byDni('...'))

    /**
     * Scope para buscar personas por DNI.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $dni
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDni($query, $dni)
    {
        return $query->where('DNI', $dni);
    }
}
