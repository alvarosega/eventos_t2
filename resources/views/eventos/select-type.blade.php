@extends('layouts.app')

@section('title', 'Seleccionar Tipo de Evento')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Encabezado -->
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center bg-primary/10 p-3 rounded-full mb-4">
            <i class="fas fa-calendar-plus text-3xl text-primary"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
            Seleccionar Tipo de Evento
        </h2>
        <p class="text-gray-600 dark:text-gray-300 mt-2">
            Elija el tipo de evento que desea crear
        </p>
    </div>

    <!-- Tarjetas de selección -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Evento Tipo 1 -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden transition-transform duration-300 hover:shadow-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full mr-4">
                        <i class="fas fa-calendar-alt text-xl text-blue-500 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Evento Tipo 1</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Eventos generales con múltiples ubicaciones y fechas flexibles.
                </p>
                <a href="{{ route('eventos.create') }}" 
                   class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-center py-3 px-4 rounded-lg transition-colors duration-300 font-medium">
                   <i class="fas fa-arrow-right mr-2"></i> Seleccionar
                </a>
            </div>
        </div>

        <!-- Evento Tipo 2 -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden transition-transform duration-300 hover:shadow-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full mr-4">
                        <i class="fas fa-truck-moving text-xl text-green-500 dark:text-green-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Evento Tipo 2</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Eventos logísticos con materiales, horarios específicos y personal asignado.
                </p>
                <a href="{{ route('eventos.create-tipo2') }}" 
                   class="block w-full bg-green-500 hover:bg-green-600 text-white text-center py-3 px-4 rounded-lg transition-colors duration-300 font-medium">
                   <i class="fas fa-arrow-right mr-2"></i> Seleccionar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection