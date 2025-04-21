<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Models\EventoTipo2;
use App\Models\Pedido;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        // Obtener el usuario autenticado desde los guards 'externo' o 'empleado'
        $usuario = Auth::guard('externo')->user() ?? Auth::guard('empleado')->user();

        if (!$usuario) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión']);
        }

        // Obtener el tipo de evento de la query string, por defecto 'tipo1'
        $eventType = $request->query('type', 'tipo1');

        // Obtener eventos según tipo y rol
        if ($usuario->rol == 'externo') {
            $eventos = Evento::where('estado', 'activo')->get();
            $ubicaciones = $this->getUbicacionesExterno($usuario);
        } else {
            $eventos = ($eventType === 'tipo2') ? EventoTipo2::all() : Evento::all();
            $ubicaciones = $this->getUbicacionesAdmin($eventType);
        }

        // Obtener pedidos para usuarios externos
        $pedidos = ($usuario->rol == 'externo' && $usuario->evento_id)
            ? Pedido::with('evento')
                ->where('evento_id', $usuario->evento_id)
                ->where('externo_id', $usuario->id)
                ->orderBy('id', 'desc')
                ->get()
            : collect();

        return view('home', [
            'usuario' => $usuario,
            'eventos' => $eventos,
            'eventType' => $eventType,
            'pedidos' => $pedidos,
            'ubicaciones' => $ubicaciones,
            'centerMap' => $this->getCenterMap($ubicaciones)
        ]);
    }

    /**
     * Obtiene ubicaciones para usuarios externos
     */
    protected function getUbicacionesExterno($usuario)
    {
        if (!$usuario->evento_id) return [];

        $evento = Evento::find($usuario->evento_id);
        if (!$evento || empty($evento->ubicacion)) return [];

        return [
            [
                'id' => $evento->id,
                'nombre' => $evento->nombre,
                'coords' => $this->parseUbicacion($evento->ubicacion),
                'tipo' => 'tipo1'
            ]
        ];
    }

    /**
     * Obtiene ubicaciones para administradores
     */
    protected function getUbicacionesAdmin($eventType)
    {
        $model = ($eventType === 'tipo2') ? EventoTipo2::class : Evento::class;
        
        return $model::select('id', ($eventType === 'tipo2' ? 'evento as nombre' : 'nombre'), 'ubicacion')
            ->whereNotNull('ubicacion')
            ->get()
            ->map(function($evento) use ($eventType) {
                return [
                    'id' => $evento->id,
                    'nombre' => $evento->nombre,
                    'coords' => $this->parseUbicacion($evento->ubicacion),
                    'tipo' => $eventType
                ];
            })
            ->filter(fn($e) => !empty($e['coords']))
            ->values()
            ->toArray();
    }

    /**
     * Parsea la cadena de ubicación a coordenadas
     */
    protected function parseUbicacion($ubicacion)
    {
        if (empty($ubicacion)) return [];

        // Formato esperado: "-3.749220, -73.253830"
        $coords = explode(',', trim($ubicacion));
        if (count($coords) !== 2) return [];

        return [
            [
                'lat' => (float)trim($coords[0]),
                'lng' => (float)trim($coords[1])
            ]
        ];
    }

    /**
     * Calcula el centro del mapa basado en las ubicaciones
     */
    protected function getCenterMap($ubicaciones)
    {
        if (empty($ubicaciones)) {
            return ['lat' => -12.046374, 'lng' => -77.042793]; // Coordenadas por defecto (Lima)
        }

        $firstLocation = $ubicaciones[0]['coords'][0];
        return ['lat' => $firstLocation['lat'], 'lng' => $firstLocation['lng']];
    }
}