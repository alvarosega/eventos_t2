@extends('layouts.app')

@section('title', 'Seleccionar Tipo de Evento')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-8 flex items-center justify-center">
            <i class="fas fa-calendar-plus text-primary mr-2"></i>
            Seleccionar Tipo de Evento
        </h2>

        <div class="flex flex-col md:flex-row justify-center gap-8">
            <!-- Card para Evento Tipo 1 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden w-full md:w-1/3 transform hover:scale-105 transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-calendar-alt text-3xl text-blue-500 mr-3"></i>
                        <h3 class="text-xl font-bold dark:text-white">Evento Tipo 1</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        Eventos generales con múltiples ubicaciones y fechas flexibles.
                    </p>
                    <a href="{{ route('eventos.create') }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-center py-2 px-4 rounded transition">
                        Seleccionar
                    </a>
                </div>
            </div>

            <!-- Card para Evento Tipo 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden w-full md:w-1/3 transform hover:scale-105 transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-truck-moving text-3xl text-green-500 mr-3"></i>
                        <h3 class="text-xl font-bold dark:text-white">Evento Tipo 2</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        Eventos logísticos con materiales, horarios específicos y personal asignado.
                    </p>
                    <a href="{{ route('eventos.create-tipo2') }}" class="block w-full bg-green-500 hover:bg-green-600 text-white text-center py-2 px-4 rounded transition">
                        Seleccionar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection