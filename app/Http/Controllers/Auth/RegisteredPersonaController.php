<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Correo;
use App\Models\Telefono;
use App\Models\Direccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegisteredPersonaController extends Controller
{
    // Muestra la vista de registro (opcional, si usas Blade)
    public function create()
    {
        return view('auth.register-persona');
    }
    /**
     * Procesa el registro de una persona con correo, teléfono y dirección.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'Nombre'           => ['required', 'string', 'max:25'],
            'Apellido'         => ['required', 'string', 'max:25'],
            'DNI'              => ['required', 'string', 'max:25', 'unique:persona,DNI'],
            'Fecha_Nacimiento' => ['required', 'date'],
            'Genero'           => ['required', Rule::in(['Masculino','Femenino'])],
            'Password'         => ['required', 'string', 'min:6'],

            'Correo'      => ['nullable', 'string', 'email', 'max:25'],
            'Tipo_correo' => ['nullable', Rule::in(['Personal','Laboral','Otro'])],

            'Numero'        => ['nullable', 'string', 'max:20'],
            'Cod_Pais'      => ['nullable', 'string', 'max:10'],
            'Tipo'          => ['nullable', Rule::in(['Fijo','Movil'])],
            'Descripcion_Tel'=> ['nullable', 'string', 'max:50'],

            'Direccion'     => ['nullable', 'string', 'max:50'],
            'Descripcion_Dir'=> ['nullable', 'string', 'max:120'],
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear persona
            $persona = Persona::create([
                'Nombre'           => $request->Nombre,
                'Apellido'         => $request->Apellido,
                'DNI'              => $request->DNI,
                'Fecha_Nacimiento' => $request->Fecha_Nacimiento,
                'Genero'           => $request->Genero,
                'Password'         => Hash::make($request->Password),
            ]);

            // 2. Correo
            if ($request->filled('Correo')) {
                Correo::create([
                    'Cod_Persona' => $persona->Cod_Persona,
                    'Correo'      => $request->Correo,
                    'Tipo_correo' => $request->Tipo_correo ?? 'Personal',
                ]);
            }

            // 3. Teléfono
            if ($request->filled('Numero')) {
                Telefono::create([
                    'Cod_Persona' => $persona->Cod_Persona,
                    'Numero'      => $request->Numero,
                    'Cod_Pais'    => $request->Cod_Pais,
                    'Tipo'        => $request->Tipo ?? 'Movil',
                    'Descripcion' => $request->Descripcion_Tel,
                ]);
            }

            // 4. Dirección
            if ($request->filled('Direccion')) {
                Direccion::create([
                    'Cod_Persona' => $persona->Cod_Persona,
                    'Direccion'   => $request->Direccion,
                    'Descripcion' => $request->Descripcion_Dir,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Persona registrada con éxito',
                'persona' => $persona
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Error al registrar persona',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}
