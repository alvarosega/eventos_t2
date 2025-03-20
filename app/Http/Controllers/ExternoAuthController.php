<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternoAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login-externo');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'numero_telefono' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('externo')->attempt($credentials)) {
            return redirect()->intended('/externo/dashboard');
        }

        return back()->withErrors(['numero_telefono' => 'Credenciales incorrectas']);
    }

    public function logout(Request $request)
    {
        Auth::guard('externo')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}