<x-app-layout>
    <div class="admin-wrapper min-h-screen py-10" x-data="{ openModal: false, cargando: false, cajaId: null, movimientos: [], facturas: [] }">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header principal del Historial -->
            <div
                class="admin-card border rounded-3xl p-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="admin-title text-4xl font-bold mb-2"><span>Historial de Cajas</span> 📊</h2>
                    <p class="admin-subtitle">Consulta el registro histórico de aperturas, cierres, movimientos y
                        facturas de cada turno.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('caja.index') }}"
                        class="admin-btn-secondary font-bold px-6 py-3 rounded-2xl border transition duration-300 flex items-center gap-2">
                        ⬅️ Volver a Turno Actual
                    </a>
                </div>
            </div>

            <!-- Tabla de Historial -->
            <div class="admin-card border rounded-3xl mt-8 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="admin-table w-full text-left border-collapse">
                        <thead>
                            <tr class="admin-table-th-row border-b text-sm font-semibold uppercase">
                                <th class="p-6">ID Turno</th>
                                <th class="p-6">Responsable</th>
                                <th class="p-6">Apertura</th>
                                <th class="p-6">Cierre</th>
                                <th class="p-6">Monto Apertura</th>
                                <th class="p-6">Monto Cierre</th>
                                <th class="p-6 text-center">Diferencia</th>
                                <th class="p-6 text-center">Estado</th>
                                <th class="p-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($cajas as $caja)
                                @php
                                    $diferencia =
                                        $caja->monto_cierre !== null
                                            ? $caja->monto_cierre - $caja->monto_apertura
                                            : null;
                                @endphp
                                <tr class="admin-table-row border-b border-gray-800 transition duration-200">
                                    <!-- ID -->
                                    <td class="p-6 font-bold text-white">
                                        #{{ str_pad($caja->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>

                                    <!-- Responsable / Usuario -->
                                    <td class="p-6 font-medium text-gray-200">
                                        👤 {{ $caja->user?->name ?? 'Usuario #' . $caja->user_id }}
                                    </td>

                                    <!-- Fecha Apertura -->
                                    <td class="p-6 text-sm">
                                        <span
                                            class="block text-white font-medium">{{ $caja->created_at->format('d/m/Y') }}</span>
                                        <span
                                            class="text-xs text-gray-400">{{ $caja->created_at->format('h:i A') }}</span>
                                    </td>

                                    <!-- Fecha Cierre -->
                                    <td class="p-6 text-sm">
                                        @if ($caja->fecha_cierre)
                                            <span
                                                class="block text-white font-medium">{{ \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y') }}</span>
                                            <span
                                                class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($caja->fecha_cierre)->format('h:i A') }}</span>
                                        @else
                                            <span class="text-xs text-yellow-500 font-semibold">En Proceso...</span>
                                        @endif
                                    </td>

                                    <!-- Monto Apertura -->
                                    <td class="p-6 font-semibold text-gray-300">
                                        ${{ number_format($caja->monto_apertura, 2) }}
                                    </td>

                                    <!-- Monto Cierre -->
                                    <td class="p-6 font-semibold">
                                        @if ($caja->monto_cierre !== null)
                                            <span
                                                class="text-green-400">${{ number_format($caja->monto_cierre, 2) }}</span>
                                        @else
                                            <span class="text-gray-500">—</span>
                                        @endif
                                    </td>

                                    <!-- Diferencia -->
                                    <td class="p-6 text-center">
                                        @if ($diferencia !== null)
                                            <span
                                                class="px-3 py-1 text-xs font-bold rounded-full {{ $diferencia >= 0 ? 'bg-green-500/10 text-green-400 border border-green-500/30' : 'bg-red-500/10 text-red-400 border border-red-500/30' }}">
                                                {{ $diferencia >= 0 ? '+' : '' }}${{ number_format($diferencia, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-500">—</span>
                                        @endif
                                    </td>

                                    <!-- Estado -->
                                    <td class="p-6 text-center align-middle">
                                        <div class="inline-flex items-center justify-center">
                                            @if ($caja->estado === 'abierta')
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30 shadow-sm animate-pulse">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                    Abierta
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-800/80 text-slate-400 border border-slate-700/80">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                                    Cerrada
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Botón Detalle -->
                                    <td class="p-6 text-center">
                                        <button
                                            @click="
                                                    cajaId = '{{ str_pad($caja->id, 5, '0', STR_PAD_LEFT) }}';
                                                    cargando = true;
                                                    openModal = true;
                                                    fetch('{{ route('caja.movimientos', $caja->id) }}')
                                                        .then(res => res.json())
                                                        .then(data => {
                                                            movimientos = data.movimientos || [];
                                                            facturas = data.facturas || [];
                                                            cargando = false;
                                                        });
                                                "
                                            class="admin-btn-secondary px-3 py-2 rounded-xl border text-xs font-bold hover:border-red-500 transition duration-200">
                                            🔍 Detalle
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-12 text-center text-gray-400">
                                        No hay registros de sesiones de caja en el historial.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if ($cajas->hasPages())
                    <div class="p-6 border-t border-gray-800">
                        {{ $cajas->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- MODAL DETALLE DE MOVIMIENTOS Y FACTURAS -->
        <div x-show="openModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="admin-card border rounded-3xl max-w-4xl w-full p-6 shadow-2xl relative max-h-[85vh] flex flex-col"
                @click.away="openModal = false">

                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-4 border-b border-gray-800 shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <span>📊</span> Detalle de Turno <span class="text-red-500 font-mono"
                                x-text="'#' + cajaId"></span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Auditoría de ingresos, egresos y ventas generadas
                            durante la caja.</p>
                    </div>
                    <button @click="openModal = false"
                        class="text-gray-400 hover:text-white p-2 rounded-xl transition">✕</button>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto flex-1 my-4 space-y-6 pr-1">
                    <!-- State: Loader -->
                    <template x-if="cargando">
                        <div class="py-12 text-center text-gray-400">
                            <div class="text-2xl mb-2 animate-spin">⏳</div>
                            Cargando auditoría del turno...
                        </div>
                    </template>

                    <!-- State: Loaded Content -->
                    <template x-if="!cargando">
                        <div class="space-y-6">

                            <!-- SECCIÓN: Movimientos Manuales de Caja -->
                            <div>
                                <h4
                                    class="text-sm font-bold text-gray-300 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    💸 Movimientos de Caja (Entradas / Salidas)
                                </h4>
                                <template x-if="movimientos.length === 0">
                                    <p
                                        class="text-xs text-gray-500 bg-gray-900/50 p-4 rounded-xl border border-gray-800">
                                        No se registraron entradas ni salidas manuales durante este turno.</p>
                                </template>
                                <template x-if="movimientos.length > 0">
                                    <div class="overflow-hidden border border-gray-800 rounded-xl">
                                        <table class="w-full text-left text-xs border-collapse">
                                            <thead class="bg-gray-900/80 text-gray-400 uppercase font-semibold">
                                                <tr class="border-b border-gray-800">
                                                    <th class="p-3">Tipo</th>
                                                    <th class="p-3">Concepto</th>
                                                    <th class="p-3">Descripción</th>
                                                    <th class="p-3 text-right">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-800 text-gray-300">
                                                <template x-for="mov in movimientos" :key="mov.id">
                                                    <tr>
                                                        <td class="p-3 font-bold">
                                                            <span x-show="mov.tipo === 'ingreso'"
                                                                class="text-green-400">🟢 INGRESO</span>
                                                            <span x-show="mov.tipo === 'egreso'" class="text-red-400">🔴
                                                                EGRESO</span>
                                                        </td>
                                                        <td class="p-3 capitalize"
                                                            x-text="mov.concepto ? mov.concepto.replace('_', ' ') : 'General'">
                                                        </td>
                                                        <td class="p-3 text-gray-400" x-text="mov.descripcion || '—'">
                                                        </td>
                                                        <td class="p-3 text-right font-mono font-bold"
                                                            :class="mov.tipo === 'ingreso' ? 'text-green-400' : 'text-red-400'">
                                                            <span
                                                                x-text="mov.tipo === 'ingreso' ? '+' : '-'"></span>$<span
                                                                x-text="Number(mov.monto).toFixed(2)"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>

                            <!-- SECCIÓN: Facturas Generadas -->
                            <div>
                                <h4
                                    class="text-sm font-bold text-gray-300 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    🧾 Ventas / Facturas Emitidas
                                </h4>
                                <template x-if="facturas.length === 0">
                                    <p
                                        class="text-xs text-gray-500 bg-gray-900/50 p-4 rounded-xl border border-gray-800">
                                        No hay ventas vinculadas registradas en el lapso de este turno.</p>
                                </template>
                                <template x-if="facturas.length > 0">
                                    <div class="overflow-hidden border border-gray-800 rounded-xl">
                                        <table class="w-full text-left text-xs border-collapse">
                                            <thead class="bg-gray-900/80 text-gray-400 uppercase font-semibold">
                                                <tr class="border-b border-gray-800">
                                                    <th class="p-3">N° Factura</th>
                                                    <th class="p-3">Cliente</th>
                                                    <th class="p-3">Método de Pago</th>
                                                    <th class="p-3 text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-800 text-gray-300">
                                                <template x-for="fac in facturas" :key="fac.id">
                                                    <tr>
                                                        <td class="p-3 font-mono font-bold text-white"
                                                            x-text="fac.numero_factura || ('#' + fac.id)"></td>
                                                        <td class="p-3"
                                                            x-text="fac.cliente_nombre || 'Cliente General'"></td>
                                                        <td class="p-3 capitalize">
                                                            <span
                                                                class="px-2 py-0.5 rounded-md border border-gray-700 bg-gray-800 text-gray-300"
                                                                x-text="fac.metodo_pago"></span>
                                                        </td>
                                                        <td class="p-3 text-right font-mono font-bold text-green-400">
                                                            $<span x-text="Number(fac.total).toFixed(2)"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="pt-4 border-t border-gray-800 flex justify-end shrink-0">
                    <button @click="openModal = false"
                        class="admin-btn-secondary px-5 py-2.5 rounded-xl border text-xs font-bold">
                        Cerrar Audit
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
