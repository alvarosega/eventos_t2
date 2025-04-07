@extends('layouts.app')

@section('title', 'Pantalla Principal')

@section('content')
  {{-- Mensajes de Éxito / Error --}}
  @if (session('success'))
    <div class="mb-4 p-4 border-l-4 border-green-500 bg-green-100 dark:bg-green-200 text-green-700 dark:text-green-900 rounded shadow-md transition-transform duration-300 transform hover:scale-[1.01]">
      <i class="fas fa-check-circle mr-2"></i>
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mb-4 p-4 border-l-4 border-red-500 bg-red-100 dark:bg-red-200 text-red-700 dark:text-red-900 rounded shadow-md transition-transform duration-300 transform hover:scale-[1.01]">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      {{ session('error') }}
    </div>
  @endif

  {{-- Título de Bienvenida --}}
  <h2 class="text-2xl font-bold mb-4 flex items-center space-x-2">
    <i class="fas fa-user-check text-primary drop-shadow-md"></i>
    <span>Bienvenido, {{ $usuario->nombre_completo ?? $usuario->nombre }}</span>
    <small class="text-secondary text-base font-normal">({{ $usuario->rol }})</small>
  </h2>

  @if($usuario->rol == 'externo')
  {{-- Sección para Usuarios Externos --}}
  <div class="bg-white shadow rounded mb-6 overflow-hidden transform transition duration-300 hover:scale-[1.01]">
    <div class="bg-primary text-white p-4">
      <h3 class="text-xl font-semibold">
        @if($usuario->evento_id)
          Mi Evento Actual
        @else
          Eventos Disponibles
        @endif
      </h3>
    </div>
    <div class="p-4 text-dark">
      @if($usuario->evento_id)
        @php
          $miEvento = $eventos->firstWhere('id', $usuario->evento_id);
        @endphp

        @if($miEvento)
          <!-- Detalles del evento inscrito -->
            <div class="p-4 border-t border-gray-200 flex justify-between">
              <a href="{{ route('pedidos.create', $miEvento->id) }}" class="inline-block bg-green-600 text-white text-sm px-3 py-2 rounded transition-colors hover:bg-green-700">
                <i class="fas fa-cart-plus"></i> Nuevo Pedido
              </a>
              <a href="{{ route('inscripciones.cancelForm', $miEvento->id) }}" class="inline-block border border-red-600 text-red-600 text-sm px-3 py-2 rounded transition-colors hover:bg-red-600 hover:text-white">
                <i class="fas fa-times-circle"></i> Cancelar Inscripción
              </a>
            </div>
            {{-- NUEVA SECCIÓN: Lista de Mis Pedidos --}}
          <div class="bg-white shadow rounded p-4">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-xl font-bold">Mis Pedidos</h4>
              @if(isset($pedidos) && $pedidos->isNotEmpty())
              @endif
            </div>
            @if($pedidos->isEmpty())
              <p class="text-gray-700">No tienes pedidos registrados.</p>
            @else
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">ID</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Cantidad</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Total</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Fecha</th>
                      <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Estado</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    @foreach($pedidos as $pedido)
                      <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $pedido->id }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $pedido->cantidad }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ number_format($pedido->total, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2 text-sm">
                          @if ($pedido->estado == 'pendiente')
                            <span class="px-2 py-1 bg-yellow-300 text-yellow-800 rounded-full text-xs">Pendiente</span>
                          @elseif ($pedido->estado == 'en_preparacion')
                            <span class="px-2 py-1 bg-blue-300 text-blue-800 rounded-full text-xs">En Preparación</span>
                          @elseif ($pedido->estado == 'enviado')
                            <span class="px-2 py-1 bg-indigo-300 text-indigo-800 rounded-full text-xs">Enviado</span>
                          @elseif ($pedido->estado == 'entregado')
                            <span class="px-2 py-1 bg-green-300 text-green-800 rounded-full text-xs">Entregado</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>

          {{-- NUEVA SECCIÓN: Mapa de Ubicaciones del Evento --}}
          <div class="mb-6">
            <h4 class="text-xl font-bold mb-2">Ubicación del Evento</h4>
            <div id="map" class="w-full h-64 border border-gray-300 rounded shadow"></div>
          </div>


        @else
          <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded shadow-sm">
            Tu evento registrado no está disponible. Por favor, contacta al organizador.
          </div>
        @endif
      @else
        {{-- Mostrar eventos disponibles para inscripción --}}
        @if($eventos->isEmpty())
          <div class="mb-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded shadow-sm">
            No hay eventos disponibles en este momento.
          </div>
        @else
          <div class="flex flex-wrap -mx-4">
            @foreach($eventos as $evento)
              @if($evento->estado != 'finalizado')
                <div class="w-full md:w-1/2 lg:w-1/3 px-4 mb-4">
                  <div class="bg-white border border-gray-200 rounded shadow hover:shadow-lg h-full flex flex-col transform transition hover:scale-[1.02]">
                    <div class="p-4 border-b border-gray-200">
                      <h5 class="text-lg font-bold mb-1">{{ $evento->nombre }}</h5>
                      <small class="text-gray-600">ID: {{ $evento->id }}</small>
                    </div>
                    <div class="p-4 flex-1">
                      <p class="mb-1">
                        <strong>Estado:</strong>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $evento->estado == 'activo' ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                          {{ $evento->estado }}
                        </span>
                      </p>
                      <p class="mb-1"><strong>Inicio:</strong> {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}</p>
                      <p class="mb-2"><strong>Fin:</strong> {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}</p>
                      <p class="text-sm text-gray-700">{{ Str::limit($evento->descripcion, 100) }}</p>
                    </div>
                    <div class="p-4 border-t border-gray-200 text-center">
                      <a href="{{ route('inscripciones.showMapa', $evento->id) }}" class="inline-block bg-primary text-white text-sm px-3 py-2 rounded transition-colors hover:bg-secondary">
                        <i class="fas fa-map-marker-alt"></i> Inscribirme
                      </a>
                    </div>
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
    {{-- Sección para Administradores y Superadmin --}}
    <div class="bg-white dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-100 rounded mb-6 overflow-hidden">
      <div class="bg-primary text-white p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center">
          <i class="fas fa-tools mr-2"></i>
          <h3 class="text-xl font-semibold">Gestión de Eventos</h3>
        </div>
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center space-x-2">
            <span class="text-white">Tipo 1</span>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" id="eventTypeToggle" class="sr-only peer" {{ $eventType == 'tipo2' ? 'checked' : '' }}>
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
            </label>
            <span class="text-white">Tipo 2</span>
          </div>
          @if($usuario->rol == 'superadmin')
            <div class="flex space-x-2">
              <a href="{{ route('eventos.select-type') }}" class="btn-secondary inline-flex items-center space-x-1 transition-colors hover:text-secondary">
                <i class="fas fa-plus"></i>
                <span>Nuevo Evento</span>
              </a>
              @if($eventType == 'tipo1')
                <a href="{{ route('catalogos.index') }}" class="btn-secondary inline-flex items-center space-x-1 transition-colors hover:text-secondary" id="catalogosBtn">
                  <i class="fas fa-book"></i>
                  <span>Catálogos</span>
                </a>
              @endif
            </div>
          @endif
        </div>
      </div>

      <div class="p-4">
        @if($eventos->isEmpty())
          <div class="alert-info p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded shadow-sm transition">
            <i class="fas fa-info-circle mr-2"></i>
            No hay eventos registrados
          </div>
        @else
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($eventos as $evento)
              <div class="event-card bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded shadow hover:shadow-xl transition transform hover:scale-[1.02] p-4">
                <div class="event-header border-b border-gray-200 pb-2 mb-2">
                  <h5 class="font-bold text-lg">{{ $eventType == 'tipo1' ? $evento->nombre : $evento->evento }}</h5>
                  <small class="text-gray-500">ID: {{ $evento->id }}</small>
                  @if($eventType == 'tipo2')
                    <span class="event-badge inline-block bg-secondary text-white text-xs px-2 py-1 rounded-full ml-2">Tipo 2</span>
                  @endif
                </div>
                <div class="event-body mb-2">
                  @if($eventType == 'tipo1')
                    <div class="event-field mb-1">
                      <strong>Estado:</strong>
                      <span class="status-badge inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $evento->estado == 'activo' ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                        {{ $evento->estado }}
                      </span>
                    </div>
                    <div class="event-field mb-1">
                      <strong>Inicio:</strong> {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}
                    </div>
                    <div class="event-field mb-1">
                      <strong>Fin:</strong> {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}
                    </div>
                  @else
                    <div class="event-field mb-1">
                      <strong>Fecha:</strong> {{ $evento->fecha }}
                    </div>
                    <div class="event-field mb-1">
                      <strong>Encargado:</strong> {{ $evento->encargado }}
                    </div>
                    <div class="event-field mb-1">
                      <strong>Hora Entrega:</strong> {{ $evento->hor_entrega }}
                    </div>
                    <div class="event-field mb-1">
                      <strong>Estado:</strong>
                      <span class="status-badge inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $evento->estado_evento == 'completado' ? 'bg-success text-white' : ($evento->estado_evento == 'en_proceso' ? 'bg-yellow-500 text-white' : 'bg-secondary text-white') }}">
                        {{ $evento->estado_evento }}
                      </span>
                    </div>
                  @endif
                  <div class="event-description text-sm text-gray-700">
                    {{ Str::limit($eventType == 'tipo1' ? $evento->descripcion : $evento->material, 100) }}
                  </div>
                </div>
                <div class="event-footer border-t border-gray-200 pt-2">
                  <div class="flex justify-between items-center mb-2">
                    @if($eventType == 'tipo1')
                      <a href="{{ route('catalogos.show', $evento->id) }}" class="btn-catalog inline-block bg-primary text-white text-sm px-3 py-2 rounded transition-colors hover:bg-secondary">
                        <i class="fas fa-box-open mr-1"></i> Catálogo
                      </a>
                    @else
                      <span class="text-gray-500 text-sm">Evento Logístico</span>
                    @endif
                    @if($usuario->rol == 'superadmin')
                      <div class="flex space-x-2">
                        <a href="{{ $eventType == 'tipo1' ? route('eventos.edit', $evento->id) : route('eventos.edit-tipo2', $evento->id) }}" class="btn-edit inline-block text-gray-700 hover:text-primary transition-colors">
                          <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ $eventType == 'tipo1' ? route('eventos.destroy', $evento->id) : route('eventos.destroy-tipo2', $evento->id) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn-delete inline-block text-gray-700 hover:text-red-600 transition-colors" onclick="return confirm('¿Eliminar este evento?')">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    @endif
                  </div>
                  @if($eventType == 'tipo1')
                    <a href="{{ route('pedidos.evento', $evento->id) }}" class="btn-orders inline-block bg-primary text-white text-sm px-3 py-2 rounded transition-colors hover:bg-secondary">
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
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
  let switchToggle = document.getElementById('eventTypeToggle');
  if (switchToggle) {
    switchToggle.addEventListener('change', function () {
      let eventType = this.checked ? 'tipo2' : 'tipo1';
      // Crear una nueva URL basada en la URL actual
      let currentUrl = new URL(window.location.href);
      currentUrl.searchParams.set('type', eventType);
      // Redirigir a la nueva URL
      window.location.replace(currentUrl.toString());
    });
  }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  let switchToggle = document.getElementById('eventTypeToggle');
  let catalogosBtn = document.getElementById('catalogosBtn');
  // Ocultar "Administrar Catálogos" si es tipo2 al cargar la página
  if (catalogosBtn && switchToggle.checked) {
    catalogosBtn.style.display = 'none';
  }
  if (switchToggle) {
    switchToggle.addEventListener('change', function () {
      let eventType = this.checked ? 'tipo2' : 'tipo1';
      // Si se selecciona tipo2, ocultar "Administrar Catálogos"
      if (catalogosBtn) {
        catalogosBtn.style.display = eventType === 'tipo2' ? 'none' : 'inline-block';
      }
      // Cambiar la URL sin recargar la página
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
        // Obtener la ubicación del evento; se espera que $miEvento->ubicacion contenga "lat,lng;lat,lng;..."
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
        // Inicializar mapa en el div con id 'map'
        const map = L.map('map').setView([eventoLat, eventoLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
        // Icono personalizado para las ubicaciones del evento
        const eventIcon = L.icon({
          iconUrl: '{{ Vite::asset("resources/images/imagenes/evento.png") }}',
          iconSize: [32, 32],
          iconAnchor: [16, 32],
          popupAnchor: [0, -32]
        });
        // Agregar marcadores para cada coordenada válida
        coordenadasValidas.forEach((coord, index) => {
          L.marker([coord.lat, coord.lng], { icon: eventIcon })
            .bindPopup(`Punto ${index + 1}: {{ $miEvento->nombre }}`)
            .addTo(map);
        });
      @endif
    });
  </script>
