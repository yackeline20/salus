<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Exitoso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .success-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .success-icon {
            color: #28a745;
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .success-title {
            color: #28a745;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .success-message {
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .btn-login {
            background-color: #8B4513; /* Color café como el logo */
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-login:hover {
            background-color: #654321; /* Café más oscuro al hacer hover */
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h2 class="success-title">¡Registro Exitoso!</h2>
        <p class="success-message">
            Hola <strong>{{ $nombre }} {{ $apellido }}</strong>, tu cuenta ha sido creada exitosamente.
            <br><br>
            Ya puedes acceder a tu dashboard.
        </p>
        <a href="{{ route('dashboard') }}" class="btn-login">Ingresar al Dashboard</a>
    </div>
</body>

</html>
