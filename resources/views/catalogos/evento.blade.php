@extends('layouts.app')

@section('title', 'Catálogo del Evento: ' . $evento->nombre)

@section('content')
    <!-- Título -->
    <h2 class="text-2xl font-bold mb-4">
        Catálogo del Evento: {{ $evento->nombre }}
    </h2>

    <!-- Mensaje de éxito -->
    @if (session('success'))
        <div class="mb-4 p-4 border-l-4 border-green-500 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Botones de navegación -->
    <div class="flex items-center gap-2 mb-4">
        <a href="{{ route('catalogos.index') }}"
           class="inline-block bg-secondary text-white px-4 py-2 rounded hover:bg-secondary/80 transition">
            Volver a Eventos
        </a>

        @if(Auth::user()->rol === 'superadmin')
            <a href="{{ route('catalogos.create', $evento->id) }}"
               class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
               Agregar Producto
            </a>
        @endif
    </div>

    <!-- Contenido principal -->
    @if($productos->isEmpty())
        <p class="text-gray-700">No hay productos en el catálogo para este evento.</p>
    @else
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full text-sm text-left border-collapse">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-2 font-semibold border-b">ID</th>
                        <th class="p-2 font-semibold border-b">Nombre</th>
                        <th class="p-2 font-semibold border-b">Precio</th>
                        <th class="p-2 font-semibold border-b">Stock Disponible</th>
                        <th class="p-2 font-semibold border-b">Imagen</th>
                        <th class="p-2 font-semibold border-b">Descripción</th>
                        @if(Auth::user()->rol === 'superadmin')
                            <th class="p-2 font-semibold border-b">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($productos as $producto)
                        <tr>
                            <td class="p-2 border-b">{{ $producto->id }}</td>
                            <td class="p-2 border-b">{{ $producto->nombre }}</td>
                            <td class="p-2 border-b">{{ $producto->precio }}</td>
                            <td class="p-2 border-b">{{ $producto->stock_disponible }}</td>
                            <td class="p-2 border-b">
                                @if($producto->imagen)
                                    <img
                                        src="{{ Storage::url($producto->imagen) }}"
                                        alt="{{ $producto->nombre }}"
                                        class="max-w-[100px] h-auto"
                                    />
                                @else
                                    <span class="text-gray-500">Sin imagen</span>
                                @endif
                            </td>
                            <td class="p-2 border-b">{{ $producto->descripcion }}</td>
                            @if(Auth::user()->rol === 'superadmin')
                                <td class="p-2 border-b">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('catalogos.edit', $producto->id) }}"
                                           class="inline-block bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition">
                                           Editar
                                        </a>
                                        <form
                                            action="{{ route('catalogos.destroy', $producto->id) }}"
                                            method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('¿Eliminar producto?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition"
                                            >
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
