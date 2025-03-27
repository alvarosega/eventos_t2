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
                    <div class="flex-1">
                        <p class="font-semibold">{{ $producto->nombre }}</p>
                        <p>Precio: ${{ number_format($producto->precio, 2) }}</p>
                        <p>Stock disponible: {{ $producto->stock_disponible }}</p>
                    </div>
                    <div>
                        <label for="producto_{{ $producto->id }}" class="block text-sm font-medium">
                            Cantidad:
                        </label>
                        <input 
                            type="number" 
                            name="productos[{{ $producto->id }}]" 
                            id="producto_{{ $producto->id }}"
                            class="w-20 p-1 border border-gray-300 rounded"
                            min="0"
                            max="{{ $producto->stock_disponible }}"
                            value="0"
                        />
                    </div>
                </div>
            @empty
                <p class="text-gray-700">No hay productos disponibles para este evento.</p>
            @endforelse
        </div>

        <div class="mt-6 text-right">
            <button type="submit"
                class="px-4 py-2 bg-primary text-white rounded hover:bg-secondary transition">
                Crear Pedido
            </button>
        </div>
    </form>
</div>
@endsection
