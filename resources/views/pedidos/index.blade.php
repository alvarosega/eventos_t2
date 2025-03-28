@extends('layouts.app')

@section('title', 'Lista de Pedisasdados')

@section('content')

    <h1>lista de Pedidos</h1>
    @if(Auth::guard('externo')->check() && isset($pedidos) && $pedidos->isNotEmpty())
        @php
            // Obtener el evento del primer pedido (ya que todos son del mismo evento para el externo)
            $miEvento = $pedidos->first()->evento ?? null;
        @endphp

        @if($miEvento)
            <a href="{{ route('pedidos.create', $miEvento->id) }}"
            class="inline-block bg-green-600 text-white text-sm px-3 py-2 rounded hover:bg-green-700 mb-3">
                <i class="fas fa-cart-plus"></i> Nuevo Pedido
            </a>
        @endif
    @endif


    {{-- Filtro de pedidos por estado --}}
    <form method="GET" action="{{ route('pedidos.index') }}" class="mb-3">
        <label for="estado">Filtrar por Estado:</label>
        <select name="estado" id="estado" class="form-select d-inline-block w-auto">
            <option value="">Todos</option>
            <option value="pendiente">Pendiente</option>
            <option value="en_preparacion">En Preparación</option>
            <option value="enviado">Enviado</option>
            <option value="entregado">Entregado</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
    </form>

    @if($pedidos->isEmpty())
        <p>No hay pedidos registrados.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Evento</th>
                    <th>Usuario Externo</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Fecha del Pedido</th>
                    <th>Estado</th>

                    {{-- Solo mostrar "Acciones" si el usuario es admin/superadmin --}}
                    @if(Auth::guard('empleado')->check() && in_array(Auth::user()->rol, ['admin', 'superadmin']))
                        <th>Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @foreach($pedidos as $pedido)
                <tr>
                    <td>{{ $pedido->id }}</td>
                    <td>{{ $pedido->evento->nombre ?? 'Sin evento' }}</td>
                    <td>
                        @if(isset($pedido->externo))
                            {{ $pedido->externo->nombre ?? 'Desconocido' }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $pedido->cantidad }}</td>
                    <td>{{ number_format($pedido->total, 2) }}</td>
                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>

                    {{-- Estado del pedido con colores --}}
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

                    {{-- Solo mostrar "Acciones" si el usuario es admin/superadmin --}}
                    @if(Auth::guard('empleado')->check() && in_array(Auth::user()->rol, ['admin', 'superadmin']))
                        <td>
                            <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-info btn-sm">Ver Detalles</a>

                            <form action="{{ route('pedidos.changeStatus', $pedido->id) }}" method="POST" class="d-inline">
                                @csrf
                                <select name="estado" class="form-select d-inline-block w-auto">
                                    <option value="en_preparacion" {{ $pedido->estado == 'en_preparacion' ? 'selected' : '' }}>En Preparación</option>
                                    <option value="enviado" {{ $pedido->estado == 'enviado' ? 'selected' : '' }}>Enviado</option>
                                    <option value="entregado" {{ $pedido->estado == 'entregado' ? 'selected' : '' }}>Entregado</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-secondary">Actualizar</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection
