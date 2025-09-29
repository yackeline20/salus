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
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación personalizada para correo
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar el correo en la tabla correo
        $correoModel = Correo::where('Correo', $request->email)->first();

        // Verificar que existe el correo y tiene persona asociada
        if (!$correoModel || !$correoModel->persona) {
            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->onlyInput('email');
        }

        // Verificar la contraseña
        if (!Hash::check($request->password, $correoModel->persona->Password)) {
            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->onlyInput('email');
        }

        // Login exitoso - Hacer login con la PERSONA (no con el correo)
        Auth::login($correoModel->persona, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
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
