<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Externo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class InscripcionController extends Controller
{
    public function index()
    {
        $externo = Auth::guard('externo')->user();
        if (!$externo) {
            return redirect()->route('login')->withErrors(['error' => 'Debe iniciar sesión como usuario externo.']);
        }

        if ($externo->evento_id) {
            return redirect()->route('home')->withErrors(['error' => 'Ya está inscrito a un evento.']);
        }

        $eventos = Evento::where('estado', '!=', 'finalizado')->get();
        return view('inscripciones.index', compact('eventos'));
    }

    public function showMapa($eventoId)
    {
        $externo = Auth::guard('externo')->user();
        if (!$externo) {
            return redirect()->route('login')->withErrors(['error' => 'Debe iniciar sesión como usuario externo.']);
        }

        if ($externo->evento_id) {
            return redirect()->route('home')->withErrors(['error' => 'Ya estás inscrito a un evento.']);
        }

        $evento = Evento::findOrFail($eventoId);
        if ($evento->estado == 'finalizado') {
            return redirect()->route('inscripciones.index')->withErrors(['error' => 'El evento está finalizado.']);
        }

        return view('inscripciones.mapa', compact('evento'));
    }

    public function storeUbicacion(Request $request, $eventoId)
    {
        $externo = Auth::guard('externo')->user();
        if (!$externo) {
            return redirect()->route('login')->withErrors(['error' => 'Debe iniciar sesión como usuario externo.']);
        }
    
        if ($externo->evento_id) {
            return redirect()->route('home')->withErrors(['error' => 'Ya estás inscrito a un evento.']);
        }
    
        $evento = Evento::findOrFail($eventoId);
        if ($evento->estado == 'finalizado') {
            return redirect()->route('inscripciones.index')->withErrors(['error' => 'El evento está finalizado.']);
        }
    
        // Validación: Si el usuario no tiene fotografía de referencia, se obliga a enviarla.
        $rules = [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ];
        if (!$externo->foto_referencia) {
            $rules['foto_referencia'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        } else {
            $rules['foto_referencia'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }
        $request->validate($rules);
    
        $usuarioLat = $request->input('lat');
        $usuarioLng = $request->input('lng');
    
        // Verificar que el evento tenga ubicaciones válidas (la lógica existente)
        $ubicacionEvento = $evento->ubicacion;
        if (empty($ubicacionEvento)) {
            return redirect()->route('inscripciones.index')
                ->withErrors(['error' => 'El evento no tiene ubicación definida.']);
        }
        $coordenadasEvento = explode(';', $ubicacionEvento);
        $primeraCoordenada = trim($coordenadasEvento[0]);
        $partes = explode(',', $primeraCoordenada);
        if (count($partes) !== 2 || !is_numeric($partes[0]) || !is_numeric($partes[1])) {
            return redirect()->route('inscripciones.index')
                ->withErrors(['error' => 'Ubicación del evento inválida.']);
        }
    
        // Manejo de la imagen: solo se procesa si se subió un archivo
        if ($request->hasFile('foto_referencia')) {
            $imagen = $request->file('foto_referencia');
            $telefono = $externo->numero_telefono; // Número de teléfono del usuario
            $extension = $imagen->getClientOriginalExtension();
            // Nombre de archivo: número de teléfono + '_' + eventoId + '.' + extensión
            $nombreImagen = $telefono . '_' . $evento->id . '.' . $extension;
            
            // Si ya existe un archivo anterior, lo eliminamos (opcional)
            if ($externo->foto_referencia && Storage::disk('public')->exists('externos_auth/' . $externo->foto_referencia)) {
                Storage::disk('public')->delete('externos_auth/' . $externo->foto_referencia);
            }
            
            // Almacenar la imagen en 'storage/app/public/externos_auth'
            $imagen->storeAs('externos_auth', $nombreImagen, 'public');
            $externo->foto_referencia = $nombreImagen;
        }
        
        // Guardar ubicación del usuario e inscripción al evento
        $externo->ubicacion = "{$usuarioLat},{$usuarioLng}";
        $externo->evento_id = $evento->id;
        $externo->save();
    
        return redirect()->route('home')->with('success', '¡Inscripción exitosa!');
    }
    
    

    private function calcularDistancia($lat1, $lng1, $lat2, $lng2)
    {
        $radioTierra = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $radioTierra * $c;
    }

    public function cancelarForm($id)
    {
        $usuario = Auth::user();

        // Verificar si el usuario está inscrito en el evento (columna evento_id en 'externos')
        if ($usuario->evento_id != $id) {
            return redirect()->back()->with('error', 'No se encontró tu inscripción a este evento.');
        }

        // Obtener el evento para mostrar datos en la vista
        $evento = Evento::findOrFail($id);

        return view('inscripciones.cancelar', compact('evento'));
    }

    /**
     * Procesar la cancelación de la inscripción.
     */
    public function cancelar(Request $request, $id)
    {
        $usuario = Auth::user();
    
        // Obtener los datos del usuario externo desde la tabla "externos"
        $externo = DB::table('externos')->where('id', $usuario->id)->first();
    
        if (!$externo || !$externo->evento_id) {
            return redirect()->back()->with('error', 'No tienes un evento registrado.');
        }
    
        // Validar el motivo de cancelación
        $request->validate([
            'motivo'       => 'required|string|max:255',
            'motivo_extra' => 'nullable|string|max:500',
        ]);
    
        // Construir el motivo final
        $motivoFinal = $request->motivo;
        if (!empty($request->motivo_extra)) {
            $motivoFinal .= ' - ' . $request->motivo_extra;
        }
    
        // Insertar la cancelación en "inscripciones_canceladas"
        DB::table('inscripciones_canceladas')->insert([
            'inscripcion_id'  => $externo->id, // Relacionado con 'externos.id'
            'nombre'          => $externo->nombre, // Guardamos el nombre del usuario
            'numero_telefono' => $externo->numero_telefono, // Guardamos su número de teléfono
            'motivo'          => $motivoFinal,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    
        // Eliminar la inscripción en la tabla "externos" poniendo evento_id en NULL
        DB::table('externos')->where('id', $externo->id)->update(['evento_id' => null]);
    
        return redirect()->route('home')->with('success', 'Tu inscripción ha sido cancelada exitosamente.');
    }
    
    
}