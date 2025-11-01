<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'cliente'; // Nombre de tu tabla de clientes
    protected $primaryKey = 'Cod_Cliente';
    public $timestamps = false; // Confirmado con tu esquema

    protected $fillable = [
        'Cod_Persona',
        'Tipo_Cliente',
        'Nota_Preferencia',
        'Fecha_Registro' // Agregué este campo según la imagen de tu tabla
    ];

    // --- Relaciones ---

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
     * Un cliente puede tener muchas facturas. CRÍTICA para el módulo de facturación.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facturas(): HasMany
    {
        // FK: 'Cod_Cliente' en la tabla 'factura'
        // PK: 'Cod_Cliente' en la tabla 'cliente'
        return $this->hasMany(Factura::class, 'Cod_Cliente', 'Cod_Cliente');
    }
}
