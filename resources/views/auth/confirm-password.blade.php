<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmar Seguridad | La Cabaña 🍳</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (isset($estiloActivo) && !empty($estiloActivo->nombre))
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/' . $estiloActivo->nombre) }}?v={{ time() }}">
    @else
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ time() }}">
    @endif
</head>
<body class="welcome-body min-h-screen relative overflow-x-hidden font-sans antialiased bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center relative overflow-hidden px-4">
    @if(isset($estiloActivo) && ($estiloActivo->nombre == 'Navideño' || $estiloActivo->nombre == 'navidad.css'))
    @include('partials.navidad.decoraciones')
@elseif(isset($estiloActivo) && ($estiloActivo->nombre == 'Halloween' || $estiloActivo->nombre == 'halloween.css'))
    @include('partials.halloween.decoraciones')
@endif
    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <span class="text-4xl">🛡️</span>
            <h2 class="text-white font-bold text-xl mt-4">Área Segura</h2>
            <p class="text-slate-400 text-xs mt-2 max-w-xs mx-auto leading-relaxed">
                Esta es una sección segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
            </p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
            
            @if ($errors->any())
                <div class="mb-4 bg-rose-950/40 border border-rose-500/30 text-rose-400 rounded-xl p-3 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <p>⚠️ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="password" class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Contraseña</label>
                    <input id="password" type="password" name="password" required autofocus placeholder="••••••••"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition duration-200">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-black py-3.5 rounded-xl transition duration-200 text-sm uppercase tracking-wide">
                    Confirmar Contraseña 🔐
                </button>
            </form>
        </div>
    </div>

</body>
</html>
