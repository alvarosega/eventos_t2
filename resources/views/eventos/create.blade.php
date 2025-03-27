@extends('layouts.app')

@section('styles')
    {{-- Cargar estilos de Leaflet sin integrity ni crossorigin --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endsection

@section('title', 'Crear Evento')

@section('content')
    <h2 class="text-2xl font-bold mb-4 flex items-center">
        <i class="fas fa-calendar-plus text-primary mr-2"></i>
        Crear Evento
    </h2>

    <form 
        action="{{ route('eventos.store') }}" 
        method="POST" 
        class="bg-white dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 p-4 rounded shadow-lg 
               hover:shadow-2xl transform hover:scale-105 transition-all duration-300 max-w-2xl"
    >
        @csrf

        <!-- Nombre del Evento -->
        <div class="mb-4">
            <label for="nombre" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-signature mr-1"></i> Nombre del Evento
            </label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                required
                class="w-full px-3 py-2 border border-secondary dark:border-gray-600 
                       rounded focus:outline-none focus:ring-2 focus:ring-primary 
                       dark:bg-gray-700 dark:text-gray-100"
            />
        </div>

        <!-- Fechas y horas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="fecha_inicio" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-calendar-day mr-1"></i> Fecha de Inicio
                </label>
                <input
                    type="date"
                    id="fecha_inicio"
                    name="fecha_inicio"
                    required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 
                           rounded focus:outline-none focus:ring-2 focus:ring-primary 
                           dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
            <div>
                <label for="hora_inicio" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-clock mr-1"></i> Hora de Inicio
                </label>
                <input
                    type="time"
                    id="hora_inicio"
                    name="hora_inicio"
                    required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 
                           rounded focus:outline-none focus:ring-2 focus:ring-primary 
                           dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="fecha_finalizacion" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-calendar-day mr-1"></i> Fecha de Finalización
                </label>
                <input
                    type="date"
                    id="fecha_finalizacion"
                    name="fecha_finalizacion"
                    required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 
                           rounded focus:outline-none focus:ring-2 focus:ring-primary 
                           dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
            <div>
                <label for="hora_finalizacion" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-clock mr-1"></i> Hora de Finalización
                </label>
                <input
                    type="time"
                    id="hora_finalizacion"
                    name="hora_finalizacion"
                    required
                    class="w-full px-3 py-2 border border-secondary dark:border-gray-600 
                           rounded focus:outline-none focus:ring-2 focus:ring-primary 
                           dark:bg-gray-700 dark:text-gray-100"
                />
            </div>
        </div>

        <!-- Estado -->
        <div class="mb-4">
            <label for="estado" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-info-circle mr-1"></i> Estado
            </label>
            <select
                id="estado"
                name="estado"
                required
                class="w-full px-3 py-2 border border-secondary dark:border-gray-600 
                       rounded focus:outline-none focus:ring-2 focus:ring-primary 
                       dark:bg-gray-700 dark:text-gray-100"
            >
                <option value="en espera" selected>En Espera</option>
                <option value="activo">Activo</option>
                <option value="finalizado">Finalizado</option>
            </select>
        </div>

        <!-- Descripción -->
        <div class="mb-4">
            <label for="descripcion" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-align-left mr-1"></i> Descripción
            </label>
            <textarea
                id="descripcion"
                name="descripcion"
                rows="3"
                class="w-full px-3 py-2 border border-secondary dark:border-gray-600 
                       rounded focus:outline-none focus:ring-2 focus:ring-primary 
                       dark:bg-gray-700 dark:text-gray-100"
            ></textarea>
        </div>

        <!-- Selector cantidad de ubicaciones -->
        <div class="mb-4">
            <label for="cantidad_ubicaciones" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-map-marked-alt mr-1"></i> Cantidad de Ubicaciones
            </label>
            <select
                id="cantidad_ubicaciones"
                name="cantidad_ubicaciones"
                required
                class="w-full px-3 py-2 border border-secondary dark:border-gray-600 
                       rounded focus:outline-none focus:ring-2 focus:ring-primary 
                       dark:bg-gray-700 dark:text-gray-100"
            >
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <!-- Campo oculto para almacenar las ubicaciones seleccionadas -->
        <input type="hidden" id="ubicacion" name="ubicacion">

        <!-- Botón para usar ubicación actual -->
        <button
            type="button"
            class="inline-block bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600 transition mb-4"
            onclick="obtenerUbicacion()"
        >
            <i class="fas fa-location-arrow mr-1"></i>
            Usar mi ubicación actual
        </button>

        <!-- Mapa -->
        <div id="mapa" class="w-full h-96 mb-4 border border-gray-300 dark:border-gray-600 rounded overflow-hidden"></div>

        <!-- Botón Crear Evento -->
        <button
            type="submit"
            class="bg-primary text-white px-4 py-2 rounded shadow hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-primary transition"
        >
            <i class="fas fa-check mr-1"></i>
            Crear Evento
        </button>
    </form>

    {{-- Scripts de Leaflet sin integrity ni crossorigin --}}
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

        // Arreglo de coordenadas y arreglo de marcadores
        let puntosSeleccionados = [];
        let marcadores = [];

        // Referencia al select de cantidad de ubicaciones
        const selectCantidad = document.getElementById('cantidad_ubicaciones');

        // Actualizar campo hidden
        function actualizarCampoUbicacion() {
            const ubicacionesEnTexto = puntosSeleccionados
                .map(coords => coords.join(',')) // "lat,lng"
                .join(';');                      // "lat1,lng1;lat2,lng2;..."
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
                             style="border:none; background:none; color:red; font-weight:bold;">X</button>`
                )
                .openPopup();
            marcadores.push(marker);
        }

        function borrarUbicacion(indice) {
            map.removeLayer(marcadores[indice]);
            puntosSeleccionados.splice(indice, 1);
            marcadores.splice(indice, 1);
            actualizarCampoUbicacion();
            actualizarPopups();
        }

        function actualizarPopups() {
            for (let i = 0; i < marcadores.length; i++) {
                marcadores[i].unbindPopup();
                marcadores[i].bindPopup(
                    `Ubicación ${i + 1}
                     <button type="button" onclick="borrarUbicacion(${i})"
                             style="border:none; background:none; color:red; font-weight:bold;">X</button>`
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

        // Resetear ubicaciones si se cambia la cantidad
        selectCantidad.addEventListener('change', () => {
            marcadores.forEach(marker => map.removeLayer(marker));
            marcadores = [];
            puntosSeleccionados = [];
            actualizarCampoUbicacion();
        });

        // Asegurar que el mapa se renderice al cargar
        window.onload = function() {
            map.invalidateSize();
        };
    </script>
@endsection
