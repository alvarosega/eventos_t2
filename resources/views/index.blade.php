<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Página Principal</div>

                    <div class="card-body">
                        <!-- Mostrar mensaje de bienvenida según el tipo de usuario -->
                        @if(auth()->guard('externo')->check())
                            <div class="alert alert-success">
                                ¡Bienvenido, {{ auth()->guard('externo')->user()->nombre }}! (Usuario Externo)
                            </div>
                        @elseif(auth()->guard('empleado')->check())
                            <div class="alert alert-info">
                                ¡Bienvenido, {{ auth()->guard('empleado')->user()->nombre_completo }}! (Empleado - Rol: {{ auth()->guard('empleado')->user()->rol }})
                            </div>
                        @endif

                        <p>Esta es la página principal de la aplicación.</p>

                        <!-- Botón para cerrar sesión -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>