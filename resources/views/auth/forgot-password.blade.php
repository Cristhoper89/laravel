<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Contraseña | {{ $empresaGlobal->name ?? 'TUCOMIDA1' }} 🍳</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (isset($estiloActivo) && !empty($estiloActivo->nombre))
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/' . $estiloActivo->nombre) }}?v={{ time() }}">
    @else
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ time() }}">
    @endif
</head>
</head>
<body class="welcome-body min-h-screen relative overflow-x-hidden auth-body font-sans antialiased min-h-screen flex items-center justify-center relative overflow-hidden px-4">
    @if(isset($estiloActivo) && ($estiloActivo->nombre == 'Navideño' || $estiloActivo->nombre == 'navidad.css'))
        @include('partials.navidad.decoraciones')
    @elseif(isset($estiloActivo) && ($estiloActivo->nombre == 'Halloween' || $estiloActivo->nombre == 'halloween.css'))
        @include('partials.halloween.decoraciones')
    @endif
    <div class="auth-glow absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <span class="text-4xl">🔐</span>
            <h2 class="auth-title font-bold text-xl mt-4">¿Olvidaste tu contraseña?</h2>
            <p class="auth-subtitle text-xs mt-2 max-w-sm mx-auto leading-relaxed">
                No hay problema. Escribe tu correo electrónico y te enviaremos un enlace seguro para que puedas restaurarla de inmediato.
            </p>
        </div>

        <div class="auth-card border rounded-3xl p-8 shadow-2xl">
            
            @if (session('status'))
                <div class="auth-status-box mb-4 border rounded-xl p-3 text-xs font-medium">
                    ✨ {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-errors-box mb-4 border rounded-xl p-3 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <p>⚠️ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="auth-label block text-xs font-bold uppercase tracking-wider mb-2">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="tu@ejemplo.com"
                           class="auth-input w-full border rounded-xl p-3 text-sm focus:outline-none transition duration-200">
                </div>

                <button type="submit" class="auth-btn-submit w-full font-black py-3.5 rounded-xl transition duration-200 text-sm uppercase tracking-wide shadow-lg">
                    Enviar Enlace de Recuperación ✉️
                </button>
            </form>
        </div>

        <p class="text-center text-sm auth-footer-text mt-6">
            <a href="{{ route('login') }}" class="auth-link font-bold transition duration-200">
                ← Volver al inicio de sesión
            </a>
        </p>
    </div>

</body>
</html>