<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative bg-cover bg-center bg-no-repeat" style="background-image: url('/images/fondo-login.jpg');">
  <!-- Capa de fondo para efecto ciberpunk -->
  <div class="absolute inset-0 bg-black opacity-60 mix-blend-multiply"></div>

  <!-- Logo flotante -->
  <div class="absolute top-4 left-4 z-50">
    <img src="/images/logo.png" alt="Logo" class="w-14 h-14 drop-shadow-2xl">
  </div>

  <!-- Botón flotante para cambiar modo oscuro/claro -->
  <button type="button" onclick="toggleDarkMode()" class="fixed bottom-4 right-4 z-50 p-3 rounded-full bg-primary text-white shadow-[0_0_10px_#8b5cf6,0_0_20px_#06b6d4] transition-transform transform hover:scale-110">
    <!-- Ícono de sol/luna -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707-.707" />
    </svg>
  </button>

  <!-- Contenedor principal con efecto glass y 3D sutil -->
  <div class="relative z-40 w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-md shadow-2xl rounded-2xl border border-gray-300 dark:border-gray-700 transform transition-all hover:scale-105 duration-300">
    <!-- Encabezado -->
    <div class="bg-primary p-6 rounded-t-2xl">
      <h2 class="text-2xl font-bold text-white text-center">Iniciar Sesión</h2>
    </div>
    <!-- Cuerpo -->
    <div class="p-6 space-y-4">
      <!-- Mensajes de error -->
      @if($errors->any())
      <div class="bg-red-100 dark:bg-red-200 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 shadow-inner">
        <ul class="list-disc list-inside text-sm">
          @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <!-- Formulario -->
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
          <label for="identificador" class="block text-sm font-medium text-secondary dark:text-gray-200">Número de Teléfono o Legajo</label>
          <input type="text" id="identificador" name="identificador" required class="mt-1 block w-full px-4 py-2 border border-secondary dark:border-gray-600 rounded-md shadow-md dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
        </div>
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-secondary dark:text-gray-200">Contraseña</label>
          <input type="password" id="password" name="password" required class="mt-1 block w-full px-4 py-2 border border-secondary dark:border-gray-600 rounded-md shadow-md dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
        </div>
        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md shadow-lg hover:scale-105 transition-transform hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-1">
          Iniciar Sesión
        </button>
      </form>

      <!-- Enlace de registro -->
      <div class="mt-4 text-center">
        <a href="{{ route('registro.externo') }}" class="text-primary hover:text-secondary text-sm">Registrarse como usuario externo</a>
      </div>
    </div>
  </div>

  <!-- Script para modo oscuro -->
  <script>
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
