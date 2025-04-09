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

        // Para usuarios externos se conservan los eventos activos
        if ($usuario->rol == 'externo') {
            $eventos = Evento::where('estado', 'activo')->get();
        } else {
            // Para cualquier usuario no externo (incluyendo superadmin, master, etc.), se filtra según el tipo seleccionado.
            $eventos = ($eventType === 'tipo2')
                ? EventoTipo2::all()   // Devuelve solo los eventos del tipo 2
                : Evento::all();       // Devuelve los eventos del tipo 1 (tabla original)
        }

        // Si el usuario es externo y tiene evento asignado, obtener sus pedidos filtrados
        if ($usuario->rol == 'externo' && $usuario->evento_id) {
            $pedidos = Pedido::with('evento')
                ->where('evento_id', $usuario->evento_id)
                ->where('externo_id', $usuario->id)
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $pedidos = collect(); // Colección vacía para otros roles
        }

        return view('home', [
            'usuario'   => $usuario,
            'eventos'   => $eventos,
            'eventType' => $eventType,
            'pedidos'   => $pedidos
        ]);
    }
}
