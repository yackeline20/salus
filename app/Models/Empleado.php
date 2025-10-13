<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

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
