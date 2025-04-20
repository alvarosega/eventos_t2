@extends('layouts.app')

@section('title', 'Gestión de Inventario')

@section('content')
<div class="container mx-auto px-4 py-6">
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
            {{ session('warning') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">
                <i class="fas fa-boxes mr-2"></i> Inventario de Materiales
            </h2>
            <a href="{{ route('inventario.download-template') }}" 
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            <i class="fas fa-file-download mr-1"></i> Descargar Plantilla
            </a>
        </div>

        <form action="{{ route('inventario.upload') }}" method="POST" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow">
                    <input type="file" name="inventario" accept=".csv,.txt" required
                    class="border rounded px-4 py-2 w-full @error('inventario') border-red-500 @enderror">
                    @error('inventario')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Formato requerido: CSV con columnas Nombre, Stock (y opcionalmente Detalles)</p>
                </div>
                <button type="submit" 
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">
                    <i class="fas fa-upload mr-1"></i> Importar
                </button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Material</th>
                        <th class="py-3 px-6 text-center">Stock</th>
                        <th class="py-3 px-6 text-left">Detalles</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @forelse($materiales as $material)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-6 text-left">{{ $material->nombre }}</td>
                        <td class="py-3 px-6 text-center">{{ $material->stock_total }}</td>
                        <td class="py-3 px-6 text-left">{{ $material->detalles ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-500">
                            No hay materiales registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection