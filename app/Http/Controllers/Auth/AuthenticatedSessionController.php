<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Intenta validar las credenciales (email y contraseña)
        $request->authenticate();

        // 2. Verificar si el usuario está inactivo (estado = 0 o false)
        if (! Auth::user()->estado) {
            // Cerramos la sesión que se acaba de abrir
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirigimos de vuelta con el mensaje de error
            return redirect()->route('login')->with('error', 'Tu cuenta se encuentra inactiva. Consulta con el administrador.');
        }

        // 3. Si está activo, regenera la sesión y continúa normalmente
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}