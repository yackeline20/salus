<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        'Fecha_Registro'
    ];

    protected $casts = [
        'Fecha_Registro' => 'datetime',
    ];

    /**
     * Método estático para registrar acciones manuales en la bitácora
     */
    public static function registrar($accion, $modulo, $observaciones = '')
    {
        try {
            // Establecer variables de sesión MySQL para los triggers
            if (Auth::check()) {
                DB::statement("SET @usuario_id = ?", [Auth::id()]);
                DB::statement("SET @usuario_nombre = ?", [Auth::user()->name ?? 'Usuario']);
                DB::statement("SET @usuario_ip = ?", [request()->ip()]);
            }

            self::create([
                'Cod_Usuario' => Auth::id() ?? 0,
                'Nombre_Usuario' => Auth::user()->name ?? 'Sistema',
                'Accion' => $accion,
                'Observaciones' => $observaciones,
                'Modulo' => $modulo,
                'IP_Address' => request()->ip(),
                'Fecha_Registro' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error en bitácora: ' . $e->getMessage());
            return false;
        }
    }
}