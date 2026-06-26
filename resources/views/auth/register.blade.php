<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $empresaGlobal->name ?? 'TuComida' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (isset($estiloActivo) && !empty($estiloActivo->nombre))
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/' . $estiloActivo->nombre) }}?v={{ time() }}">
    @else
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ time() }}">
    @endif
</head>
</head>

<body
    class="welcome-body min-h-screen relative overflow-x-hidden auth-body font-sans antialiased min-h-screen flex items-center justify-center relative overflow-hidden px-4 py-12">
    @if(isset($estiloActivo) && ($estiloActivo->nombre == 'Navideño' || $estiloActivo->nombre == 'navidad.css'))
        @include('partials.navidad.decoraciones')
    @elseif(isset($estiloActivo) && ($estiloActivo->nombre == 'Halloween' || $estiloActivo->nombre == 'halloween.css'))
        @include('partials.halloween.decoraciones')
    @endif
    <div
        class="auth-glow absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full blur-[130px] pointer-events-none">
    </div>

    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2 no-underline group">
                @if (!empty($empresaGlobal->logo))
                    <img src="{{ \Illuminate\Support\Str::startsWith($empresaGlobal->logo, 'http') ? $empresaGlobal->logo : (\Illuminate\Support\Str::startsWith($empresaGlobal->logo, '/storage') ? $empresaGlobal->logo : asset('storage/' . $empresaGlobal->logo)) }}"
                        alt="Logo"
                        class="h-10 w-10 object-cover rounded-xl transition-transform duration-200 group-hover:scale-110">
                @else
                    <span class="text-4xl transition-transform duration-200 group-hover:scale-110">🍳</span>
                @endif

                <span class="auth-brand-name text-2xl font-black tracking-wider">
                    {{ $empresaGlobal->name ?? 'TuComida' }}
                </span>
            </a>
            <h2 class="auth-title font-bold text-xl mt-4">¡Crea tu perfil gourmet! 🥗</h2>
            <p class="auth-subtitle text-xs mt-1">Regístrate para guardar tu carrito e historial de compras.</p>
        </div>

        <div class="auth-card border rounded-3xl p-8 shadow-2xl">

            @if ($errors->any())
                <div class="auth-errors-box mb-4 border rounded-xl p-3 text-xs font-medium">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name"
                        class="auth-label block text-xs font-bold uppercase tracking-wider mb-2">Nombre Completo</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Juan Pérez"
                        class="auth-input w-full border rounded-xl p-3 text-sm focus:outline-none transition duration-200">
                </div>

                <div>
                    <label for="email"
                        class="auth-label block text-xs font-bold uppercase tracking-wider mb-2">Correo
                        Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        placeholder="juan@ejemplo.com"
                        class="auth-input w-full border rounded-xl p-3 text-sm focus:outline-none transition duration-200">
                </div>

                <div>
                    <label for="password"
                        class="auth-label block text-xs font-bold uppercase tracking-wider mb-2">Contraseña</label>
                    <input id="password" type="password" name="password" required placeholder="Mínimo 8 caracteres"
                        class="auth-input w-full border rounded-xl p-3 text-sm focus:outline-none transition duration-200">
                </div>

                <div>
                    <label for="password_confirmation"
                        class="auth-label block text-xs font-bold uppercase tracking-wider mb-2">Confirmar
                        Contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        placeholder="Repite tu contraseña"
                        class="auth-input w-full border rounded-xl p-3 text-sm focus:outline-none transition duration-200">
                </div>

                <button type="submit"
                    class="auth-btn-submit w-full font-black py-3.5 rounded-xl transition duration-200 text-sm uppercase tracking-wide shadow-lg mt-2">
                    Crear mi Cuenta 🚀
                </button>
            </form>
        </div>

        <p class="text-center text-sm auth-footer-text mt-6">
            ¿Ya tienes una cuenta registrada?
            <a href="{{ route('login') }}" class="auth-link font-bold transition duration-200 ml-1">
                Inicia sesión aquí
            </a>
        </p>
    </div>

</body>

</html>
