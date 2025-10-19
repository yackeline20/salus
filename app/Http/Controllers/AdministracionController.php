<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdministracionController extends Controller
{
    public function index()
    {
        return view('partials.administracion');
    }

    public function backup()
    {
        return view('partials.administracion.backup');
    }

    public function crearBackup(Request $request)
    {
        return back()->with('success', 'Backup creado exitosamente');
    }

    public function restaurarBackup(Request $request)
    {
        return back()->with('success', 'Backup restaurado exitosamente');
    }

    public function password()
    {
        return view('partials.administracion.password');
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password_nueva' => 'required|min:6',
            'password_confirmar' => 'required|same:password_nueva',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_actual, $user->password)) {
            return back()->with('error', 'La contraseña actual es incorrecta');
        }

        $user->password = Hash::make($request->password_nueva);
        $user->save();

        return back()->with('success', 'Contraseña cambiada exitosamente');
    }

    public function bitacora()
    {
        return view('partials.administracion.bitacora');
    }
}