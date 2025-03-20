@extends('layouts.app')

@section('title', 'Pedidos del Evento')

@section('styles')
    <!-- Cargar estilos Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')

@php
    // Preparar la estructura de marcadores para Leaflet
    $markers = [
        'evento' => [],
        'usuarios' => [],
    ];

    // 1. Parsear TODAS las ubicaciones del evento (separadas por ';')
    //    Ejemplo en BDD: "-16.563546,-68.238281;-16.631981,-68.018555"
    $ubicacionEvento = $evento->ubicacion ?? '';
    $coordsEvento = array_filter(explode(';', $ubicacionEvento));

    foreach ($coordsEvento as $coord) {
        $latLng = explode(',', $coord);
        if (count($latLng) === 2 && is_numeric(trim($latLng[0])) && is_numeric(trim($latLng[1]))) {
            $markers['evento'][] = [
                'lat'    => (float) trim($latLng[0]),
                'lng'    => (float) trim($latLng[1]),
                'nombre' => $evento->nombre,
                'tipo'   => 'evento'
            ];
        }
    }

    // 2. Recorrer todos los pedidos para generar marcadores de los usuarios
    foreach ($pedidos as $pedido) {
        $externo = $pedido->externo;
        if (!$externo) continue;

        $ubicacionUsuario = $externo->ubicacion ?? '';
        // Suponiendo que la ubicación del usuario viene como "lat,lng" (sólo 1 par)
        $parts = explode(',', $ubicacionUsuario);
        if (count($parts) === 2 && is_numeric(trim($parts[0])) && is_numeric(trim($parts[1]))) {
            $markers['usuarios'][] = [
                'lat'       => (float) trim($parts[0]),
                'lng'       => (float) trim($parts[1]),
                'nombre'    => $externo->nombre ?? 'Desconocido',
                'telefono'  => $externo->numero_telefono ?? '',
                'pedido_id' => $pedido->id,
                'tipo'      => 'usuario',
            ];
        }
    }

@endphp

<h2>Pedidos del Evento: {{ $evento->nombre }}</h2>
<p>Total de pedidos para este evento: <strong>{{ $pedidos->count() }}</strong></p>

<!-- Modal para imágenes -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Imagen de Referencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Imagen" class="img-fluid">
            </div>
        </div>
    </div>
</div>

{{-- Contenedor para el mapa --}}
<div id="map" style="width: 100%; height: 500px;" class="mb-4"></div>

@if ($pedidos->isEmpty())
    <p>No hay pedidos registrados para este evento.</p>
