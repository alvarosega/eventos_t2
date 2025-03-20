@extends('layouts.app')

@section('title', 'Inscripción al Evento')

@section('content')
    <h2>Selecciona tu ubicacasdión para inscribirte a: {{ $evento->nombre }}</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Mostrar detalles del evento --}}
    <div class="card mb-4">
        <div class="card-body">
            <h4>Detalles del Evento</h4>
            <p><strong>Nombre:</strong> {{ $evento->nombre }}</p>
            <p><strong>Fecha Inicio:</strong> {{ $evento->fecha_inicio }} - Hora: {{ $evento->hora_inicio }}</p>
            <p><strong>Fecha Finalización:</strong> {{ $evento->fecha_finalizacion }} - Hora: {{ $evento->hora_finalizacion }}</p>
            <p><strong>Estado:</strong> {{ $evento->estado }}</p>
            <p><strong>Descripción:</strong> {{ $evento->descripcion }}</p>
        </div>
    </div>

    {{-- Contenedor del mapa --}}
    <div id="map" style="width: 100%; height: 500px;" class="mb-3"></div>

    {{-- Formulario para enviar lat y lng, y la foto de referencia --}}
    <form action="{{ route('inscripciones.storeUbicacion', $evento->id) }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="mt-3">
        @csrf

        {{-- Campos ocultos para lat y lng --}}
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lng" id="lng">

        {{-- Campo opcional para subir la foto de referencia --}}
        <div class="mb-3">
            <label for="foto_referencia" class="form-label">Foto de Referencia (opcional)</label>
            <input type="file" name="foto_referencia" id="foto_referencia" class="form-control" accept="image/*">
        </div>

        {{-- Botón para usar la ubicación real --}}
        <button type="button" id="btn-ubicacion-real" class="btn btn-primary mb-3">
            Usar mi ubicación real
        </button>

        {{-- Botón para confirmar la ubicación --}}
        <button type="submit" class="btn btn-success">
            Confirmar Ubicación
        </button>
    </form>

    <!-- Hoja de estilos de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

    <!-- Script principal de Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        // Obtener todas las coordenadas del evento desde la columna 'ubicacion'
        const ubicacionEvento = "{{ $evento->ubicacion }}"; // Ejemplo: "-16.2902,-63.5887;-16.3000,-63.6000"
        const coordenadasEvento = ubicacionEvento.split(';').filter(c => c.trim() !== '');

        let coordenadasValidas = [];
        let eventoLat = 0;
        let eventoLng = 0;

        if (coordenadasEvento.length > 0) {
            coordenadasValidas = coordenadasEvento.map(coord => {
                const partes = coord.split(',').map(p => parseFloat(p.trim()));
                return { lat: partes[0], lng: partes[1] };
            }).filter(coord => !isNaN(coord.lat) && !isNaN(coord.lng));

            if (coordenadasValidas.length > 0) {
                eventoLat = coordenadasValidas[0].lat;
                eventoLng = coordenadasValidas[0].lng;
            }
        }

        // Validar coordenadas
        if (coordenadasValidas.length === 0) {
            alert("El evento no tiene ubicaciones válidas.");
            document.querySelector('form').style.display = 'none'; // Ocultar formulario si no hay coordenadas válidas
        } else {
            // Inicializar mapa solo si las coordenadas son válidas
            const map = L.map('map').setView([eventoLat, eventoLng], 14);

            // Capa base (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            // Icono personalizado para el evento
            const eventIcon = L.icon({
                iconUrl: '{{ asset("images/evento.jpeg") }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            // Agregar todos los marcadores del evento
            coordenadasValidas.forEach((coord, index) => {
                L.marker([coord.lat, coord.lng], { icon: eventIcon })
                    .bindPopup(`Punto ${index + 1}: {{ $evento->nombre }}`)
                    .addTo(map);
            });

            // Variables y funciones
            let userMarker = null;
            const updateHiddenFields = (lat, lng) => {
                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;
            };

            // Evento click en el mapa (selección manual)
            map.on('click', function(e) {
                const lat = e.latlng.lat.toFixed(6); // Precisión de 6 decimales
                const lng = e.latlng.lng.toFixed(6);

                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.marker([lat, lng]).addTo(map);
                updateHiddenFields(lat, lng);
            });

            // Botón de geolocalización
            document.getElementById('btn-ubicacion-real').addEventListener('click', () => {
                if (!navigator.geolocation) {
                    alert("Tu navegador no soporta geolocalización.");
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude.toFixed(6);
                        const lng = pos.coords.longitude.toFixed(6);

                        if (userMarker) map.removeLayer(userMarker);
                        userMarker = L.marker([lat, lng]).addTo(map);
                        map.setView([lat, lng], 16); // Zoom más cercano
                        updateHiddenFields(lat, lng);
                    },
                    (error) => {
                        alert("Error al obtener tu ubicación. Selecciónala en el mapa.");
                    },
                    { enableHighAccuracy: true }
                );
            });

            // Inicializar campos ocultos vacíos (no 0,0)
            updateHiddenFields('', '');
        }
    </script>
@endsection