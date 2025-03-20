@extends('layouts.app')

@section('title', 'Administrar Catálogos')

@section('content')
    <h2>Administrar Catálogos</h2>
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Evento</th>
                <th>Nombre del Evento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eventos as $evento)
                <tr>
                    <td>{{ $evento->id }}</td>
                    <td>{{ $evento->nombre }}</td>
                    <td>
                        <a href="{{ route('catalogos.show', $evento->id) }}" class="btn btn-primary">Administrar Catálogo</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
