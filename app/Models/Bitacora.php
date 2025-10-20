<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'bitacora';
    protected $primaryKey = 'Cod_Bitacora';
    public $timestamps = false;

    protected $fillable = [
        'Cod_Usuario',
        'Nombre_Usuario',
        'Accion',
        'Observaciones',
        'Modulo',
        'IP_Address',
    ];

    protected $dates = [
        'Fecha_Registro'
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'Cod_Usuario', 'Cod_Usuario');
    }

    // Método helper para registrar acciones fácilmente
    public static function registrar($accion, $modulo = null, $observaciones = null)
    {
        return self::create([
            'Cod_Usuario' => auth()->user()->Cod_Usuario ?? 0,
            'Nombre_Usuario' => auth()->user()->Nombre_Usuario ?? 'Sistema',
            'Accion' => $accion,
            'Modulo' => $modulo,
            'Observaciones' => $observaciones,
            'IP_Address' => request()->ip(),
        ]);
    }
}