@extends('layouts.app')

@section('title', 'Catálogo del Evento: ' . $evento->nombre)

@section('content')
    <h2>Catálogo del Evento: {{ $evento->nombre }}</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('catalogos.index') }}" class="btn btn-secondary mb-3">Volver a Eventos</a>

    @if(Auth::user()->rol === 'superadmin')
        <a href="{{ route('catalogos.create', $evento->id) }}" class="btn btn-success mb-3">Agregar Producto</a>
    @endif

    @if($productos->isEmpty())
        <p>No hay productos en el catálogo para este evento.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock Disponible</th>
                    <th>Imagen</th>
                    <th>Descripción</th>
                    @if(Auth::user()->rol === 'superadmin')
                        <th>Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                    <tr>
                        <td>{{ $producto->id }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->precio }}</td>
                        <td>{{ $producto->stock_disponible }}</td>
                        <td>
                            @if($producto->imagen)
                                <img src="{{ Storage::url($producto->imagen) }}" alt="{{ $producto->nombre }}" style="max-width: 100px;">
                            @else
                                Sin imagen
                            @endif
                        </td>
                        <td>{{ $producto->descripcion }}</td>
                        @if(Auth::user()->rol === 'superadmin')
                            <td>
                                <a href="{{ route('catalogos.edit', $producto->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{ route('catalogos.destroy', $producto->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar producto?')">Eliminar</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
