@extends('layouts.app')

@section('content')
    <h1>Realizar Pedido</h1>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('pedidos.store', $eventoId) }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad a pedir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->precio }}</td>
                        <td>
                            <input type="number" name="productos[{{ $producto->id }}]" min="0" max="{{ $producto->stock_disponible }}" value="0">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No hay productos en este evento.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Enviar Pedido</button>
    </form>
@endsection
