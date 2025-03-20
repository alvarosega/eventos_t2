@extends('layouts.app')

@section('title', 'Agregar Producto al Catálogo')

@section('content')
    <h2>Agregar Producto al Catálogo para: {{ $evento->nombre }}</h2>
    <form action="{{ route('catalogos.store', $evento->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
        </div>
        <div class="mb-3">
            <label for="stock_disponible" class="form-label">Stock Disponible</label>
            <input type="number" class="form-control" id="stock_disponible" name="stock_disponible" required>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen</label>
            <input type="file" class="form-control" id="imagen" name="imagen">
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Producto</button>
    </form>
@endsection
