<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión | {{ $empresaGlobal->name ?? 'TuComida' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (isset($estiloActivo) && !empty($estiloActivo->nombre))
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/' . $estiloActivo->nombre) }}?v={{ time() }}">
    @else
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ time() }}">
    @endif
</head>
<body class="welcome-body min-h-screen relative overflow-x-hidden auth-body font-sans antialiased flex items-center justify-center px-4">
    
    @if(isset($estiloActivo) && ($estiloActivo->nombre == 'Navideño' || $estiloActivo->nombre == 'navidad.css'))
        @include('partials.navidad.decoraciones')
    @elseif(isset($estiloActivo) && ($estiloActivo->nombre == 'Halloween' || $estiloActivo->nombre == 'halloween.css'))
        @include('partials.halloween.decoraciones')
    @endif

    <div class="auth-glow absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full blur-[130px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2 no-underline group">
                @if(!empty($empresaGlobal->logo))
                    <img src="{{ \Illuminate\Support\Str::startsWith($empresaGlobal->logo, 'http') ? $empresaGlobal->logo : (\Illuminate\Support\Str::startsWith($empresaGlobal->logo, '/storage') ? $empresaGlobal->logo : asset('storage/' . $empresaGlobal->logo)) }}" 
                         alt="Logo" class="h-10 w-10 object-cover rounded-xl transition-transform duration-200 group-hover:scale-110">
                @else
                    <span class="text-4xl transition-transform duration-200 group-hover:scale-110">🍳</span>
                @endif
                
                <span class="auth-brand-name text-2xl font-black tracking-wider">
                    {{ $empresaGlobal->name ?? 'TuComida' }}
                </span>
            </a>
            <h2 class="auth-title font-bold text-xl mt-4">¡Qué bueno verte de nuevo! 🔥</h2>
            <p class="auth-subtitle text-xs mt-1">Ingresa tus datos para acceder a tu cuenta y ordenar.</p>
        </div>

        <div class="auth-card border rounded-3xl p-8 shadow-2xl">
            
            {{-- Alerta para mensajes flash de error (ej: cuenta inactiva) --}}
            @if (session('error'))
                <div class="auth-errors-box mb-4 border border-rose-500/30 bg-rose-500/10 text-rose-400 rounded-xl p-3 text-xs font-medium flex items-center gap-2 shadow-md">
                    <span>🚫 {{ session('error') }}</span>
                </div>
            @endif

            {{-- Errores de validación de Laravel (credenciales incorrectas, etc.) --}}
            @if ($errors->any())
                <div class="auth-errors-box mb-4 border border-rose-500/30 bg-rose-500/10 text-rose-400 rounded-xl p-3 text-xs font-medium">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="auth-label block text-xs font-bold uppercase tracking-wider mb-2">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="tu@ejemplo.com"
                           class="auth-input w-full border rounded-xl p-3 text-sm focus:outline-none transition duration-200">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="auth-label block text-xs font-bold uppercase tracking-wider">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="auth-link-forgot text-xs font-medium transition duration-200">
                                ¿La olvidaste?
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required placeholder="••••••••"
                           class="auth-input w-full border rounded-xl p-3 text-sm focus:outline-none transition duration-200">
                </div>

                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" 
                           class="auth-checkbox rounded w-4 h-4 cursor-pointer">
                    <label for="remember_me" class="auth-label-checkbox ml-2 text-xs cursor-pointer select-none">Mantener sesión iniciada</label>
                </div>

                <button type="submit" class="auth-btn-primary w-full font-black py-3.5 rounded-xl transition duration-200 text-sm uppercase tracking-wide shadow-lg mt-2">
                    Ingresar 🚪
                </button>
            </form>
        </div>

        <p class="text-center text-sm auth-footer-text mt-6">
            ¿Aún no tienes una cuenta? 
            <a href="{{ route('register') }}" class="auth-link-alt font-bold transition duration-200 ml-1">
                Regístrate aquí 🛒
            </a>
        </p>
    </div>

</body>
</html>