<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BitacoraTrait;  // ← AGREGAR ESTA LÍNEA

class Cliente extends Model
{
    use HasFactory;
    use BitacoraTrait;  // ← AGREGAR ESTA LÍNEA

    protected $table = 'cliente';
    protected $primaryKey = 'Cod_Cliente';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Persona',
        'Tipo_Cliente',
        'Nota_Preferencia'
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona');
    }
}