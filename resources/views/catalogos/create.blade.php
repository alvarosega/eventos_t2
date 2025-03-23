@extends('layouts.app')

@section('title', 'Agregar Producto al Catálogo')

@section('content')
    <h2 class="text-2xl font-bold mb-4">
        Agregar Producto al Catálogo para: {{ $evento->nombre }}
    </h2>

    <form
        action="{{ route('catalogos.store', $evento->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white shadow rounded p-4 max-w-lg"
    >
        @csrf

        <!-- Nombre del Producto -->
        <div class="mb-4">
            <label for="nombre" class="block mb-1 font-semibold text-sm text-secondary">
                Nombre del Producto
            </label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                required
                class="mt-1 block w-full px-3 py-2 border border-secondary rounded focus:outline-none focus:ring-2 focus:ring-primary"
            />
        </div>

        <!-- Precio -->
        <div class="mb-4">
            <label for="precio" class="block mb-1 font-semibold text-sm text-secondary">
                Precio
            </label>
            <input
                type="number"
                step="0.01"
                id="precio"
                name="precio"
                required
                class="mt-1 block w-full px-3 py-2 border border-secondary rounded focus:outline-none focus:ring-2 focus:ring-primary"
            />
        </div>

        <!-- Stock Disponible -->
        <div class="mb-4">
            <label for="stock_disponible" class="block mb-1 font-semibold text-sm text-secondary">
                Stock Disponible
            </label>
            <input
                type="number"
                id="stock_disponible"
                name="stock_disponible"
                required
                class="mt-1 block w-full px-3 py-2 border border-secondary rounded focus:outline-none focus:ring-2 focus:ring-primary"
            />
        </div>

        <!-- Imagen -->
        <div class="mb-4">
            <label for="imagen" class="block mb-1 font-semibold text-sm text-secondary">
                Imagen
            </label>
            <input
                type="file"
                id="imagen"
                name="imagen"
                class="block w-full text-sm text-gray-500
                       file:mr-4 file:py-2 file:px-4
                       file:rounded file:border-0
                       file:text-sm file:font-semibold
                       file:bg-primary file:text-white
                       hover:file:bg-secondary"
            />
        </div>

        <!-- Descripción -->
        <div class="mb-4">
            <label for="descripcion" class="block mb-1 font-semibold text-sm text-secondary">
                Descripción
            </label>
            <textarea
                id="descripcion"
                name="descripcion"
                rows="3"
                class="mt-1 block w-full px-3 py-2 border border-secondary rounded focus:outline-none focus:ring-2 focus:ring-primary"
            ></textarea>
        </div>

        <!-- Botón de Agregar -->
        <button
            type="submit"
            class="bg-primary text-white px-4 py-2 rounded hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-primary transition"
        >
            Agregar Producto
        </button>
    </form>
@endsection
