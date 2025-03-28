@extends('layouts.app')

@section('title', 'Editar Evento Tipo 2')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Editar Evento Tipo 2</h1>

    <form action="{{ route('eventos.update-tipo2', $evento->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="evento" class="block font-semibold mb-1">Nombre del Evento:</label>
            <input type="text" name="evento" id="evento" class="w-full p-2 border rounded"
                   value="{{ old('evento', $evento->evento) }}">
            @error('evento')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="fecha" class="block font-semibold mb-1">Fecha:</label>
            <input type="date" name="fecha" id="fecha" class="w-full p-2 border rounded"
                   value="{{ old('fecha', $evento->fecha) }}">
            @error('fecha')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Continúa con los demás campos: encargado, celular, direccion, ubicacion, material, hor_entrega, etc. -->

        <div class="mb-4">
            <label for="estado_evento" class="block font-semibold mb-1">Estado del Evento:</label>
            <select name="estado_evento" id="estado_evento" class="w-full p-2 border rounded">
                <option value="pendiente" {{ old('estado_evento', $evento->estado_evento) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="en_proceso" {{ old('estado_evento', $evento->estado_evento) == 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                <option value="completado" {{ old('estado_evento', $evento->estado_evento) == 'completado' ? 'selected' : '' }}>Completado</option>
            </select>
            @error('estado_evento')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- ... más campos ... -->

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Actualizar Evento
        </button>
    </form>
@endsection
