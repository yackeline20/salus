<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Necesario para la relación inversa con Persona
use Illuminate\Database\Eloquent\Relations\HasMany;   // Necesario para la relación con Factura

// Importamos el trait para el sistema de Bitácora
use App\Traits\BitacoraTrait;

// Importamos los modelos relacionados
use App\Models\Persona;
use App\Models\Factura; // Asumimos que existe un modelo Factura para la relación HasMany

class Cliente extends Model
{
    // Usamos los Traits HasFactory y BitacoraTrait
    use HasFactory, BitacoraTrait;

    protected $table = 'cliente';
    protected $primaryKey = 'Cod_Cliente';
    public $timestamps = false; // Deshabilitar timestamps

    protected $fillable = [
        'Cod_Persona',
        'Tipo_Cliente',
        'Nota_Preferencia',
        'Fecha_Registro'
    ];

    // --- Relaciones Eloquent ---

    /**
     * Relación UNO a UNO inversa (MUCHOS a UNO) con Persona.
     * Un cliente pertenece a una persona.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function persona(): BelongsTo
    {
        // FK: 'Cod_Persona' en la tabla 'cliente'
        // PK: 'Cod_Persona' en la tabla 'persona'
        return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona');
    }

    /**
     * Relación UNO a MUCHOS con Factura.
     * Un cliente puede tener muchas facturas.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facturas(): HasMany
    {
        // FK: 'Cod_Cliente' en la tabla 'factura'
        // PK: 'Cod_Cliente' en la tabla 'cliente'
        return $this->hasMany(Factura::class, 'Cod_Cliente', 'Cod_Cliente');
    }
}
