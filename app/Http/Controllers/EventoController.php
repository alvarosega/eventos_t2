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
    
        if (!in_array($usuario->rol, ['superadmin', 'master'])) {
            abort(403, 'Acceso no autorizado. Se requiere superadmin o master.');
        }
    
        return view('eventos.select-type');
    }
    

    /**
     * Mostrar formulario para crear evento tipo1 (original)
     */
    public function create()
    {
        $usuario = Auth::user();
    
        if (!in_array($usuario->rol, ['superadmin', 'master'])) {
            abort(403, 'Acceso no autorizado. Se requiere superadmin o master.');
        }
    
        return view('eventos.create');
    }
    

    /**
     * Mostrar formulario para crear evento tipo2 (nuevo)
     */
    public function createTipo2()
    {
        $usuario = Auth::user();
        
        if (!in_array($usuario->rol, ['superadmin', 'master'])) {
            abort(403, 'Acceso no autorizado. Se requiere superadmin.');
        }
    
        if ($usuario->rol === 'master') {
            $empleados = \App\Models\Empleado::whereIn('rol', ['superadmin', 'master'])->get();
            return view('eventos.create-tipo2', compact('usuario', 'empleados'));
        }
    
        // Si es superadmin (o cualquier otro rol permitido), 
        // retornamos la vista igual, pero pasando también $usuario
        return view('eventos.create-tipo2', compact('usuario'));
    }
    
    

    /**
     * Almacenar evento tipo1
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();
    
        // Validar
        $validated = $request->validate([
            'nombre'             => 'required|string|max:255',
            'fecha_inicio'       => 'required|date',
            'hora_inicio'        => 'required',
            'fecha_finalizacion' => 'required|date|after_or_equal:fecha_inicio',
            'hora_finalizacion'  => 'required',
            'estado'             => 'required|in:activo,en espera,finalizado',
            'descripcion'        => 'nullable|string',
            'ubicacion'          => 'required|string',  // Ej.: "lat,lng" o "lat1,lng1;lat2,lng2;..."
        ]);
    
        // Asignar automáticamente el legajo del usuario autenticado
        $validated['legajo'] = $usuario->legajo;
    
        // Crear el evento
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
        if (!in_array($usuario->rol, ['superadmin', 'master'])) {
            abort(403, 'Acceso no autorizado. Se requiere superadmin.');
        }
    
        $validated = $request->validate([
            'fecha'         => 'required|date',
            'evento'        => 'required|string|max:255',
            'encargado'     => 'required|string|max:255',
            'celular'       => 'required|digits:8',
            'direccion'     => 'required|string|max:255',
            'ubicacion'     => 'required|string',
            'material'      => 'nullable|string', // Este campo puede quedar vacío
            'hor_entrega'   => 'required',
            'recojo'        => 'required|string',
            'operador'      => 'required|string|max:255',
            'supervisor'    => 'required|string|max:255',
            'estado_evento' => 'required|in:pendiente,aprobado,cancelado,rechazado',
        ]);
    
        // Si no se envía 'material' en el request, se asigna cadena vacía
        if (!isset($validated['material'])) {
            $validated['material'] = '';
        }
    
        // Convertir el valor de recojo de "YYYY-MM-DDTHH:MM" a "YYYY-MM-DD HH:MM:SS"
        $validated['recojo'] = str_replace('T', ' ', $validated['recojo']) . ':00';
    
        // Se toma el legajo enviado desde el formulario
        $validated['legajo'] = $request->input('legajo');
    
        // Crear el evento tipo2
        $eventoTipo2 = \App\Models\EventoTipo2::create($validated);
    
        // Procesar los materiales dinámicos enviados
        if ($request->has('material_item')) {
            $materialItems = $request->input('material_item'); // Array de materiales
            $materialQuantities = $request->input('material_quantity'); // Array de cantidades
    
            foreach ($materialItems as $index => $materialName) {
                $cantidad = $materialQuantities[$index] ?? 0;
                $fotoEntregadoPath = null;
                if ($request->hasFile("material_photo.$index")) {
                    $fotoEntregadoPath = $request->file("material_photo.$index")->store('evento_materials', 'public');
                }
    
                \App\Models\EventoMaterial::create([
                    'evento_tipo2_id' => $eventoTipo2->id,
                    'material'        => $materialName,
                    'cantidad'        => $cantidad,
                    'foto_entregado'  => $fotoEntregadoPath,
                    'foto_recibido'   => null, // Se asigna null inicialmente
                    'legajo'          => $request->input('legajo'),
                ]);
            }
        }
    
        return redirect()->route('home')
            ->with('success', 'Evento tipo 2 creado correctamente.');
    }
    
    
    

    public function edit($id)
    {
        $usuario = Auth::user();
        // Antes: if (!in_array($usuario->rol, ['superadmin', 'master'])) {
        if ($usuario->rol !== 'master') {
            abort(403, 'Acceso no autorizado (solo master).');
        }
    
        $evento = Evento::findOrFail($id);
        return view('eventos.edit', compact('evento'));
    }
    
    public function editTipo2($id)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'master') {
            abort(403, 'Acceso no autorizado (solo master).');
        }
    
        $evento = EventoTipo2::findOrFail($id);
        return view('eventos.edit-tipo2', compact('evento'));
    }
    
    public function update(Request $request, $id)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'master') {
            abort(403, 'Acceso no autorizado (solo master).');
        }
    
        $evento = Evento::findOrFail($id);
    
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
            ->with('success', 'Evento tipo 1 actualizado correctamente.');
    }
    
    public function updateTipo2(Request $request, $id)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'master') {
            abort(403, 'Acceso no autorizado (solo master).');
        }
    
        $evento = \App\Models\EventoTipo2::findOrFail($id);
    
        $validated = $request->validate([
            'fecha'         => 'required|date',
            'evento'        => 'required|string|max:255',
            'encargado'     => 'required|string|max:255',
            'celular'       => 'required|digits:8',
            'direccion'     => 'required|string|max:255',
            'ubicacion'     => 'required|string',
            'material'      => 'nullable|string',
            'hor_entrega'   => 'required',
            'recojo'        => 'required|string',
            'operador'      => 'required|string|max:255',
            'supervisor'    => 'required|string|max:255',
            'estado_evento' => 'required|in:pendiente,aprobado,cancelado,rechazado',
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
        if ($usuario->rol !== 'master') {
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
        if ($usuario->rol !== 'master') {
            abort(403, 'Acceso no autorizado (solo admin o superadmin).');
        }

        $evento = EventoTipo2::findOrFail($id);
        $evento->delete();

        return redirect()->route('home')
            ->with('success', 'Evento tipo 2 eliminado correctamente.');
    }

    public function admin(Request $request)
    {
        $usuario = Auth::user();
    
        // Determinar el tipo de evento según query string o default
        $eventType = $request->query('type', 'tipo1');
    
        if (in_array($usuario->rol, ['superadmin', 'master'])) {
            $eventos = $eventType === 'tipo2'
                ? EventoTipo2::all()
                : Evento::all();
        } else {
            $eventos = $eventType === 'tipo2'
                ? EventoTipo2::where('legajo', $usuario->legajo)->get()
                : Evento::where('legajo', $usuario->legajo)->get();
        }
    
        return view('eventos.admin', compact('usuario', 'eventType', 'eventos'));
    } 

    public function adminEvents(Request $request)
    {
        $usuario = Auth::user();
        $eventType = $request->query('type', 'tipo1'); // Por defecto, 'tipo1'
        
        if ($eventType === 'tipo2') {
            $eventos = EventoTipo2::all();
        } else {
            $eventos = Evento::all();
        }
        
        return view('eventos.admin', compact('usuario', 'eventType', 'eventos'));
    }   
}