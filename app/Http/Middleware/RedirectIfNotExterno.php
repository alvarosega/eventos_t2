<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotExterno
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = 'externo')
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('externo.login');
        }

        return $next($request);
    }
}

