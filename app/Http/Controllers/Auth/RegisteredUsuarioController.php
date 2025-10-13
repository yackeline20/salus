<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class RegisteredUsuarioController extends Controller
{
    /**
     * Mostrar vista de registro de usuario
     */
    public function create()
    {
        return view('auth.register-usuario');
    }

    /**
     * Procesar el registro de un nuevo usuario
     */
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'Nombre_Usuario' => [
                'required', 
                'string', 
                'max:255',
                'regex:/^[A-Za-z0-9_.-]+$/', // Solo letras, números, guiones y puntos
                'unique:usuarios,Nombre_Usuario'
            ],
            'Correo' => [
                'required', 
                'string', 
                'email', 
                'max:255',
                // Opcional: verificar si quieres que sea único
                // 'unique:usuarios,correo_temporal' 
            ],
            'Password' => [
                'required', 
                'string', 
                'min:8',
                'confirmed' // Esto requiere que exista Password_confirmation
            ],
        ], [
            'Nombre_Usuario.required' => 'El nombre de usuario es obligatorio.',
            'Nombre_Usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'Nombre_Usuario.regex' => 'El nombre de usuario solo puede contener letras, números, guiones y puntos.',
            'Correo.required' => 'El correo electrónico es obligatorio.',
            'Correo.email' => 'El correo electrónico debe ser válido.',
            'Password.required' => 'La contraseña es obligatoria.',
            'Password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'Password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        DB::beginTransaction();

        try {
            // Crear usuario
            $usuario = Usuario::create([
                'Cod_Persona' => null, // NULL según requerimiento
                'Cod_Rol' => null, // NULL según requerimiento
                'Nombre_Usuario' => $request->Nombre_Usuario,
                'Password' => Hash::make($request->Password),
                'Indicador_Usuario_Activo' => '1',
                'Indicador_Insertado' => '1',
                'Usuario_Registro' => $request->Nombre_Usuario, // Se auto-registra
                'Fecha_Registro' => now(),
            ]);

            DB::commit();

            // NO iniciar sesión automáticamente, solo mostrar mensaje de éxito
            return redirect()->route('register.usuario')
                ->with('success', true)
                ->with('nombre_usuario', $usuario->Nombre_Usuario);

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar usuario: ' . $e->getMessage())
                ->withInput();
        }
    }
}