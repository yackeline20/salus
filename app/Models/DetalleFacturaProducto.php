<?php
// En: app/Models/DetalleFacturaProducto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleFacturaProducto extends Model
{
    use HasFactory;

    protected $table = 'detalle_factura_producto';
    protected $primaryKey = 'Cod_Detalle_Fp';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Factura',
        'Cod_Producto',
        'Cantidad_Vendida',
        'Precio_Unitario_Venta',
        'Subtotal',
    ];

    // --- Relaciones ---

    /**
     * Relación: Pertenece a una Factura (MUCHOS a UNO)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factura(): BelongsTo
    {
        // Conecta este detalle con la factura principal
        return $this->belongsTo(Factura::class, 'Cod_Factura', 'Cod_Factura');
    }

    /**
     * Relación: Pertenece a un Producto (MUCHOS a UNO)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto(): BelongsTo
    {
        // Conecta este detalle con la información del producto (inventario)
        // Usamos la clase 'Product' que definimos anteriormente
        return $this->belongsTo(Product::class, 'Cod_Producto', 'Cod_Producto');
    }
}
