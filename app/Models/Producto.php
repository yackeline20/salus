<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';
    protected $primaryKey = 'Cod_Producto';
    public $timestamps = true;

    protected $fillable = [
        'Nombre_Producto',
        'Descripcion',
        'Precio_Venta',
        'Costo_Compra',
        'Cantidad_En_Stock',
        'Fecha_Vencimiento',
        'Precio_Ultima_Compra',
        'Fecha_Ultima_Compra',
        'Cod_Proveedor'
    ];

    protected $casts = [
        'Precio_Venta' => 'decimal:2',
        'Costo_Compra' => 'decimal:2',
        'Cantidad_En_Stock' => 'integer',
        'Precio_Ultima_Compra' => 'decimal:2',
        'Fecha_Vencimiento' => 'date',
        'Fecha_Ultima_Compra' => 'datetime'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'Cod_Proveedor', 'Cod_Proveedor');
    }
}