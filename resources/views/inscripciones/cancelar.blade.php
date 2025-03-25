@extends('layouts.app')

@section('title', 'Cancelar Inscripción')

@section('content')
    <div class="container max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4">Cancelar Inscripción</h2>
        
        <p class="mb-4">Selecciona un motivo para cancelar tu inscripción al evento: <strong>{{ $evento->nombre }}</strong></p>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('inscripciones.cancel', $evento->id) }}">
            @csrf
            <label for="motivo" class="block mb-2 text-sm font-medium">Motivo de cancelación</label>
            <select name="motivo" id="motivo" required
                class="w-full p-2 border border-gray-300 rounded focus:ring-primary focus:border-primary">
                <option value="">Selecciona un motivo...</option>
                <option value="Cambio de planes">Cambio de planes</option>
                <option value="Problemas personales">Problemas personales</option>
                <option value="No puedo asistir">No puedo asistir</option>
                <option value="Otro">Otro (especificar abajo)</option>
            </select>

            <textarea name="motivo_extra" id="motivo_extra" rows="3" placeholder="Especifica tu motivo (opcional)"
                class="w-full mt-3 p-2 border border-gray-300 rounded"></textarea>

            <button type="submit"
                class="mt-4 w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition">
                Confirmar Cancelación
            </button>
        </form>
    </div>
@endsection
