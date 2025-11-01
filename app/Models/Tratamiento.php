<?php
// En: app/Models/Tratamiento.php

namespace App\Models;
use App\Traits\BitacoraTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tratamiento extends Model
{
    use HasFactory;
use BitacoraTrait;
    protected $table = 'tratamiento';
    protected $primaryKey = 'Cod_Tratamiento';
    public $timestamps = false; // Confirmado con tu esquema

    protected $fillable = [
        'Nombre_Tratamiento',
        'Descripcion',
        'Costo',
        'Precio_Estandar', // Agregado: Es el precio de venta al cliente
        'Url_Imagen'       // Agregado si usas imágenes para tratamientos
    ];

    // --- Relaciones ---

    /**
     * Relación UNO a MUCHOS con DetalleFacturaTratamiento.
     * Un tratamiento puede ser vendido en muchas líneas de detalle de factura.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalleFacturaTratamientos(): HasMany
    {
        // FK: 'Cod_Tratamiento' en la tabla 'detalle_factura_tratamiento'
        return $this->hasMany(DetalleFacturaTratamiento::class, 'Cod_Tratamiento', 'Cod_Tratamiento');
    }
}
