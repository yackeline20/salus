<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait BitacoraTrait
{
    /**
     * Boot del trait - se ejecuta automáticamente
     */
    protected static function bootBitacoraTrait()
    {
        // Antes de eliminar
        static::deleting(function ($model) {
            self::setMySQLVariables();
        });

        // Antes de actualizar
        static::updating(function ($model) {
            self::setMySQLVariables();
        });
    }

    /**
     * Establecer variables de sesión MySQL para triggers
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
            } else {
                DB::unprepared("SET @usuario_id = 0");
                DB::unprepared("SET @usuario_nombre = 'Sistema'");
                DB::unprepared("SET @usuario_ip = '0.0.0.0'");
            }
        } catch (\Exception $e) {
            \Log::error('Error setting MySQL variables: ' . $e->getMessage());
        }
    }
}