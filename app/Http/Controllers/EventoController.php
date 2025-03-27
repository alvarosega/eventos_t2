<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Models\EventoTipo2;

class EventoController extends Controller
{
    /**
     * Mostrar selección de tipo de evento
     */
    public function selectType()
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado. Se requiere superadmin.');
        }

        return view('eventos.select-type');
    }

    /**
     * Mostrar formulario para crear evento tipo1 (original)
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
     * Mostrar formulario para crear evento tipo2 (nuevo)
     */
    public function createTipo2()
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado. Se requiere superadmin.');
        }

        return view('eventos.create-tipo2');
    }

    /**
     * Almacenar evento tipo1
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado. Se requiere superadmin.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'hora_inicio' => 'required',
            'fecha_finalizacion' => 'required|date|after_or_equal:fecha_inicio',
            'hora_finalizacion' => 'required',
            'estado' => 'required|in:activo,en espera,finalizado',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'required|string',
        ]);

        Evento::create($validated);

        return redirect()->route('home')
            ->with('success', 'Evento tipo 1 creado correctamente.');
    }

    /**
     * Almacenar evento tipo2
     */
    public function storeTipo2(Request $request)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado. Se requiere superadmin.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'evento' => 'required|string|max:255',
            'encargado' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'ubicacion' => 'required|string',
            'material' => 'required|string',
            'hor_entrega' => 'required',
            'recojo' => 'required|boolean',
            'operador' => 'required|string|max:255',
            'supervisor' => 'required|string|max:255',
            'estado_evento' => 'required|in:pendiente,en_proceso,completado',
        ]);

        EventoTipo2::create($validated);

        return redirect()->route('home')
            ->with('success', 'Evento tipo 2 creado correctamente.');
    }

    /**
     * Editar evento tipo1
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
     * Editar evento tipo2
     */
    public function editTipo2($id)
    {
        $usuario = Auth::user();
        if (!in_array($usuario->rol, ['admin', 'superadmin'])) {
            abort(403, 'Acceso no autorizado (solo admin o superadmin).');
        }

        $evento = EventoTipo2::findOrFail($id);
        return view('eventos.edit-tipo2', compact('evento'));
    }

    /**
     * Actualizar evento tipo1
     */
    public function update(Request $request, $id)
    {
        $usuario = Auth::user();
        if (!in_array($usuario->rol, ['admin', 'superadmin'])) {
            abort(403, 'Acceso no autorizado (solo admin o superadmin).');
        }

        $evento = Evento::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'hora_inicio' => 'required',
            'fecha_finalizacion' => 'required|date|after_or_equal:fecha_inicio',
            'hora_finalizacion' => 'required',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,en espera,finalizado',
            'ubicacion' => 'required|string',
        ]);

        $evento->update($validated);

        return redirect()->route('home')
            ->with('success', 'Evento tipo 1 actualizado correctamente.');
    }

    /**
     * Actualizar evento tipo2
     */
    public function updateTipo2(Request $request, $id)
    {
        $usuario = Auth::user();
        if (!in_array($usuario->rol, ['admin', 'superadmin'])) {
            abort(403, 'Acceso no autorizado (solo admin o superadmin).');
        }

        $evento = EventoTipo2::findOrFail($id);

        $validated = $request->validate([
            'fecha' => 'required|date',
            'evento' => 'required|string|max:255',
            'encargado' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'ubicacion' => 'required|string',
            'material' => 'required|string',
            'hor_entrega' => 'required',
            'recojo' => 'required|boolean',
            'operador' => 'required|string|max:255',
            'supervisor' => 'required|string|max:255',
            'estado_evento' => 'required|in:pendiente,en_proceso,completado',
        ]);

        $evento->update($validated);

        return redirect()->route('home')
            ->with('success', 'Evento tipo 2 actualizado correctamente.');
    }

    /**
     * Eliminar evento tipo1
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
            ->with('success', 'Evento tipo 1 eliminado correctamente.');
    }

    /**
     * Eliminar evento tipo2
     */
    public function destroyTipo2($id)
    {
        $usuario = Auth::user();
        if (!in_array($usuario->rol, ['admin', 'superadmin'])) {
            abort(403, 'Acceso no autorizado (solo admin o superadmin).');
        }

        $evento = EventoTipo2::findOrFail($id);
        $evento->delete();

        return redirect()->route('home')
            ->with('success', 'Evento tipo 2 eliminado correctamente.');
    }
}