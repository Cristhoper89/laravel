<x-app-layout>
    <div class="his-wrapper min-h-screen py-10" x-data="{ filtro: 'facturas' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="his-card border rounded-3xl p-6 shadow-xl mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div>
                    <h2 class="text-3xl font-black his-main-title tracking-tight">Historial de Ventas y Caja 🧾</h2>
                    <p class="his-subtitle text-sm mt-1">Audita las facturas emitidas, métodos de pago y movimientos automáticos o manuales de caja.</p>
                </div>
                
                <div class="flex flex-wrap gap-4 w-full lg:w-auto">
                    <button @click="filtro = 'facturas'"
                            :class="filtro === 'facturas' ? 'his-filter-active-facturas ring-2' : 'his-filter-inactive'"
                            class="flex-1 sm:flex-initial text-left px-5 py-3 rounded-2xl border transition duration-200 outline-none">
                        <span class="text-xs his-text-success block uppercase font-bold tracking-wider mb-0.5">🛒 Facturas (Ventas)</span>
                        <span class="text-xl font-black his-text-success font-mono">
                            ${{ number_format($facturas->filter(fn($f) => $f->reporte && $f->reporte->status === 'activo')->sum('total'), 2) }}
                        </span>
                    </button>
                    
                    <button @click="filtro = 'gastos'"
                            :class="filtro === 'gastos' ? 'his-filter-active-gastos ring-2' : 'his-filter-inactive'"
                            class="flex-1 sm:flex-initial text-left px-5 py-3 rounded-2xl border transition duration-200 outline-none">
                        <span class="text-xs his-text-warning block uppercase font-bold tracking-wider mb-0.5">🔴 Gastos / Egresos</span>
                        <span class="text-xl font-black his-text-warning font-mono">
                            ${{ number_format($movimientos->where('tipo', 'egreso')->sum('monto'), 2) }}
                        </span>
                    </button>

                    <button @click="filtro = 'ingresos'"
                            :class="filtro === 'ingresos' ? 'his-filter-active-ingresos ring-2' : 'his-filter-inactive'"
                            class="flex-1 sm:flex-initial text-left px-5 py-3 rounded-2xl border transition duration-200 outline-none">
                        <span class="text-xs his-text-accent block uppercase font-bold tracking-wider mb-0.5">🟢 Entradas Extras</span>
                        <span class="text-xl font-black his-text-accent font-mono">
                            ${{ number_format($movimientos->where('tipo', 'ingreso')->sum('monto'), 2) }}
                        </span>
                    </button>
                    
                    <div class="flex-1 sm:flex-initial his-container-anulado border px-5 py-3 rounded-2xl text-left">
                        <span class="text-xs his-text-danger block uppercase font-bold tracking-wider mb-0.5">Anulado ❌</span>
                        <span class="text-xl font-black his-text-danger font-mono">
                            ${{ number_format($facturas->filter(fn($f) => !$f->reporte || $f->reporte->status !== 'activo')->sum('total'), 2) }}
                        </span>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 his-alert-success border rounded-xl p-4 text-sm flex items-center gap-2">
                    <span>✨</span> {{ session('success') }}
                </div>
            @endif

            <div class="his-card border rounded-3xl overflow-hidden shadow-2xl">
                
                <div x-show="filtro === 'facturas'" x-transition>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800/50 bg-slate-950/40 his-subtitle text-xs uppercase tracking-wider font-semibold">
                                    <th class="py-4 px-6">Factura</th>
                                    <th class="py-4 px-6">Cliente / Cajero</th>
                                    <th class="py-4 px-6">Método de Pago</th>
                                    <th class="py-4 px-6">Monto Total</th>
                                    <th class="py-4 px-6">Reporte de Caja</th>
                                    <th class="py-4 px-6 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40 text-sm">
                                @forelse($facturas as $factura)
                                    <tr class="hover:bg-slate-800/20 transition duration-150 group">
                                        <td class="py-4 px-6 font-mono his-text-warning font-bold">
                                            {{ $factura->numero_factura ?? 'FAC-'.$factura->id }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="his-text-white font-medium">{{ $factura->cliente_nombre }}</div>
                                            <div class="his-text-muted text-xs">{{ $factura->created_at->format('d/m/Y h:i A') }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold his-badge border">
                                                {{ $factura->metodo_pago }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 his-text-white font-bold">
                                            ${{ number_format($factura->total, 2) }}
                                        </td>
                                        <td class="py-4 px-6">
                                            @if($factura->reporte)
                                                @if($factura->reporte->status === 'activo')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium his-status-active-bg his-text-success border">
                                                        <span class="h-1.5 w-1.5 rounded-full his-status-active-dot animate-pulse"></span>
                                                        Activo (Entrada)
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium his-status-inactive-bg his-text-danger border">
                                                        <span class="h-1.5 w-1.5 rounded-full his-status-inactive-dot"></span>
                                                        Desactivado
                                                    </span>
                                                @endif
                                            @else
                                                <span class="his-text-muted text-xs italic">Sin movimiento</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('facturas.show', $factura->id) }}" class="p-2 his-btn-action border rounded-xl transition duration-200" title="Ver Comanda Detallada">
                                                    👁️ Ver Detalle
                                                </a>

                                                @if($factura->reporte && $factura->reporte->status === 'activo')
                                                    <form action="/admin/reportes/{{ $factura->reporte->id }}/toggle" method="POST" onsubmit="return confirm('¿Seguro que deseas desactivar este movimiento de caja? Esto anulará el reporte financiero de esta venta.')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="p-2 his-btn-danger border rounded-xl transition duration-200 text-xs font-medium">
                                                            ❌ Desactivar
                                                        </button>
                                                    </form>
                                                @elseif($factura->reporte)
                                                    <form action="/admin/reportes/{{ $factura->reporte->id }}/toggle" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="p-2 his-btn-action border text-slate-500 rounded-xl transition duration-200 text-xs font-medium">
                                                            🔄 Activar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-12 px-6 text-center his-text-muted">
                                            <div class="text-3xl mb-2">📊</div>
                                            No se han registrado facturas de venta todavía.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="filtro === 'gastos'" x-transition class="hidden" :class="{ 'hidden': filtro !== 'gastos' }">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800/50 bg-slate-950/40 his-subtitle text-xs uppercase tracking-wider font-semibold">
                                    <th class="py-4 px-6">ID Mov</th>
                                    <th class="py-4 px-6">Concepto / Clasificación</th>
                                    <th class="py-4 px-6">Descripción Detallada</th>
                                    <th class="py-4 px-6">Vínculo Extra</th>
                                    <th class="py-4 px-6">Monto Retirado</th>
                                    <th class="py-4 px-6 text-center">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40 text-sm his-text-main">
                                @forelse($movimientos->where('tipo', 'egreso') as $mov)
                                    <tr class="hover:bg-slate-800/20 transition duration-150">
                                        <td class="py-4 px-6 font-mono his-text-muted">#MOV-{{ $mov->id }}</td>
                                        <td class="py-4 px-6">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold his-status-warning-bg his-text-warning border uppercase tracking-wider">
                                                {{ str_replace('_', ' ', $mov->concepto) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 his-text-white max-w-xs truncate" title="{{ $mov->descripcion }}">
                                            {{ $mov->descripcion }}
                                        </td>
                                        <td class="py-4 px-6 text-xs his-text-muted">
                                            @if($mov->producto_id && $mov->producto)
                                                <div class="flex flex-col">
                                                    <span class="his-text-main font-medium">📦 Surtido: {{ $mov->producto->name }}</span>
                                                    @if($mov->cantidad_producto) <span class="text-[11px] his-text-muted">Cantidad: +{{ $mov->cantidad_producto }} uds</span> @endif
                                                </div>
                                            @else
                                                <span class="his-text-muted italic">Gasto Directo</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 his-text-warning font-bold font-mono">
                                            -${{ number_format($mov->monto, 2) }}
                                        </td>
                                        <td class="py-4 px-6 text-center his-text-muted text-xs">
                                            {{ $mov->created_at->format('d/m/Y h:i A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-12 px-6 text-center his-text-muted">
                                            <div class="text-3xl mb-2">💸</div>
                                            No hay gastos ni egresos operativos registrados en el sistema.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="filtro === 'ingresos'" x-transition class="hidden" :class="{ 'hidden': filtro !== 'ingresos' }">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800/50 bg-slate-950/40 his-subtitle text-xs uppercase tracking-wider font-semibold">
                                    <th class="py-4 px-6">ID Mov</th>
                                    <th class="py-4 px-6">Concepto / Clasificación</th>
                                    <th class="py-4 px-6">Descripción Detallada</th>
                                    <th class="py-4 px-6">Monto Recibido</th>
                                    <th class="py-4 px-6 text-center">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40 text-sm his-text-main">
                                @forelse($movimientos->where('tipo', 'ingreso') as $mov)
                                    <tr class="hover:bg-slate-800/20 transition duration-150">
                                        <td class="py-4 px-6 font-mono his-text-muted">#MOV-{{ $mov->id }}</td>
                                        <td class="py-4 px-6">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold his-status-accent-bg his-text-accent border uppercase tracking-wider">
                                                {{ str_replace('_', ' ', $mov->concepto) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 his-text-white max-w-sm" title="{{ $mov->descripcion }}">
                                            {{ $mov->descripcion }}
                                        </td>
                                        <td class="py-4 px-6 his-text-accent font-bold font-mono">
                                            +${{ number_format($mov->monto, 2) }}
                                        </td>
                                        <td class="py-4 px-6 text-center his-text-muted text-xs">
                                            {{ $mov->created_at->format('d/m/Y h:i A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-12 px-6 text-center his-text-muted">
                                            <div class="text-3xl mb-2">💰</div>
                                            No se han inyectado flujos de entrada extras a la caja base.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>