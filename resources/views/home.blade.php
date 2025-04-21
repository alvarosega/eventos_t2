@extends('layouts.app')

@section('title', 'Pantalla Principal')

@section('content')
  {{-- Mensajes --}}
  @if (session('success'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm">
      <i class="fas fa-check-circle mr-2"></i>
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-800 rounded-lg shadow-sm">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      {{ session('error') }}
    </div>
  @endif

  {{-- Título --}}
  <div class="flex items-center gap-3 mb-6 p-3 bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="p-2 bg-primary text-white rounded-full">
      <i class="fas fa-user-check"></i>
    </div>
    <h2 class="text-xl font-bold">
      Bienvenido, {{ $usuario->nombre_completo ?? $usuario->nombre }}
      <span class="text-sm font-normal text-primary">({{ $usuario->rol }})</span>
    </h2>
  </div>

  @if($usuario->rol == 'externo')
  {{-- Sección Externo --}}
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6 border border-gray-200 dark:border-gray-700">
    <div class="bg-primary text-white p-3 rounded-t-lg">
      <h3 class="text-lg font-semibold">
        @if($usuario->evento_id)
          <i class="fas fa-calendar-day mr-2"></i> Mi Evento Actual
        @else
          <i class="fas fa-calendar-alt mr-2"></i> Eventos Disponibles
        @endif
      </h3>
    </div>

    <div class="p-4">
      @if($usuario->evento_id)
        @php $miEvento = $eventos->firstWhere('id', $usuario->evento_id); @endphp

        @if($miEvento)
          <div class="flex gap-3 mb-4">
            <a href="{{ route('pedidos.create', $miEvento->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm">
              <i class="fas fa-cart-plus mr-1"></i> Nuevo Pedido
            </a>
            <a href="{{ route('inscripciones.cancelForm', $miEvento->id) }}" class="border border-red-600 text-red-600 hover:bg-red-600 hover:text-white px-3 py-2 rounded-lg text-sm">
              <i class="fas fa-times-circle mr-1"></i> Cancelar Inscripción
            </a>
          </div>

          {{-- Pedidos --}}
          <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
            <h4 class="text-lg font-bold mb-3 flex items-center">
              <i class="fas fa-clipboard-list mr-2 text-primary"></i> Mis Pedidos
            </h4>
            
            @if($pedidos->isEmpty())
              <p class="text-gray-500 text-center py-2">No tienes pedidos registrados.</p>
            @else
              <div class="overflow-x-auto">
                <table class="min-w-full">
                  <thead class="bg-gray-200 dark:bg-gray-600">
                    <tr>
                      <th class="px-4 py-2 text-left">ID</th>
                      <th class="px-4 py-2 text-left">Cantidad</th>
                      <th class="px-4 py-2 text-left">Total</th>
                      <th class="px-4 py-2 text-left">Fecha</th>
                      <th class="px-4 py-2 text-left">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pedidos as $pedido)
                      <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600">
                        <td class="px-4 py-2">{{ $pedido->id }}</td>
                        <td class="px-4 py-2">{{ $pedido->cantidad }}</td>
                        <td class="px-4 py-2">{{ number_format($pedido->total, 2) }}</td>
                        <td class="px-4 py-2">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">
                          @if ($pedido->estado == 'pendiente')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Pendiente</span>
                          @elseif ($pedido->estado == 'en_preparacion')
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">En Preparación</span>
                          @elseif ($pedido->estado == 'enviado')
                            <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs">Enviado</span>
                          @elseif ($pedido->estado == 'entregado')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Entregado</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>

          {{-- Mapa --}}
          <div class="mb-4">
            <h4 class="text-lg font-bold mb-2 flex items-center">
              <i class="fas fa-map-marked-alt mr-2 text-primary"></i> Ubicación
            </h4>
            <div id="map" class="w-full h-64 border border-gray-300 rounded-lg"></div>
          </div>

        @else
          <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-3 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i>
            Tu evento registrado no está disponible.
          </div>
        @endif
      @else
        {{-- Eventos disponibles --}}
        @if($eventos->isEmpty())
          <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-3 rounded-lg">
            <i class="fas fa-info-circle mr-2"></i>
            No hay eventos disponibles.
          </div>
        @else
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($eventos as $evento)
              @if($evento->estado != 'finalizado')
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md">
                  <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="font-bold">{{ $evento->nombre }}</h5>
                    <small class="text-gray-500">ID: {{ $evento->id }}</small>
                  </div>
                  <div class="p-3">
                    <p class="mb-1">
                      <span class="font-medium">Estado:</span>
                      <span class="px-2 py-1 text-xs rounded-full {{ $evento->estado == 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $evento->estado }}
                      </span>
                    </p>
                    <p class="mb-1"><span class="font-medium">Inicio:</span> {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}</p>
                    <p class="mb-2"><span class="font-medium">Fin:</span> {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($evento->descripcion, 100) }}</p>
                  </div>
                  <div class="p-3 border-t border-gray-200 dark:border-gray-700 text-center">
                    <a href="{{ route('inscripciones.showMapa', $evento->id) }}" class="bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded text-sm">
                      <i class="fas fa-map-marker-alt mr-1"></i> Inscribirme
                    </a>
                  </div>
                </div>
              @endif
            @endforeach
          </div>
        @endif
      @endif
    </div>
  </div>
