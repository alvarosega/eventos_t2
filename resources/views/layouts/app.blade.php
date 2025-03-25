<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi Aplicación')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="bg-background text-dark min-h-screen flex flex-col font-sans">

    <!-- Header -->
    <header class="bg-primary text-white p-4 shadow-2xl drop-shadow-xl transform transition-all duration-300 hover:scale-[1.01]">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Botón de inicio en el nombre de la aplicación -->
            <a href="{{ route('home') }}" class="text-2xl font-bold hover:underline drop-shadow-md">
                Eventos
            </a>

            <!-- Menú de navegación -->
            <nav>
                <ul class="flex space-x-4 items-center text-sm font-medium">
                    @if (Auth::guard('externo')->check())
                        <li>
                            <a href="{{ route('inscripciones.index') }}" class="hover:underline transition hover:text-secondary">
                                Eventos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pedidos.index') }}" class="hover:underline transition hover:text-secondary">
                                Mis Pedidos
                            </a>
                        </li>
                    @endif

                    @if (Auth::guard('empleado')->check())
                        <li>
                            <a href="{{ route('pedidos.index') }}" class="hover:underline transition hover:text-secondary">
                                Gestionar Pedidos
                            </a>
                        </li>
                    @endif

                    @if (Auth::guard('empleado')->check() && Auth::user()->rol == 'superadmin')
                        <li>
                            <a href="{{ route('eventos.create') }}" class="hover:underline transition hover:text-secondary">
                                Crear Evento
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('catalogos.index') }}" class="hover:underline transition hover:text-secondary">
                                Administrar Catálogos
                            </a>
                        </li>
                    @endif

                    <!-- Botón de Cerrar Sesión -->
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded shadow-md transition duration-200 transform hover:scale-105">
                                Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contenedor principal -->
    <main class="container mx-auto flex-1 mt-10 p-4 bg-white/80 rounded-xl shadow-xl transform transition duration-300 hover:scale-[1.005]">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-4 mt-10 shadow-inner">
        <div class="container mx-auto text-sm opacity-90">
            <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>
