@extends('layouts.app')

@section('content')
    <h1>Eventos dispoghjknibles</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @if ($eventos->isEmpty())
        <p>No hay eventos disponibles para inscribirse.</p>
    @else
        <div class="row">
            @foreach($eventos as $evento)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ $evento->nombre }}</h5>
                            <p>{{ $evento->descripcion }}</p>
                            <p><strong>Inicia:</strong> {{ $evento->fecha_inicio }} {{ $evento->hora_inicio }}</p>
                            <p><strong>Finaliza:</strong> {{ $evento->fecha_finalizacion }} {{ $evento->hora_finalizacion }}</p>

                            <div class="card-footer text-center">
                                        {{-- Botón para ir a la pantalla de mapa (inscripción) --}}
                                        <a href="{{ route('inscripciones.showMapa', $evento->id) }}" class="btn btn-primary btn-sm">
                                            Inscribirme
                                        </a>
                                    </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
