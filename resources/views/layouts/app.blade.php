<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (isset($estiloActivo) && !empty($estiloActivo->nombre))
        <link id="tema-visual" rel="stylesheet"
            href="{{ asset('css/' . $estiloActivo->nombre) }}?v={{ time() }}">
    @else
        <link id="tema-visual" rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ time() }}">
    @endif

</head>

<body class="welcome-body min-h-screen relative overflow-x-hidden font-sans antialiased bg-[#090d16] text-gray-200">
    @if (isset($estiloActivo) && ($estiloActivo->nombre == 'Navideño' || $estiloActivo->nombre == 'navidad.css'))
        @include('partials.navidad.decoraciones')
    @elseif(isset($estiloActivo) && ($estiloActivo->nombre == 'Halloween' || $estiloActivo->nombre == 'halloween.css'))
        @include('partials.halloween.decoraciones')
    @endif
    <div class="min-h-screen w-full bg-[#090d16]">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-[#0f172a] shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

    </div>
</body>

</html>
