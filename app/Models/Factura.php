<?php
// En: app/Models/Factura.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'factura';
    protected $primaryKey = 'Cod_Factura';
    public $timestamps = false; // Confirmado con tu esquema

    protected $fillable = [
        'Cod_Cliente',
        'Fecha_Factura',
        'Total_Factura', // Corregido: El campo es 'Total_Factura' en tu tabla (Imagen 3)
        'Metodo_Pago',     // Agregado
        'Estado_Pago',     // Agregado
        'Descuento_Aplicado', // Agregado
    ];

    // --- Relaciones ---

    /**
     * Relación UNO a UNO inversa (MUCHOS a UNO) con Cliente.
     * La factura pertenece a un cliente.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        // FK: 'Cod_Cliente' en la tabla 'factura'
        // PK: 'Cod_Cliente' en la tabla 'cliente'
        return $this->belongsTo(Cliente::class, 'Cod_Cliente', 'Cod_Cliente');
    }

    /**
     * Relación UNO a MUCHOS con DetalleFacturaProducto.
     * Una factura puede tener muchas líneas de detalle de producto.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalleProductos(): HasMany
    {
        // FK: 'Cod_Factura' en la tabla 'detalle_factura_producto'
        return $this->hasMany(DetalleFacturaProducto::class, 'Cod_Factura', 'Cod_Factura');
    }

    /**
     * Relación UNO a MUCHOS con DetalleFacturaTratamiento.
     * Una factura puede tener muchas líneas de detalle de tratamiento.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalleTratamientos(): HasMany
    {
        // FK: 'Cod_Factura' en la tabla 'detalle_factura_tratamiento'
        return $this->hasMany(DetalleFacturaTratamiento::class, 'Cod_Factura', 'Cod_Factura');
    }
}
