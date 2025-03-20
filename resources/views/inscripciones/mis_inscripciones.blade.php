@extends('layouts.app')

@section('title', 'Mis Inscripciones')

@section('content')
    <h1>Mis Inscripciones</h1>

    @if($inscripciones->isEmpty())
        <p>No estás inscrito en ningún evento.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Finalización</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            @foreach($inscripciones as $inscripcion)
                <tr>
                    <td>{{ $inscripcion->evento->nombre }}</td>
                    <td>{{ $inscripcion->evento->fecha_inicio }} {{ $inscripcion->evento->hora_inicio }}</td>
                    <td>{{ $inscripcion->evento->fecha_finalizacion }} {{ $inscripcion->evento->hora_finalizacion }}</td>
                    <td>
                        {{-- Enlace para cancelar la inscripción (opcional) --}}
                        <a href="{{ route('inscripciones.cancelForm', $inscripcion->id) }}" class="btn btn-danger btn-sm">
                            Cancelar Inscripción
                        </a>
                        {{-- Enlace para hacer pedido (opcional) si el evento está activo --}}
                        <a href="{{ route('pedidos.create', $inscripcion->evento->id) }}" class="btn btn-primary btn-sm">
                            Realizar Pedido
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection
