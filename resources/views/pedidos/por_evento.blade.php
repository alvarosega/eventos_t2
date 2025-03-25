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
        // Suponiendo que la ubicación del usuario viene como "lat,lng" (solo 1 par)
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

<h2 class="text-2xl font-bold mb-4">Pedidos del Evento: {{ $evento->nombre }}</h2>
<p class="mb-6">Total de pedidos para este evento: <strong>{{ $pedidos->count() }}</strong></p>

<!-- Modal de imagen con Tailwind (oculto por defecto) -->
<div
    id="imageModal"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden"
>
    <div class="bg-white rounded p-4 max-w-2xl w-full relative">
        <!-- Botón para cerrar modal -->
        <button
            id="closeModal"
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold"
        >
            &times;
        </button>
        <!-- Imagen central -->
        <img id="modalImage" src="" alt="Imagen" class="max-w-full h-auto mx-auto">
    </div>
</div>

{{-- Contenedor para el mapa --}}
<div id="map" class="w-full h-[500px] mb-6 rounded border border-gray-200"></div>

@if ($pedidos->isEmpty())
    <p class="text-gray-700">No hay pedidos registrados para este evento.</p>
@else
    <!-- Tabla con Tailwind -->
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="w-full table-auto text-sm text-left border-collapse">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-4 py-2 font-semibold border-b">ID</th>
                    <th class="px-4 py-2 font-semibold border-b">Nombre</th>
                    <th class="px-4 py-2 font-semibold border-b">Teléfono</th>
                    <th class="px-4 py-2 font-semibold border-b">Foto Ref.</th>
                    <th class="px-4 py-2 font-semibold border-b">Ubicación</th>
                    <th class="px-4 py-2 font-semibold border-b">Cantidad</th>
                    <th class="px-4 py-2 font-semibold border-b">Total</th>
                    <th class="px-4 py-2 font-semibold border-b">Estado</th>
                    <th class="px-4 py-2 font-semibold border-b">Evidencia</th>
                    <th class="px-4 py-2 font-semibold border-b">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach ($pedidos as $pedido)
                    @php
                        $externo   = $pedido->externo;
                        $ubicacion = $externo->ubicacion ? explode(',', $externo->ubicacion) : null;
                    @endphp
                    <tr
                        data-pedido-id="{{ $pedido->id }}"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-4 py-2 border-b">{{ $pedido->id }}</td>
                        <td class="px-4 py-2 border-b">{{ $externo->nombre ?? 'Desconocido' }}</td>
                        <td class="px-4 py-2 border-b">
                            @if($externo->numero_telefono)
                                <a
                                    href="https://wa.me/591{{ $externo->numero_telefono }}"
                                    target="_blank"
                                    class="text-blue-600 hover:underline"
                                >
                                    +591 {{ $externo->numero_telefono }}
                                </a>
                            @else
                                <span class="text-gray-500">Sin teléfono</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b">
                            @if($externo->foto_referencia)
                                <a
                                    href="#"
                                    class="view-image text-blue-600 hover:underline"
                                    data-image-src="{{ asset('storage/'.$externo->foto_referencia) }}"
                                >
                                    Ver imagen
                                </a>
                            @else
                                <span class="text-gray-500">Sin foto</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b">
                            @if($ubicacion && count($ubicacion) === 2)
                                <a
                                    href="#"
                                    class="view-location text-blue-600 hover:underline"
                                    data-lat="{{ trim($ubicacion[0]) }}"
                                    data-lng="{{ trim($ubicacion[1]) }}"
                                >
                                    Ver ubicación
                                </a>
                            @else
                                <span class="text-gray-500">Sin ubicación</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b">{{ $pedido->cantidad }}</td>
                        <td class="px-4 py-2 border-b">${{ number_format($pedido->total, 2) }}</td>
                        <td class="px-4 py-2 border-b">
                            <select
                                class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring focus:ring-primary text-sm status-select"
                                data-pedido-id="{{ $pedido->id }}"
                            >
                                <option value="pendiente" {{ $pedido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_preparacion" {{ $pedido->estado == 'en_preparacion' ? 'selected' : '' }}>En preparación</option>
                                <option value="enviado" {{ $pedido->estado == 'enviado' ? 'selected' : '' }}>Enviado</option>
                            </select>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <form class="evidence-form" enctype="multipart/form-data">
                                @csrf
                                <input
                                    type="file"
                                    name="evidence"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-secondary evidence-input"
                                    data-pedido-id="{{ $pedido->id }}"
                                    accept="image/*"
                                />
                                @if($pedido->foto_evidencia)
                                    <img
                                        src="{{ asset('storage/'.$pedido->foto_evidencia) }}"
                                        alt="Evidencia"
                                        class="mt-2 w-16 h-auto rounded shadow"
                                    />
                                @endif
                            </form>
                        </td>
                        <td class="px-4 py-2 border-b">
                            {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<!-- Botón "Volver" -->
<a
    href="{{ route('home') }}"
    class="inline-block mt-4 bg-secondary text-white px-4 py-2 rounded hover:bg-secondary/80 transition"
>
    Volver
</a>

<!-- Scripts de Leaflet y lógica del mapa -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Marcadores JSON generados en PHP
    const eventLocations = @json($markers['evento']);
    const userLocations = @json($markers['usuarios']);

    // Inicializar mapa
    let map = L.map('map');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Iconos de evento y usuarios
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

    const markersGroup = L.featureGroup();

    // 1) Marcadores del evento
    eventLocations.forEach((loc, index) => {
        if (!isNaN(loc.lat) && !isNaN(loc.lng)) {
            L.marker([loc.lat, loc.lng], { icon: eventIcon })
                .bindPopup(`<b>${loc.nombre}</b><br>Ubicación del evento #${index + 1}`)
                .addTo(markersGroup);
        }
    });

    // 2) Marcadores de usuarios
    const userMarkers = {};
    userLocations.forEach(loc => {
        const lat = parseFloat(loc.lat);
        const lng = parseFloat(loc.lng);
        if (!isNaN(lat) && !isNaN(lng)) {
            userMarkers[loc.pedido_id] = L.marker([lat, lng], { icon: userIcon })
                .bindPopup(`<b>${loc.nombre}</b><br>Tel: ${loc.telefono}`)
                .addTo(markersGroup);
        }
    });

    markersGroup.addTo(map);

    // Ajustar vista al contenido
    if (markersGroup.getLayers().length > 0) {
        map.fitBounds(markersGroup.getBounds());
    } else {
        // Si no hay marcadores, centrar en Bolivia
        map.setView([-16.2902, -63.5887], 6);
    }

    // --------------------------
    // Lógica para ver imagen (modal)
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const closeModal = document.getElementById('closeModal');

    // Abrir modal al hacer clic en "Ver imagen"
    document.querySelectorAll('.view-image').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            modalImage.src = this.dataset.imageSrc;
            imageModal.classList.remove('hidden');
        });
    });

    // Cerrar modal
    closeModal.addEventListener('click', () => {
        imageModal.classList.add('opacity-0');
        imageModal.classList.add('hidden');
        modalImage.src = '';
        imageModal.classList.remove('opacity-0');
    });

    // Ver ubicación de usuario (centrar mapa y abrir popup)
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
                if (!response.ok) {
                    alert('Error actualizando estado');
                }
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
