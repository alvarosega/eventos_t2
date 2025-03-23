<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar Sesión</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-dark min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Encabezado -->
            <div class="bg-primary p-6">
                <h2 class="text-2xl font-bold text-white text-center">Iniciar Sesión</h2>
            </div>

            <!-- Cuerpo -->
            <div class="p-6">
                <!-- Mensajes de error -->
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
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
                        <label for="identificador" class="block text-sm font-medium text-secondary">
                            Número de Teléfono o Legajo
                        </label>
                        <input
                            type="text"
                            id="identificador"
                            name="identificador"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-secondary rounded-md shadow-sm
                                   focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary"
                        />
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-secondary">
                            Contraseña
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="mt-1 block w-full px-3 py-2 border border-secondary rounded-md shadow-sm
                                   focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-secondary transition
                               focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-1"
                    >
                        Iniciar Sesión
                    </button>
                </form>

                <!-- Enlace de registro -->
                <div class="mt-4 text-center">
                    <a href="{{ route('registro.externo') }}" class="text-primary hover:text-secondary text-sm">
                        Registrarse como usuario externo
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
