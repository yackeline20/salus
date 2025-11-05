<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    // El nombre de la tabla DEBE ser 'producto' (en español y minúscula),
    // ya que tu base de datos la tiene así.
    protected $table = 'producto';
    protected $primaryKey = 'Cod_Producto';
    public $timestamps = false; // Confirmado con tu esquema

    protected $fillable = [
        'Nombre_Producto',
        'Descripcion',
        'Precio_Venta',
        'Costo_Compra',
        'Cantidad_En_Stock',
        'Fecha_Vencimiento',
        'Url_Imagen', // Agregado según la estructura de tu tabla
    ];

    protected $casts = [
        'Precio_Venta' => 'decimal:2',
        'Costo_Compra' => 'decimal:2',
        'Fecha_Vencimiento' => 'date'
    ];

    // --- Relaciones ---

    /**
     * Relación UNO a MUCHOS con DetalleFacturaProducto.
     * Un producto puede ser vendido en muchas líneas de detalle de factura.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalleFacturaProductos(): HasMany
    {
        // FK: 'Cod_Producto' en la tabla 'detalle_factura_producto'
        return $this->hasMany(DetalleFacturaProducto::class, 'Cod_Producto', 'Cod_Producto');
    }
}