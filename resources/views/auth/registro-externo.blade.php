<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario Externo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Registro de Usuario Externo</div>
                    <div class="card-body">
                        <!-- Mostrar mensajes de éxito o error -->
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Formulario de registro -->
                        <form method="POST" action="{{ route('registro.externo') }}" enctype="multipart/form-data">
                            @csrf

                            <!-- Campo: Nombre -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                            </div>

                            <!-- Campo: Número de Teléfono -->
                            <div class="mb-3">
                                <label for="numero_telefono" class="form-label">Número de Teléfono</label>
                                <input type="text" class="form-control" id="numero_telefono" name="numero_telefono" value="{{ old('numero_telefono') }}" required>
                            </div>

                            <!-- Campo: Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <!-- Campo: Confirmar Contraseña -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <!-- Campo: Foto de Referencia -->
                            <div class="mb-3">
                                <label for="foto_referencia" class="form-label">Foto de Referencia (opcional)</label>
                                <input type="file" class="form-control" id="foto_referencia" name="foto_referencia">
                            </div>

                            <!-- Botón de Registro -->
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>