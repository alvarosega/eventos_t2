@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
    <h2>Editar Producto: {{ $producto->nombre }}</h2>
    <form action="{{ route('catalogos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $producto->nombre }}" required>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="{{ $producto->precio }}" required>
        </div>
        <div class="mb-3">
            <label for="stock_disponible" class="form-label">Stock Disponible</label>
            <input type="number" class="form-control" id="stock_disponible" name="stock_disponible" value="{{ $producto->stock_disponible }}" required>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen</label>
            @if($producto->imagen)
                <div class="mb-2">
                    <img src="{{ Storage::url($producto->imagen) }}" alt="{{ $producto->nombre }}" style="max-width: 150px;">
                </div>
            @endif
            <input type="file" class="form-control" id="imagen" name="imagen">
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ $producto->descripcion }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
    </form>
@endsection
