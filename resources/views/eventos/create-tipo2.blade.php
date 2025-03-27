@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endsection

@section('title', 'Crear Evento Tipo 2')

@section('content')
    <h2 class="text-2xl font-bold mb-4 flex items-center">
        <i class="fas fa-truck-moving text-green-500 mr-2"></i>
        Crear Evento Tipo 2
    </h2>

    <form action="{{ route('eventos.store-tipo2') }}" method="POST" 
          class="bg-white dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 p-4 rounded shadow-lg hover:shadow-2xl transition-all duration-300 max-w-4xl">
        @csrf

        <!-- Fila 1: Fecha y Nombre del Evento -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="fecha" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-calendar-day mr-1"></i> Fecha del Evento
                </label>
                <input type="date" id="fecha" name="fecha" required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div>
                <label for="evento" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-signature mr-1"></i> Nombre del Evento
                </label>
                <input type="text" id="evento" name="evento" required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
        </div>

        <!-- Fila 2: Encargado y Celular -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="encargado" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-user-tie mr-1"></i> Encargado
                </label>
                <input type="text" id="encargado" name="encargado" required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div>
                <label for="celular" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-mobile-alt mr-1"></i> Celular
                </label>
                <input type="text" id="celular" name="celular" required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
        </div>

        <!-- Dirección -->
        <div class="mb-4">
            <label for="direccion" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-map-marker-alt mr-1"></i> Dirección
            </label>
            <input type="text" id="direccion" name="direccion" required
                class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
        </div>

        <!-- Material -->
        <div class="mb-4">
            <label for="material" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-boxes mr-1"></i> Material
            </label>
            <textarea id="material" name="material" rows="2" required
                class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100"></textarea>
        </div>

        <!-- Fila 3: Hora de Entrega y Recojo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="hor_entrega" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-clock mr-1"></i> Hora de Entrega
                </label>
                <input type="time" id="hor_entrega" name="hor_entrega" required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div>
                <label for="recojo" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-truck-loading mr-1"></i> Recojo
                </label>
                <select id="recojo" name="recojo" required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>

        <!-- Fila 4: Operador y Supervisor -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="operador" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-hard-hat mr-1"></i> Operador
                </label>
                <input type="text" id="operador" name="operador" required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div>
                <label for="supervisor" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-user-shield mr-1"></i> Supervisor
                </label>
                <input type="text" id="supervisor" name="supervisor" required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
        </div>

        <!-- Estado del Evento -->
        <div class="mb-4">
            <label for="estado_evento" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-info-circle mr-1"></i> Estado del Evento
            </label>
            <select id="estado_evento" name="estado_evento" required
                class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
                <option value="pendiente">Pendiente</option>
                <option value="en_proceso">En Proceso</option>
                <option value="completado">Completado</option>
            </select>
        </div>

        <!-- Mapa y Ubicación -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-map-marked-alt mr-1"></i> Ubicación Geográfica
            </label>
            <input type="hidden" id="ubicacion" name="ubicacion">
            <button type="button" onclick="obtenerUbicacion()"
                class="inline-block bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600 transition mb-4">
                <i class="fas fa-location-arrow mr-1"></i> Usar mi ubicación actual
            </button>
            <div id="mapa" class="w-full h-96 border border-gray-300 dark:border-gray-600 rounded overflow-hidden"></div>
        </div>

        <!-- Botón Crear Evento -->
        <button type="submit"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            <i class="fas fa-check mr-1"></i> Crear Evento Tipo 2
        </button>
    </form>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Mapa (similar al original pero con marcador único)
        let map = L.map('mapa').setView([-16.2902, -63.5887], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        let marker = null;

        function obtenerUbicacion() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude.toFixed(6);
                        const lng = position.coords.longitude.toFixed(6);
                        
                        if (marker) map.removeLayer(marker);
                        
                        marker = L.marker([lat, lng]).addTo(map)
                            .bindPopup("Ubicación seleccionada")
                            .openPopup();
                            
                        map.setView([lat, lng], 16);
                        document.getElementById('ubicacion').value = `${lat},${lng}`;
                    },
                    (error) => {
                        alert("Error al obtener ubicación: " + error.message);
                    }
                );
            } else {
                alert("Tu navegador no soporta geolocalización.");
            }
        }

        // Permitir seleccionar ubicación con clic
        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            
            if (marker) map.removeLayer(marker);
            
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup("Ubicación seleccionada")
                .openPopup();
                
            document.getElementById('ubicacion').value = `${lat},${lng}`;
        });

        window.onload = function() {
            map.invalidateSize();
        };
    </script>
@endsection