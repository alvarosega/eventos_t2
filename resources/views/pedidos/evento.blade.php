@extends('layouts.app')

@section('title', 'Pedidos del Evento')

@section('content')
    <h2>Pedasdasidos del Evento: {{ $evento->nombre }}</h2>

    <a href="{{ route('home') }}" class="btn btn-secondary mb-3">Volver a Eventos</a>

    @if($pedidos->isEmpty())
        <p>No hay pedidos registrados para este evento.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Usuario Externo</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Fecha del Pedido</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedidos as $pedido)
                    <tr>
                        <td>{{ $pedido->id }}</td>
                        <td>{{ $pedido->externo->nombre ?? 'N/A' }}</td>
                        <td>{{ $pedido->cantidad }}</td>
                        <td>{{ number_format($pedido->total, 2) }}</td>
                        <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if ($pedido->estado == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif ($pedido->estado == 'en_preparacion')
                                <span class="badge bg-primary">En Preparación</span>
                            @elseif ($pedido->estado == 'enviado')
                                <span class="badge bg-info">Enviado</span>
                            @elseif ($pedido->estado == 'entregado')
                                <span class="badge bg-success">Entregado</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection

