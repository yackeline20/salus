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
            max-width: 400px;
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

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            font-size: 1rem;
            color: #333;
            box-sizing: border-box;
        }

        .form-group input:focus {
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
    </style>
</head>

<body>
    <div class="register-card">
        <img src="{{ asset('images/logo_salus.jpeg') }}" alt="Logo de Salus" class="logo">

        @if(session('success'))
            <div class="success-message">
                <h3 style="margin: 0 0 0.5rem 0; color: #166534;">¡Registro Exitoso!</h3>
                <p style="margin: 0;">
                    Tu cuenta <strong>{{ session('nombre_usuario') }}</strong> ha sido creada exitosamente.
                    <br><br>
                    Ya puedes iniciar sesión.
                </p>
            </div>
            <a href="{{ route('login') }}" class="button-primary">Iniciar Sesión</a>
            <a href="{{ url('/register-usuario') }}" class="button-secondary">Registrar Otra Cuenta</a>
        @else
            @if(session('error'))
                <div class="error-message">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
            @endif

            <h2 style="color: #4C342C; margin-bottom: 2rem;">Registrarse</h2>

            <form method="POST" action="{{ route('register.usuario') }}" id="registerForm">
                @csrf

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
            
            // Validación simple al enviar el formulario
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = document.getElementById('Password').value;
                    const confirmPassword = document.getElementById('Password_confirmation').value;
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Las contraseñas no coinciden. Por favor, verifica.');
                        document.getElementById('Password_confirmation').focus();
                    }
                });
            }
        });
    </script>
</body>

</html>