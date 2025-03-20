@extends('layouts.app')

@section('title', 'Pantalla Principal')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h2 class="mb-4">Bienvenido, {{ $usuario->nombre_completo ?? $usuario->nombre }} <small class="text-muted">({{ $usuario->rol }})</small></h2>

        {{-- Tarjeta de información del usuario --}}
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Información del Usuario</h3>
            </div>
            <div class="card-body">
                @if ($usuario->rol == 'externo')
                    <p><strong>Nombre:</strong> {{ $usuario->nombre }}</p>
                    <p><strong>Teléfono:</strong> {{ $usuario->numero_telefono }}</p>
                @else
                    <p><strong>Nombre:</strong> {{ $usuario->nombre_completo }}</p>
                    <p><strong>Legajo/Identificación:</strong> {{ $usuario->legajo }}</p>
                @endif
                <p class="mb-0"><strong>Rol:</strong> <span class="badge bg-secondary">{{ $usuario->rol }}</span></p>
            </div>
        </div>

        {{-- Contenido específico por rol --}}
        @if($usuario->rol == 'externo')
            {{-- Sección para usuarios externos --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        @if($usuario->evento_id)
                            Mi Evento Actual
                        @else
                            Eventos Disponibles
                        @endif
                    </h3>
                </div>
                
                <div class="card-body">
                    @if($usuario->evento_id)
                        @php $miEvento = $eventos->firstWhere('id', $usuario->evento_id) @endphp
                        
                        @if($miEvento)
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">{{ $miEvento->nombre }}</h5>
                                    <small class="text-muted">ID: {{ $miEvento->id }}</small>
                                </div>
                                <div class="card-body">
                                    <p><strong>Ubicación:</strong> {{ $miEvento->latitud_evento }}, {{ $miEvento->longitud_evento }}</p>
                                    <p><strong>Fechas:</strong> 
                                        {{ $miEvento->fecha_inicio }} - {{ $miEvento->fecha_finalizacion }}
                                    </p>
                                    <p class="mb-0">{{ $miEvento->descripcion }}</p>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('pedidos.create', $miEvento->id) }}" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-cart-plus"></i> Nuevo Pedido
                                        </a>
                                        <a href="{{ route('inscripciones.cancelForm', $miEvento->id) }}" 
                                           class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-times-circle"></i> Cancelar Inscripción
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Tu evento registrado no está disponible. Por favor contacta al organizador.
                            </div>
                        @endif
                    @else
                        @if($eventos->isEmpty())
                            <div class="alert alert-info">No hay eventos disponibles en este momento</div>
                        @else
                            <div class="row">
                                @foreach($eventos as $evento)
                                    @if($evento->estado != 'finalizado')
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card h-100 shadow-sm">
                                                <div class="card-header bg-light">
                                                    <h5 class="card-title mb-0">{{ $evento->nombre }}</h5>
                                                    <small class="text-muted">ID: {{ $evento->id }}</small>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <p class="mb-1"><strong>Estado:</strong> 
                                                            <span class="badge {{ $evento->estado == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $evento->estado }}
                                                            </span>
                                                        </p>
                                                        <p class="mb-1"><strong>Inicio:</strong> 
                                                            {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}
                                                        </p>
                                                        <p class="mb-0"><strong>Fin:</strong> 
                                                            {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}
                                                        </p>
                                                    </div>
                                                    <p class="text-muted small">{{ Str::limit($evento->descripcion, 100) }}</p>
                                                </div>
                                                <div class="card-footer text-center">
                                                    <a href="{{ route('inscripciones.showMapa', $evento->id) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-map-marker-alt"></i> Inscribirme
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            
        @else
            {{-- Sección para administradores y superadmin --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Gestión de Eventos</h3>
                    @if($usuario->rol == 'superadmin')
                        <div>
                            <a href="{{ route('eventos.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus"></i> Nuevo Evento
                            </a>
                            <a href="{{ route('catalogos.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-book"></i> Catálogos
                            </a>
                        </div>
                    @endif
                </div>
                
                <div class="card-body">
                    @if($eventos->isEmpty())
                        <div class="alert alert-info">No hay eventos registrados</div>
                    @else
                        <div class="row">
                            @foreach($eventos as $evento)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0">{{ $evento->nombre }}</h5>
                                            <small class="text-muted">ID: {{ $evento->id }}</small>
                                        </div>
                                        
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <p class="mb-1"><strong>Estado:</strong> 
                                                    <span class="badge {{ $evento->estado == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $evento->estado }}
                                                    </span>
                                                </p>
                                                <p class="mb-1"><strong>Inicio:</strong> 
                                                    {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}
                                                </p>
                                                <p class="mb-0"><strong>Fin:</strong> 
                                                    {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}
                                                </p>
                                            </div>
                                            
                                            <div class="border-top pt-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <a href="{{ route('catalogos.show', $evento->id) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-box-open"></i> Catálogo
                                                    </a>
                                                    
                                                    @if($usuario->rol == 'superadmin')
                                                        <div class="btn-group">
                                                            <a href="{{ route('eventos.edit', $evento->id) }}" 
                                                               class="btn btn-outline-secondary btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('eventos.destroy', $evento->id) }}" 
                                                                  method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="btn btn-outline-danger btn-sm"
                                                                        onclick="return confirm('¿Eliminar este evento?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="mt-2">
                                                    <a href="{{ route('pedidos.evento', $evento->id) }}" 
                                                       class="btn btn-warning btn-sm w-100">
                                                        <i class="fas fa-clipboard-list"></i> Ver Pedidos
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Botón de cierre de sesión --}}
        <div class="text-center mt-5">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger px-4">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
@endsection