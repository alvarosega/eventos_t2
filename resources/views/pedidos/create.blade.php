@extends('layouts.app')

@section('title', 'Crear Pedido')

@section('content')
<div class="max-w-3xl mx-auto p-4 bg-white shadow rounded">
    <h2 class="text-xl font-bold mb-4">Crear Pedido para Evento ID: {{ $eventoId }}</h2>

    <!-- Mostrar errores -->
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pedidos.store', $eventoId) }}" method="POST">
        @csrf
        <div class="space-y-4">
            @forelse($productos as $producto)
                <div class="flex items-center gap-4 p-2 border border-gray-200 rounded">
                    <!-- Información del producto -->
                    <div class="flex-1 flex flex-col md:flex-row items-center gap-4">
                        <div class="w-16 h-16 flex-shrink-0">
                          <img src="{{ Storage::url($producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover rounded">
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold">{{ $producto->nombre }}</p>
                            <p class="text-sm text-gray-600">Precio: ${{ number_format($producto->precio, 2) }}</p>
                            <p class="text-sm text-gray-600">Stock: {{ $producto->stock_disponible }}</p>
                            <p class="text-sm text-gray-700">{{ $producto->descripcion }}</p>
                        </div>
                    </div>
                    <!-- Campo de cantidad -->
                    <div>
                        <label for="producto_{{ $producto->id }}" class="block text-sm font-medium">Cantidad:</label>
                        <input 
                            type="number" 
                            name="productos[{{ $producto->id }}]" 
                            id="producto_{{ $producto->id }}"
                            class="w-20 p-1 border border-gray-300 rounded cantidad-input"
                            min="0"
                            max="{{ $producto->stock_disponible }}"
                            value="0"
                            data-precio="{{ $producto->precio }}"
                        />
                    </div>
                    <!-- Subtotal del producto -->
                    <div class="w-24 text-right">
                        <p class="text-sm font-semibold">Subtotal:</p>
                        <p id="subtotal_{{ $producto->id }}" class="subtotal text-sm">$0.00</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-700">No hay productos disponibles para este evento.</p>
            @endforelse
        </div>

        <!-- Sección de Total y botón de envío -->
        <div class="mt-6 text-right">
            <p class="text-lg font-bold">Total Pedido: $<span id="totalPedido">0.00</span></p>
            <button type="submit" class="mt-4 px-4 py-2 bg-primary text-white rounded hover:bg-secondary transition">
                Crear Pedido
            </button>
        </div>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateTotals() {
        let totalPedido = 0;
        const inputs = document.querySelectorAll('.cantidad-input');
        inputs.forEach(function(input) {
            const cantidad = parseFloat(input.value) || 0;
            const precio = parseFloat(input.getAttribute('data-precio')) || 0;
            const subtotal = cantidad * precio;
            const productoId = input.id.split('_')[1];
            const subtotalEl = document.getElementById('subtotal_' + productoId);
            if(subtotalEl) {
                subtotalEl.textContent = '$' + subtotal.toFixed(2);
            }
            totalPedido += subtotal;
        });
        document.getElementById('totalPedido').textContent = totalPedido.toFixed(2);
    }
    
    const cantidadInputs = document.querySelectorAll('.cantidad-input');
    cantidadInputs.forEach(function(input) {
        input.addEventListener('input', updateTotals);
    });
});
</script>

