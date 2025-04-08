@extends('layouts.app')
@php
    $usuario = Auth::user(); // Esto cubre tanto admin, master como superadmin
@endphp
@section('title', 'Pedidos del Evento')

{{-- Hoja de estilos de Leaflet --}}
@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
@php
    // Verificar acceso
    if(!in_array($usuario->rol, ['master', 'admin', 'superadmin'])) {
        echo '<div class="bg-red-100 text-red-800 p-4 rounded shadow">No tienes acceso a esta sección.</div>';
        return;
    }
@endphp

<h2 class="text-2xl font-bold mb-4">Pedidos del Evanto: {{ $evento->nombre }}</h2>
<p class="mb-2">Total de pedidos para este evento: <strong>{{ $pedidos->count() }}</strong></p>

@if ($pedidos->isEmpty())
    <p class="text-gray-700 mb-6">No hay pedidos registrados para este evento.</p>
@else
    <div class="overflow-x-auto bg-white shadow rounded mb-8">
        <table class="w-full table-auto text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Nombre (Usuario)</th>
                    <th class="px-4 py-2">Producto</th>
                    <th class="px-4 py-2">Precio Unitario</th>
                    <th class="px-4 py-2">Teléfono</th>
                    <th class="px-4 py-2">Foto Ref.</th>
                    <th class="px-4 py-2">Ubicación</th>
                    <th class="px-4 py-2">Cantidad</th>
                    <th class="px-4 py-2">Total</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Evidencia</th>
                    <th class="px-4 py-2">Fecha</th>
                </tr>
            </thead>


            <tbody class="divide-y">
                @foreach ($pedidos as $pedido)
                    @php
                        // Asumimos que la relación 'externo' devuelve el usuario externo
                        $externo = $pedido->externo;
                        // Si el usuario tiene ubicacion, se obtiene en array
                        $ubicacion = $externo && $externo->ubicacion 
                                    ? explode(',', $externo->ubicacion) 
                                    : null;
                    @endphp
                    <tr class="hover:bg-gray-50" data-pedido-id="{{ $pedido->id }}">
                        <td class="px-4 py-2">{{ $pedido->id }}</td>
                        <!-- Nombre del usuario externo -->
                        <td class="px-4 py-2">{{ $externo->nombre ?? 'Desconocido' }}</td>
                        <!-- Nombre del producto (nuevo) -->
                        <td class="px-4 py-2">{{ $pedido->nombre }}</td>
                        <!-- Precio unitario del producto (nuevo) -->
                        <td class="px-4 py-2">${{ number_format($pedido->precio, 2) }}</td>
                        <!-- Teléfono -->
                        <td class="px-4 py-2">
                            @if($externo && $externo->numero_telefono)
                                <a href="https://wa.me/591{{ $externo->numero_telefono }}" target="_blank"
                                class="text-blue-600 hover:underline">
                                    +591 {{ $externo->numero_telefono }}
                                </a>
                            @else
                                <span class="text-gray-500">Sin teléfono</span>
                            @endif
                        </td>
                        <!-- Foto Referencia -->
                        <td class="px-4 py-2">
                            @if($externo && $externo->foto_referencia)
                                <a href="#" class="view-image text-blue-600 hover:underline"
                                data-image-src="{{ asset('storage/externos_auth/'.$externo->foto_referencia) }}">
                                    Ver imagen
                                </a>
                            @else
                                <span class="text-gray-500">Sin foto</span>
                            @endif
                        </td>

                        <!-- Ubicación -->
                        <td class="px-4 py-2">
                            @if($ubicacion && count($ubicacion) === 2)
                                <a href="#" class="view-location text-blue-600 hover:underline"
                                data-lat="{{ trim($ubicacion[0]) }}"
                                data-lng="{{ trim($ubicacion[1]) }}">
                                    Ver ubicación
                                </a>
                            @else
                                <span class="text-gray-500">Sin ubicación</span>
                            @endif
                        </td>
                        <!-- Cantidad -->
                        <td class="px-4 py-2">{{ $pedido->cantidad }}</td>
                        <!-- Total -->
                        <td class="px-4 py-2">${{ number_format($pedido->total, 2) }}</td>
                        <!-- Estado -->
                        <td class="px-4 py-2">
                            <select class="border rounded px-2 py-1 status-select" data-pedido-id="{{ $pedido->id }}">
                                <option value="pendiente" {{ $pedido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_preparacion" {{ $pedido->estado == 'en_preparacion' ? 'selected' : '' }}>En preparación</option>
                                <option value="enviado" {{ $pedido->estado == 'enviado' ? 'selected' : '' }}>Enviado</option>
                            </select>
                        </td>
                        <!-- Evidencia -->
                        <td class="px-4 py-2">
                            <form class="evidence-form" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="evidence" class="block w-full text-sm text-gray-500 evidence-input" data-pedido-id="{{ $pedido->id }}" accept="image/*">
                                @if($pedido->foto_evidencia)
                                    <img src="{{ asset('storage/'.$pedido->foto_evidencia) }}" alt="Evidencia" class="mt-2 w-16 h-auto rounded shadow">
                                @endif
                            </form>
                        </td>
                        <!-- Fecha -->
                        <td class="px-4 py-2">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
@endif

@php
    // ================================
    // 2. MAPA CON UBICACIONES
    // ================================

    // Armamos markers para el evento y para los usuarios inscritos (tabla externos).
    $markers = [
        'evento' => [],
        'usuarios' => [],
    ];

    // -- Ubicación del evento (puede tener múltiples coords separadas por ";")
    $coordsEvento = array_filter(explode(';', $evento->ubicacion ?? ''));
    foreach ($coordsEvento as $coord) {
        $parts = explode(',', $coord);
        if (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            $markers['evento'][] = [
                'lat' => (float) trim($parts[0]),
                'lng' => (float) trim($parts[1]),
                'nombre' => $evento->nombre,
            ];
        }
    }

    // -- Ubicaciones de los usuarios inscritos (tabla externos)
    //    Debe coincidir 'evento_id' con $evento->id
    $externos = \App\Models\Externo::where('evento_id', $evento->id)->get();

    foreach ($externos as $ex) {
        if (!$ex->ubicacion) continue;
        $exCoords = explode(',', $ex->ubicacion);
        if (count($exCoords) == 2 && is_numeric($exCoords[0]) && is_numeric($exCoords[1])) {
            $markers['usuarios'][] = [
                'lat'     => (float) trim($exCoords[0]),
                'lng'     => (float) trim($exCoords[1]),
                'nombre'  => $ex->nombre,
                'telefono'=> $ex->numero_telefono ?? '',
                'foto' => $ex->foto_referencia 
                    ? asset('storage/externos_auth/'.$ex->foto_referencia)
                    : null,

            ];
        }
    }
@endphp

<!-- Mapa de Usuarios Inscritos -->
<h3 class="text-xl font-bold mb-4">Mapa de Usuarios Inscritos</h3>
<div id="map" class="w-full mb-8 rounded border border-gray-300" style="height: 500px;"></div>

@php
    // ================================
    // 3. TABLA DE USUARIOS INSCRITOS
    // ================================
@endphp
<h3 class="text-xl font-bold mb-4">Usuarios Inscritos al Evento</h3>
@if($externos->isEmpty())
    <p class="text-gray-600">No hay usuarios inscritos en este evento.</p>
@else
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="w-full table-auto text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Teléfono</th>
                    <th class="px-4 py-2">Foto Ref.</th>
                    <th class="px-4 py-2">Ubicación</th>
                    <th class="px-4 py-2">Rol</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($externos as $ex)
                    @php
                        $exUbic = $ex->ubicacion ? explode(',', $ex->ubicacion) : null;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $ex->id }}</td>
                        <td class="px-4 py-2">{{ $ex->nombre }}</td>
                        <td class="px-4 py-2">
                            @if($ex->numero_telefono)
                                <a href="https://wa.me/591{{ $ex->numero_telefono }}" target="_blank"
                                   class="text-blue-600 hover:underline">
                                    +591 {{ $ex->numero_telefono }}
                                </a>
                            @else
                                <span class="text-gray-500">Sin teléfono</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if($ex->foto_referencia)
                                <a href="#" class="view-image text-blue-600 hover:underline"
                                data-image-src="{{ asset('storage/externos_auth/'.$ex->foto_referencia) }}">
                                    Ver imagen
                                </a>
                            @else
                                <span class="text-gray-500">Sin foto</span>
                            @endif
                        </td>

                        <td class="px-4 py-2">
                            @if($exUbic && count($exUbic) === 2)
                                <a href="#" class="view-ext-location text-blue-600 hover:underline"
                                   data-lat="{{ trim($exUbic[0]) }}"
                                   data-lng="{{ trim($exUbic[1]) }}">
                                    Ver ubicación
                                </a>
                            @else
                                <span class="text-gray-500">Sin ubicación</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            {{ $ex->rol ?? '---' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- Modal de imagen (oculto por defecto) --}}
{{-- Modal de imagen (oculto por defecto) --}}
<div id="imageModal" 
     class="fixed inset-0 bg-black/60 hidden items-center justify-center" 
     style="z-index: 9999;">
    <div class="bg-white p-4 rounded shadow max-w-lg w-full relative">
        <button id="closeModal" 
                class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">
            &times;
        </button>
        <img id="modalImage" src="" 
             alt="Imagen de referencia" 
             class="w-full h-auto rounded">
    </div>
