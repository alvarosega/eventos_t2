<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Iniciar Sesión</div>
                    <div class="card-body">
                        <!-- Mostrar mensajes de error -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Formulario de login -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="identificador" class="form-label">Número de Teléfono o Legajo</label>
                                <input type="text" class="form-control" id="identificador" name="identificador" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                        </form>

                        <!-- Enlace al registro -->
                        <div class="mt-3">
                            <a href="{{ route('registro.externo') }}">Registrarse como usuario externo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>