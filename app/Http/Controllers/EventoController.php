<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;

class EventoController extends Controller
{
    /**
     * Mostrar formulario para crear un evento (solo superadmin).
     */
    public function create()
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado. Se requiere superadmin.');
        }

        return view('eventos.create');
    }

    /**
     * Procesar y almacenar el evento creado.
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado. Se requiere superadmin.');
        }

        // Validar los datos
        $validated = $request->validate([
            'nombre'             => 'required|string|max:255',
            'fecha_inicio'       => 'required|date',
            'hora_inicio'        => 'required',
            'fecha_finalizacion' => 'required|date|after_or_equal:fecha_inicio',
            'hora_finalizacion'  => 'required',
            'estado'             => 'required|in:activo,en espera,finalizado',
            'descripcion'        => 'nullable|string',
            'ubicacion'          => 'required|string',
        ]);

        // Crear el evento
        Evento::create($validated);

        return redirect()->route('home')
            ->with('success', 'Evento creado correctamente.');
    }

    /**
     * Mostrar formulario para editar un evento (admin y superadmin).
     */
    public function edit($id)
    {
        $usuario = Auth::user();
        if (!in_array($usuario->rol, ['admin', 'superadmin'])) {
            abort(403, 'Acceso no autorizado (solo admin o superadmin).');
        }

        $evento = Evento::findOrFail($id);
        return view('eventos.edit', compact('evento'));
    }

    /**
     * Procesar la actualización del evento.
     */
    public function update(Request $request, $id)
    {
        $usuario = Auth::user();
        if (!in_array($usuario->rol, ['admin', 'superadmin'])) {
            abort(403, 'Acceso no autorizado (solo admin o superadmin).');
        }

        $evento = Evento::findOrFail($id);

        // Validar los datos
        $validated = $request->validate([
            'nombre'             => 'required|string|max:255',
            'fecha_inicio'       => 'required|date',
            'hora_inicio'        => 'required',
            'fecha_finalizacion' => 'required|date|after_or_equal:fecha_inicio',
            'hora_finalizacion'  => 'required',
            'descripcion'        => 'nullable|string',
            'estado'             => 'required|in:activo,en espera,finalizado',
            'ubicacion'          => 'required|string',
        ]);

        $evento->update($validated);

        return redirect()->route('home')
            ->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Eliminar un evento (admin y superadmin).
     */
    public function destroy($id)
    {
        $usuario = Auth::user();
        if (!in_array($usuario->rol, ['admin', 'superadmin'])) {
            abort(403, 'Acceso no autorizado (solo admin o superadmin).');
        }

        $evento = Evento::findOrFail($id);
        $evento->delete();

        return redirect()->route('home')
            ->with('success', 'Evento eliminado correctamente.');
    }
}
