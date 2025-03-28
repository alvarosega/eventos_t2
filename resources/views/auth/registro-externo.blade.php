<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro de Usuario Externo</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative bg-cover bg-center bg-no-repeat" style="background-image: url('/images/fondo-login.jpg');">
  <!-- Capa de fondo para efecto ciberpunk -->
  <div class="absolute inset-0 bg-black opacity-60 mix-blend-multiply"></div>

  <!-- Logo flotante -->
  <div class="absolute top-4 left-4 z-50">
    <img src="/images/logo.png" alt="Logo" class="w-14 h-14 drop-shadow-2xl">
  </div>

  <!-- Contenedor principal con efecto glass y 3D sutil -->
  <div class="relative z-40 w-full max-w-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-600 p-8 transform transition-all hover:scale-105 duration-300">
    <h1 class="text-2xl font-bold text-center mb-6 text-primary">Registro de Usuario Externo</h1>

    <!-- Mensajes de éxito -->
    @if(session('success'))
    <div class="mb-4 p-4 border-l-4 border-green-500 bg-green-100 text-green-700 rounded shadow-inner">
      {{ session('success') }}
    </div>
    @endif

    <!-- Mensajes de error -->
    @if($errors->any())
    <div class="mb-4 p-4 border-l-4 border-red-500 bg-red-100 dark:bg-red-200 text-red-700 rounded shadow-inner">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <!-- Formulario de registro -->
    <form method="POST" action="{{ route('registro.externo') }}" enctype="multipart/form-data" class="space-y-5">
      @csrf

      <!-- Campo: Nombre -->
      <div>
        <label for="nombre" class="block mb-1 font-medium text-sm text-secondary dark:text-gray-200">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required class="w-full px-4 py-2 border border-secondary dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 transition-all" />
      </div>

      <!-- Campo: Número de Teléfono -->
      <div>
        <label for="numero_telefono" class="block mb-1 font-medium text-sm text-secondary dark:text-gray-200">Número de Teléfono</label>
        <input type="text" id="numero_telefono" name="numero_telefono" value="{{ old('numero_telefono') }}" required class="w-full px-4 py-2 border border-secondary dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 transition-all" />
      </div>

      <!-- Campo: Contraseña -->
      <div>
        <label for="password" class="block mb-1 font-medium text-sm text-secondary dark:text-gray-200">Contraseña</label>
        <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-secondary dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 transition-all" />
      </div>

      <!-- Campo: Confirmar Contraseña -->
      <div>
        <label for="password_confirmation" class="block mb-1 font-medium text-sm text-secondary dark:text-gray-200">Confirmar Contraseña</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border border-secondary dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 transition-all" />
      </div>

      <!-- Campo: Foto de Referencia (opcional) -->
      <div>
        <label for="foto_referencia" class="block mb-1 font-medium text-sm text-secondary dark:text-gray-200">Foto de Referencia (opcional)</label>
        <input type="file" id="foto_referencia" name="foto_referencia" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-secondary transition-all" />
      </div>

      <!-- Botón de Registro -->
      <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-lg shadow-lg hover:scale-105 hover:bg-secondary transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary">
        Registrarse
      </button>
    </form>
  </div>

  <!-- Botón flotante modo oscuro -->
  <button type="button" onclick="toggleDarkMode()" class="fixed bottom-4 right-4 z-50 p-3 rounded-full bg-primary text-white shadow-[0_0_10px_#8b5cf6,0_0_20px_#06b6d4] transition-transform transform hover:scale-110">
    <!-- Ícono (sol/luna) -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707-.707" />
    </svg>
  </button>

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
