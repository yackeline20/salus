<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reportes')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Estilos personalizados para el botón de logout con !important -->
    <style>
        .logout-button {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 10px 20px !important;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f) !important;
            color: white !important;
            border: none !important;
            border-radius: 12px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(238, 90, 111, 0.25) !important;
            text-decoration: none !important;
            margin: 0 !important;
            width: auto !important;
            text-align: center !important;
        }

        .logout-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 25px rgba(238, 90, 111, 0.35) !important;
            background: linear-gradient(135deg, #ff5252, #e73c7e) !important;
            color: white !important;
        }

        .logout-button:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 10px rgba(238, 90, 111, 0.25) !important;
        }

        /* Icono de power */
        .logout-button::before {
            content: '⏻' !important;
            font-size: 16px !important;
            font-weight: bold !important;
            margin-right: 4px !important;
        }
        
        /* Asegurarse de que cualquier formulario padre no interfiera */
        form .logout-button,
        .logout-button[type="submit"],
        button.logout-button {
            all: unset !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 10px 20px !important;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f) !important;
            color: white !important;
            border-radius: 12px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(238, 90, 111, 0.25) !important;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header/Navbar -->
    <header class="bg-white shadow">
        <nav class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-gray-800">Salus</a>
            </div>
            
            <!-- Botón de Logout visible -->
            <div class="flex items-center gap-4">
                @auth
                    <span class="text-gray-600">Hola, {{ Auth::user()->name ?? 'alma' }}</span>
                    <button type="button" 
                            class="logout-button" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Log Out
                    </button>
                @endauth
            </div>
        </nav>
    </header>

    <!-- Contenido principal -->
    <main class="container mx-auto p-4">
        @yield('content')
    </main>

    <!-- Footer si existe -->
    @if(View::exists('partials.footer'))
        @include('partials.footer')
    @endif

    <!-- Formulario de cierre de sesión (oculto) -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts -->
    @yield('scripts')

</body>
</html>