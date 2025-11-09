<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoProveedor extends Model
{
    use HasFactory;

    protected $table = 'producto_proveedor';
    protected $primaryKey = 'Cod_Prod_Prov';
    public $timestamps = true;

    protected $fillable = [
        'Cod_Producto',
        'Cod_Proveedor',
        'Precio_Ultima_Compra',
        'Fecha_Ultima_Compra'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'Cod_Producto');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'Cod_Proveedor');
    }
}