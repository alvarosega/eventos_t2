@extends('layouts.app')

@section('title', 'Cancelar Inscripción')

@section('content')
    <div class="container max-w-lg mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg transition-transform hover:scale-[1.01]">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">Cancelar Inscripción</h2>
        
        <p class="mb-4 text-gray-700 dark:text-gray-300">
            Selecciona un motivo para cancelar tu inscripción al evento: <strong>{{ $evento->nombre }}</strong>
        </p>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 dark:bg-red-200 border-l-4 border-red-500 text-red-700 dark:text-red-900 rounded shadow transition hover:shadow-md">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('inscripciones.cancel', $evento->id) }}">
            @csrf
            <label for="motivo" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Motivo de cancelación
            </label>
            <select name="motivo" id="motivo" required
                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded focus:ring-primary focus:border-primary transition">
                <option value="">Selecciona un motivo...</option>
                <option value="Cambio de planes">Cambio de planes</option>
                <option value="Problemas personales">Problemas personales</option>
                <option value="No puedo asistir">No puedo asistir</option>
                <option value="Otro">Otro</option>
            </select>

            <textarea name="motivo_extra" id="motivo_extra" rows="3" placeholder="Especifica tu motivo (opcional)"
                class="w-full mt-3 p-2 border border-gray-300 dark:border-gray-600 rounded focus:ring-primary transition"></textarea>

            <button type="submit"
                class="mt-4 w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded transition transform hover:scale-[1.02]">
                Confirmar Cancelación
            </button>
        </form>
    </div>
@endsection