@else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Foto Ref.</th>
                <th>Ubicación</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Evidencia</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedidos as $pedido)
                @php
                    $externo   = $pedido->externo;
                    $ubicacion = $externo->ubicacion ? explode(',', $externo->ubicacion) : null;
                @endphp
                <tr data-pedido-id="{{ $pedido->id }}">
                    <td>{{ $pedido->id }}</td>
                    <td>{{ $externo->nombre ?? 'Desconocido' }}</td>
                    <td>
                        @if($externo->numero_telefono)
                            <a href="https://wa.me/591{{ $externo->numero_telefono }}" target="_blank">
                                +591 {{ $externo->numero_telefono }}
                            </a>
                        @else
                            Sin teléfono
                        @endif
                    </td>
                    <td>
                        @if($externo->foto_referencia)
                            <a href="#" class="view-image"
                               data-image-src="{{ asset('storage/'.$externo->foto_referencia) }}">
                                Ver imagen
                            </a>
                        @else
                            Sin foto
                        @endif
                    </td>
                    <td>
                        @if($ubicacion && count($ubicacion) === 2)
                            <a href="#" class="view-location"
                               data-lat="{{ trim($ubicacion[0]) }}"
                               data-lng="{{ trim($ubicacion[1]) }}">
                                Ver ubicación
                            </a>
                        @else
                            Sin ubicación
                        @endif
                    </td>
                    <td>{{ $pedido->cantidad }}</td>
                    <td>${{ number_format($pedido->total, 2) }}</td>
                    <td>
                        <select class="form-select status-select" data-pedido-id="{{ $pedido->id }}">
                            <option value="pendiente" {{ $pedido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_preparacion" {{ $pedido->estado == 'en_preparacion' ? 'selected' : '' }}>En preparación</option>
                            <option value="enviado" {{ $pedido->estado == 'enviado' ? 'selected' : '' }}>Enviado</option>
                        </select>
                    </td>
                    <td>
                        <form class="evidence-form" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="evidence" class="form-control evidence-input"
                                   data-pedido-id="{{ $pedido->id }}" accept="image/*">
                            @if($pedido->foto_evidencia)
                                <img src="{{ asset('storage/'.$pedido->foto_evidencia) }}"
                                     alt="Evidencia" width="60" class="mt-2">
                            @endif
                        </form>
                    </td>
                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<a href="{{ route('home') }}" class="btn btn-secondary mt-3">Volver</a>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Recibimos datos JSON con múltiples ubicaciones del evento y ubicaciones de usuarios
        const eventLocations = @json($markers['evento']);
        const userLocations = @json($markers['usuarios']);

        // Crear el mapa (sin definir centro todavía)
        let map = L.map('map');

        // Agregar capa base de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Definir íconos
        const eventIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [0, -41]
        });
        const userIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [0, -41]
        });

        // Agrupamos todos los marcadores en un FeatureGroup para ajustar el zoom
        const markersGroup = L.featureGroup();

        // 1. Agregar marcadores del evento (puede haber varios)
        eventLocations.forEach((loc, index) => {
            if(!isNaN(loc.lat) && !isNaN(loc.lng)) {
                L.marker([loc.lat, loc.lng], { icon: eventIcon })
                    .bindPopup(`<b>${loc.nombre}</b><br>Ubicación del evento #${index + 1}`)
                    .addTo(markersGroup);
            }
        });

        // 2. Agregar marcadores de usuarios
        const userMarkers = {}; // Para poder abrir popup al hacer "Ver ubicación"
        userLocations.forEach(loc => {
            const lat = parseFloat(loc.lat);
            const lng = parseFloat(loc.lng);
            if(!isNaN(lat) && !isNaN(lng)) {
                userMarkers[loc.pedido_id] = L.marker([lat, lng], { icon: userIcon })
                    .bindPopup(`<b>${loc.nombre}</b><br>Tel: ${loc.telefono}`)
                    .addTo(markersGroup);
            }
        });

        markersGroup.addTo(map);

        // Ajustar el mapa para que muestre todos los marcadores
        if (markersGroup.getLayers().length > 0) {
            map.fitBounds(markersGroup.getBounds());
        } else {
            // Si no hay marcadores, centrar en Bolivia como vista por defecto
            map.setView([-16.2902, -63.5887], 6);
        }

        // ---------------------
        // Modal de visualización de imágenes
        document.querySelectorAll('.view-image').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('modalImage').src = this.dataset.imageSrc;
                new bootstrap.Modal(document.getElementById('imageModal')).show();
            });
        });

        // Centrar el mapa en la ubicación del usuario cuando se hace clic en "Ver ubicación"
        document.querySelectorAll('.view-location').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);
                const pedidoId = this.closest('tr').dataset.pedidoId;

                if (!isNaN(lat) && !isNaN(lng)) {
                    map.flyTo([lat, lng], 15);
                    if (userMarkers[pedidoId]) {
                        userMarkers[pedidoId].openPopup();
                    }
                }
            });
        });

        // Actualizar estado del pedido vía AJAX
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const pedidoId = this.dataset.pedidoId;
                const newStatus = this.value;

                fetch(`/pedidos/${pedidoId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ estado: newStatus })
                }).then(response => {
                    if (!response.ok) alert('Error actualizando estado');
                });
            });
        });

        // Subir evidencia al cambiar el input de archivo
        document.querySelectorAll('.evidence-input').forEach(input => {
            input.addEventListener('change', function() {
                const formData = new FormData(this.closest('form'));
                const pedidoId = this.dataset.pedidoId;

                fetch(`/pedidos/${pedidoId}/evidence`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Error subiendo evidencia');
                    }
                });
            });
        });

        // Asegurar que el mapa se recalcule su tamaño
        setTimeout(() => {
            map.invalidateSize();
        }, 500);
    </script>
@endsection
