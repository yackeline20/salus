<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario; // Asegúrate de que este sea el nombre correcto de tu modelo
use App\Models\Correo; // Asegúrate de que este sea el nombre correcto de tu modelo

class AdminController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión para el administrador.
     * Esta es la forma estándar de servir una vista de formulario desde un controlador.
     */
    public function showAdminLoginForm()
    {
        // Retorna la vista con el formulario de inicio de sesión.
        // CORREGIDO: Ahora busca en la carpeta 'admin' -> 'admin/adminlogin.blade.php'
        return view('admin.adminlogin');
    }

    /**
     * Lógica para el formulario de Iniciar Sesión (Empleados y Clientes).
     * Maneja la autenticación POST, buscando credenciales en 'correo' y 'usuarios'.
     */
    public function login(Request $request)
    {
        // 1. Validar solo los campos 'email' y 'password'
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $inputEmail = $credentials['email'];
        $inputPassword = $credentials['password'];

        // 2. Buscar el Cod_Persona asociado al correo en la tabla 'correo'.
        $correo = Correo::where('Correo', $inputEmail)->first();

        if (!$correo) {
            return back()->withErrors([
                'email' => 'El correo proporcionado no coincide con ningún registro.',
            ]);
        }

        // 3. Buscar el Usuario en la tabla 'usuarios' usando el Cod_Persona
        // y asegurando que esté activo.
        $user = Usuario::where('Cod_Persona', $correo->Cod_Persona)
                         ->where('Indicador_Usuario_Activo', '1')
                         ->first();

        if ($user) {
            // 4. Verificar la contraseña de texto plano contra el hash de la BD
            if (Hash::check($inputPassword, $user->Password)) {

                // Autenticación exitosa
                Auth::login($user);
                $request->session()->regenerate();

                // 5. Redirección según el rol: El Administrador tiene Cod_Rol = 1.
                if ($user->Cod_Rol === 1) {
                    return redirect()->route('dashboard');
                }

                // Redirección por defecto para Empleados/Clientes
                return redirect()->route('dashboard');

            }
        }

        // Si la contraseña no coincide o el usuario no fue encontrado/está inactivo
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }


    /**
     * Maneja el login automático (solo para TESTING/DEMO) del Administrador
     * desde el botón dedicado en la landing page.
     * * Requisito: Correo='carolcalderon@gmail.com' y Pass='123456789' y Cod_Rol=1.
     */
    public function adminLoginDemo(Request $request)
    {
        // 1. Definir las credenciales fijas y el Rol.
        $ADMIN_EMAIL = 'carolcalderon@gmail.com';
        $ADMIN_PASSWORD = '123456789';
        $ADMIN_ROLE_ID = 1; // Cod_Rol del Administrador

        // 2. Buscar el Cod_Persona asociado al correo fijo en la tabla 'correo'.
        $correo = Correo::where('Correo', $ADMIN_EMAIL)->first();

        if (!$correo) {
            return back()->withErrors(['admin' => 'Error de autenticación: El correo fijo no se encuentra registrado.']);
        }

        // 3. Usar el Cod_Persona para buscar el usuario en la tabla 'usuarios'.
        $user = Usuario::where('Cod_Persona', $correo->Cod_Persona)
                         ->where('Cod_Rol', $ADMIN_ROLE_ID)
                         ->where('Indicador_Usuario_Activo', '1')
                         ->first();

        if ($user) {
            // 4. Verificar la contraseña de texto plano con el hash guardado en $user->Password.
            if (Hash::check($ADMIN_PASSWORD, $user->Password)) {

                // 5. Autenticación exitosa.
                Auth::login($user);
                $request->session()->regenerate();

                // 6. Redirección
                return redirect()->route('dashboard'); // Debe llevarte a la ruta 'dashboard'
            }
        }

        // Si falló la búsqueda del usuario (no es Admin, no está activo) o la contraseña no coincide.
        return back()->withErrors(['admin' => 'Autenticación automática fallida. Las credenciales fijas no son válidas o el usuario no es Administrador.']);
    }
}


