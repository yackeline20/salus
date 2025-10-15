<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\Correo;
use App\Models\Usuario;
use App\Models\Persona;

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
     * Soporta login tanto con email (persona/usuario) como con username (usuario).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $loginField = $request->input('login');
        $password = $request->input('password');
        $remember = $request->filled('remember');
        $isEmail = filter_var($loginField, FILTER_VALIDATE_EMAIL);

        // Intenta obtener el objeto Usuario
        $usuario = $isEmail
            ? $this->findUsuarioByEmail($loginField)
            : $this->findUsuarioByUsername($loginField);

        // 1. Verificar si se encontró un usuario activo
        if (!$usuario) {
            return back()->withErrors([
                'login' => 'Las credenciales proporcionadas no coinciden con nuestros registros o el usuario está inactivo.',
            ])->onlyInput('login');
        }

        // 2. Verificar la contraseña usando el HASH de la tabla usuarios (CRÍTICO)
        if (!Hash::check($password, $usuario->Password)) {
            return back()->withErrors([
                'login' => 'La contraseña es incorrecta.',
            ])->onlyInput('login');
        }

        // 3. Login exitoso - Autenticar al usuario con el guard 'web'
        Auth::guard('web')->login($usuario, $remember);

        // 4. Regenerar la sesión para seguridad
        $request->session()->regenerate();

        // ✅ 5. LÓGICA DE 2FA OBLIGATORIO
        Log::info('=== DEBUG LOGIN 2FA ===');
        Log::info('Usuario: ' . $usuario->Nombre_Usuario);
        Log::info('google2fa_enabled: ' . ($usuario->google2fa_enabled ? 'SI' : 'NO'));
        Log::info('google2fa_secret: ' . ($usuario->google2fa_secret ? 'EXISTE' : 'NO EXISTE'));

        // IMPORTANTE: Verificar PRIMERO si tiene el secreto configurado
        // Si NO tiene secreto configurado, debe ir a SETUP (mostrar QR)
        if (empty($usuario->google2fa_secret)) {
            Log::info('⚠️ Usuario SIN secreto 2FA - Redirigiendo a CONFIGURACIÓN (mostrar QR)');
            
            $request->session()->put('2fa_verified', false);
            $request->session()->put('2fa_setup_required', true);
            
            return redirect()->route('2fa.setup')
                ->with('warning', '⚠️ Debes configurar la autenticación de dos factores para continuar.');
        }

        // Si tiene secreto pero NO está habilitado, forzar habilitación
        if (!$usuario->google2fa_enabled) {
            Log::info('⚠️ Usuario tiene secreto pero 2FA no habilitado - Redirigiendo a CONFIGURACIÓN');
            
            $request->session()->put('2fa_verified', false);
            $request->session()->put('2fa_setup_required', true);
            
            return redirect()->route('2fa.setup')
                ->with('warning', '⚠️ Debes activar la autenticación de dos factores para continuar.');
        }

        // Si tiene secreto Y está habilitado, pedir código de VERIFICACIÓN
        Log::info('✅ Usuario con 2FA completo - Redirigiendo a VERIFICACIÓN (pedir código)');
        $request->session()->put('2fa_verified', false);
        
        return redirect()->route('2fa.verify.show');
    }

    /**
     * Busca el modelo Usuario asociado al correo electrónico.
     */
    private function findUsuarioByEmail(string $email): ?Usuario
    {
        // 1. Buscar el Correo y obtener Cod_Persona
        $correoModel = Correo::where('Correo', $email)->first();

        if (!$correoModel) {
            return null; // Correo no encontrado
        }

        // 2. Buscar el Usuario por Cod_Persona y que esté activo
        $usuario = Usuario::where('Cod_Persona', $correoModel->Cod_Persona)
                          ->where('Indicador_Usuario_Activo', 1)
                          ->first();

        return $usuario;
    }

    /**
     * Busca el modelo Usuario por Nombre de Usuario.
     */
    private function findUsuarioByUsername(string $username): ?Usuario
    {
        // 1. Buscar el usuario por Nombre_Usuario y que esté activo
        $usuario = Usuario::where('Nombre_Usuario', $username)
                          ->where('Indicador_Usuario_Activo', 1)
                          ->first();

        return $usuario;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Log::info('=== LOGOUT ===');
        Log::info('Usuario cerrando sesión: ' . Auth::user()->Nombre_Usuario);
        
        // ✅ Limpiar el flag de verificación 2FA de la sesión
        $request->session()->forget('2fa_verified');
        $request->session()->forget('2fa_setup_required');
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Sesión cerrada correctamente');

        return redirect('/');
    }
}