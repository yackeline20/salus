<?php
// En: app/Models/Tratamiento.php

namespace App\Models;
use App\Traits\BitacoraTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;
use BitacoraTrait;
    protected $table = 'tratamiento';
    protected $primaryKey = 'Cod_Tratamiento';
    public $timestamps = false;

    protected $fillable = [
        'Nombre_Tratamiento',
        'Descripcion',
        'Costo'
    ];
}
