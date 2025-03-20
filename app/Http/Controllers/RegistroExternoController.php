<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Externo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RegistroExternoController extends Controller
{
    // Mostrar el formulario de registro
    public function showRegistrationForm()
    {
        return view('auth.registro-externo');
    }

    // Procesar el registro
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'numero_telefono' => 'required|string|unique:externos',
            'password' => 'required|string|min:8|confirmed',
            'foto_referencia' => 'nullable|image|max:2048',
        ]);
    
        // Hashear la contraseña antes de guardarla
        $externo = Externo::create([
            'nombre' => $request->nombre,
            'numero_telefono' => $request->numero_telefono,
            'password' => Hash::make($request->password), // ¡Aquí se hashea la contraseña!
            'foto_referencia' => $request->file('foto_referencia') ? $request->file('foto_referencia')->store('fotos') : null,
        ]);
    
        return redirect()->route('login')->with('success', 'Registro exitoso. Inicia sesión.');
    }
}