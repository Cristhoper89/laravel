@php
    // Evaluamos si la caja/sesión asociada a la transacción ya se encuentra cerrada
    // Puedes reemplazar esta variable por la de tu controlador (ej. $cajaCerrada)
    $cajaCerrada = isset($cajaCerrada) ? $cajaCerrada : !$factura->reporte || $factura->reporte->status !== 'activo';
@endphp

<x-app-layout>
    <div class="his-wrapper min-h-screen py-10">
        <div class="max-w-4xl mx-auto px-6">

            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('facturas.index') }}"
                    class="his-text-muted hover:text-amber-400 text-sm font-semibold transition duration-200 no-underline flex items-center gap-2">
                    ← Volver al Historial General
                </a>

                @if ($cajaCerrada)
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        Caja Cerrada (Solo Lectura)
                    </span>
                @endif
            </div>

            {{-- Banner de Alerta cuando la Caja está Cerrada --}}
            @if ($cajaCerrada)
                <div
                    class="mb-6 rounded-2xl bg-amber-500/10 border border-amber-500/20 p-4 text-amber-300 flex items-start gap-3 shadow-lg">
                    <span class="text-xl shrink-0">🔒</span>
                    <div class="text-xs space-y-1">
                        <h5 class="font-bold text-sm text-amber-400">Caja Cerrada - Modo Solo Lectura</h5>
                        <p class="his-text-muted leading-relaxed">
                            Esta transacción pertenece a una caja que ya fue cerrada o se encuentra inactiva.
                            Las funciones de modificación de método de pago y reimpresión de tickets han sido
                            inhabilitadas.
                        </p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Sección Principal: Detalle de Platillos --}}
                <div class="md:col-span-2 space-y-6">
                    <div class="his-card border rounded-3xl p-6 shadow-xl">
                        <h3
                            class="text-xl font-bold his-text-white mb-4 pb-2 border-b border-slate-800/50 flex items-center gap-2">
                            <span>🍳</span> Platillos Solicitados
                        </h3>

                        <div class="divide-y divide-slate-800/40">
                            @foreach ($factura->detalles as $detalle)
                                <div class="py-4 flex justify-between items-center first:pt-0 last:pb-0">
                                    <div>
                                        <h4 class="his-text-white font-bold text-base">
                                            {{ $detalle->producto->name ?? 'Platillo Eliminado' }}
                                        </h4>
                                        <p class="his-text-muted text-xs">
                                            {{ $detalle->cantidad }} unidad(es) x
                                            ${{ number_format($detalle->precio_unitario, 2) }}
                                        </p>
                                    </div>
                                    <span class="his-text-warning font-mono font-bold">
                                        ${{ number_format($detalle->total_linea, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Lateral: Totales y Acciones --}}
                {{-- Lateral: Totales y Acciones --}}
                <div class="space-y-6">

                    {{-- Tarjeta de Totales + Ticket --}}
                    <div class="his-card border rounded-3xl p-6 shadow-xl flex flex-col justify-between">
                        <div>
                            <h4 class="text-sm font-bold uppercase tracking-wider his-text-muted mb-4">Totales</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between his-text-muted">
                                    <span>Subtotal:</span>
                                    <span
                                        class="his-text-white font-medium">${{ number_format($factura->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between his-text-muted">
                                    <span>IVA (19%):</span>
                                    <span
                                        class="his-text-white font-medium">${{ number_format($factura->impuesto, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-base pt-3 border-t border-slate-800/50 mb-4">
                                    <span class="his-text-white font-bold">Total:</span>
                                    <span
                                        class="text-xl font-black his-text-warning">${{ number_format($factura->total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Generación/Reimpresión de Ticket siempre disponible --}}
                        <div class="pt-4 border-t border-slate-800/40">
                            <a href="/admin/facturas/{{ $factura->id }}/imprimir" target="_blank"
                                class="w-full bg-cyan-950 hover:bg-cyan-400 border border-cyan-800/50 text-cyan-400 hover:text-slate-950 text-xs font-bold py-3 rounded-xl transition duration-200 uppercase tracking-wider flex items-center justify-center gap-2 text-center no-underline">
                                <span>🖨️</span> Generar Factura / Ticket
                            </a>
                        </div>
                    </div>

                    {{-- Bloque de Método de Pago --}}
<div class="his-card border rounded-3xl p-6 shadow-xl">
    <h4 class="text-sm font-bold uppercase tracking-wider his-text-muted mb-3">Método de Pago</h4>

    @php
        // Es deshabilitado si la caja está cerrada O si el usuario es cajero2
        $esSoloLectura = $cajaCerrada || auth()->user()->role === 'cajero2';
    @endphp

    <form action="/admin/facturas/{{ $factura->id }}/update-pago" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block his-text-muted mb-1 text-xs">
                {{ $esSoloLectura ? 'Método Registrado (Solo Lectura)' : 'Método de Pago' }}
            </label>
            
            <select name="metodo_pago" 
                @disabled($esSoloLectura)
                class="w-full his-badge border rounded-xl p-2.5 his-text-white text-xs focus:outline-none focus:border-cyan-500 transition duration-200 @if ($esSoloLectura) opacity-60 cursor-not-allowed bg-slate-900/50 @endif">
                
                <option value="Efectivo" {{ $factura->metodo_pago == 'Efectivo' ? 'selected' : '' }}>
                    Efectivo 💵
                </option>
                <option value="Tarjeta" {{ $factura->metodo_pago == 'Tarjeta' ? 'selected' : '' }}>
                    Tarjeta 💳
                </option>
                <option value="Transferencia" {{ $factura->metodo_pago == 'Transferencia' ? 'selected' : '' }}>
                    Transferencia 🏦
                </option>
                <option value="Plataforma" {{ $factura->metodo_pago == 'Plataforma' ? 'selected' : '' }}>
                    Plataforma 🏦
                </option>
            </select>
        </div>

        {{-- Alerta informativa solo si es un cajero2 observando la comanda --}}
        @if (auth()->user()->role === 'cajero2')
            <div class="his-status-inactive-bg border border-red-500/20 text-red-400 text-xs font-medium p-2.5 rounded-xl flex items-center gap-2">
                <span>🔒</span> Sin permisos para modificar pagos.
            </div>
        @endif

        {{-- El botón de guardar solo aparece si NO es solo lectura --}}
        @if (!$esSoloLectura)
            <button type="submit"
                class="w-full his-btn-action border text-xs font-bold py-2.5 rounded-xl transition duration-200 uppercase tracking-wider">
                Guardar Cambios 💾
            </button>
        @endif
    </form>
</div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
