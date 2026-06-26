<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer Contraseña | La Cabaña 🍳</title>
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
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-cyan-500/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-8">
            <span class="text-4xl">🛠️</span>
            <h2 class="text-white font-bold text-xl mt-4">Actualiza tu contraseña</h2>
            <p class="text-slate-400 text-xs mt-1">Ingresa tu nueva clave de acceso seguro.</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
            
            @if ($errors->any())
                <div class="mb-4 bg-rose-950/40 border border-rose-500/30 text-rose-400 rounded-xl p-3 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <p>⚠️ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                           class="w-full bg-slate-950/60 border border-slate-800 text-slate-500 rounded-xl p-3 text-sm focus:outline-none cursor-not-allowed">
                </div>

                <div>
                    <label for="password" class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Nueva Contraseña</label>
                    <input id="password" type="password" name="password" required autofocus placeholder="Mínimo 8 caracteres"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition duration-200">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Confirmar Contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Repite tu nueva contraseña"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition duration-200">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-black py-3.5 rounded-xl transition duration-200 text-sm uppercase tracking-wide shadow-lg shadow-cyan-500/10">
                    Restablecer Contraseña 🚀
                </button>
            </form>
        </div>
    </div>

</body>
</html>