<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedor';
    protected $primaryKey = 'Cod_Proveedor';
    public $timestamps = true;

    protected $fillable = [
        'Nombre_Proveedor',
        'Contacto_Principal',
        'Telefono',
        'Email',
        'Direccion'
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_proveedor', 'Cod_Proveedor', 'Cod_Producto')
                    ->withPivot('Precio_Ultima_Compra', 'Fecha_Ultima_Compra')
                    ->withTimestamps();
    }
}