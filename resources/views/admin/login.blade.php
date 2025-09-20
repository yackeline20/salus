<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión de Administrador</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            text-align: center;
            box-sizing: border-box;
        }
        .logo-container {
            margin-bottom: 2rem;
        }
        .logo {
            max-width: 150px;
        }
        h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 0.5rem;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #5a8dee;
        }
        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background-color: #A0522D;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-login:hover {
            background-color: #8C4426;
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .remember-me input {
            margin-right: 0.5rem;
        }
        .forgot-password, .register-link {
            display: block;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #5a8dee;
            text-decoration: none;
        }
        @media (max-width: 500px) {
            .login-card {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-container">
            <img src="{{ asset('images/logo_salus.jpeg') }}" alt="Salus Logo" class="logo">
        </div>
        <h2>Iniciar Sesión de Administrador</h2>
        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="remember-me">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Recordarme</label>
            </div>
            <button type="submit" class="btn-login">Iniciar Sesión</button>
            <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
        </form>
    </div>
</body>
</html>
