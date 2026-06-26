<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 🔐 Tus políticas de acceso (Gates) actuales
        Gate::define('gestionar-usuarios', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('crear-cursos', function ($user) {
            return in_array($user->role, ['admin', 'docente']);
        });

        Gate::define('ver-notas', function ($user) {
            return in_array($user->role, ['admin', 'docente', 'estudiante']);
        });

        // 🌟 NUEVO: Compartir los datos de la empresa y estilos en tiempo real con todas las vistas
        View::composer('*', function ($view) {
            $empresaGlobal = DB::table('company')->where('id', 1)->first();
            $estiloActivo = DB::table('estilos')->where('estado', 1)->first();
            
            $view->with([
                'empresaGlobal' => $empresaGlobal,
                'estiloActivo'  => $estiloActivo
            ]);
        });
    }
}
