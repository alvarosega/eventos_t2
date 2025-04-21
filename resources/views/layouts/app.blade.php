<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/imagenes/favicon.png') }}">
  <title>@yield('title', 'Mi Aplicación')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @yield('styles')
</head>
<body class="bg-lightBg text-lightText dark:bg-darkBg dark:text-darkText min-h-screen flex flex-col font-sans relative">
  <!-- Header con gradiente y efectos -->
  <header class="bg-gradient-to-r from-primary to-primary-dark text-white p-4 shadow-lg backdrop-blur-xs sticky top-0 z-40">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
      <!-- Logo y nombre -->
      <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
        <img src="{{ Vite::asset('resources/images/imagenes/logo.png') }}" alt="Logo" class="w-8 h-8 drop-shadow-[0_0_6px_rgba(0,209,255,0.6)]">
        <span class="text-2xl font-bold group-hover:text-accentBlue transition-colors duration-300">
          Eventos
        </span>
      </a>

      <!-- Menú de navegación -->
      <nav>
        <ul class="flex flex-wrap items-center gap-4 text-sm font-medium">
          @if (Auth::guard('externo')->check())
            <li>
              <a href="{{ route('inscripciones.index') }}" 
                 class="px-3 py-1 rounded-lg hover:bg-white/10 hover:text-accentBlue transition-colors duration-200 flex items-center gap-1">
                <i class="fas fa-calendar-alt text-sm"></i>
                <span>Eventos</span>
              </a>
            </li>
            <li>
              <a href="{{ route('pedidos.index') }}" 
                 class="px-3 py-1 rounded-lg hover:bg-white/10 hover:text-accentBlue transition-colors duration-200 flex items-center gap-1">
                <i class="fas fa-clipboard-list text-sm"></i>
                <span>Mis Pedidos</span>
              </a>
            </li>
          @endif

          @if (Auth::guard('empleado')->check() && in_array(Auth::user()->rol, ['superadmin', 'master','admin']))
            <li>
              <a href="{{ route('eventos.admin') }}" 
                 class="px-3 py-1 rounded-lg hover:bg-white/10 hover:text-accentBlue transition-colors duration-200 flex items-center gap-1">
                <i class="fas fa-tasks text-sm"></i>
                <span>Eventos</span>
              </a>
            </li>
            <li>
              <a href="{{ route('catalogos.index') }}" 
                 class="px-3 py-1 rounded-lg hover:bg-white/10 hover:text-accentBlue transition-colors duration-200 flex items-center gap-1">
                <i class="fas fa-boxes text-sm"></i>
                <span>Catálogos</span>
              </a>
            </li>
          @endif

          @if (Auth::guard('empleado')->check() && in_array(Auth::user()->rol, ['superadmin', 'master']))
            <li>
              <a href="{{ route('eventos.select-type') }}" 
                 class="bg-accentBlue/90 hover:bg-accentBlue text-white px-3 py-1.5 rounded-lg shadow-md transition-all duration-200 flex items-center gap-1">
                <i class="fas fa-plus text-sm"></i>
                <span>Nuevo Evento</span>
              </a>
            </li>
          @endif

          <!-- Botón de Cerrar Sesión -->
          <li>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" 
                      class="bg-danger/90 hover:bg-danger text-white px-3 py-1.5 rounded-lg shadow-md transition-all duration-200 flex items-center gap-1">
                <i class="fas fa-sign-out-alt text-sm"></i>
                <span>Cerrar Sesión</span>
              </button>
            </form>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Contenedor principal -->
  <main class="container mx-auto flex-1 my-6 px-4 sm:px-6">
    <div class="bg-lightCard/80 dark:bg-darkCard/80 rounded-xl shadow-lg backdrop-blur-sm border border-primary/20 dark:border-accentBlue/30 p-6 transition-all duration-300">
      @yield('content')
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-lightCard dark:bg-darkCard border-t border-primary/20 dark:border-accentBlue/30 py-4 mt-6">
    <div class="container mx-auto text-center text-sm opacity-80">
      <p>&copy; {{ date('Y') }} Sistema de Eventos. Todos los derechos reservados.</p>
    </div>
  </footer>

  <!-- Botón modo oscuro mejorado -->
  <button type="button" onclick="toggleDarkMode()" 
          class="fixed bottom-6 right-6 p-3 rounded-full bg-darkCard text-accentBlue shadow-[0_0_8px_rgba(0,209,255,0.5)] hover:shadow-[0_0_12px_rgba(255,45,247,0.7)] transition-all duration-300 transform hover:scale-110 z-50">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707-.707"/>
    </svg>
  </button>

  <!-- Script para modo oscuro optimizado -->
  <script>
    function toggleDarkMode() {
      const html = document.documentElement;
      html.classList.toggle('dark');
      localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
      
      // Feedback visual
      const button = document.querySelector('[onclick="toggleDarkMode()"]');
      button.classList.add('animate-pulse');
      setTimeout(() => button.classList.remove('animate-pulse'), 300);
    }

    // Inicialización del tema
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  </script>
</body>
</html>