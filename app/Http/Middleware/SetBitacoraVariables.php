<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetBitacoraVariables
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Establecer variables de sesión MySQL para los triggers
        if (Auth::check()) {
            try {
                DB::statement("SET @usuario_id = ?", [Auth::id()]);
                DB::statement("SET @usuario_nombre = ?", [Auth::user()->name ?? 'Usuario']);
                DB::statement("SET @usuario_ip = ?", [$request->ip()]);
            } catch (\Exception $e) {
                \Log::error('Error setting bitacora variables: ' . $e->getMessage());
            }
        } else {
            // Si no hay usuario autenticado, establecer valores por defecto
            try {
                DB::statement("SET @usuario_id = 0");
                DB::statement("SET @usuario_nombre = 'Sistema'");
                DB::statement("SET @usuario_ip = ?", [$request->ip()]);
            } catch (\Exception $e) {
                \Log::error('Error setting default bitacora variables: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}