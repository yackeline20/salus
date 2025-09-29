<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Correo;
use App\Models\Telefono;
use App\Models\Direccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RegisteredPersonaController extends Controller
{
    public function create()
    {
        return view('auth.register-persona');
    }

    public function store(Request $request)
    {
        // Log para debug
        Log::info('Iniciando registro de persona', $request->all());

        try {
            $request->validate([
                'Nombre' => ['required', 'string', 'max:25'],
                'Apellido' => ['required', 'string', 'max:25'],
                'DNI' => ['required', 'string', 'max:25', 'unique:persona,DNI'],
                'Fecha_Nacimiento' => ['required', 'date'],
                'Genero' => ['required', Rule::in(['Masculino', 'Femenino'])],
                'Password' => ['required', 'string', 'min:6'],
                'Correo' => ['required', 'string', 'email', 'max:25'],
                'Tipo_correo' => ['nullable', Rule::in(['Personal', 'Laboral', 'Otro'])],
                'Numero' => ['nullable', 'string', 'max:20'],
                'Cod_Pais' => ['nullable', 'string', 'max:10'],
                'Tipo' => ['nullable', Rule::in(['Fijo', 'Movil'])],
                'Descripcion_Tel' => ['nullable', 'string', 'max:50'],
                'Direccion' => ['nullable', 'string', 'max:50'],
                'Descripcion_Dir' => ['nullable', 'string', 'max:120'],
            ]);

            Log::info('Validación exitosa');

            DB::beginTransaction();

            // Crear persona
            $persona = Persona::create([
                'Nombre' => $request->Nombre,
                'Apellido' => $request->Apellido,
                'DNI' => $request->DNI,
                'Fecha_Nacimiento' => $request->Fecha_Nacimiento,
                'Genero' => $request->Genero,
                'Password' => Hash::make($request->Password),
            ]);

            Log::info('Persona creada', ['id' => $persona->Cod_Persona]);

            // Crear correo
            $correo = Correo::create([
                'Cod_Persona' => $persona->Cod_Persona,
                'Correo' => $request->Correo,
                'Tipo_correo' => $request->Tipo_correo ?? 'Personal',
            ]);

            Log::info('Correo creado');

            // Teléfono opcional
            if ($request->filled('Numero')) {
                Telefono::create([
                    'Cod_Persona' => $persona->Cod_Persona,
                    'Numero' => $request->Numero,
                    'Cod_Pais' => $request->Cod_Pais,
                    'Tipo' => $request->Tipo ?? 'Movil',
                    'Descripcion' => $request->Descripcion_Tel,
                ]);
                Log::info('Teléfono creado');
            }

            // Dirección opcional
            if ($request->filled('Direccion')) {
                Direccion::create([
                    'Cod_Persona' => $persona->Cod_Persona,
                    'Direccion' => $request->Direccion,
                    'Descripcion' => $request->Descripcion_Dir,
                ]);
                Log::info('Dirección creada');
            }

            DB::commit();
            Log::info('Transacción completada');

            // Iniciar sesión
            Auth::login($persona);
            Log::info('Usuario autenticado');

            // Redirigir con mensaje de éxito
            return redirect()->route('register.persona')->with([
                'success' => true,
                'nombre' => $persona->Nombre,
                'apellido' => $persona->Apellido,
                'correo' => $correo->Correo
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en registro', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->with('error', 'Error al registrar: ' . $e->getMessage())
                ->withInput();
        }
    }
}
