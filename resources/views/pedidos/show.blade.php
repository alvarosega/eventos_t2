@extends('layouts.app')

@section('content')
    <h1>Detalle del Pedido #{{ $pedido->id }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <p><strong>Estado:</strong> {{ $pedido->estado }}</p>
    <p><strong>Total de artículos:</strong> {{ $pedido->cantidad }}</p>
    <p><strong>Monto total:</strong> ${{ $pedido->total }}</p>

    <h3>Productos:</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->productoEvento->nombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ $detalle->precio_unitario }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Si el usuario es Externo y el pedido no está 'entregado', permitir confirmar entrega --}}
    @if (auth()->guard('externo')->check() && $pedido->estado !== 'entregado' && $pedido->externo_id == auth()->guard('externo')->id())
        <form action="{{ route('pedidos.confirmarEntrega', $pedido->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Confirmar que he recibido el pedido</button>
        </form>
    @endif
@endsection
