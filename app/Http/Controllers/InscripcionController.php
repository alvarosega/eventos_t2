<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Externo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'foto_referencia' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $usuarioLat = $request->input('lat');
        $usuarioLng = $request->input('lng');

        // Verificar si el evento tiene ubicaciones definidas
        $ubicacionEvento = $evento->ubicacion;
        if (empty($ubicacionEvento)) {
            return redirect()->route('inscripciones.index')
                ->withErrors(['error' => 'El evento no tiene ubicación definida.']);
        }

        // Obtener la primera ubicación válida del evento
        $coordenadasEvento = explode(';', $ubicacionEvento);
        $primeraCoordenada = trim($coordenadasEvento[0]);
        $partes = explode(',', $primeraCoordenada);

        if (count($partes) !== 2 || !is_numeric($partes[0]) || !is_numeric($partes[1])) {
            return redirect()->route('inscripciones.index')
                ->withErrors(['error' => 'Ubicación del evento inválida.']);
        }

        $latEvento = $partes[0];
        $lngEvento = $partes[1];

        // Calcular distancia entre el usuario y la primera ubicación del evento
        $distanciaKm = $this->calcularDistancia(
            $latEvento,
            $lngEvento,
            $usuarioLat,
            $usuarioLng
        );

        if ($distanciaKm > 3) {
            return redirect()->route('inscripciones.index')
                ->withErrors(['error' => 'Debes estar a menos de 3km del evento. Distancia actual: ' . round($distanciaKm, 2) . ' km']);
        }

        // Manejo de la imagen (opcional)
        if ($request->hasFile('foto_referencia')) {
            $imagen = $request->file('foto_referencia');
            $nombreImagen = 'ref_' . time() . '_' . $imagen->getClientOriginalName();
            $ruta = $imagen->storeAs('fotos_referencia', $nombreImagen, 'public');
            $externo->foto_referencia = $ruta;
        }

        // Guardar ubicación del usuario
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
}