@else
  {{-- Sección Admin --}}
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6 border border-gray-200 dark:border-gray-700">
    <div class="bg-primary text-white p-3 rounded-t-lg flex flex-wrap justify-between items-center gap-3">
      <div class="flex items-center">
        <i class="fas fa-tools mr-2"></i>
        <h3 class="font-semibold">Gestión de Eventos</h3>
      </div>
      
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 bg-white/20 px-2 py-1 rounded-full">
          <span>Tipo 1</span>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="eventTypeToggle" class="sr-only peer" {{ $eventType == 'tipo2' ? 'checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
          </label>
          <span>Tipo 2</span>
        </div>
        
        @if($usuario->rol == 'superadmin')
          <div class="flex gap-2">
            <a href="{{ route('eventos.select-type') }}" class="bg-secondary hover:bg-secondary-dark text-white px-3 py-1 rounded text-sm">
              <i class="fas fa-plus mr-1"></i> Nuevo Evento
            </a>
            @if($eventType == 'tipo1')
              <a href="{{ route('catalogos.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm" id="catalogosBtn">
                <i class="fas fa-book mr-1"></i> Catálogos
              </a>
            @endif
          </div>
        @endif
      </div>
    </div>

    <div class="p-3">
      @if($eventos->isEmpty())
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-3 rounded-lg">
          <i class="fas fa-info-circle mr-2"></i>
          No hay eventos registrados
        </div>
      @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach($eventos as $evento)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md">
              <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                <h5 class="font-bold">{{ $eventType == 'tipo1' ? $evento->nombre : $evento->evento }}</h5>
                <small class="text-gray-500">ID: {{ $evento->id }}</small>
                @if($eventType == 'tipo2')
                  <span class="bg-gray-600 text-white px-2 py-0.5 rounded-full text-xs ml-2">Tipo 2</span>
                @endif
              </div>
              
              <div class="p-3">
                @if($eventType == 'tipo1')
                  <p class="mb-1">
                    <span class="font-medium">Estado:</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $evento->estado == 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                      {{ $evento->estado }}
                    </span>
                  </p>
                  <p class="mb-1"><span class="font-medium">Inicio:</span> {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}</p>
                  <p class="mb-1"><span class="font-medium">Fin:</span> {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}</p>
                @else
                  <p class="mb-1"><span class="font-medium">Fecha:</span> {{ $evento->fecha }}</p>
                  <p class="mb-1"><span class="font-medium">Encargado:</span> {{ $evento->encargado }}</p>
                  <p class="mb-1"><span class="font-medium">Hora Entrega:</span> {{ $evento->hor_entrega }}</p>
                  <p class="mb-1">
                    <span class="font-medium">Estado:</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $evento->estado_evento == 'completado' ? 'bg-green-100 text-green-800' : ($evento->estado_evento == 'en_proceso' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                      {{ $evento->estado_evento }}
                    </span>
                  </p>
                @endif
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                  {{ Str::limit($eventType == 'tipo1' ? $evento->descripcion : $evento->material, 100) }}
                </p>
              </div>
              
              <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-2">
                  @if($eventType == 'tipo1')
                    <a href="{{ route('catalogos.show', $evento->id) }}" class="bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded text-sm">
                      <i class="fas fa-box-open mr-1"></i> Catálogo
                    </a>
                  @else
                    <span class="text-sm text-gray-500">Evento Logístico</span>
                  @endif
                  
                  @if($usuario->rol == 'superadmin')
                    <div class="flex gap-2">
                      <a href="{{ $eventType == 'tipo1' ? route('eventos.edit', $evento->id) : route('eventos.edit-tipo2', $evento->id) }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-600">
                        <i class="fas fa-edit"></i>
                      </a>
                      <form action="{{ $eventType == 'tipo1' ? route('eventos.destroy', $evento->id) : route('eventos.destroy-tipo2', $evento->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-700 dark:text-gray-300 hover:text-red-600" onclick="return confirm('¿Eliminar este evento?')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  @endif
                </div>
                
                @if($eventType == 'tipo1')
                  <a href="{{ route('pedidos.evento', $evento->id) }}" class="block bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm text-center">
                    <i class="fas fa-clipboard-list mr-1"></i> Ver Pedidos
                  </a>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
@endif

@if($usuario->rol != 'externo')
<div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
  <h3 class="text-lg font-bold mb-3 flex items-center">
    <i class="fas fa-map-marked-alt mr-2 text-primary"></i>
    Mapa de Eventos {{ $eventType == 'tipo1' ? 'Tipo 1' : 'Tipo 2' }}
  </h3>
  <div id="adminMap" class="w-full h-96 border border-gray-300 rounded-lg"></div>
</div>
@endif

<!-- Scripts (mantenidos exactamente igual) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  let switchToggle = document.getElementById('eventTypeToggle');
  if (switchToggle) {
    switchToggle.addEventListener('change', function () {
      let eventType = this.checked ? 'tipo2' : 'tipo1';
      let currentUrl = new URL(window.location.href);
      currentUrl.searchParams.set('type', eventType);
      window.location.replace(currentUrl.toString());
    });
  }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  let switchToggle = document.getElementById('eventTypeToggle');
  let catalogosBtn = document.getElementById('catalogosBtn');
  if (catalogosBtn && switchToggle.checked) {
    catalogosBtn.style.display = 'none';
  }
  if (switchToggle) {
    switchToggle.addEventListener('change', function () {
      let eventType = this.checked ? 'tipo2' : 'tipo1';
      if (catalogosBtn) {
        catalogosBtn.style.display = eventType === 'tipo2' ? 'none' : 'inline-block';
      }
      let currentUrl = new URL(window.location.href);
      currentUrl.searchParams.set('type', eventType);
      window.location.replace(currentUrl.toString());
    });
  }
});
</script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    @if($usuario->rol == 'externo' && $usuario->evento_id && isset($miEvento))
      const ubicacionEvento = "{{ $miEvento->ubicacion }}";
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
      const map = L.map('map').setView([eventoLat, eventoLng], 14);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
      const eventIcon = L.icon({
        iconUrl: '{{ Vite::asset("resources/images/imagenes/evento.png") }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      });
      coordenadasValidas.forEach((coord, index) => {
        L.marker([coord.lat, coord.lng], { icon: eventIcon })
          .bindPopup(`Punto ${index + 1}: {{ $miEvento->nombre }}`)
          .addTo(map);
      });
    @endif
  });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ubicaciones = @json($ubicaciones);
    const eventType = "{{ $eventType }}";
    
    if (ubicaciones.length > 0) {
        const map = L.map('adminMap').setView([ubicaciones[0].coords[0].lat, ubicaciones[0].coords[0].lng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        const iconUrl = eventType === 'tipo1' 
            ? "{{ Vite::asset('resources/images/markers/evento-icon.png') }}"
            : "{{ Vite::asset('resources/images/markers/evento-tipo2-icon.jpeg') }}";
        
        const customIcon = L.icon({
            iconUrl: iconUrl,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });
        
        ubicaciones.forEach(evento => {
            if (evento.coords.length > 0) {
                evento.coords.forEach((coord, index) => {
                    L.marker([coord.lat, coord.lng], { icon: customIcon })
                        .bindPopup(`
                            <div class="font-bold">${evento.nombre}</div>
                            <div class="text-sm">${evento.tipo === 'tipo1' ? 'Evento Principal' : 'Evento Logístico'}</div>
                            <div class="text-xs mt-1">ID: ${evento.id}</div>
                        `)
                        .addTo(map);
                });
            }
        });
    } else {
        document.getElementById('adminMap').innerHTML = 
            '<div class="p-4 text-center text-gray-500">No hay ubicaciones disponibles</div>';
    }
});
</script>
@endsection