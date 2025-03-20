<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpleadoAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login-empleado');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'legajo' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('empleado')->attempt($credentials)) {
            return redirect()->intended('/empleado/dashboard');
        }

        return back()->withErrors(['legajo' => 'Credenciales incorrectas']);
    }

    public function logout(Request $request)
    {
        Auth::guard('empleado')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}