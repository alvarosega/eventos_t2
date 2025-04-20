@extends('layouts.app')

@section('title', 'Gestión de Inventario')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Mensajes de sesión mejorados -->
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    
    @if (session('warning'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>{{ session('warning') }}</span>
            </div>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-times-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <!-- Encabezado con botón de descarga -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-boxes mr-2 text-blue-500"></i> Inventario de Materiales
            </h2>
            <a href="{{ route('inventario.download-template') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition duration-200 flex items-center">
               <i class="fas fa-file-download mr-2"></i> Descargar Plantilla CSV
            </a>
        </div>

        <!-- Formulario de importación mejorado -->
        <form action="{{ route('inventario.upload') }}" method="POST" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <input type="file" name="inventario" accept=".csv,.txt" required
                           class="w-full border border-gray-300 rounded-l px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <span class="text-xs text-gray-500 mt-1 block">Formatos aceptados: .csv, .txt (Tamaño máximo: 2MB)</span>
                </div>
                <button type="submit" 
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-r sm:rounded whitespace-nowrap transition duration-200">
                    <i class="fas fa-upload mr-2"></i> Importar CSV
                </button>
            </div>
            @error('inventario')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </form>

        <!-- Tabla de materiales responsive -->
        <div class="overflow-x-auto shadow rounded-lg">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm">
                        <th class="py-3 px-6 text-left">Material</th>
                        <th class="py-3 px-6 text-center">Stock</th>
                        <th class="py-3 px-6 text-left">Detalles</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($materiales as $material)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150">
                        <td class="py-4 px-6">
                            <div class="flex items-center">
                                <i class="fas fa-box-open text-blue-400 mr-2"></i>
                                <span>{{ $material->nombre }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="{{ $material->stock_total > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-2 py-1 rounded-full text-xs font-medium">
                                {{ $material->stock_total }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-left text-gray-500">
                            {{ $material->detalles ?? 'Sin detalles' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                <p class="text-lg">No hay materiales registrados</p>
                                <p class="text-sm mt-1">Importa un archivo CSV para comenzar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación (si es necesario) -->
        @if($materiales instanceof \Illuminate\Pagination\LengthAwarePaginator && $materiales->hasPages())
            <div class="mt-6">
                {{ $materiales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection