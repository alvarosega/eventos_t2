<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotEmpleado
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = 'empleado')
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('empleado.login');
        }

        return $next($request);
    }
}