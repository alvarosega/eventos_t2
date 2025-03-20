@extends('layouts.app')

@section('styles')
    {{-- Cargar estilos de Leaflet en el head sin integrity ni crossorigin --}}
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
@endsection

@section('title', 'Crear Evento')

@section('content')
    <h2>Crear Evento</h2>

    <form action="{{ route('eventos.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Evento</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="col">
                <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="fecha_finalizacion" class="form-label">Fecha de Finalización</label>
                <input type="date" class="form-control" id="fecha_finalizacion" name="fecha_finalizacion" required>
            </div>
            <div class="col">
                <label for="hora_finalizacion" class="form-label">Hora de Finalización</label>
                <input type="time" class="form-control" id="hora_finalizacion" name="hora_finalizacion" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select class="form-select" id="estado" name="estado" required>
                <option value="en espera" selected>En Espera</option>
                <option value="activo">Activo</option>
                <option value="finalizado">Finalizado</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
        </div>

        {{-- Selector para la cantidad de ubicaciones --}}
        <div class="mb-3">
            <label for="cantidad_ubicaciones" class="form-label">Cantidad de Ubicaciones</label>
            <select class="form-select" id="cantidad_ubicaciones" name="cantidad_ubicaciones" required>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        {{-- Campo oculto para almacenar todas las ubicaciones seleccionadas (lat,lng;lat,lng;...) --}}
        <input type="hidden" id="ubicacion" name="ubicacion">

        {{-- Botón para obtener la ubicación actual (si aún hay espacios de ubicación disponibles) --}}
        <button type="button" class="btn btn-info mb-3" onclick="obtenerUbicacion()">
            Usar mi ubicación actual
        </button>

        {{-- Mapa --}}
        <div id="mapa" style="height: 400px; width: 100%;"></div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Crear Evento</button>
        </div>
    </form>

    {{-- Cargar script de Leaflet al final del documento sin integrity ni crossorigin --}}
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

        // Función para actualizar el input oculto 'ubicacion'
        function actualizarCampoUbicacion() {
            /*
              Transformamos puntosSeleccionados => ["lat1,lng1", "lat2,lng2", ...]
              y luego unimos con ';'
            */
            const ubicacionesEnTexto = puntosSeleccionados
                .map(coords => coords.join(',')) // "lat,lng"
                .join(';');                      // "lat1,lng1;lat2,lng2;..."
            document.getElementById('ubicacion').value = ubicacionesEnTexto;
        }

        // Función para colocar un nuevo marcador en el mapa
        function colocarMarcador(lat, lng) {
            // Se obtiene el índice que será la posición actual (basado en la cantidad de puntos ya seleccionados)
            const indice = puntosSeleccionados.length - 1;
            // Crear el marcador y asignarle el popup con un botón para borrar
            const marker = L.marker([lat, lng])
                .addTo(map)
                .bindPopup(`Ubicación ${indice + 1} <button type="button" onclick="borrarUbicacion(${indice})" style="border:none; background:none; color:red; font-weight:bold;">X</button>`)
                .openPopup();
            marcadores.push(marker);
        }
        function borrarUbicacion(indice) {
            // Remover el marcador del mapa
            map.removeLayer(marcadores[indice]);
            // Remover el punto del arreglo y también el marcador
            puntosSeleccionados.splice(indice, 1);
            marcadores.splice(indice, 1);
            // Actualizar el campo oculto con las coordenadas restantes
            actualizarCampoUbicacion();
            // Actualizar el contenido de los popups para reflejar los nuevos índices
            actualizarPopups();
        }

        function actualizarPopups() {
            for (let i = 0; i < marcadores.length; i++) {
                marcadores[i].unbindPopup();
                marcadores[i].bindPopup(`Ubicación ${i + 1} <button type="button" onclick="borrarUbicacion(${i})" style="border:none; background:none; color:red; font-weight:bold;">X</button>`);
            }
        }

        // Evento de clic en el mapa: el usuario define una ubicación
        map.on('click', function(e) {
            // Obtenemos el número máximo de ubicaciones que el usuario seleccionó
            const maxPuntos = parseInt(selectCantidad.value);

            // Si no hemos alcanzado el límite, agregamos la nueva ubicación
            if (puntosSeleccionados.length < maxPuntos) {
                const lat = e.latlng.lat.toFixed(6);
                const lng = e.latlng.lng.toFixed(6);

                // Agregar al arreglo
                puntosSeleccionados.push([lat, lng]);
                // Colocar marcador en el mapa
                colocarMarcador(lat, lng);
                // Actualizar el campo hidden
                actualizarCampoUbicacion();
            } else {
                alert(`Ya se alcanzó el máximo de ${maxPuntos} ubicaciones.`);
            }
        });

        // Función de geolocalización (usando la API del navegador)
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

                        // Mover el mapa a la ubicación
                        map.setView([lat, lng], 18);

                        // Agregar la ubicación al arreglo y colocar marcador
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

        // Si se cambia la cantidad de ubicaciones, se resetea todo
        selectCantidad.addEventListener('change', () => {
            // Limpiamos marcadores del mapa
            marcadores.forEach(marker => map.removeLayer(marker));
            marcadores = [];
            puntosSeleccionados = [];
            actualizarCampoUbicacion();
        });

        // Asegurar que el mapa se redibuje correctamente
        window.onload = function() {
            map.invalidateSize();
        };
    </script>
@endsection
