<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mi Aplicación')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    {{-- Sección para estilos adicionales --}}
    @yield('styles')
</head>
<body>

    {{-- Header --}}
    <header class="bg-primary text-white p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>Mi Aplicación</h1>
            {{-- Menú de navegación --}}
            <nav>
                <ul class="nav">
                    @if (Auth::guard('externo')->check())
                        {{-- Usuario Externo --}}
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('inscripciones.index') }}">Eventos</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('pedidos.index') }}">Mis Pedidos</a></li>
                    @endif

                    @if (Auth::guard('empleado')->check())
                        {{-- Empleado (Admin/Superadmin) --}}
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('pedidos.index') }}">Gestionar Pedidos</a></li>
                    @endif

                    @if (Auth::guard('empleado')->check() && Auth::user()->rol == 'superadmin')
                        {{-- Solo Superadmin --}}
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('eventos.create') }}">Crear Evento</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('catalogos.index') }}">Administrar Catálogos</a></li>
                    @endif

                    {{-- Botón de Cerrar Sesión --}}
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    {{-- Contenedor principal --}}
    <div class="container mt-5">
        @yield('content')
    </div>

    {{-- Footer --}}
    <footer class="bg-light text-center p-3 mt-5">
        <div class="container">
            <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
