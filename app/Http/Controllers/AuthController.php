<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Procesar login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required'
        ]);

        $loginData = [
            'Nombre_Usuario' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($loginData, $request->filled('remember'))) {
            $user = Auth::user();

            // ========================================
            // VERIFICAR SI EL USUARIO TIENE 2FA HABILITADO
            // ========================================
            if ($user->google2fa_enabled) {
                // Cerrar la sesión temporal
                Auth::logout();
                
                // Guardar el ID del usuario en sesión para verificación posterior
                $request->session()->put('2fa:user:id', $user->Cod_Usuario);
                
                // Redirigir a la página de verificación 2FA
                return redirect()->route('2fa.verify');
            }

            // Si no tiene 2FA habilitado, continuar normalmente
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ]);
    }

    // Mostrar formulario de registro
    public function showRegister()
    {
        return view('auth.register');
    }

    // Procesar registro
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('register')->with('success', 'Registro exitoso');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}