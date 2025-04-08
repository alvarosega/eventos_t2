@extends('layouts.app')

@section('title', 'Editar Evento Tipo 1')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Ediatar Evento Tipo 1</h1>

    <form action="{{ route('eventos.update', $evento->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nombre del Evento --}}
        <div class="mb-4">
            <label for="nombre" class="block font-semibold mb-1">Nombre del Evento:</label>
            <input type="text" name="nombre" id="nombre" class="w-full p-2 border rounded"
                   value="{{ old('nombre', $evento->nombre) }}">
            @error('nombre')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Fecha de Inicio --}}
        <div class="mb-4">
            <label for="fecha_inicio" class="block font-semibold mb-1">Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="w-full p-2 border rounded"
                   value="{{ old('fecha_inicio', $evento->fecha_inicio) }}">
            @error('fecha_inicio')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Hora de Inicio --}}
        <div class="mb-4">
            <label for="hora_inicio" class="block font-semibold mb-1">Hora de Inicio:</label>
            <input type="time" name="hora_inicio" id="hora_inicio" class="w-full p-2 border rounded"
                   value="{{ old('hora_inicio', $evento->hora_inicio) }}">
            @error('hora_inicio')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Fecha de Finalización --}}
        <div class="mb-4">
            <label for="fecha_finalizacion" class="block font-semibold mb-1">Fecha de Finalización:</label>
            <input type="date" name="fecha_finalizacion" id="fecha_finalizacion" class="w-full p-2 border rounded"
                   value="{{ old('fecha_finalizacion', $evento->fecha_finalizacion) }}">
            @error('fecha_finalizacion')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Hora de Finalización --}}
        <div class="mb-4">
            <label for="hora_finalizacion" class="block font-semibold mb-1">Hora de Finalización:</label>
            <input type="time" name="hora_finalizacion" id="hora_finalizacion" class="w-full p-2 border rounded"
                   value="{{ old('hora_finalizacion', $evento->hora_finalizacion) }}">
            @error('hora_finalizacion')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Descripción --}}
        <div class="mb-4">
            <label for="descripcion" class="block font-semibold mb-1">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="w-full p-2 border rounded" rows="4">{{ old('descripcion', $evento->descripcion) }}</textarea>
            @error('descripcion')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Estado --}}
        <div class="mb-4">
            <label for="estado" class="block font-semibold mb-1">Estado:</label>
            <select name="estado" id="estado" class="w-full p-2 border rounded">
                <option value="activo" {{ old('estado', $evento->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="en espera" {{ old('estado', $evento->estado) == 'en espera' ? 'selected' : '' }}>En espera</option>
                <option value="finalizado" {{ old('estado', $evento->estado) == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
            </select>
            @error('estado')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ubicación --}}
        <div class="mb-4">
            <label for="ubicacion" class="block font-semibold mb-1">Ubicación (lat,lng):</label>
            <input type="text" name="ubicacion" id="ubicacion" class="w-full p-2 border rounded"
                   value="{{ old('ubicacion', $evento->ubicacion) }}" readonly>
            <small class="text-gray-500">Haz clic en el mapa para seleccionar una nueva ubicación.</small>
            @error('ubicacion')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror

            {{-- Mapa --}}
            <div id="map" class="w-full h-64 mt-3 rounded shadow border"></div>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Actualizar Evento
        </button>
    </form>
@endsection

    {{-- Leaflet.js CDN --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Obtener coordenadas iniciales
            let ubicacionInput = document.getElementById("ubicacion");
            let ubicacion = ubicacionInput.value || "-16.5,-68.1"; // valor por defecto

            let [lat, lng] = ubicacion.split(",").map(parseFloat);
            let map = L.map("map").setView([lat, lng], 13);

            // Añadir capa base (mapa)
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Marcador inicial
            let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            // Al hacer clic en el mapa, actualizar marcador y valor en input
            map.on("click", function (e) {
                let { lat, lng } = e.latlng;
                marker.setLatLng([lat, lng]);
                ubicacionInput.value = `${lat.toFixed(6)},${lng.toFixed(6)}`;
            });

            // También actualiza input cuando se arrastra el marcador
            marker.on("dragend", function (e) {
                let { lat, lng } = marker.getLatLng();
                ubicacionInput.value = `${lat.toFixed(6)},${lng.toFixed(6)}`;
            });
        });
    </script>
