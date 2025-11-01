<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bitacora del Sistema')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Header/Navbar -->
    <header class="bg-white shadow">
        <nav class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-gray-800">Salus</a>
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

    <!-- Formulario de cierre de sesión -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts -->
    @yield('scripts')

</body>
</html>
