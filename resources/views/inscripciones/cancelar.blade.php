@extends('layouts.app')

@section('content')
    <h1>Cancelar Inscripción</h1>

    <form action="{{ route('inscripciones.cancel', $inscripcionId) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="motivo" class="form-label">Motivo de la cancelación</label>
            <textarea name="motivo" id="motivo" rows="3" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
    </form>
@endsection
