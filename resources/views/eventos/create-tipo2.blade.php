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

    <form id="formularioEvento" onsubmit="return validarFormulario()" action="{{ route('eventos.store-tipo2') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 p-4 rounded shadow-lg transition-all duration-300 max-w-4xl">
        @csrf

        <!-- Fila 1: Fecha y Nombre del Evento -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="fecha" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-calendar-day mr-1"></i> Fecha del Evento
                </label>
                <input type="date" id="fecha" name="fecha" required class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div>
                <label for="evento" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-signature mr-1"></i> Nombre del Evento
                </label>
                <input type="text" id="evento" name="evento" required class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
        </div>

        <!-- Fila 2: Encargado y Celular -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="encargado" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-user-tie mr-1"></i> Encargado
                </label>
                <input type="text" id="encargado" name="encargado" required class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div>
                <label for="celular" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-mobile-alt mr-1"></i> Celular
                </label>
                <input type="text" id="celular" name="celular" required pattern="\d{8}" title="Debe contener 8 dígitos" class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
        </div>

        <!-- Dirección -->
        <div class="mb-4">
            <label for="direccion" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-map-marker-alt mr-1"></i> Dirección
            </label>
            <input type="text" id="direccion" name="direccion" required class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
        </div>

        <!-- Materiales Dinámicos -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-boxes mr-1"></i> Materiales
            </label>
            <div id="materialContainer"></div>
            <button type="button" onclick="addMaterialRow()" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded mt-2">
                Agregar Material
            </button>
            @if($usuario->rol === 'master')
                <div class="mt-2">
                    <input type="text" id="nuevoMaterial" placeholder="Nuevo material" class="px-2 py-1 border rounded">
                    <button type="button" onclick="addNewMaterial()" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                        Agregar a lista
                    </button>
                </div>
            @endif
        </div>

        <!-- Fotos de Recepción -->
        <div class="mb-4">
            <label for="fotos_recepcion" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-image mr-1"></i> Fotos de Recepción
            </label>
            <input type="file" id="fotos_recepcion" name="fotos_recepcion[]" accept="image/*" multiple class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded">
        </div>

        <!-- Fila 3: Hora de Entrega y Recojo (Fecha y Hora) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="hor_entrega" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-clock mr-1"></i> Hora de Entrega
                </label>
                <input type="time" id="hor_entrega" name="hor_entrega" required class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
            <div>
                <label for="recojo" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-truck-loading mr-1"></i> Recojo (Fecha y Hora)
                </label>
                <input type="datetime-local" id="recojo" name="recojo" required class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
            </div>
        </div>

        <!-- Fila 4: Operador y Supervisor / Legajo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="operador" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-hard-hat mr-1"></i> Operador
                </label>
                <select id="operador" name="operador" required class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
                    <!-- Las opciones se cargarán vía JavaScript -->
                </select>
                @if($usuario->rol === 'master')
                    <div class="mt-2">
                        <input type="text" id="nuevoOperador" placeholder="Nuevo Operador" class="px-2 py-1 border rounded">
                        <button type="button" onclick="addNewOperator()" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                            Agregar Operador
                        </button>
                    </div>
                @endif
            </div>

            <div>
                <label for="supervisor" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                    <i class="fas fa-user-shield mr-1"></i> Supervisor
                </label>

                @php $usuario = Auth::user(); @endphp

                @if($usuario->rol === 'master' && isset($empleados))
                    <select id="supervisor" name="supervisor" required onchange="asignarLegajo(this)"
                            class="w-full mb-2 px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Selecciona Supervisor</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->nombre_completo }}" data-legajo="{{ $empleado->legajo }}">
                                {{ $empleado->nombre_completo }}
                            </option>
                        @endforeach
                    </select>

                    <label for="legajo" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">Legajo</label>
                    <input type="text" id="legajo" name="legajo" readonly class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-400 rounded text-gray-700 dark:text-gray-100">
                @else
                    <input type="text" id="supervisor" name="supervisor" value="{{ $usuario->nombre_completo }}" readonly class="w-full mb-2 px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-400 rounded text-gray-700 dark:text-gray-100">

                    <label for="legajo" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">Legajo</label>
                    <input type="text" id="legajo" name="legajo" value="{{ $usuario->legajo }}" readonly class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-400 rounded text-gray-700 dark:text-gray-100">
                @endif
            </div>
        </div>

        <!-- Estado del Evento -->
        <div class="mb-4">
            <label for="estado_evento" class="block mb-1 font-semibold text-sm text-secondary dark:text-gray-200">
                <i class="fas fa-info-circle mr-1"></i> Estado del Evento
            </label>
            <select id="estado_evento" name="estado_evento" required class="w-full px-3 py-2 border border-secondary dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-gray-100">
                <option value="pendiente">Pendiente</option>
                <option value="aprobado">Aprobado</option>
                <option value="cancelado">Cancelado</option>
                <option value="rechazado">Rechazado</option>
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
        // Función para asignar el legajo según el supervisor seleccionado
        function asignarLegajo(selectElement) {
            var legajo = selectElement.options[selectElement.selectedIndex].getAttribute('data-legajo');
            document.getElementById('legajo').value = legajo || '';
        }

        // Inicialización del mapa con Leaflet
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

        // SECCIÓN: Materiales Dinámicos
        let materialOptions = ["vallas", "kiosko", "tarima", "plataformas", "sillas"];

        function addMaterialRow() {
            const container = document.getElementById('materialContainer');
            const row = document.createElement('div');
            row.className = "flex flex-wrap items-center gap-2 mb-2";

            // Select para elegir el material
            const select = document.createElement('select');
            select.name = "material_item[]";
            select.required = true;
            select.className = "px-2 py-1 border rounded";
            materialOptions.forEach(function(option) {
                const opt = document.createElement('option');
                opt.value = option;
                opt.text = option.charAt(0).toUpperCase() + option.slice(1);
                select.appendChild(opt);
            });

            // Input para cantidad
            const quantity = document.createElement('input');
            quantity.type = "number";
            quantity.name = "material_quantity[]";
            quantity.required = true;
            quantity.placeholder = "Cantidad";
            quantity.min = "1";
            quantity.className = "px-2 py-1 border rounded w-24";

            // Input para foto entregada
            const photo = document.createElement('input');
            photo.type = "file";
            photo.name = "material_photo[]";
            photo.accept = "image/*";
            photo.required = true;
            photo.className = "px-2 py-1 border rounded";

            // Botón para eliminar la fila
            const removeBtn = document.createElement('button');
            removeBtn.type = "button";
            removeBtn.innerText = "Eliminar";
            removeBtn.className = "bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded";
            removeBtn.onclick = function() {
                container.removeChild(row);
            };

            row.appendChild(select);
            row.appendChild(quantity);
            row.appendChild(photo);
            row.appendChild(removeBtn);

            container.appendChild(row);
        }

        function addNewMaterial() {
            const newMatInput = document.getElementById('nuevoMaterial');
            const newMat = newMatInput.value.trim();
            if(newMat && !materialOptions.includes(newMat.toLowerCase())){
                materialOptions.push(newMat.toLowerCase());
                updateMaterialSelectOptions();
                newMatInput.value = '';
            } else {
                alert("Material ya existe o valor inválido");
            }
        }

        function updateMaterialSelectOptions() {
            const selects = document.querySelectorAll('select[name="material_item[]"]');
            selects.forEach(function(select) {
                const selectedValue = select.value;
                select.innerHTML = "";
                materialOptions.forEach(function(option) {
                    const opt = document.createElement('option');
                    opt.value = option;
                    opt.text = option.charAt(0).toUpperCase() + option.slice(1);
                    select.appendChild(opt);
                });
                if(materialOptions.includes(selectedValue)) {
                    select.value = selectedValue;
                }
            });
        }

        // SECCIÓN: Operador Dinámico
        let operatorOptions = ["CBN2", "Operador 2"];

        function updateOperatorOptions() {
            const operadorSelect = document.getElementById('operador');
            operadorSelect.innerHTML = '<option value="">Selecciona un operador</option>';
            operatorOptions.forEach(function(op) {
                const opt = document.createElement('option');
                opt.value = op;
                opt.text = op;
                operadorSelect.appendChild(opt);
            });
        }

        function addNewOperator() {
            const newOpInput = document.getElementById('nuevoOperador');
            const newOp = newOpInput.value.trim();
            if(newOp && !operatorOptions.includes(newOp)){
                operatorOptions.push(newOp);
                updateOperatorOptions();
                newOpInput.value = '';
            } else {
                alert("Operador ya existe o valor inválido");
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateOperatorOptions();
        });
    </script>
<script>
    function validarFormulario() {
        const ubicacion = document.getElementById('ubicacion').value;

        if (!ubicacion || ubicacion.trim() === '') {
            alert('Por favor, selecciona una ubicación en el mapa.');
            return false; // Evita que el formulario se envíe
        }

        return true; // Todo ok, se permite el envío
    }
</script>

@endsection
