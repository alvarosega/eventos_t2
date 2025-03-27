@extends('layouts.app')

@section('title', 'Pantalla Principal')

@section('content')
    {{-- Mensajes de Éxito / Error --}}
    @if (session('success'))
        <div class="mb-4 p-4 border-l-4 border-green-500 bg-green-100 dark:bg-green-200 text-green-700 dark:text-green-900 rounded shadow-md hover:shadow-lg transition transform hover:scale-[1.01]">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 border-l-4 border-red-500 bg-red-100 dark:bg-red-200 text-red-700 dark:text-red-900 rounded shadow-md hover:shadow-lg transition transform hover:scale-[1.01]">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Título de Bienvenida --}}
    <h2 class="text-2xl font-bold mb-4 flex items-center">
        <i class="fas fa-user-check text-primary mr-2"></i>
        Bienvenido, 
        {{ $usuario->nombre_completo ?? $usuario->nombre }}
        <small class="text-secondary text-base font-normal ml-2">({{ $usuario->rol }})</small>
    </h2>

    {{-- Contenido Específico por Rol --}}
    @if($usuario->rol == 'externo')
        {{-- Sección para Usuarios Externos (sin cambios) --}}
        {{-- ... Mantener el contenido original para externos ... --}}
    @else
        {{-- Sección para Administradores y Superadmin --}}
        <div class="bg-white dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 rounded mb-6 overflow-hidden">
            <div class="bg-primary text-white p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center">
                    <i class="fas fa-tools mr-2"></i>
                    <h3 class="text-xl font-semibold">Gestión de Eventos</h3>
                </div>
                
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-white">Tipo 1</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="eventTypeToggle" class="sr-only peer" {{ $eventType == 'tipo2' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </label>
                        <span class="text-white">Tipo 2</span>
                    </div>
                    
                    @if($usuario->rol == 'superadmin')
                        <div class="flex space-x-2">
                            <a href="{{ route('eventos.select-type') }}" class="btn-secondary">
                                <i class="fas fa-plus mr-1"></i> Nuevo Evento
                            </a>
                            @if($eventType == 'tipo1')
                                <a href="{{ route('catalogos.index') }}" class="btn-secondary" id="catalogosBtn">
                                    <i class="fas fa-book mr-1"></i> Catálogos
                                </a>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

            <div class="p-4">
                @if($eventos->isEmpty())
                    <div class="alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        No hay eventos registrados
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($eventos as $evento)
                            <div class="event-card">
                                <div class="event-header">
                                    <h5>{{ $eventType == 'tipo1' ? $evento->nombre : $evento->evento }}</h5>
                                    <small>ID: {{ $evento->id }}</small>
                                    @if($eventType == 'tipo2')
                                        <span class="event-badge">Tipo 2</span>
                                    @endif
                                </div>
                                
                                <div class="event-body">
                                    @if($eventType == 'tipo1')
                                        <div class="event-field">
                                            <strong>Estado:</strong>
                                            <span class="status-badge {{ $evento->estado == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $evento->estado }}
                                            </span>
                                        </div>
                                        <div class="event-field">
                                            <strong>Inicio:</strong> 
                                            {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}
                                        </div>
                                        <div class="event-field">
                                            <strong>Fin:</strong> 
                                            {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}
                                        </div>
                                    @else
                                        <div class="event-field">
                                            <strong>Fecha:</strong> {{ $evento->fecha }}
                                        </div>
                                        <div class="event-field">
                                            <strong>Encargado:</strong> {{ $evento->encargado }}
                                        </div>
                                        <div class="event-field">
                                            <strong>Hora Entrega:</strong> {{ $evento->hor_entrega }}
                                        </div>
                                        <div class="event-field">
                                            <strong>Estado:</strong>
                                            <span class="status-badge 
                                                {{ $evento->estado_evento == 'completado' ? 'bg-success' : 
                                                   ($evento->estado_evento == 'en_proceso' ? 'bg-yellow-500' : 'bg-secondary') }}">
                                                {{ $evento->estado_evento }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <div class="event-description">
                                        {{ Str::limit($eventType == 'tipo1' ? $evento->descripcion : $evento->material, 100) }}
                                    </div>
                                </div>

                                <div class="event-footer">
                                    <div class="flex justify-between items-center mb-2">
                                        @if($eventType == 'tipo1')
                                            <a href="{{ route('catalogos.show', $evento->id) }}" class="btn-catalog">
                                                <i class="fas fa-box-open mr-1"></i> Catálogo
                                            </a>
                                        @else
                                            <span class="text-gray-500 text-sm">Evento Logístico</span>
                                        @endif
                                        
                                        @if($usuario->rol == 'superadmin')
                                            <div class="flex space-x-2">
                                                <a href="{{ $eventType == 'tipo1' ? route('eventos.edit', $evento->id) : route('eventos.edit-tipo2', $evento->id) }}" 
                                                   class="btn-edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ $eventType == 'tipo1' ? route('eventos.destroy', $evento->id) : route('eventos.destroy-tipo2', $evento->id) }}" 
                                                      method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-delete" 
                                                            onclick="return confirm('¿Eliminar este evento?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($eventType == 'tipo1')
                                        <a href="{{ route('pedidos.evento', $evento->id) }}" class="btn-orders">
                                            <i class="fas fa-clipboard-list mr-1"></i> Ver Pedidos
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    let switchToggle = document.getElementById('eventTypeToggle');

    if (switchToggle) {
        switchToggle.addEventListener('change', function () {
            let eventType = this.checked ? 'tipo2' : 'tipo1';

            // Crear una nueva URL basada en la URL actual
            let currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('type', eventType);

            // Redirigir a la nueva URL
            window.location.replace(currentUrl.toString());
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let switchToggle = document.getElementById('eventTypeToggle');
    let catalogosBtn = document.getElementById('catalogosBtn');

    // Ocultar "Administrar Catálogos" si es tipo2 al cargar la página
    if (catalogosBtn && switchToggle.checked) {
        catalogosBtn.style.display = 'none';
    }

    if (switchToggle) {
        switchToggle.addEventListener('change', function () {
            let eventType = this.checked ? 'tipo2' : 'tipo1';

            // Si se selecciona tipo2, ocultar "Administrar Catálogos"
            if (catalogosBtn) {
                catalogosBtn.style.display = eventType === 'tipo2' ? 'none' : 'inline-block';
            }

            // Cambiar la URL sin recargar la página
            let currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('type', eventType);
            window.location.replace(currentUrl.toString());
        });
    }
});
</script>
