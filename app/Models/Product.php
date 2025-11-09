<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';
    protected $primaryKey = 'Cod_Producto';
    public $timestamps = true;

    protected $fillable = [
        'Nombre_Producto',
        'Descripcion',
        'Precio_Venta',
        'Costo_Compra',
        'Cantidad_En_Stock',
        'Fecha_Vencimiento'
    ];

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'producto_proveedor', 'Cod_Producto', 'Cod_Proveedor')
                    ->withPivot('Precio_Ultima_Compra', 'Fecha_Ultima_Compra')
                    ->withTimestamps();
    }
}