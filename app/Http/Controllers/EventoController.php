<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Models\EventoTipo2;
use App\Models\Material;
use App\Models\Externo;

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
    
        // Validación principal
        $validated = $request->validate([
            'fecha'         => 'required|date',
            'evento'        => 'required|string|max:255',
            'encargado'     => 'required|string|max:255',
            'celular'       => 'required|digits:8',
            'direccion'     => 'required|string|max:255',
            'ubicacion'     => 'required|string',
            'hor_entrega'   => 'required',
            'recojo'        => 'required|string',
            'operador'      => 'required|string|max:255',
            'supervisor'    => 'required|string|max:255',
            'estado_evento' => 'required|in:pendiente,aprobado,cancelado,rechazado',
            'materiales'    => 'required|array|min:1',
            'materiales.*.id' => 'required|exists:materiales,id',
            'materiales.*.cantidad' => 'required|integer|min:1',
            'materiales.*.foto_entrega' => 'required|image',
        ]);
    
        // Procesamiento de fechas
        $validated['recojo'] = Carbon::createFromFormat('Y-m-d\TH:i', $validated['recojo'])->format('Y-m-d H:i:s');    
        DB::beginTransaction();
    
        try {
            // Crear evento
            $evento = EventoTipo2::create([
                'fecha' => $validated['fecha'],
                'evento' => $validated['evento'],
                'encargado' => $validated['encargado'],
                'celular' => $validated['celular'],
                'direccion' => $validated['direccion'],
                'ubicacion' => $validated['ubicacion'],
                'hor_entrega' => $validated['hor_entrega'],
                'recojo' => $validated['recojo'],
                'operador' => $validated['operador'],
                'supervisor' => $validated['supervisor'],
                'estado_evento' => $validated['estado_evento'],
                'legajo' => $request->input('legajo'),
            ]);
    
            // Procesar materiales
            foreach ($request->materiales as $materialData) {
                $material = Material::findOrFail($materialData['id']);
    
                // Verificar stock
                if ($material->stock_total < $materialData['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$material->nombre}");
                }
    
                // Subir foto
                $fotoPath = $materialData['foto_entrega']->store('materiales/entrega', 'public');
    
                // Adjuntar material al evento
                $evento->materiales()->attach($material->id, [
                    'cantidad' => $materialData['cantidad'],
                    'fecha_entrega' => now(),
                    'fecha_devolucion_estimada' => $validated['recojo'],
                    'foto_entrega' => $fotoPath,
                    'estado' => 'reservado'
                ]);
    
                // Actualizar stock
                $material->decrement('stock_total', $materialData['cantidad']);
            }
    
            DB::commit();
    
            return redirect()->route('home')
                ->with('success', 'Evento tipo 2 creado correctamente.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
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
    
        // Actualizar el evento
        $evento->update($validated);
    
        // Si el estado del evento pasó a 'finalizado',
        // actualizamos la tabla "externos" para desinscribir a los usuarios
        if ($validated['estado'] === 'finalizado') {
            Externo::where('evento_id', $evento->id)
                ->update(['evento_id' => null]);
        }
    
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

    public function devolverMaterial(Request $request, $eventoId, $materialId) {
        // Validar solo master
        if (Auth::user()->rol !== 'master') abort(403);
    
        $validated = $request->validate([
            'foto_devolucion' => 'required|image',
            'cantidad_devuelta' => 'required|integer|min:1',
            'estado' => 'required|in:devuelto,dañado',
            'notas' => 'nullable|string'
        ]);
    
        // Actualizar pivot
        $pivot = DB::table('evento_material')
            ->where('evento_tipo2_id', $eventoId)
            ->where('material_id', $materialId)
            ->update([
                'fecha_devolucion_real' => now(),
                'estado' => $validated['estado'],
                'foto_devolucion' => $request->file('foto_devolucion')->store('devoluciones', 'public'),
                'notas_devolucion' => $validated['notas']
            ]);
    
        // Si está devuelto y no dañado, aumentar stock
        if ($validated['estado'] === 'devuelto') {
            Material::find($materialId)->increment('stock_total', $validated['cantidad_devuelta']);
        }
    
        return back()->with('success', 'Material devuelto.');
    }
}