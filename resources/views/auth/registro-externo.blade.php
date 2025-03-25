<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registro de Usuario Externo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-cover bg-center bg-no-repeat text-dark"
      style="background-image: url('/images/fondo-login.jpg');">

    <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-gray-200 p-8 transform transition duration-300 hover:scale-[1.01] backdrop-filter-none">
        <h1 class="text-2xl font-bold text-center mb-6 text-primary">Registro de Usuario Externo</h1>

        <!-- Mensajes de éxito -->
        @if(session('success'))
            <div class="mb-4 p-4 border-l-4 border-green-500 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Mensajes de error -->
        @if($errors->any())
            <div class="mb-4 p-4 border-l-4 border-red-500 bg-red-100 text-red-700 rounded">
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
                <label for="nombre" class="block mb-1 font-medium text-sm text-secondary">Nombre</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    required
                    class="w-full px-4 py-2 border border-secondary rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>

            <!-- Campo: Número de Teléfono -->
            <div>
                <label for="numero_telefono" class="block mb-1 font-medium text-sm text-secondary">Número de Teléfono</label>
                <input
                    type="text"
                    id="numero_telefono"
                    name="numero_telefono"
                    value="{{ old('numero_telefono') }}"
                    required
                    class="w-full px-4 py-2 border border-secondary rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>

            <!-- Campo: Contraseña -->
            <div>
                <label for="password" class="block mb-1 font-medium text-sm text-secondary">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border border-secondary rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>

            <!-- Campo: Confirmar Contraseña -->
            <div>
                <label for="password_confirmation" class="block mb-1 font-medium text-sm text-secondary">Confirmar Contraseña</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    class="w-full px-4 py-2 border border-secondary rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>

            <!-- Campo: Foto de Referencia (opcional) -->
            <div>
                <label for="foto_referencia" class="block mb-1 font-medium text-sm text-secondary">Foto de Referencia (opcional)</label>
                <input
                    type="file"
                    id="foto_referencia"
                    name="foto_referencia"
                    class="block w-full text-sm text-gray-500
                           file:mr-4 file:py-2 file:px-4
                           file:rounded file:border-0
                           file:text-sm file:font-semibold
                           file:bg-primary file:text-white
                           hover:file:bg-secondary"
                />
            </div>

            <!-- Botón de Registro -->
            <button
                type="submit"
                class="w-full bg-primary text-white py-2 px-4 rounded-lg shadow-md hover:bg-secondary transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary"
            >
                Registrarse
            </button>
        </form>
    </div>

</body>
</html>
