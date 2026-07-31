<x-app-layout>
    <div class="min-h-screen cfg-wrapper py-10">
        <div class="max-w-5xl mx-auto px-6">

            <div class="mb-8">
                <h2 class="text-3xl font-black text-white tracking-tight">⚙️ Configuración del Sistema</h2>
                <p class="text-slate-400 text-sm mt-1">Gestiona los datos de facturación de la empresa y la apariencia de
                    la plataforma.</p>
            </div>

            @if (session('success'))
                <div
                    class="mb-6 p-4 cfg-alert-success border rounded-2xl text-sm font-medium flex items-center gap-2 shadow-lg">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-6">
                    <div class="cfg-card border rounded-3xl p-6 shadow-xl">
                        <h3
                            class="text-xl font-bold text-white mb-6 pb-2 border-b border-slate-800 flex items-center gap-2">
                            <span>🏢</span> Datos de la Empresa / Restaurante
                        </h3>

                        <form action="{{ route('admin.configuracion.empresa') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-slate-400 mb-1.5 text-xs font-semibold uppercase tracking-wider">Nombre
                                        del Negocio</label>
                                    <input type="text" name="name" value="{{ old('name', $empresa->name ?? '') }}"
                                        class="w-full cfg-input border rounded-xl px-4 py-3 text-sm focus:outline-none transition duration-200">
                                </div>

                                <div>
                                    <label
                                        class="block text-slate-400 mb-1.5 text-xs font-semibold uppercase tracking-wider">NIT
                                        / Identificación Fiscal</label>
                                    <input type="text" name="NIT" value="{{ old('NIT', $empresa->NIT ?? '') }}"
                                        class="w-full cfg-input border rounded-xl px-4 py-3 text-sm focus:outline-none transition duration-200">
                                </div>
                            </div>

                            <div
                                class="p-4 rounded-2xl border grid grid-cols-1 md:grid-cols-3 gap-4 items-center cfg-inner-box">
                                <div class="flex flex-col items-center justify-center p-2 border-r border-slate-800/60">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 mb-2">Logo Actual</span>
                                    @if (!empty($empresa->logo))
                                        <img src="{{ \Illuminate\Support\Str::startsWith($empresa->logo, 'http') ? $empresa->logo : (\Illuminate\Support\Str::startsWith($empresa->logo, '/storage') ? $empresa->logo : asset('storage/' . $empresa->logo)) }}"
                                            class="w-16 h-16 rounded-xl object-cover border border-slate-700 shadow-md">
                                    @else
                                        <div
                                            class="w-16 h-16 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-slate-600">
                                            🍽️
                                        </div>
                                    @endif
                                </div>

                                <div class="md:col-span-2 space-y-3">
                                    <div>
                                        <label
                                            class="block text-slate-400 mb-1 text-[11px] font-semibold uppercase">Opción
                                            A: Subir imagen local (PC)</label>
                                        <input type="file" name="logo_file" accept="image/*"
                                            class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-slate-400 mb-1 text-[11px] font-semibold uppercase">Opción
                                            B: Pegar Enlace / URL web</label>
                                        <input type="text" name="logo_url"
                                            placeholder="https://ejemplo.com/mi-logo.png"
                                            value="{{ old('logo_url', isset($empresa->logo) && \Illuminate\Support\Str::startsWith($empresa->logo, 'http') ? $empresa->logo : '') }}"
                                            class="w-full cfg-input border rounded-xl px-3 py-2 text-xs focus:outline-none transition duration-200">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-slate-400 mb-1.5 text-xs font-semibold uppercase tracking-wider">Dirección
                                        Física</label>
                                    <input type="text" name="address"
                                        value="{{ old('address', $empresa->address ?? '') }}"
                                        class="w-full cfg-input border rounded-xl px-4 py-3 text-sm focus:outline-none transition duration-200">
                                </div>

                                <div>
                                    <label
                                        class="block text-slate-400 mb-1.5 text-xs font-semibold uppercase tracking-wider">Ciudad</label>
                                    <input type="text" name="city" value="{{ old('city', $empresa->city ?? '') }}"
                                        class="w-full cfg-input border rounded-xl px-4 py-3 text-sm focus:outline-none transition duration-200">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label
                                        class="block text-slate-400 mb-1.5 text-xs font-semibold uppercase tracking-wider">Teléfono
                                        / Contacto</label>
                                    <input type="text" name="contact"
                                        value="{{ old('contact', $empresa->contact ?? '') }}"
                                        class="w-full cfg-input border rounded-xl px-4 py-3 text-sm focus:outline-none transition duration-200">
                                </div>

                                <div>
                                    <label
                                        class="block text-slate-400 mb-1.5 text-xs font-semibold uppercase tracking-wider">Correo
                                        Electrónico</label>
                                    <input type="email" name="email"
                                        value="{{ old('email', $empresa->email ?? '') }}"
                                        class="w-full cfg-input border rounded-xl px-4 py-3 text-sm focus:outline-none transition duration-200">
                                </div>

                                {{-- Campo IVA agregado --}}
                                <div>
                                    <label
                                        class="block text-slate-400 mb-1.5 text-xs font-semibold uppercase tracking-wider">IVA
                                        (%)</label>
                                    <input type="number" step="0.01" min="0" name="iva"
                                        value="{{ old('iva', $empresa->IVA ?? '0') }}" placeholder="Ej: 19.00"
                                        class="w-full cfg-input border rounded-xl px-4 py-3 text-sm focus:outline-none transition duration-200">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-800/60 flex justify-end">
                                <button type="submit"
                                    class="cfg-btn-submit font-bold px-6 py-3 rounded-xl transition duration-200 uppercase tracking-wider text-xs shadow-lg">
                                    Guardar Configuración 💾
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="cfg-card border rounded-3xl p-6 shadow-xl">
                        <h3
                            class="text-xl font-bold text-white mb-4 pb-2 border-b border-slate-800 flex items-center gap-2">
                            <span>🎨</span> Tema Visual
                        </h3>
                        <p class="text-slate-500 text-xs mb-4">Elige la paleta estética activa para toda la interfaz.
                        </p>

                        <form action="{{ route('admin.configuracion.estilo') }}" method="POST" class="space-y-3">
                            @csrf

                            @foreach ($estilos as $estilo)
                                <label class="block cursor-pointer cfg-theme-label">
                                    <input type="radio" name="estilo_id" value="{{ $estilo->id }}"
                                        {{ $estilo->estado == 1 ? 'checked' : '' }} class="hidden cfg-radio-input">

                                    <div
                                        class="w-full p-4 rounded-2xl text-left flex items-center justify-between cfg-theme-card">
                                        <div class="flex items-center gap-3">
                                            @if ($estilo->nombre == 'Navideño' || $estilo->nombre == 'navidad.css')
                                                <span class="text-xl">🎄</span>
                                            @elseif($estilo->nombre == 'Halloween' || $estilo->nombre == 'halloween.css')
                                                <span class="text-xl">🎃</span>
                                            @else
                                                <span class="text-xl">🌌</span>
                                            @endif

                                            <div>
                                                <h4
                                                    class="text-sm font-bold text-white cfg-theme-title transition-colors">
                                                    {{ $estilo->nombre }}
                                                </h4>
                                                <p class="text-[11px] text-slate-500">
                                                    {{ $estilo->estado == 1 ? 'Activo actualmente' : 'Haga clic para activar' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            class="w-4 h-4 rounded-full flex items-center justify-center cfg-dot-outer">
                                            <div class="w-2 h-2 rounded-full cfg-dot-inner"></div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach

                            <button type="submit"
                                class="w-full mt-4 cfg-btn-secondary text-xs font-bold py-3 rounded-xl transition duration-200 uppercase tracking-wider shadow-lg">
                                Aplicar Tema 🎭
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
