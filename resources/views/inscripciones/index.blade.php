@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-center">Eventos Disponibles</h1>

    @if (session('success'))
        <div class="mb-4 p-4 border-l-4 border-green-500 bg-green-100 dark:bg-green-200 text-green-700 dark:text-green-900 rounded shadow-md transition hover:shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 border-l-4 border-red-500 bg-red-100 dark:bg-red-200 text-red-700 dark:text-red-900 rounded shadow-md transition hover:shadow-lg">
            {{ $errors->first() }}
        </div>
    @endif

    @if ($eventos->isEmpty())
        <p class="text-center text-lg">No hay eventos disponibles para inscribirse.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($eventos as $evento)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg hover:shadow-2xl transition transform hover:scale-[1.02] p-6">
                    <h5 class="text-xl font-bold mb-2">{{ $evento->nombre }}</h5>
                    <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">{{ $evento->descripcion }}</p>
                    <p class="mb-1"><strong>Inicia:</strong> {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}</p>
                    <p class="mb-4"><strong>Finaliza:</strong> {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}</p>
                    <div class="text-center">
                        <a href="{{ route('inscripciones.showMapa', $evento->id) }}" class="inline-block bg-primary text-white text-sm px-4 py-2 rounded transition-colors hover:bg-secondary">
                            Inscribirme
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
