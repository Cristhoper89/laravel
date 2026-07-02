@auth
    @php
        $inicioUrl = '#';
        $inicioTexto = 'Inicio';
        $inicioIcono = '🏠';

        if (auth()->user()->role === 'admin') {
            $inicioUrl = route('admin.dashboard');
            $inicioTexto = 'Gestionar Productos';
            $inicioIcono = '🍔';
        } elseif (auth()->user()->role === 'cliente') {
            $inicioUrl = route('cliente.dashboard');
            $inicioTexto = 'Menú Digital';
            $inicioIcono = '🛒';
        }
    @endphp

    <body class="main-body font-sans antialiased">

        <div class="app-layout h-screen w-screen flex overflow-hidden">

            <aside class="main-sidebar w-64 p-5 flex flex-col justify-between shrink-0 h-full">

                <div class="flex flex-col flex-1 min-h-0">

                    <div class="sidebar-header flex items-center gap-3 mb-6 shrink-0 overflow-hidden">
                        @if (!empty($empresaGlobal->logo))
                            <img src="{{ \Illuminate\Support\Str::startsWith($empresaGlobal->logo, 'http') ? $empresaGlobal->logo : (\Illuminate\Support\Str::startsWith($empresaGlobal->logo, '/storage') ? $empresaGlobal->logo : asset('storage/' . $empresaGlobal->logo)) }}"
                                class="w-9 h-9 rounded-xl object-cover shadow-md shrink-0 ring-2 ring-primary/10"
                                alt="Logo">
                        @else
                            <div class="w-9 h-9 rounded-xl fallback-logo flex items-center justify-center text-lg shrink-0">
                                🍳
                            </div>
                        @endif

                        <h1 class="sidebar-title text-xl font-black tracking-wide truncate"
                            title="{{ $empresa->name ?? $empresaGlobal->name ?? 'TuComida' }}">
                            {{ $empresa->name ?? $empresaGlobal->name ?? 'TuComida' }}
                        </h1>
                    </div>

                    <div class="profile-card flex items-center gap-3 p-3 rounded-2xl mb-6 shrink-0">
                        @if (auth()->user()->photo)
                            <img src="{{ \Illuminate\Support\Str::startsWith(auth()->user()->photo, 'http') ? auth()->user()->photo : asset('storage/' . auth()->user()->photo) }}"
                                class="w-11 h-11 rounded-full object-cover" alt="Foto de perfil">
                        @else
                            <div class="profile-avatar w-11 h-11 rounded-full flex items-center justify-center font-bold uppercase shrink-0">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </div>
                        @endif

                        <div class="overflow-hidden">
                            <p class="profile-name text-sm font-semibold truncate" title="{{ auth()->user()->name }}">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="profile-role-container text-xs font-medium capitalize mt-0.5">
                                <span class="profile-badge inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px]">
                                    {{ auth()->user()->role }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <nav class="sidebar-nav space-y-2.5 flex-1 overflow-y-auto pr-1 scrollbar-thin">

                        <a href="{{ $inicioUrl }}" class="nav-link flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                            <span>{{ $inicioIcono }}</span> {{ $inicioTexto }}
                        </a>

                        @if (auth()->user()->role === 'cliente')
                            <a href="{{ route('carrito.index') }}" class="nav-link nav-success flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>🛒</span> Mi Carrito
                            </a>
                            <a href="{{ route('cliente.compras') }}" class="nav-link nav-success flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>🧾</span> Mis Compras
                            </a>
                        @endif

                        @if (auth()->user()->role === 'admin')
                            <!-- MENÚ CORREGIDO: Redirección directa al index de caja -->
                            <a href="{{ route('caja.index') }}" class="nav-link flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>🏦</span> Control de Caja
                            </a>

                            <a href="{{ route('admin.ventas.create') }}" class="nav-link nav-success flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>🛒</span> Registrar Venta (Caja)
                            </a>
                            <a href="{{ route('users.index') }}" class="nav-link flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>👥</span> Gestionar usuarios
                            </a>
                            <a href="{{ route('proveedores.index') }}" class="nav-link flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>📦</span> Gestión de proveedores
                            </a>
                            <a href="{{ route('categorias.index') }}" class="nav-link flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>🗂️</span> Gestión de categorías
                            </a>
                            <a href="{{ route('facturas.index') }}" class="nav-link flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>📊</span> Reportes de caja
                            </a>
                            <a href="{{ route('admin.estadisticas') }}" class="nav-link nav-success flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>📊</span> Panel Estadístico
                            </a>
                            <a href="{{ route('admin.configuracion.index') }}" class="nav-link flex items-center gap-3 p-3 rounded-xl transition duration-200 font-medium text-sm">
                                <span>⚙️</span> Configuración
                            </a>
                        @endif

                    </nav>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="logout-form pt-4 shrink-0 mt-4">
                    @csrf
                    <button type="submit" class="logout-btn w-full p-3 rounded-xl transition duration-200 text-sm font-medium flex items-center justify-center gap-2">
                        <span>🚪</span> Cerrar sesión
                    </button>
                </form>
            </aside>

            <main class="main-content flex-1 p-8 overflow-y-auto h-full">
                {{ $slot }}
            </main>

        </div>
    </body>
@endauth