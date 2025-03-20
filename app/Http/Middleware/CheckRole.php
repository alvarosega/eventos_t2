<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verifica si el usuario está autenticado
        if (!auth()->check()) {
            return redirect()->route('empleado.login');
        }

        // Verifica si el rol del usuario está en los roles permitidos
        $user = auth()->user();
        if (!in_array($user->rol, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}