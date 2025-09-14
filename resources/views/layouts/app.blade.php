<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reportes')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    
    <!-- Header/Navbar si existe -->
    @if(View::exists('partials.navbar'))
        @include('partials.navbar')
    @endif

    <!-- Contenido principal -->
    <main>
        @yield('content')  
    </main>

    <!-- Footer si existe -->
    @if(View::exists('partials.footer'))
        @include('partials.footer')
    @endif

    <!-- Scripts -->
    @yield('scripts')
    
</body>
</html>