</div>


{{-- Botón "Volver" --}}
<a href="{{ route('home') }}"
   class="inline-block mt-6 bg-secondary text-white px-4 py-2 rounded hover:bg-secondary/80">
    Volver
</a>
@endsection

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // =============== VARIABLES PARA MAPA ===============
    const markersData = @json($markers);
    // markersData['evento'] => array de coords del evento
    // markersData['usuarios'] => array de coords de externos (con foto/no foto)

    // Inicializar mapa
    let map = L.map('map');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Iconos
    const eventIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [0, -41]
    });

    const userIconNormal = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [0, -41]
    });

    const userIconWithPhoto = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [0, -41]
    });

    let markersGroup = L.featureGroup();

    // =============== MARCADORES DE EVENTO ===============
    markersData.evento.forEach((ev, index) => {
        if (!isNaN(ev.lat) && !isNaN(ev.lng)) {
            L.marker([ev.lat, ev.lng], { icon: eventIcon })
             .bindPopup(`<b>${ev.nombre}</b><br>Ubicación #${index + 1}`)
             .addTo(markersGroup);
        }
    });

    // =============== MARCADORES DE USUARIOS ===============
    window.userMarkers = [];
    markersData.usuarios.forEach((u, idx) => {
        if (!isNaN(u.lat) && !isNaN(u.lng)) {
            let iconToUse = u.foto ? userIconWithPhoto : userIconNormal;
            let popupContent = `<b>${u.nombre}</b><br>Tel: ${u.telefono}`;
            if (u.foto) {
                popupContent += `<br><a href="#" class="popup-image text-blue-600" data-img="${u.foto}">Ver imagen</a>`;
            }
            let marker = L.marker([u.lat, u.lng], { icon: iconToUse })
                          .bindPopup(popupContent)
                          .addTo(markersGroup);
            window.userMarkers.push(marker);
        }
    });

    markersGroup.addTo(map);

    if (markersGroup.getLayers().length > 0) {
        map.fitBounds(markersGroup.getBounds());
    } else {
        map.setView([-16.2902, -63.5887], 6);
    }

    // =============== MODAL IMAGEN EN POPUP ===============
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const closeModal = document.getElementById('closeModal');

    closeModal.addEventListener('click', () => {
        imageModal.classList.add('hidden');
        modalImage.src = '';
    });

    map.on('popupopen', function(e) {
        let popupNode = e.popup._contentNode;
        let link = popupNode.querySelector('.popup-image');
        if (link) {
            link.addEventListener('click', function(ev) {
                ev.preventDefault();
                let imgSrc = this.dataset.img;
                modalImage.src = imgSrc;
                imageModal.classList.remove('hidden');
            });
        }
    });

    document.querySelectorAll('.view-image').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            modalImage.src = link.dataset.imageSrc;
            imageModal.classList.remove('hidden');
        });
    });


    document.querySelectorAll('.view-location').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const lat = parseFloat(link.dataset.lat);
            const lng = parseFloat(link.dataset.lng);
            if (!isNaN(lat) && !isNaN(lng)) {
                map.flyTo([lat, lng], 18);
            }
        });
    });

    document.querySelectorAll('.view-ext-location').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const lat = parseFloat(link.dataset.lat);
            const lng = parseFloat(link.dataset.lng);
            if (!isNaN(lat) && !isNaN(lng)) {
                map.flyTo([lat, lng], 18);
            }
        });
    });

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
            }).then(resp => {
                if (!resp.ok) {
                    alert('Error actualizando estado');
                }
            });
        });
    });

    document.querySelectorAll('.evidence-input').forEach(input => {
        input.addEventListener('change', function() {
            const pedidoId = this.dataset.pedidoId;
            const formData = new FormData(this.closest('form'));

            fetch(`/pedidos/${pedidoId}/evidence`, {
                method: 'POST', // Se usa POST para archivos
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    response.text().then(txt => console.error(txt));
                    alert('Error subiendo evidencia');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error de conexión o servidor');
            });
        });
    });

    setTimeout(() => {
        map.invalidateSize();
    }, 400);
});
</script>


