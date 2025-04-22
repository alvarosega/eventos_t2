@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endsection

@section('title', 'Crear Evento')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Encabezado -->
    <div class="flex items-center mb-6">
        <div class="bg-primary/10 p-3 rounded-full mr-4">
            <i class="fas fa-calendar-plus text-2xl text-primary"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Crear Evento
        </h2>
    </div>

    <!-- Formulario -->
    <form action="{{ route('eventos.store') }}" method="POST" 
          class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
        @csrf

        <!-- Nombre del Evento -->
        <div class="mb-6">
            <label for="nombre" class="block mb-2 font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-signature mr-2 text-primary"></i> Nombre del Evento
            </label>
            <input type="text" id="nombre" name="nombre" required
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                          focus:ring-2 focus:ring-primary focus:border-primary 
                          dark:bg-gray-700 dark:text-gray-100 transition">
        </div>

        <!-- Fechas y horas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="fecha_inicio" class="block mb-2 font-medium text-gray-700 dark:text-gray-300">
                    <i class="fas fa-calendar-day mr-2 text-primary"></i> Fecha de Inicio
                </label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                              focus:ring-2 focus:ring-primary focus:border-primary 
                              dark:bg-gray-700 dark:text-gray-100 transition">
            </div>
            <div>
                <label for="hora_inicio" class="block mb-2 font-medium text-gray-700 dark:text-gray-300">
                    <i class="fas fa-clock mr-2 text-primary"></i> Hora de Inicio
                </label>
                <input type="time" id="hora_inicio" name="hora_inicio" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                              focus:ring-2 focus:ring-primary focus:border-primary 
                              dark:bg-gray-700 dark:text-gray-100 transition">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="fecha_finalizacion" class="block mb-2 font-medium text-gray-700 dark:text-gray-300">
                    <i class="fas fa-calendar-day mr-2 text-primary"></i> Fecha de Finalización
                </label>
                <input type="date" id="fecha_finalizacion" name="fecha_finalizacion" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                              focus:ring-2 focus:ring-primary focus:border-primary 
                              dark:bg-gray-700 dark:text-gray-100 transition">
            </div>
            <div>
                <label for="hora_finalizacion" class="block mb-2 font-medium text-gray-700 dark:text-gray-300">
                    <i class="fas fa-clock mr-2 text-primary"></i> Hora de Finalización
                </label>
                <input type="time" id="hora_finalizacion" name="hora_finalizacion" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                              focus:ring-2 focus:ring-primary focus:border-primary 
                              dark:bg-gray-700 dark:text-gray-100 transition">
            </div>
        </div>

        <!-- Estado -->
        <div class="mb-6">
            <label for="estado" class="block mb-2 font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-info-circle mr-2 text-primary"></i> Estado
            </label>
            <select id="estado" name="estado" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                           focus:ring-2 focus:ring-primary focus:border-primary 
                           dark:bg-gray-700 dark:text-gray-100 transition">
                <option value="en espera" selected>En Espera</option>
                <option value="activo">Activo</option>
                <option value="finalizado">Finalizado</option>
            </select>
        </div>

        <!-- Descripción -->
        <div class="mb-6">
            <label for="descripcion" class="block mb-2 font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-align-left mr-2 text-primary"></i> Descripción
            </label>
            <textarea id="descripcion" name="descripcion" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                             focus:ring-2 focus:ring-primary focus:border-primary 
                             dark:bg-gray-700 dark:text-gray-100 transition"></textarea>
        </div>

        <!-- Selector cantidad de ubicaciones -->
        <div class="mb-6">
            <label for="cantidad_ubicaciones" class="block mb-2 font-medium text-gray-700 dark:text-gray-300">
                <i class="fas fa-map-marked-alt mr-2 text-primary"></i> Cantidad de Ubicaciones
            </label>
            <select id="cantidad_ubicaciones" name="cantidad_ubicaciones" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                           focus:ring-2 focus:ring-primary focus:border-primary 
                           dark:bg-gray-700 dark:text-gray-100 transition">
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <!-- Campo oculto para ubicaciones -->
        <input type="hidden" id="ubicacion" name="ubicacion">

        <!-- Botón de ubicación actual -->
        <div class="mb-6">
            <button type="button" onclick="obtenerUbicacion()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg 
                           transition-colors duration-300 inline-flex items-center">
                <i class="fas fa-location-arrow mr-2"></i>
                Usar mi ubicación actual
            </button>
        </div>

        <!-- Mapa -->
        <div class="mb-6">
            <div id="mapa" class="w-full h-96 rounded-lg border border-gray-300 dark:border-gray-600"></div>
        </div>

        <!-- Botón de envío -->
        <div class="text-right">
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-lg 
                           shadow-md transition-colors duration-300 inline-flex items-center">
                <i class="fas fa-check mr-2"></i>
                Crear Evento
            </button>
        </div>
    </form>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Variables globales
    const zoomInicial = 6;
    const latitudInicial = -16.2902;
    const longitudInicial = -63.5887;

    // Inicializar el mapa
    let map = L.map('mapa').setView([latitudInicial, longitudInicial], zoomInicial);

    // Capa base (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Arreglo de coordenadas y marcadores
    let puntosSeleccionados = [];
    let marcadores = [];

    // Referencia al select de cantidad
    const selectCantidad = document.getElementById('cantidad_ubicaciones');

    // Actualizar campo hidden
    function actualizarCampoUbicacion() {
        const ubicacionesEnTexto = puntosSeleccionados
            .map(coords => coords.join(','))
            .join(';');
        document.getElementById('ubicacion').value = ubicacionesEnTexto;
    }

    // Colocar un marcador
    function colocarMarcador(lat, lng) {
        const indice = puntosSeleccionados.length - 1;
        const marker = L.marker([lat, lng])
            .addTo(map)
            .bindPopup(
                `Ubicación ${indice + 1}
                 <button type="button" onclick="borrarUbicacion(${indice})"
                         class="text-red-500 font-bold hover:text-red-700">X</button>`
            )
            .openPopup();
        marcadores.push(marker);
    }

    // Borrar ubicación
    function borrarUbicacion(indice) {
        map.removeLayer(marcadores[indice]);
        puntosSeleccionados.splice(indice, 1);
        marcadores.splice(indice, 1);
        actualizarCampoUbicacion();
        actualizarPopups();
    }

    // Actualizar popups
    function actualizarPopups() {
        for (let i = 0; i < marcadores.length; i++) {
            marcadores[i].unbindPopup();
            marcadores[i].bindPopup(
                `Ubicación ${i + 1}
                 <button type="button" onclick="borrarUbicacion(${i})"
                         class="text-red-500 font-bold hover:text-red-700">X</button>`
            );
        }
    }

    // Evento de clic en el mapa
    map.on('click', function(e) {
        const maxPuntos = parseInt(selectCantidad.value);

        if (puntosSeleccionados.length < maxPuntos) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            puntosSeleccionados.push([lat, lng]);
            colocarMarcador(lat, lng);
            actualizarCampoUbicacion();
        } else {
            alert(`Ya se alcanzó el máximo de ${maxPuntos} ubicaciones.`);
        }
    });

    // Obtener ubicación actual
    function obtenerUbicacion() {
        const maxPuntos = parseInt(selectCantidad.value);

        if (puntosSeleccionados.length >= maxPuntos) {
            alert(`Ya se alcanzó el máximo de ${maxPuntos} ubicaciones.`);
            return;
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);

                    map.setView([lat, lng], 18);
                    puntosSeleccionados.push([lat, lng]);
                    colocarMarcador(lat, lng);
                    actualizarCampoUbicacion();
                },
                (error) => {
                    alert("Error al obtener la ubicación: " + error.message);
                },
                { enableHighAccuracy: true }
            );
        } else {
            alert("Tu navegador no soporta geolocalización.");
        }
    }

    // Resetear ubicaciones al cambiar cantidad
    selectCantidad.addEventListener('change', () => {
        marcadores.forEach(marker => map.removeLayer(marker));
        marcadores = [];
        puntosSeleccionados = [];
        actualizarCampoUbicacion();
    });

    // Asegurar que el mapa se renderice correctamente
    window.onload = function() {
        map.invalidateSize();
    };
</script>
@endsection