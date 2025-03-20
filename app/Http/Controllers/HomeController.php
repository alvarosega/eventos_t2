<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;

class HomeController extends Controller
{
    public function home()
    {
        // Obtener el usuario autenticado
        $usuario = Auth::guard('externo')->user() ?? Auth::guard('empleado')->user();

        if (!$usuario) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión']);
        }

        // Obtener todos los eventos
        $eventos = Evento::all();

        // Enviar la variable eventos a la vista
        return view('home', compact('usuario', 'eventos'));
    }
}
