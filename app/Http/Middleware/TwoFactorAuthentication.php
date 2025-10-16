<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthentication
{
    // ID del rol de Administrador. Debe coincidir con la constante en el controlador (1).
    private const ADMIN_ROLE_ID = 1;

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Verificar si hay un usuario autenticado.
        if (!$user) {
            return $next($request);
        }

        // 2. EXCEPCIÓN: Si el usuario es Administrador (Cod_Rol = 1), pasar directamente.
        // Esto evita que el Administrador sea forzado a verificar el 2FA.
        if ($user->Cod_Rol == self::ADMIN_ROLE_ID) {
            return $next($request);
        }

        // 3. Lógica para el resto de roles (Requiere 2FA).
        // Si el usuario tiene 2FA habilitado y NO ha verificado el código de la sesión
        if ($user->google2fa_enabled && !$request->session()->get('2fa_verified')) {
            // Si el usuario ya completó el login, pero falta el código 2FA,
            // lo enviamos a la vista de verificación.
            return redirect()->route('2fa.verify.show');
        }

        return $next($request);
    }
}
