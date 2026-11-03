```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="theme-color" content="#173F32">
    <meta name="description" content="Explora quintas de San Sebastián, Cusco, conoce sus servicios y solicita una reserva.">

    <title>@yield('title', 'Quintas de San Sebastián')</title>

    <!-- Fuentes tipográficas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Young+Serif&family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet: mapas interactivos -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css"
        integrity="sha512-h0K1zQ8F0j7Y4qJ7m0M5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q5Y5Q=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    >

    <!-- Estilos principales -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v=3">

    @stack('styles')
</head>

<body>

    <!-- Accesibilidad: saltar directamente al contenido -->
    <a class="skip-link" href="#contenido">
        Saltar al contenido
    </a>

    <!-- Encabezado principal -->
    <header class="site-header" id="site-header">

        <a class="logo" href="{{ route('quintas.index') }}" aria-label="Ir al inicio">
            <span class="logo-icon" aria-hidden="true">⌂</span>
            <span>Quintas de San Sebastián</span>
        </a>

        <!-- Botón del menú para dispositivos móviles -->
        <button
            type="button"
            class="nav-toggle"
            id="nav-toggle"
            aria-expanded="false"
            aria-controls="menu"
            aria-label="Abrir menú">

            <span class="barras" aria-hidden="true"></span>
        </button>

        <!-- Navegación -->
        <nav id="menu" aria-label="Navegación principal">
            <ul class="nav-list">

                <li>
                    <a
                        href="{{ route('quintas.index') }}"
                        @if(request()->routeIs('quintas.index', 'quintas.show')) aria-current="page" @endif>
                        Explorar quintas
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('quintas.reservas') }}"
                        @if(request()->routeIs('quintas.reservas', 'reservas.confirmacion')) aria-current="page" @endif>
                        Mis reservas
                    </a>
                </li>

            </ul>
        </nav>

    </header>

    <!-- Aviso informativo del proyecto -->
    <div class="aviso" role="note">
        Proyecto académico: los establecimientos, precios, horarios y ubicaciones son datos demostrativos no verificados.
    </div>

    <!-- Contenido principal -->
    <main id="contenido" tabindex="-1">

        <!-- Mensaje de operación exitosa -->
        @if(session('success'))
            <div class="ok" role="status" aria-live="polite">
                {{ session('success') }}
            </div>
        @endif

        <!-- Errores de validación -->
        @if($errors->any())
            <div class="error" role="alert">
                <strong>Revisa los siguientes datos:</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </main>

    <!-- Pie de página -->
    <footer class="site-footer">
        <div class="footer-contenido">
            <p class="footer-logo">
                Quintas de San Sebastián
            </p>

            <p>
                Descubre espacios para compartir y disfrutar en San Sebastián, Cusco.
            </p>

            <p class="footer-copy">
                © {{ date('Y') }} Quintas de San Sebastián.
                Proyecto académico. Mapa © colaboradores de OpenStreetMap.
            </p>
        </div>
    </footer>

    <!-- Leaflet JavaScript -->
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"
        crossorigin="anonymous"
        referrerpolicy="no-referrer">
    </script>

    <!-- JavaScript principal -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Scripts específicos de cada vista -->
    @stack('scripts')

</body>
</html>
```