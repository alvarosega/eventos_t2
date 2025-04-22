@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        .form-section {
            background-color: #f9fafb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
        }
        .dark .form-section {
            background-color: #1f2937;
            border-color: #374151;
        }
        .input-label {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #4b5563;
        }
        .dark .input-label {
            color: #d1d5db;
        }
        .input-field {
            transition: all 0.2s;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }
        .material-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        @media (max-width: 768px) {
            .material-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('title', 'Crear Evento Tipo 2')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Encabezado mejorado -->
    <div class="flex items-start mb-8">
        <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full mr-4 mt-1">
            <i class="fas fa-truck-moving text-green-600 dark:text-green-300 text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Crear Evento Tipo 2
            </h1>
            <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">
                Complete todos los campos requeridos para registrar un nuevo evento de transporte
            </p>
        </div>
    </div>

    <!-- Formulario principal -->
    <form id="formularioEvento" onsubmit="return validarFormulario()" action="{{ route('eventos.store-tipo2') }}" method="POST" enctype="multipart/form-data" 
          class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
        @csrf

        <!-- Sección 1: Información básica -->
        <div class="form-section dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fas fa-info-circle text-green-500 mr-2"></i>
                Información Básica
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Fecha del Evento -->
                <div>
                    <label for="fecha" class="input-label">
                        <i class="fas fa-calendar-day text-green-500 mr-2"></i> Fecha del Evento
                    </label>
                    <input type="date" id="fecha" name="fecha" required 
                           class="w-full input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                </div>
                
                <!-- Nombre del Evento -->
                <div>
                    <label for="evento" class="input-label">
                        <i class="fas fa-signature text-green-500 mr-2"></i> Nombre del Evento
                    </label>
                    <input type="text" id="evento" name="evento" required 
                           class="w-full input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100" 
                           placeholder="Ej: Transporte de materiales a obra">
                </div>
            </div>
        </div>

        <!-- Sección 2: Responsables -->
        <div class="form-section dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fas fa-users text-green-500 mr-2"></i>
                Responsables
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Encargado -->
                <div>
                    <label for="encargado" class="input-label">
                        <i class="fas fa-user-tie text-green-500 mr-2"></i> Encargado
                    </label>
                    <input type="text" id="encargado" name="encargado" required 
                           class="w-full input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100" 
                           placeholder="Nombre completo del encargado">
                </div>
                
                <!-- Celular -->
                <div>
                    <label for="celular" class="input-label">
                        <i class="fas fa-mobile-alt text-green-500 mr-2"></i> Celular
                    </label>
                    <input type="text" id="celular" name="celular" required pattern="\d{8}" title="Debe contener 8 dígitos" 
                           class="w-full input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100" 
                           placeholder="8 dígitos">
                </div>
            </div>
            
            <!-- Dirección -->
            <div class="mt-4">
                <label for="direccion" class="input-label">
                    <i class="fas fa-map-marker-alt text-green-500 mr-2"></i> Dirección
                </label>
                <input type="text" id="direccion" name="direccion" required 
                       class="w-full input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100" 
                       placeholder="Dirección exacta del evento">
            </div>
        </div>

        <!-- Sección 3: Materiales -->
        <div class="form-section dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fas fa-boxes text-green-500 mr-2"></i>
                Materiales
            </h3>
            
            <div id="materialContainer">
                <div class="material-row">
                    <select name="materiales[0][id]" class="material-selector input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100" required>
                        <option value="">Seleccionar material</option>
                        @foreach(App\Models\Material::all() as $material)
                            <option value="{{ $material->id }}" data-stock="{{ $material->stock_total }}">
                                {{ $material->nombre }} (Stock: {{ $material->stock_total }})
                            </option>
                        @endforeach
                    </select>
                    
                    <input type="number" name="materiales[0][cantidad]" min="1" placeholder="Cantidad" required 
                           class="input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                    
                    <input type="file" name="materiales[0][foto_entrega]" accept="image/*" required 
                           class="input-field file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-green-900 dark:file:text-green-200">
                    
                    <button type="button" onclick="removeMaterialRow(this)" 
                            class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <button type="button" onclick="addMaterialRow()" 
                    class="mt-3 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Agregar Material
            </button>
        </div>

        <!-- Sección 4: Horarios -->
        <div class="form-section dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fas fa-clock text-green-500 mr-2"></i>
                Horarios
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Hora de Entrega -->
                <div>
                    <label for="hor_entrega" class="input-label">
                        <i class="fas fa-truck-loading text-green-500 mr-2"></i> Hora de Entrega
                    </label>
                    <input type="time" id="hor_entrega" name="hor_entrega" required 
                           class="w-full input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                </div>
                
                <!-- Recojo -->
                <div>
                    <label for="recojo" class="input-label">
                        <i class="fas fa-truck text-green-500 mr-2"></i> Recojo (Fecha y Hora)
                    </label>
                    <input type="datetime-local" id="recojo" name="recojo" required 
                           class="w-full input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </div>
        </div>

        <!-- Sección 5: Personal -->
        <div class="form-section dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fas fa-user-shield text-green-500 mr-2"></i>
                Personal Asignado
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Operador -->
                <div>
                    <label for="operador" class="input-label">
                        <i class="fas fa-hard-hat text-green-500 mr-2"></i> Operador
                    </label>
                    <select id="operador" name="operador" required 
                            class="w-full input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                        <!-- Las opciones se cargarán vía JavaScript -->
                    </select>
                    
                    @if($usuario->rol === 'master')
                        <div class="mt-3 flex gap-2">
                            <input type="text" id="nuevoOperador" placeholder="Nuevo Operador" 
                                   class="flex-1 input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                            <button type="button" onclick="addNewOperator()" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                Agregar
                            </button>
                        </div>
                    @endif
                </div>
                
                <!-- Supervisor -->
                <div>
                    <label for="supervisor" class="input-label">
                        <i class="fas fa-user-shield text-green-500 mr-2"></i> Supervisor
                    </label>
                    
                    @php $usuario = Auth::user(); @endphp
                    
                    @if($usuario->rol === 'master' && isset($empleados))
                        <select id="supervisor" name="supervisor" required onchange="asignarLegajo(this)"
                                class="w-full mb-3 input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Selecciona Supervisor</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->nombre_completo }}" data-legajo="{{ $empleado->legajo }}">
                                    {{ $empleado->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                        
                        <label for="legajo" class="input-label">Legajo</label>
                        <input type="text" id="legajo" name="legajo" readonly 
                               class="w-full input-field px-4 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-100">
                    @else
                        <input type="text" id="supervisor" name="supervisor" value="{{ $usuario->nombre_completo }}" readonly 
                               class="w-full mb-3 input-field px-4 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-100">
                        
                        <label for="legajo" class="input-label">Legajo</label>
                        <input type="text" id="legajo" name="legajo" value="{{ $usuario->legajo }}" readonly 
                               class="w-full input-field px-4 py-2 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 dark:text-gray-100">
                    @endif
                </div>
            </div>
        </div>

        <!-- Sección 6: Estado y Ubicación -->
        <div class="form-section dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fas fa-map-marked-alt text-green-500 mr-2"></i>
                Estado y Ubicación
            </h3>
            
            <!-- Estado del Evento -->
            <div class="mb-6">
                <label for="estado_evento" class="input-label">
                    <i class="fas fa-info-circle text-green-500 mr-2"></i> Estado del Evento
                </label>
                <select id="estado_evento" name="estado_evento" required 
                        class="w-full input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="pendiente">Pendiente</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="cancelado">Cancelado</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>
            
            <!-- Mapa -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="input-label">
                        <i class="fas fa-map-marker-alt text-green-500 mr-2"></i> Ubicación Geográfica
                    </label>
                    <button type="button" onclick="obtenerUbicacion()"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center">
                        <i class="fas fa-location-arrow mr-2"></i> Usar mi ubicación
                    </button>
                </div>
                <input type="hidden" id="ubicacion" name="ubicacion">
                <div id="mapa" class="w-full h-96 rounded-lg border-2 border-gray-300 dark:border-gray-600 shadow-inner"></div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Haz clic en el mapa para seleccionar la ubicación o usa el botón para obtener tu ubicación actual
                </p>
            </div>
        </div>

        <!-- Botón de envío -->
        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600 text-right">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-200 inline-flex items-center transform hover:scale-[1.02]">
                <i class="fas fa-check-circle mr-2"></i> Crear Evento Tipo 2
            </button>
        </div>
    </form>
</div>

<!-- Scripts (Mantenidos exactamente igual) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Función para asignar el legajo según el supervisor seleccionado
    function asignarLegajo(selectElement) {
        const legajo = selectElement.options[selectElement.selectedIndex].getAttribute('data-legajo');
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
    let materialRowIndex = 0;

    function addMaterialRow() {
        materialRowIndex++;
        const newRow = `
            <div class="material-row">
                <select name="materiales[${materialRowIndex}][id]" class="material-selector input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100" required>
                    <option value="">Seleccionar material</option>
                    @foreach(App\Models\Material::all() as $material)
                        <option value="{{ $material->id }}" data-stock="{{ $material->stock_total }}">
                            {{ $material->nombre }} (Stock: {{ $material->stock_total }})
                        </option>
                    @endforeach
                </select>
                
                <input type="number" 
                       name="materiales[${materialRowIndex}][cantidad]" 
                       min="1" 
                       placeholder="Cantidad" 
                       required 
                       class="input-field px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100">
                
                <input type="file" 
                       name="materiales[${materialRowIndex}][foto_entrega]" 
                       accept="image/*" 
                       required 
                       class="input-field file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-green-900 dark:file:text-green-200">
                
                <button type="button" 
                        onclick="removeMaterialRow(this)" 
                        class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        const container = document.getElementById('materialContainer');
        container.insertAdjacentHTML('beforeend', newRow);
        
        // Agregar validación de stock en tiempo real
        const newSelect = container.lastElementChild.querySelector('select');
        newSelect.addEventListener('change', function() {
            const stock = parseInt(this.options[this.selectedIndex].dataset.stock);
            const cantidadInput = this.closest('div').querySelector('input[type="number"]');
            cantidadInput.setAttribute('max', stock);
            cantidadInput.value = Math.min(parseInt(cantidadInput.value || 0), stock);
        });
    }

    function removeMaterialRow(button) {
        button.closest('div').remove();
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

    // Validación general del formulario
    function validarFormulario() {
        const ubicacion = document.getElementById('ubicacion').value;
        if (!ubicacion || ubicacion.trim() === '') {
            alert('Por favor, selecciona una ubicación en el mapa.');
            return false;
        }
        
        // Validar que al menos haya un material
        const materiales = document.querySelectorAll('[name^="materiales["]');
        if (materiales.length === 0) {
            alert('Debe agregar al menos un material');
            return false;
        }
        
        return true;
    }

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        updateOperatorOptions();
        
        // Agregar evento a los selectores de material existentes
        document.querySelectorAll('.material-selector').forEach(select => {
            select.addEventListener('change', function() {
                const stock = parseInt(this.options[this.selectedIndex].dataset.stock);
                const cantidadInput = this.closest('div').querySelector('input[type="number"]');
                cantidadInput.setAttribute('max', stock);
            });
        });
    });
</script>
@endsection