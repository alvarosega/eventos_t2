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
<body 
  class="bg-lightBg text-lightText dark:bg-darkBg dark:text-darkText 
         min-h-screen flex flex-col font-sans relative"
>
    <!-- Header -->
    <header class="bg-primary text-white p-4 shadow-2xl drop-shadow-xl 
                   transform transition-all duration-300 hover:scale-[1.01]">
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

                    @if (Auth::guard('empleado')->check() && Auth::user()->rol == 'superadmin')
                        <li>
                            <a href="{{ route('eventos.select-type') }}" class="hover:underline transition hover:text-secondary">
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
                                class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded shadow-md 
                                       transition duration-200 transform hover:scale-105">
                                Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contenedor principal -->
    <main class="container mx-auto flex-1 mt-10 p-4 bg-lightCard/80 dark:bg-darkCard/80 
                 rounded-xl shadow-xl transform transition duration-300 hover:scale-[1.005]">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-lightBg dark:bg-darkBg text-lightText dark:text-darkText 
                  text-center p-4 mt-10 shadow-inner">
        <div class="container mx-auto text-sm opacity-90">
            <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Botón flotante para cambiar modo oscuro/claro -->
    <button 
      type="button"
      class="fixed bottom-4 right-4 p-3 rounded-full bg-primary text-white shadow-lg hover:shadow-xl 
             transition transform hover:scale-105 z-50"
      onclick="toggleDarkMode()"
    >
      <!-- Ícono de sol/luna -->
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707
             M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707
             M6.343 17.657l-.707-.707" 
        />
      </svg>
    </button>

    <!-- Script para modo oscuro -->
    <script>
      // Al cargar la página, aplicamos la preferencia guardada
      if (localStorage.theme === 'dark') {
        document.documentElement.classList.add('dark');
      }

      function toggleDarkMode() {
        const html = document.documentElement;
        if (html.classList.contains('dark')) {
          html.classList.remove('dark');
          localStorage.theme = 'light';
        } else {
          html.classList.add('dark');
          localStorage.theme = 'dark';
        }
      }
    </script>
</body>
</html>
