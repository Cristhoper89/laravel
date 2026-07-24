<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Soporta uno o varios roles separados por coma
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // 1. Extraemos todos los roles pasados (ej: "admin,cajero,cajero2" o ["admin", "cajero"])
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, explode(',', $role));
        }

        // Limpiamos espacios en blanco por seguridad
        $allowedRoles = array_map('trim', $allowedRoles);

        // 2. Verificamos si el rol del usuario autenticado existe dentro del array
        if (!in_array(auth()->user()->role, $allowedRoles, true)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}