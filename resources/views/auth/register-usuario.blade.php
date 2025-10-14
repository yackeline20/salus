<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #F8F4F0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }

        .register-card {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 450px; /* Aumentamos el ancho para los nuevos campos */
            text-align: center;
        }

        .logo {
            width: 150px;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4C342C;
            font-weight: 600;
        }

        /* Aplica el estilo de input también al select para uniformidad */
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            font-size: 1rem;
            color: #333;
            box-sizing: border-box;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #4C342C;
            box-shadow: 0 0 0 2px rgba(76, 52, 44, 0.2);
        }

        .button-primary {
            width: 100%;
            padding: 0.75rem;
            background-color: #4C342C;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .button-primary:hover {
            background-color: #6a493f;
            color: #fff;
            text-decoration: none;
        }

        .error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .success-message {
            background-color: #dcfce7;
            border: 1px solid #16a34a;
            color: #166534;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .error-message {
            background-color: #fef2f2;
            border: 1px solid #dc2626;
            color: #dc2626;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .button-secondary {
            width: 100%;
            padding: 0.75rem;
            background-color: #6b7280;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 1rem;
            text-decoration: none;
            display: inline-block;
        }

        .button-secondary:hover {
            background-color: #4b5563;
            color: #fff;
            text-decoration: none;
        }

        .section-title {
            color: #4C342C;
            margin-top: 2rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #ddd;
            padding-bottom: 0.5rem;
            text-align: left;
            font-size: 1.25rem;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="register-card">
        <!-- Reemplaza la URL de la imagen si es necesario -->
        <!-- NOTA: La ruta de asset('images/logo_salus.jpeg') solo funcionará si tienes el archivo en public/images/ -->
        <img src="https://placehold.co/150x50/4C342C/ffffff?text=LOGO" alt="Logo de Salus" class="logo">

        @if(session('success'))
            <!-- ESTADO 2: REGISTRO EXITOSO -->
            <div class="success-message">
                <h3 style="margin: 0 0 0.5rem 0; color: #166534;">¡Registro Exitoso!</h3>
                <p style="margin: 0;">
                    Tu cuenta **{{ session('nombre_usuario') }}** ha sido creada exitosamente.
                    <br><br>
                    Ya puedes iniciar sesión.
                </p>
            </div>
            <a href="{{ route('login') }}" class="button-primary">Iniciar Sesión</a>
            <a href="{{ url('/register-usuario') }}" class="button-secondary">Registrar Otra Cuenta</a>
        @else
            <!-- ESTADO 1: FORMULARIO DE REGISTRO ACTIVO -->
            @if(session('error'))
                <div class="error-message">
                    **Error:** {{ session('error') }}
                </div>
            @endif

            <h2 style="color: #4C342C; margin-bottom: 2rem;">Registro de Usuario</h2>

            <form method="POST" action="{{ route('register.usuario') }}" id="registerForm">
                @csrf

                <!-- ============================================= -->
                <!-- DATOS PERSONALES (Nuevos campos para tabla 'persona') -->
                <!-- ============================================= -->
                <div class="section-title">Datos Personales</div>

                <div class="form-group">
                    <label for="Nombre_Persona">Nombre</label>
                    <input type="text" id="Nombre_Persona" name="Nombre_Persona" value="{{ old('Nombre_Persona') }}"
                        required maxlength="25">
                    @error('Nombre_Persona')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="Apellido">Apellido</label>
                    <input type="text" id="Apellido" name="Apellido" value="{{ old('Apellido') }}"
                        required maxlength="25">
                    @error('Apellido')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="DNI">DNI / Identidad</label>
                    <input type="text" id="DNI" name="DNI" value="{{ old('DNI') }}"
                        required maxlength="25">
                    @error('DNI')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="Fecha_Nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="Fecha_Nacimiento" name="Fecha_Nacimiento" value="{{ old('Fecha_Nacimiento') }}"
                        required>
                    @error('Fecha_Nacimiento')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="Genero">Género</label>
                    <select id="Genero" name="Genero" required>
                        <option value="" disabled {{ old('Genero') ? '' : 'selected' }}>Selecciona el género</option>
                        <option value="Femenino" {{ old('Genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                        <option value="Masculino" {{ old('Genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    </select>
                    @error('Genero')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ============================================= -->
                <!-- DATOS DE CUENTA (Campos para tablas 'usuarios' y 'correo') -->
                <!-- ============================================= -->
                <div class="section-title">Datos de Cuenta</div>

                <div class="form-group">
                    <label for="Nombre_Usuario">Nombre de Usuario</label>
                    <input type="text" id="Nombre_Usuario" name="Nombre_Usuario" value="{{ old('Nombre_Usuario') }}"
                        required autofocus maxlength="50">
                    @error('Nombre_Usuario')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="Correo">Correo Electrónico</label>
                    <input type="email" id="Correo" name="Correo" value="{{ old('Correo') }}"
                        required maxlength="255">
                    @error('Correo')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- CAMPO DE SELECCIÓN DE ROL -->
                <div class="form-group">
                    <label for="Cod_Rol">Rol de Usuario</label>
                    <select id="Cod_Rol" name="Cod_Rol" required>
                        <option value="" disabled {{ old('Cod_Rol') ? '' : 'selected' }}>Selecciona un rol</option>

                        <!-- NOTA: La variable $roles debe ser pasada desde el controlador -->
                        @if(isset($roles) && is_array($roles) || (isset($roles) && $roles instanceof \Illuminate\Support\Collection))
                            @foreach ($roles as $rol)
                                <option
                                    value="{{ $rol->Cod_Rol }}"
                                    {{ old('Cod_Rol') == $rol->Cod_Rol ? 'selected' : '' }}
                                >
                                    {{ $rol->Nombre_Rol }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    @error('Cod_Rol')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <!-- FIN CAMPO ROL -->

                <div class="form-group">
                    <label for="Password">Contraseña</label>
                    <input type="password" id="Password" name="Password" required minlength="8">
                    @error('Password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="Password_confirmation">Confirmar Contraseña</label>
                    <input type="password" id="Password_confirmation" name="Password_confirmation" required minlength="8">
                    @error('Password_confirmation')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="button-primary">Registrarse</button>
            </form>

            <p style="margin-top: 1rem; font-size: 0.875rem;">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}" style="color: #4C342C; text-decoration: none;">Inicia
                    sesión aquí</a>
            </p>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');

            // Mantenemos la validación de cliente para mejorar la UX.
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = document.getElementById('Password').value;
                    const confirmPassword = document.getElementById('Password_confirmation').value;

                    if (password !== confirmPassword) {
                        e.preventDefault();

                        // Buscamos un contenedor de errores o creamos uno para mostrar el mensaje
                        let errorContainer = document.querySelector('.error-message');
                        if (!errorContainer) {
                             errorContainer = document.createElement('div');
                             errorContainer.className = 'error-message';
                             form.parentNode.insertBefore(errorContainer, form);
                        }

                        errorContainer.innerHTML = '<strong>Error de Contraseña:</strong> Las contraseñas no coinciden. Por favor, verifica.';
                        errorContainer.style.display = 'block';

                        document.getElementById('Password_confirmation').focus();
                    }
                });
            }
        });
    </script>
</body>

</html>
