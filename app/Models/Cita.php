<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BitacoraTrait;  // ← AGREGAR ESTA LÍNEA

class Cita extends Model
{
    use HasFactory;
    use BitacoraTrait;  // ← AGREGAR ESTA LÍNEA

    protected $table = 'cita';
    protected $primaryKey = 'Cod_Cita';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Cliente',
        'Cod_Empleado',
        'Fecha_Cita',
        'Hora_Cita',
        'Estado'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'Cod_Cliente', 'Cod_Cliente');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'Cod_Empleado', 'Cod_Empleado');
    }
}