<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correo extends Model
{
    use HasFactory;

    protected $table = 'correo';
    protected $primaryKey = 'Cod_Correo';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Persona',
        'Correo',
        'Tipo_correo'
    ];

    public function persona() { return $this->belongsTo(Persona::class, 'Cod_Persona'); }
}