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
     * Método para registrar acciones manualmente
     */
    public static function registrar($accion, $modulo, $observaciones = '')
    {
        try {
            // Establecer variables para triggers
            self::setMySQLVariables();

            self::create([
                'Cod_Usuario' => Auth::id() ?? 0,
                'Nombre_Usuario' => Auth::user()->name ?? 'Sistema',
                'Accion' => $accion,
                'Observaciones' => $observaciones,
                'Modulo' => $modulo,
                'IP_Address' => request()->ip() ?? '0.0.0.0',
                'Fecha_Registro' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error en bitácora: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Establecer variables MySQL
     */
    public static function setMySQLVariables()
    {
        try {
            if (Auth::check()) {
                $userId = Auth::id();
                $userName = Auth::user()->name ?? 'Usuario';
                $userIp = request()->ip() ?? '0.0.0.0';
                
                DB::unprepared("SET @usuario_id = {$userId}");
                DB::unprepared("SET @usuario_nombre = '{$userName}'");
                DB::unprepared("SET @usuario_ip = '{$userIp}'");
            }
        } catch (\Exception $e) {
            \Log::error('Error setting MySQL variables: ' . $e->getMessage());
        }
    }

    /**
     * Relación con usuario (si tienes tabla usuarios)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'Cod_Usuario', 'id');
    }
}