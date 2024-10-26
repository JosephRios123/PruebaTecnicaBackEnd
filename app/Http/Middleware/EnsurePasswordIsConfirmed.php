<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordIsConfirmed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Verifica si el usuario está autenticado y si su contraseña está confirmada
        if (Auth::check() && !Auth::user()->hasVerifiedPassword()) {
            return redirect()->route('password.confirm'); // Redirige a la ruta de confirmación de contraseña
        }

        return $next($request);
    }
}
