  <!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Clínica Salus</title>
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

        .login-card {
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
        }

        .error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
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

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .remember-forgot label {
            display: flex;
            align-items: center;
            color: #4C342C;
        }

        .remember-forgot input[type="checkbox"] {
            margin-right: 0.5rem;
        }

        .remember-forgot a {
            color: #4C342C;
            text-decoration: none;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .help-text {
            font-size: 0.75rem;
            color: #666;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <img src="{{ asset('images/logo_salus.jpeg') }}" alt="Logo de Salus" class="logo">

        <h2 style="color: #4C342C; margin-bottom: 2rem;">Iniciar Sesión</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="login">Usuario o Correo Electrónico</label>
                <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus>
                @error('login')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="display: flex; align-items: center; justify-content: flex-start;">
                <input type="checkbox" name="remember" id="remember" style="width: auto; margin-right: 0.5rem;">
                <label for="remember" style="margin: 0; font-weight: 400;">Recordarme</label>
            </div>

            <button type="submit" class="button-primary">Iniciar Sesión</button>

            <p style="margin-top: 1rem; font-size: 0.875rem;">
                ¿No tienes cuenta? <a href="{{ route('register.usuario') }}"
                    style="color: #4C342C; text-decoration: none;">Regístrate aquí</a>
            </p>
        </form>
    </div>
</body>

</html>