<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión</title>
  <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/imagenes/favicon.png') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center p-4 relative bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url('{{ Vite::asset('resources/images/imagenes/fondo-login.jpg') }}');">
  <!-- Capa de overlay con gradiente sutil -->
  <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-darkBg/60 to-accentPink/20"></div>
  
  <!-- Logo con efecto glow consistente -->
  <div class="absolute top-6 left-6 z-50">
    <img src="{{ Vite::asset('resources/images/imagenes/logo.png') }}" alt="Logo" 
         class="w-14 h-14 drop-shadow-[0_0_8px_rgba(0,209,255,0.6)] hover:drop-shadow-[0_0_12px_rgba(255,45,247,0.8)] transition-all duration-300">
  </div>

  <!-- Botón modo oscuro con colores temáticos -->
  <button type="button" onclick="toggleDarkMode()" 
          class="fixed bottom-6 right-6 z-50 p-3 rounded-full bg-darkCard text-accentBlue shadow-[0_0_8px_rgba(0,209,255,0.5)] hover:shadow-[0_0_12px_rgba(255,45,247,0.7)] transition-all duration-300 transform hover:scale-110">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707-.707" />
    </svg>
  </button>

  <!-- Contenedor principal con estilos coherentes -->
  <div class="relative z-40 w-full max-w-md bg-lightCard/90 dark:bg-darkCard/90 backdrop-blur-sm shadow-xl rounded-xl border border-primary/20 dark:border-accentBlue/30 overflow-hidden transition-all duration-500 hover:shadow-cardGlow">
    <!-- Header con gradiente temático -->
    <div class="bg-gradient-to-r from-primary to-primary-dark p-6">
      <h2 class="text-2xl font-bold text-center text-white tracking-wide">
        <span class="inline-block transform transition duration-300 hover:scale-[1.02]">INICIAR SESIÓN</span>
      </h2>
    </div>

    <!-- Cuerpo del formulario -->
    <div class="p-6 space-y-5">
      @if($errors->any())
      <div class="bg-danger/10 border border-danger/30 text-danger dark:text-danger/90 px-4 py-3 rounded-lg shadow-inner">
        <ul class="space-y-1 text-sm">
          @foreach($errors->all() as $error)
          <li class="flex items-start">
            <svg class="h-4 w-4 mt-0.5 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            {{ $error }}
          </li>
          @endforeach
        </ul>
      </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        
        <!-- Campo Identificador -->
        <div class="space-y-1">
          <label for="identificador" class="block text-sm font-medium text-secondary dark:text-accentBlue/90">Teléfono o Legajo</label>
          <div class="relative">
            <input type="text" id="identificador" name="identificador" required
              class="block w-full px-4 py-2.5 bg-white/80 dark:bg-gray-700/80 border border-gray-300/80 dark:border-gray-600/50 rounded-lg shadow-sm focus:ring-2 focus:ring-accentBlue/50 focus:border-accentBlue transition-all duration-200 placeholder-gray-400/70 dark:placeholder-gray-400/50"
              placeholder="Ingrese su identificación">
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
              </svg>
            </div>
          </div>
        </div>
        
        <!-- Campo Contraseña -->
        <div class="space-y-1">
          <label for="password" class="block text-sm font-medium text-secondary dark:text-accentBlue/90">Contraseña</label>
          <div class="relative">
            <input type="password" id="password" name="password" required
              class="block w-full px-4 py-2.5 bg-white/80 dark:bg-gray-700/80 border border-gray-300/80 dark:border-gray-600/50 rounded-lg shadow-sm focus:ring-2 focus:ring-accentBlue/50 focus:border-accentBlue transition-all duration-200 placeholder-gray-400/70 dark:placeholder-gray-400/50"
              placeholder="••••••••">
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
              </svg>
            </div>
          </div>
        </div>
        
        <!-- Botón de Submit con gradiente temático -->
        <button type="submit" 
          class="w-full py-3 px-4 bg-gradient-to-r from-primary to-primary-dark text-white font-medium rounded-lg shadow-md hover:shadow-lg transform transition duration-300 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-accentBlue/50 focus:ring-offset-2">
          Acceder al Sistema
        </button>
      </form>

      <!-- Enlace de registro con colores coherentes -->
      <div class="pt-2 text-center">
        <a href="{{ route('registro.externo') }}" class="text-sm font-medium text-primary dark:text-accentBlue hover:text-secondary dark:hover:text-accentPink transition-colors duration-300 inline-flex items-center">
          <span>Registro para usuarios externos</span>
          <svg class="h-4 w-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <!-- Script optimizado para modo oscuro -->
  <script>
    function toggleDarkMode() {
      const html = document.documentElement;
      html.classList.toggle('dark');
      localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
      
      // Feedback visual sutil
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