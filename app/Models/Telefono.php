<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    use HasFactory;
    protected $table = 'telefono';
    protected $primaryKey = 'Cod_Telefono';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Persona',
        'Numero',
        'Cod_Pais',
        'Tipo',
        'Descripcion'
    ];

    public function persona() { return $this->belongsTo(Persona::class, 'Cod_Persona'); }
}
