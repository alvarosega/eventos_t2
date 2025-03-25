@extends('layouts.app')

@section('title', 'Pantalla Principal')

@section('content')
    {{-- Mensajes de Éxito / Error --}}
    @if (session('success'))
        <div class="mb-4 p-4 border-l-4 border-green-500 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 border-l-4 border-red-500 bg-red-100 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Título de Bienvenida --}}
    <h2 class="text-2xl font-bold mb-4">
        Bienvenido, 
        {{ $usuario->nombre_completo ?? $usuario->nombre }}
        <small class="text-secondary text-base font-normal">({{ $usuario->rol }})</small>
    </h2>

    {{-- Contenido Específico por Rol --}}
    @if($usuario->rol == 'externo')
        {{-- Sección para Usuarios Externos --}}
        <div class="bg-white shadow rounded mb-6 overflow-hidden">
            <div class="bg-primary text-white p-4">
                <h3 class="text-xl font-semibold">
                    @if($usuario->evento_id)
                        Mi Evento Actual
                    @else
                        Eventos Disponibles
                    @endif
                </h3>
            </div>
            <div class="p-4 text-dark">
                @if($usuario->evento_id)
                    @php
                        $miEvento = $eventos->firstWhere('id', $usuario->evento_id);
                    @endphp

                    @if($miEvento)
                        <div class="bg-light border border-gray-200 rounded shadow-sm mb-4">
                            <div class="p-4 border-b border-gray-200">
                                <h5 class="text-lg font-bold mb-1">{{ $miEvento->nombre }}</h5>
                                <small class="text-gray-600">ID: {{ $miEvento->id }}</small>
                            </div>
                            <div class="p-4 text-dark">
                                <p class="mb-1"><strong>Ubicación:</strong> {{ $miEvento->latitud_evento }}, {{ $miEvento->longitud_evento }}</p>
                                <p class="mb-1"><strong>Fechas:</strong> {{ $miEvento->fecha_inicio }} - {{ $miEvento->fecha_finalizacion }}</p>
                                <p class="text-sm text-gray-700">{{ $miEvento->descripcion }}</p>
                            </div>
                            <div class="p-4 border-t border-gray-200 flex justify-between">
                                <a href="{{ route('pedidos.create', $miEvento->id) }}" class="inline-block bg-green-600 text-white text-sm px-3 py-2 rounded hover:bg-green-700">
                                    <i class="fas fa-cart-plus"></i> Nuevo Pedido
                                </a>
                                <a href="{{ route('inscripciones.cancelForm', $miEvento->id) }}" class="inline-block border border-red-600 text-red-600 text-sm px-3 py-2 rounded hover:bg-red-600 hover:text-white">
                                    <i class="fas fa-times-circle"></i> Cancelar Inscripción
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded">
                            Tu evento registrado no está disponible. Por favor contacta al organizador.
                        </div>
                    @endif
                @else
                    @if($eventos->isEmpty())
                        <div class="mb-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded">
                            No hay eventos disponibles en este momento
                        </div>
                    @else
                        <div class="flex flex-wrap -mx-4">
                            @foreach($eventos as $evento)
                                @if($evento->estado != 'finalizado')
                                    <div class="w-full md:w-1/2 lg:w-1/3 px-4 mb-4">
                                        <div class="bg-white border border-gray-200 rounded shadow-sm h-full flex flex-col">
                                            <div class="p-4 border-b border-gray-200">
                                                <h5 class="text-lg font-bold mb-1">{{ $evento->nombre }}</h5>
                                                <small class="text-gray-600">ID: {{ $evento->id }}</small>
                                            </div>
                                            <div class="p-4 flex-1">
                                                <p class="mb-1">
                                                    <strong>Estado:</strong>
                                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                                        {{ $evento->estado == 'activo' ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                                                        {{ $evento->estado }}
                                                    </span>
                                                </p>
                                                <p class="mb-1"><strong>Inicio:</strong> {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}</p>
                                                <p class="mb-2"><strong>Fin:</strong> {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}</p>
                                                <p class="text-sm text-gray-700">
                                                    {{ Str::limit($evento->descripcion, 100) }}
                                                </p>
                                            </div>
                                            <div class="p-4 border-t border-gray-200 text-center">
                                                <a href="{{ route('inscripciones.showMapa', $evento->id) }}"
                                                   class="inline-block bg-primary text-white text-sm px-3 py-2 rounded hover:bg-secondary transition">
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
        {{-- Sección para Administradores y Superadmin --}}
        <div class="bg-white shadow rounded mb-6 overflow-hidden">
            <div class="bg-primary text-white p-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold">Gestión de Eventos</h3>
                @if($usuario->rol == 'superadmin')
                    <div class="space-x-2">
                        <a href="{{ route('eventos.create') }}" class="inline-block bg-white text-primary text-sm px-3 py-2 rounded hover:bg-gray-100">
                            <i class="fas fa-plus"></i> Nuevo Evento
                        </a>
                        <a href="{{ route('catalogos.index') }}" class="inline-block bg-white text-primary text-sm px-3 py-2 rounded hover:bg-gray-100">
                            <i class="fas fa-book"></i> Catálogos
                        </a>
                    </div>
                @endif
            </div>

            <div class="p-4 text-dark">
                @if($eventos->isEmpty())
                    <div class="mb-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded">
                        No hay eventos registrados
                    </div>
                @else
                    <div class="flex flex-wrap -mx-4">
                        @foreach($eventos as $evento)
                            <div class="w-full md:w-1/2 lg:w-1/3 px-4 mb-4">
                                <div class="bg-white border border-gray-200 rounded shadow-sm h-full flex flex-col">
                                    <div class="p-4 border-b border-gray-200">
                                        <h5 class="text-lg font-bold mb-1">{{ $evento->nombre }}</h5>
                                        <small class="text-gray-600">ID: {{ $evento->id }}</small>
                                    </div>
                                    <div class="p-4 flex-1">
                                        <p class="mb-1">
                                            <strong>Estado:</strong>
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $evento->estado == 'activo' ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                                                {{ $evento->estado }}
                                            </span>
                                        </p>
                                        <p class="mb-1"><strong>Inicio:</strong> {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}</p>
                                        <p class="mb-2"><strong>Fin:</strong> {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}</p>
                                        <p class="text-sm text-gray-700">
                                            {{ Str::limit($evento->descripcion, 100) }}
                                        </p>
                                    </div>
                                    <div class="p-4 border-t border-gray-200">
                                        <div class="flex justify-between items-center mb-2">
                                            <a href="{{ route('catalogos.show', $evento->id) }}"
                                               class="inline-block border border-primary text-primary text-sm px-3 py-2 rounded hover:bg-primary hover:text-white transition">
                                                <i class="fas fa-box-open"></i> Catálogo
                                            </a>
                                            @if($usuario->rol == 'superadmin')
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('eventos.edit', $evento->id) }}"
                                                       class="inline-block border border-gray-500 text-gray-600 text-sm px-3 py-2 rounded hover:bg-gray-600 hover:text-white transition">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('eventos.destroy', $evento->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-block border border-red-600 text-red-600 text-sm px-3 py-2 rounded hover:bg-red-600 hover:text-white transition"
                                                                onclick="return confirm('¿Eliminar este evento?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                        <a href="{{ route('pedidos.evento', $evento->id) }}"
                                           class="inline-block bg-yellow-500 text-white text-sm px-3 py-2 rounded hover:bg-yellow-600 transition w-full text-center">
                                            <i class="fas fa-clipboard-list"></i> Ver Pedidos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif


@endsection
