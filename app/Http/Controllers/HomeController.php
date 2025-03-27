<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Models\EventoTipo2;

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

        // Solo el superadmin puede alternar entre eventos de tipo 1 y tipo 2
        if ($usuario->rol == 'superadmin') {
            $eventos = ($eventType === 'tipo2') 
                ? EventoTipo2::all()  // Usar la tabla "eventos_tipo2"
                : Evento::all();      // Usar la tabla original "eventos"
        } else {
            $eventos = Evento::all();
        }

        return view('home', [
            'usuario'    => $usuario,
            'eventos'    => $eventos,
            'eventType'  => $eventType
        ]);
    }
}
