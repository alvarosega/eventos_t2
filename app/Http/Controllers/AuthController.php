<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ IMPORTANTE
use App\Models\Externo;
use App\Models\Empleado;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validar los datos del formulario
        $credentials = $request->validate([
            'identificador' => 'required|string',
            'password' => 'required|string',
        ]);

        // Intentar autenticar como usuario externo
        if (Auth::guard('externo')->attempt(['numero_telefono' => $credentials['identificador'], 'password' => $credentials['password']])) {
            Auth::shouldUse('externo'); // 🔹 Asegurar que Laravel usa este guard
            session()->regenerate(); // 🔹 Regeneramos sesión
            return redirect()->route('home')->with('success', '¡Bienvenido, usuario externo!');
        }

        // Intentar autenticar como empleado
        if (Auth::guard('empleado')->attempt(['legajo' => $credentials['identificador'], 'password' => $credentials['password']])) {
            Auth::shouldUse('empleado'); // 🔹 Asegurar que Laravel usa este guard
            session()->regenerate(); // 🔹 Regeneramos sesión
            return redirect()->route('home')->with('success', '¡Bienvenido, empleado!');
        }

        return back()->withErrors(['identificador' => 'Credenciales incorrectas']);
    }

    /**
     * Cerrar sesión.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Cerrar sesión en el guard correspondiente
        Auth::logout(); // 🔹 Cerrar sesión de cualquier guard activo

        // Invalidar la sesión y regenerar el token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirigir al login
        return redirect()->route('login');
    }
}
