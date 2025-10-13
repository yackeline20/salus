<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\Correo;
use App\Models\Usuario;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Soporta login tanto con email (personas) como con username (usuarios)
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación flexible - puede ser email o username
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $loginField = $request->input('login');
        $password = $request->input('password');

        // Determinar si es email o username
        $isEmail = filter_var($loginField, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // FLUJO ORIGINAL: Login con CORREO (tabla persona)
            return $this->loginWithEmail($request, $loginField, $password);
        } else {
            // NUEVO FLUJO: Login con USERNAME (tabla usuarios)
            return $this->loginWithUsername($request, $loginField, $password);
        }
    }

    /**
     * Login usando correo electrónico (sistema de personas - ORIGINAL)
     */
    private function loginWithEmail(Request $request, string $email, string $password): RedirectResponse
    {
        // Buscar el correo en la tabla correo
        $correoModel = Correo::where('Correo', $email)->first();

        // Verificar que existe el correo y tiene persona asociada
        if (!$correoModel || !$correoModel->persona) {
            return back()->withErrors([
                'login' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->onlyInput('login');
        }

        // Verificar la contraseña
        if (!Hash::check($password, $correoModel->persona->Password)) {
            return back()->withErrors([
                'login' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->onlyInput('login');
        }

        // Login exitoso - Hacer login con la PERSONA
        Auth::guard('web')->login($correoModel->persona, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->intended('dashboard');
    }

    /**
     * Login usando nombre de usuario (sistema de usuarios - NUEVO)
     */
    private function loginWithUsername(Request $request, string $username, string $password): RedirectResponse
    {
        // Buscar usuario por nombre de usuario
        $usuario = Usuario::where('Nombre_Usuario', $username)
                         ->where('Indicador_Usuario_Activo', '1')
                         ->first();

        // Verificar que existe el usuario
        if (!$usuario) {
            return back()->withErrors([
                'login' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->onlyInput('login');
        }

        // Verificar la contraseña
        if (!Hash::check($password, $usuario->Password)) {
            return back()->withErrors([
                'login' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->onlyInput('login');
        }

        // CRÍTICO: Login exitoso - Autenticar manualmente al usuario
        // Usamos loginUsingId para asegurar que funcione correctamente
        Auth::guard('web')->loginUsingId($usuario->Cod_Usuario, $request->filled('remember'));
        
        // Regenerar la sesión para seguridad
        $request->session()->regenerate();

        // Redirigir al dashboard
        return redirect()->intended('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}