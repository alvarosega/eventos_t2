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
            'nombre'            => 'required|string|max:255',
            'numero_telefono'   => 'required|string|unique:externos',
            'password'          => 'required|string|min:8|confirmed',
            'foto_referencia'   => 'nullable|image|max:2048',
        ]);

        $filename = null;

        if ($request->hasFile('foto_referencia')) {
            // Obtenemos la extensión del archivo
            $extension = $request->file('foto_referencia')->getClientOriginalExtension();
            // Formamos el nombre del archivo usando el número de teléfono (por ejemplo, 123.png)
            $filename = $request->numero_telefono . '.' . $extension;
            // Guardamos el archivo en storage/app/public/externos_auth usando el disco "public"
            $request->file('foto_referencia')->storeAs('externos_auth', $filename, 'public');
        }

        // Creamos el registro y hasheamos la contraseña
        $externo = Externo::create([
            'nombre'           => $request->nombre,
            'numero_telefono'  => $request->numero_telefono,
            'password'         => Hash::make($request->password),
            'foto_referencia'  => $filename, // Aquí se guarda solo el nombre, sin ruta
        ]);

        return redirect()->route('login')->with('success', 'Registro exitoso. Inicia sesión.');
    }
}
