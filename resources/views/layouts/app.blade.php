<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi Aplicación')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="bg-background text-dark min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-primary text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Botón de inicio en el nombre de la aplicación -->
            <a href="{{ route('home') }}" class="text-xl font-bold hover:underline">
                Mi Aplicación
            </a>

            <!-- Menú de navegación -->
            <nav>
                <ul class="flex space-x-4">
                    @if (Auth::guard('externo')->check())
                        <li>
                            <a href="{{ route('inscripciones.index') }}" class="hover:underline">
                                Eventos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pedidos.index') }}" class="hover:underline">
                                Mis Pedidos
                            </a>
                        </li>
                    @endif

                    @if (Auth::guard('empleado')->check())
                        <li>
                            <a href="{{ route('pedidos.index') }}" class="hover:underline">
                                Gestionar Pedidos
                            </a>
                        </li>
                    @endif

                    @if (Auth::guard('empleado')->check() && Auth::user()->rol == 'superadmin')
                        <li>
                            <a href="{{ route('eventos.create') }}" class="hover:underline">
                                Crear Evento
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('catalogos.index') }}" class="hover:underline">
                                Administrar Catálogos
                            </a>
                        </li>
                    @endif

                    <!-- Botón de Cerrar Sesión -->
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded">
                                Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contenedor principal -->
    <main class="container mx-auto flex-1 mt-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-4 mt-8">
        <div class="container mx-auto">
            <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>
