<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BitacoraTrait;  // ← AGREGAR ESTA LÍNEA

class Empleado extends Model
{
    use HasFactory;
    use BitacoraTrait;  // ← AGREGAR ESTA LÍNEA

    protected $table = 'empleado';
    protected $primaryKey = 'Cod_Empleado';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Persona',
        'Rol',
        'Fecha_Contratacion',
        'Salario',
        'Disponibilidad'
    ];

    /** Relación inversa a Persona */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'Cod_Persona', 'Cod_Persona');
    }
}