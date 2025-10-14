<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Correo;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class RegisteredUsuarioController extends Controller
{
    /**
     * Mostrar vista de registro de usuario
     * Pasa la lista de roles a la vista.
     */
    public function create()
    {
        // 1. Obtener los roles activos (Administrador, Recepcionista, Esteticista, etc.)
        $roles = Role::where('Indicador_Rol_Activo', 1)->get(['Cod_Rol', 'Nombre_Rol']);

        // 2. Retornar la vista, pasando la colección de roles.
        return view('auth.register-usuario', compact('roles'));
    }

    /**
     * Procesar el registro de un nuevo usuario
     */
    public function store(Request $request)
    {
        // =========================================================================
        // PASO 1: VALIDACIÓN
        // La regla 'unique:correo,Correo' valida que el email no exista en la tabla 'correo'.
        // =========================================================================
        $request->validate([
            // Campos de Persona
            'Nombre_Persona' => ['required', 'string', 'max:25'],
            'Apellido' => ['required', 'string', 'max:25'],
            'DNI' => ['required', 'string', 'max:25', 'unique:persona,DNI'],
            'Fecha_Nacimiento' => ['required', 'date'],
            'Genero' => ['required', 'string', 'in:Masculino,Femenino'],

            // Campos de Usuario y Correo
            'Nombre_Usuario' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9_.-]+$/',
                'unique:usuarios,Nombre_Usuario'
            ],
            'Correo' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:correo,Correo' // VALIDACIÓN CRUCIAL: El correo debe ser único en la tabla 'correo'
            ],
            'Password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
            'Cod_Rol' => [
                'required',
                'integer',
                'exists:roles,Cod_Rol,Indicador_Rol_Activo,1'
            ],
        ], [
            // Mensajes de error de Persona
            'Nombre_Persona.required' => 'El nombre de la persona es obligatorio.',
            'Apellido.required' => 'El apellido es obligatorio.',
            'DNI.required' => 'El DNI/Identidad es obligatorio.',
            'DNI.unique' => 'El DNI ingresado ya está registrado.',
            'Fecha_Nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'Genero.required' => 'El género es obligatorio.',

            // Mensajes de error de Usuario y Correo
            'Nombre_Usuario.required' => 'El nombre de usuario es obligatorio.',
            'Nombre_Usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'Nombre_Usuario.regex' => 'El nombre de usuario solo puede contener letras, números, guiones y puntos.',
            'Correo.required' => 'El correo electrónico es obligatorio.',
            'Correo.email' => 'El correo electrónico debe ser válido.',
            'Correo.unique' => 'El correo electrónico ingresado ya está asociado a otra persona.', // Mensaje claro
            'Password.required' => 'La contraseña es obligatoria.',
            'Password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'Password.confirmed' => 'Las contraseñas no coinciden.',
            'Cod_Rol.required' => 'Debe seleccionar un Rol para el usuario.',
            'Cod_Rol.exists' => 'El Rol seleccionado no es válido o no está activo.',
        ]);


        DB::beginTransaction();

        try {
            // =========================================================================
            // PASO 2: CREAR EL REGISTRO EN LA TABLA PERSONA
            // =========================================================================
            $persona = Persona::create([
                'Nombre' => $request->Nombre_Persona,
                'Apellido' => $request->Apellido,
                'DNI' => $request->DNI,
                'Fecha_Nacimiento' => $request->Fecha_Nacimiento,
                'Genero' => $request->Genero,
                // Asumo que estos campos tienen valores por defecto definidos en el modelo o DB.
            ]);

            // =========================================================================
            // PASO 3: CREAR EL REGISTRO EN LA TABLA CORREO
            // =========================================================================
            Correo::create([
                'Cod_Persona' => $persona->Cod_Persona, // Usamos el ID recién creado
                'Correo' => $request->Correo,
                'Tipo_correo' => 'Laboral', // Tipo por defecto para usuarios del sistema
            ]);


            // =========================================================================
            // PASO 4: CREAR EL REGISTRO EN LA TABLA USUARIOS
            // CRUCIAL: Uso de Hash::make() para Bcrypt.
            // =========================================================================
            $usuario = Usuario::create([
                'Cod_Persona' => $persona->Cod_Persona,
                'Cod_Rol' => $request->Cod_Rol,
                'Nombre_Usuario' => $request->Nombre_Usuario,
                'Password' => Hash::make($request->Password), // HASH BCrypt
                'Indicador_Usuario_Activo' => '1',
                'Indicador_Insertado' => '1',
                // Si el usuario está autenticado (Admin), usa su nombre, sino usa 'Sistema'.
                'Usuario_Registro' => Auth::check() ? Auth::user()->Nombre_Usuario : 'Sistema',
                'Fecha_Registro' => now(),
            ]);

            DB::commit();

            // Redireccionar con mensaje de éxito (para mostrar en la vista)
            return redirect()->route('register.usuario')
                ->with('success', true)
                ->with('nombre_usuario', $usuario->Nombre_Usuario);

        } catch (\Throwable $e) {
            DB::rollBack();

            // Loguear el error para la depuración
            // \Log::error("Error de registro: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al registrar usuario: ' . $e->getMessage())
                ->withInput();
        }
    }
}
