@extends('layouts.app')

@section('title', 'Inscripción al Evento')

@section('content')
    <h2 class="text-2xl font-bold mb-4 flex items-center space-x-2">
        <i class="fas fa-map-marker-alt text-primary mr-2"></i>
        <span>Selecciona tu ubicación para inscribirte a:</span> {{ $evento->nombre }}
    </h2>

    @if (session('error'))
        <div class="mb-4 p-4 border-l-4 border-red-500 bg-red-100 dark:bg-red-200 text-red-700 dark:text-red-900 rounded shadow transition transform hover:scale-[1.01]">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Detalles del Evento -->
    <div class="bg-white dark:bg-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-600 shadow rounded p-4 mb-4 transition transform hover:scale-[1.01] hover:shadow-xl">
        <h4 class="text-xl font-semibold mb-2 flex items-center space-x-2">
            <i class="fas fa-info-circle mr-2"></i>
            <span>Detalles del Evento</span>
        </h4>
        <p class="mb-1"><strong>Nombre:</strong> {{ $evento->nombre }}</p>
        <p class="mb-1"><strong>Fecha Inicio:</strong> {{ $evento->fecha_inicio }} - Hora: {{ $evento->hora_inicio }}</p>
        <p class="mb-1"><strong>Fecha Finalización:</strong> {{ $evento->fecha_finalizacion }} - Hora: {{ $evento->hora_finalizacion }}</p>
        <p class="mb-1"><strong>Estado:</strong> {{ $evento->estado }}</p>
        <p class="mb-0"><strong>Descripción:</strong> {{ $evento->descripcion }}</p>
    </div>

    <!-- Mapa -->
    <div id="map" class="w-full h-96 mb-4 border border-gray-300 dark:border-gray-600 rounded overflow-hidden shadow-md"></div>

    <!-- Formulario para enviar lat y lng -->
    <form id="formularioRegistro" action="{{ route('inscripciones.storeUbicacion', $evento->id) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-600 shadow rounded p-4 transition transform hover:scale-[1.01] hover:shadow-xl">
        @csrf

        <!-- Campos ocultos para lat y lng -->
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lng" id="lng">

        <!-- Campo: Foto de Referencia -->
        <div class="mb-4">
            <label for="foto_referencia" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-camera mr-1"></i>
                Foto de Referencia
            </label>
            <input type="file" name="foto_referencia" id="foto_referencia" accept="image/*" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-secondary transition-all" />
        </div>


        <!-- Botón ubicación real -->
        <button type="button" id="btn-ubicacion-real" class="inline-block bg-primary text-white px-4 py-2 rounded transition hover:bg-secondary mb-4">
            <i class="fas fa-location-arrow mr-1"></i>
            Usar mi ubicación real
        </button>

        <!-- Botón confirmar ubicación -->
        <button type="submit" class="inline-block bg-green-600 text-white px-4 py-2 rounded transition hover:bg-green-700">
            <i class="fas fa-check mr-1"></i>
            Confirmar Ubicación
        </button>
    </form>

    <!-- Hoja de estilos de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <!-- Script principal de Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Obtenemos la columna 'ubicacion' del evento, ejemplo: "-16.2902,-63.5887;-16.3000,-63.6000"
        const ubicacionEvento = "{{ $evento->ubicacion }}";
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

        if (coordenadasValidas.length === 0) {
            alert("El evento no tiene ubicaciones válidas.");
            document.querySelector('form').style.display = 'none';
        } else {
            // Inicializar mapa
            const map = L.map('map').setView([eventoLat, eventoLng], 14);

            // Capa base (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            // Icono personalizado
            const eventIcon = L.icon({
                iconUrl: '{{ Vite::asset("resources/images/imagenes/evento.png") }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });


            // Agregar marcadores del evento
            coordenadasValidas.forEach((coord, index) => {
                L.marker([coord.lat, coord.lng], { icon: eventIcon })
                    .bindPopup(`Punto ${index + 1}: {{ $evento->nombre }}`)
                    .addTo(map);
            });

            // Marcador de usuario
            let userMarker = null;
            const updateHiddenFields = (lat, lng) => {
                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;
            };

            // Selección manual en el mapa
            map.on('click', function(e) {
                const lat = e.latlng.lat.toFixed(6);
                const lng = e.latlng.lng.toFixed(6);
                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.marker([lat, lng]).addTo(map);
                updateHiddenFields(lat, lng);
            });

            // Botón geolocalización
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
                        map.setView([lat, lng], 16);
                        updateHiddenFields(lat, lng);
                    },
                    (error) => {
                        alert("Error al obtener tu ubicación. Selecciónala en el mapa.");
                    },
                    { enableHighAccuracy: true }
                );
            });

            // Inicializar campos ocultos
            updateHiddenFields('', '');
        }

        // Agregar verificación al envío del formulario
        const formulario = document.getElementById('formularioRegistro');
        formulario.addEventListener('submit', function(e) {
            const lat = document.getElementById('lat').value;
            const lng = document.getElementById('lng').value;
            if (!lat || !lng) {
                e.preventDefault();
                alert("Por favor, selecciona tu ubicación haciendo clic en el mapa o usando 'Usar mi ubicación real'.");
                return false;
            }
        });

    </script>
@endsection
 