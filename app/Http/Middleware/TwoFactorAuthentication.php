<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si el usuario tiene 2FA habilitado y no ha verificado el código
        if ($user && $user->google2fa_enabled && !$request->session()->get('2fa_verified')) {
            return redirect()->route('2fa.verify.show');
        }

        return $next($request);
    }
}