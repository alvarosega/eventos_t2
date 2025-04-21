<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro de Usuario Externo</title>
  <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/imagenes/favicon.png') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url('{{ Vite::asset('resources/images/imagenes/fondo-login.jpg') }}');">
  <!-- Capa de overlay con gradiente sutil -->
  <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-darkBg/60 to-accentPink/20"></div>

  <!-- Logo con efecto glow -->
  <div class="absolute top-6 left-6 z-50">
    <img src="{{ Vite::asset('resources/images/imagenes/logo.png') }}" alt="Logo" 
         class="w-14 h-14 drop-shadow-[0_0_8px_rgba(0,209,255,0.6)] hover:drop-shadow-[0_0_12px_rgba(255,45,247,0.8)] transition-all duration-300">
  </div>

  <!-- Contenedor principal -->
  <div class="relative z-40 w-full max-w-xl bg-lightCard/90 dark:bg-darkCard/90 backdrop-blur-sm rounded-xl shadow-xl border border-primary/20 dark:border-accentBlue/30 p-8 transition-all duration-500 hover:shadow-cardGlow">
    <h1 class="text-2xl font-bold text-center mb-6 text-primary dark:text-accentBlue">Registro de Usuario Externo</h1>

    <!-- Mensajes de éxito -->
    @if(session('success'))
    <div class="mb-4 p-4 border-l-4 border-success bg-success/10 text-success dark:text-success/90 rounded-lg shadow-inner">
      <div class="flex items-center">
        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
      </div>
    </div>
    @endif

    <!-- Mensajes de error -->
    @if($errors->any())
    <div class="mb-4 p-4 border-l-4 border-danger bg-danger/10 text-danger dark:text-danger/90 rounded-lg shadow-inner">
      <ul class="space-y-1">
        @foreach($errors->all() as $error)
        <li class="flex items-start">
          <svg class="h-5 w-5 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          {{ $error }}
        </li>
        @endforeach
      </ul>
    </div>
    @endif

    <!-- Formulario de registro -->
    <form method="POST" action="{{ route('registro.externo') }}" enctype="multipart/form-data" class="space-y-5">
      @csrf

      <!-- Campo: Nombre -->
      <div class="space-y-1">
        <label for="nombre" class="block font-medium text-sm text-secondary dark:text-accentBlue/90">Nombre</label>
        <div class="relative">
          <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required 
                 class="block w-full px-4 py-2.5 bg-white/80 dark:bg-gray-700/80 border border-gray-300/80 dark:border-gray-600/50 rounded-lg shadow-sm focus:ring-2 focus:ring-accentBlue/50 focus:border-accentBlue transition-all duration-200 placeholder-gray-400/70">
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Campo: Número de Teléfono -->
      <div class="space-y-1">
        <label for="numero_telefono" class="block font-medium text-sm text-secondary dark:text-accentBlue/90">Teléfono</label>
        <div class="relative">
          <input type="text" id="numero_telefono" name="numero_telefono" value="{{ old('numero_telefono') }}" required 
                 class="block w-full px-4 py-2.5 bg-white/80 dark:bg-gray-700/80 border border-gray-300/80 dark:border-gray-600/50 rounded-lg shadow-sm focus:ring-2 focus:ring-accentBlue/50 focus:border-accentBlue transition-all duration-200 placeholder-gray-400/70">
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" clip-rule="evenodd"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Campo: Contraseña -->
      <div class="space-y-1">
        <label for="password" class="block font-medium text-sm text-secondary dark:text-accentBlue/90">Contraseña</label>
        <div class="relative">
          <input type="password" id="password" name="password" required 
                 class="block w-full px-4 py-2.5 bg-white/80 dark:bg-gray-700/80 border border-gray-300/80 dark:border-gray-600/50 rounded-lg shadow-sm focus:ring-2 focus:ring-accentBlue/50 focus:border-accentBlue transition-all duration-200 placeholder-gray-400/70">
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Campo: Confirmar Contraseña -->
      <div class="space-y-1">
        <label for="password_confirmation" class="block font-medium text-sm text-secondary dark:text-accentBlue/90">Confirmar Contraseña</label>
        <div class="relative">
          <input type="password" id="password_confirmation" name="password_confirmation" required 
                 class="block w-full px-4 py-2.5 bg-white/80 dark:bg-gray-700/80 border border-gray-300/80 dark:border-gray-600/50 rounded-lg shadow-sm focus:ring-2 focus:ring-accentBlue/50 focus:border-accentBlue transition-all duration-200 placeholder-gray-400/70">
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Campo: Foto de Referencia -->
      <div class="space-y-1">
        <label for="foto_referencia" class="block font-medium text-sm text-secondary dark:text-accentBlue/90">Foto de Referencia (opcional)</label>
        <div class="relative">
          <input type="file" id="foto_referencia" name="foto_referencia" 
                 class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary file:text-white hover:file:bg-primaryLight transition-all duration-200">
        </div>
      </div>

      <!-- Botón de Registro -->
      <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-primary to-primary-dark text-white font-medium rounded-lg shadow-md hover:shadow-lg transform transition duration-300 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-accentBlue/50 focus:ring-offset-2">
        Registrarse
      </button>
    </form>
  </div>

  <!-- Botón modo oscuro -->
  <button type="button" onclick="toggleDarkMode()" 
          class="fixed bottom-6 right-6 z-50 p-3 rounded-full bg-darkCard text-accentBlue shadow-[0_0_8px_rgba(0,209,255,0.5)] hover:shadow-[0_0_12px_rgba(255,45,247,0.7)] transition-all duration-300 transform hover:scale-110">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707-.707"/>
    </svg>
  </button>

  <!-- Script para modo oscuro -->
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