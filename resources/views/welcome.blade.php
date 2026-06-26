<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $empresaGlobal->name ?? 'TuComida' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (isset($estiloActivo) && !empty($estiloActivo->nombre))
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/' . $estiloActivo->nombre) }}?v={{ time() }}">
    @else
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ time() }}">
    @endif
</head>

<body class="welcome-body font-sans antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">
    @if(isset($estiloActivo) && ($estiloActivo->nombre == 'Navideño' || $estiloActivo->nombre == 'navidad.css'))
    @include('partials.navidad.decoraciones')
    @elseif(isset($estiloActivo) && ($estiloActivo->nombre == 'Halloween' || $estiloActivo->nombre == 'halloween.css'))
        @include('partials.halloween.decoraciones')
    @endif
    <div
        class="glow-light glow-top absolute top-[-10%] left-[-10%] w-[500px] h-[500px] rounded-full blur-[120px] pointer-events-none">
    </div>
    <div
        class="glow-light glow-bottom absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full blur-[120px] pointer-events-none">
    </div>

    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center relative z-10">
        <div class="flex items-center gap-2">
            @if (!empty($empresaGlobal->logo))
                <img src="{{ \Illuminate\Support\Str::startsWith($empresaGlobal->logo, 'http') ? $empresaGlobal->logo : (\Illuminate\Support\Str::startsWith($empresaGlobal->logo, '/storage') ? $empresaGlobal->logo : asset('storage/' . $empresaGlobal->logo)) }}"
                    alt="Logo" class="h-10 w-10 object-cover rounded-xl shadow-md ring-2 ring-primary/10">
            @else
                <span class="text-3xl">🍳</span>
            @endif

            <span class="brand-name text-2xl font-black tracking-wider">
                {{ $empresaGlobal->name ?? 'TuComida' }}
            </span>
        </div>

        <nav class="flex items-center gap-4">
            @auth
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('cliente.dashboard') }}"
                    class="btn-panel font-bold px-5 py-2.5 rounded-xl transition duration-200 text-sm shadow-md">
                    Ir al Panel 🏠
                </a>
            @else
                <a href="{{ route('login') }}" class="link-login font-semibold text-sm transition duration-200">
                    Iniciar Sesión
                </a>
                <a href="{{ route('register') }}"
                    class="btn-register font-black px-5 py-2.5 rounded-xl transition duration-200 text-sm shadow-lg">
                    Registrarse
                </a>
            @endauth
        </nav>
    </header>

    <main
        class="w-full max-w-5xl mx-auto px-6 py-12 md:py-24 text-center relative z-10 flex flex-col items-center justify-center flex-1">

        <span
            class="welcome-badge inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase mb-6 animate-pulse">
            ✨ Sabor único en cada rincón
        </span>

        <!-- Título de Impacto -->
        <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white mb-6 max-w-3xl leading-tight">
            Descubre el auténtico sabor de <span
                class="hero-highlight">{{ $empresaGlobal->name ?? 'La Cabaña' }}</span>
        </h1>

        <p class="hero-subtitle text-base md:text-xl max-w-2xl mb-10 leading-relaxed">
            Pide tus platillos favoritos desde nuestro menú digital, acumula historial de compras y disfruta de una
            experiencia gastronómica rápida y moderna.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center w-full max-w-sm sm:max-w-none">
            @auth
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('cliente.dashboard') }}"
                    class="btn-hero-primary font-black px-8 py-4 rounded-2xl transition duration-200 text-sm uppercase tracking-wide shadow-xl">
                    Ver el Menú del Día 🍽️
                </a>
            @else
                <a href="{{ route('register') }}"
                    class="btn-hero-primary font-black px-8 py-4 rounded-2xl transition duration-200 text-sm uppercase tracking-wide shadow-xl">
                    Empezar a Ordenar 🛒
                </a>
                <a href="{{ route('login') }}"
                    class="btn-hero-secondary font-bold px-8 py-4 rounded-2xl transition duration-200 text-sm">
                    Ingresar a mi Cuenta
                </a>
            @endauth
        </div>

        <div class="features-grid grid grid-cols-1 sm:grid-cols-3 gap-6 mt-16 md:mt-24 w-full pt-12">
            <div class="feature-card border rounded-2xl p-6 text-left">
                <div class="text-2xl mb-3">🔥</div>
                <h3 class="text-white font-bold text-base mb-1">Ingredientes Frescos</h3>
                <p class="feature-desc text-xs leading-relaxed">Cocinados al momento bajo los más altos estándares de
                    calidad.</p>
            </div>
            <div class="feature-card border rounded-2xl p-6 text-left">
                <div class="text-2xl mb-3">⚡</div>
                <h3 class="text-white font-bold text-base mb-1">Pedido Express</h3>
                <p class="feature-desc text-xs leading-relaxed">Arma tu carrito, confirma tu pago y recíbelo
                    directamente en tu mesa o domicilio.</p>
            </div>
            <div class="feature-card border rounded-2xl p-6 text-left">
                <div class="text-2xl mb-3">🧾</div>
                <h3 class="text-white font-bold text-base mb-1">Historial Inteligente</h3>
                <p class="feature-desc text-xs leading-relaxed">Accede a tus comprobantes, facturas con IVA y repite tus
                    antojos favoritos.</p>
            </div>
        </div>
    </main>

    <footer class="main-footer w-full py-6 text-center text-xs relative z-10">
        <p>&copy; {{ date('Y') }} {{ $empresaGlobal->name ?? 'TuComida' }}. Todos los derechos reservados.</p>
    </footer>

</body>

</html>
