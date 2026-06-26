<x-app-layout>
    <div class="min-h-screen bg-slate-950 py-10">
        <div class="max-w-4xl mx-auto px-6">

            <div class="mb-6">
                <a href="{{ route('facturas.index') }}"
                    class="text-slate-400 hover:text-amber-400 text-sm font-semibold transition duration-200 no-underline flex items-center gap-2">
                    ← Volver al Historial General
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div class="md:col-span-2 space-y-6">
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
                        <h3
                            class="text-xl font-bold text-white mb-4 pb-2 border-b border-slate-800 flex items-center gap-2">
                            <span>🍳</span> Platillos Solicitados
                        </h3>

                        <div class="divide-y divide-slate-800/60">
                            @foreach ($factura->detalles as $detalle)
                                <div class="py-4 flex justify-between items-center first:pt-0 last:pb-0">
                                    <div>
                                        <h4 class="text-white font-bold text-base">
                                            {{ $detalle->producto->name ?? 'Platillo Eliminado' }}</h4>
                                        <p class="text-slate-500 text-xs">
                                            {{ $detalle->cantidad }} unidad(es) x
                                            ${{ number_format($detalle->precio_unitario, 2) }}
                                        </p>
                                    </div>
                                    <span class="text-amber-400 font-mono font-bold">
                                        ${{ number_format($detalle->total_linea, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div
                        class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between">
                        <div>
                            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Totales</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between text-slate-500">
                                    <span>Subtotal:</span>
                                    <span
                                        class="text-white font-medium">${{ number_format($factura->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-slate-500">
                                    <span>IVA (19%):</span>
                                    <span
                                        class="text-white font-medium">${{ number_format($factura->impuesto, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-base pt-3 border-t border-slate-800 mb-4">
                                    <span class="text-slate-300 font-bold">Total:</span>
                                    <span
                                        class="text-xl font-black text-amber-400">${{ number_format($factura->total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-800/60">
                            @if ($factura->reporte && $factura->reporte->status === 'activo')
                                <a href="/admin/facturas/{{ $factura->id }}/imprimir" target="_blank"
                                    class="w-full bg-cyan-950 hover:bg-cyan-400 border border-cyan-800/80 text-cyan-400 hover:text-slate-950 text-xs font-bold py-3 rounded-xl transition duration-200 uppercase tracking-wider flex items-center justify-center gap-2 text-center no-underline">
                                    <span>🖨️</span> Generar Factura / Ticket
                                </a>
                            @else
                                <div
                                    class="w-full bg-rose-950/40 border border-rose-900/60 text-rose-400 text-xs font-medium p-3 rounded-xl flex items-center justify-center gap-2 text-center select-none">
                                    <span>🚫</span> Transacción Anulada - Impresión Desactivada
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3">Editar Transacción
                        </h4>

                        <form action="/admin/facturas/{{ $factura->id }}/update-pago" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-slate-500 mb-1 text-xs">Método de Pago Efectivo</label>
                                <select name="metodo_pago"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-slate-300 text-xs focus:outline-none focus:border-amber-500 transition duration-200">
                                    <option value="Efectivo"
                                        {{ $factura->metodo_pago == 'Efectivo' ? 'selected' : '' }}>Efectivo 💵
                                    </option>
                                    <option value="Tarjeta" {{ $factura->metodo_pago == 'Tarjeta' ? 'selected' : '' }}>
                                        Tarjeta 💳</option>
                                    <option value="Transferencia"
                                        {{ $factura->metodo_pago == 'Transferencia' ? 'selected' : '' }}>Transferencia
                                        🏦</option>
                                </select>
                            </div>

                            <button type="submit"
                                class="w-full bg-slate-950 hover:bg-amber-500 border border-slate-800 text-amber-400 hover:text-slate-950 text-xs font-bold py-2.5 rounded-xl transition duration-200 uppercase tracking-wider">
                                Guardar Cambios 💾
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
