<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Cuenta | La Cabaña 🍳</title>
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
            <span class="text-4xl">📩</span>
            <h2 class="text-white font-bold text-xl mt-4">¡Ya casi estamos listos!</h2>
            <p class="text-slate-400 text-xs mt-3 max-w-sm mx-auto leading-relaxed">
                Gracias por registrarte en TuTienda. Antes de comenzar a ordenar, ¿podrías verificar tu cuenta haciendo clic en el enlace que te acabamos de enviar a tu correo electrónico? Si no te llegó, podemos enviarte otro.
            </p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
            
            @if (session('status') == 'verification-link-sent')
                <div class="mb-5 bg-emerald-950/40 border border-emerald-500/30 text-emerald-400 rounded-xl p-3 text-xs font-medium">
                    ✨ Un nuevo enlace de verificación ha sido enviado a la dirección de correo proporcionada durante el registro.
                </div>
            @endif

            <div class="flex flex-col gap-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-slate-950 font-black py-3.5 rounded-xl transition duration-200 text-sm uppercase tracking-wide">
                        Reenviar Correo de Verificación 🔄
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-slate-950 border border-slate-800 hover:bg-slate-850 text-slate-400 hover:text-white py-3 rounded-xl transition duration-200 text-xs font-bold uppercase tracking-wider">
                        Cerrar Sesión 🚪
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>