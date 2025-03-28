@extends('layouts.app')

@section('title', 'Mis Inscripciones')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-center">Mis Inscripciones</h1>

    @if($inscripciones->isEmpty())
        <p class="text-center text-lg">No estás inscrito en ningún evento.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded shadow-lg">
                <thead>
                    <tr class="bg-primary text-white">
                        <th class="py-3 px-4 text-left">Evento</th>
                        <th class="py-3 px-4 text-left">Fecha Inicio</th>
                        <th class="py-3 px-4 text-left">Fecha Finalización</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($inscripciones as $inscripcion)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <td class="py-3 px-4">{{ $inscripcion->evento->nombre }}</td>
                            <td class="py-3 px-4">{{ $inscripcion->evento->fecha_inicio }} {{ $inscripcion->evento->hora_inicio }}</td>
                            <td class="py-3 px-4">{{ $inscripcion->evento->fecha_finalizacion }} {{ $inscripcion->evento->hora_finalizacion }}</td>
                            <td class="py-3 px-4 text-center space-x-2">
                                <a href="{{ route('inscripciones.cancelForm', $inscripcion->id) }}" class="inline-block bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-2 rounded transition-colors">
                                    Cancelar Inscripción
                                </a>
                                <a href="{{ route('pedidos.create', $inscripcion->evento->id) }}" class="inline-block bg-primary hover:bg-secondary text-white text-sm px-3 py-2 rounded transition-colors">
                                    Realizar Pedido
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
