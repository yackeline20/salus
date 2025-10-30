<?php
// En: app/Models/Factura.php

namespace App\Models;
use App\Traits\BitacoraTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;
use BitacoraTrait;
    protected $table = 'factura';
    protected $primaryKey = 'Cod_Factura';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Cliente',
        'Fecha_Factura',
        'Total'
    ];
}
