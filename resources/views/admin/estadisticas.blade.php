<x-app-layout>
    <div class="min-h-screen db-wrapper py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div
                class="db-card rounded-3xl p-6 shadow-xl mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-black db-text-white tracking-tight">Panel Estadístico "La Cabaña" 📊</h2>
                    <p class="db-text-muted text-sm mt-1">Monitoreo financiero en tiempo real, control de inventario y
                        actividad de clientes.</p>
                </div>
                <div class="text-sm db-text-muted db-card-inner px-4 py-2 rounded-2xl font-mono">
                    Última actualización: {{ now()->format('d/m/Y h:i A') }}
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <div
                    class="db-card p-6 rounded-3xl shadow-md relative overflow-hidden group flex flex-col justify-between">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-10 text-4xl group-hover:scale-110 transition duration-300">
                        💰</div>
                    <div>
                        <span class="text-xs db-text-muted uppercase font-bold tracking-wider block">Ingresos Reales
                            (Caja)</span>
                        <p class="text-3xl font-black db-color-success mt-2">${{ number_format($totalIngresos, 2) }}</p>
                    </div>
                    <div class="mt-4 pt-3 border-t db-border-split space-y-1">
                        <div class="flex justify-between text-[11px]">
                            <span class="db-text-muted">🛒 Por Ventas:</span>
                            <span
                                class="font-mono db-color-success font-bold">${{ number_format($ingresosVentas, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="db-text-muted">✨ Otros Ingresos:</span>
                            <span
                                class="font-mono db-color-primary font-bold">${{ number_format($ingresosExtras, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="db-card p-6 rounded-3xl shadow-md relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-10 text-4xl group-hover:scale-110 transition duration-300">
                        🍳</div>
                    <span class="text-xs db-text-muted uppercase font-bold tracking-wider block">Platillos en
                        Menú</span>
                    <p class="text-3xl font-black db-text-white mt-2">{{ $totalPlatillos }}</p>
                    @if ($platillosCriticos > 0)
                        <span class="text-xs db-color-danger mt-2 block font-bold animate-pulse">⚠️
                            {{ $platillosCriticos }} por agotarse en cocina</span>
                    @else
                        <span class="text-xs db-text-muted mt-2 block font-medium">✅ Inventario estable</span>
                    @endif
                </div>

                <div class="db-card p-6 rounded-3xl shadow-md relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-10 text-4xl group-hover:scale-110 transition duration-300">
                        📉</div>
                    <span class="text-xs db-text-muted uppercase font-bold tracking-wider block">Egresos / Gastos</span>
                    <p class="text-3xl font-black db-color-danger mt-2">${{ number_format($totalEgresos, 2) }}</p>
                    <span class="text-xs db-text-muted mt-2 block font-medium">Sincronizado con movimientos</span>
                </div>

                <div class="db-card p-6 rounded-3xl shadow-md relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-10 text-4xl group-hover:scale-110 transition duration-300">
                        👥</div>
                    <span class="text-xs db-text-muted uppercase font-bold tracking-wider block">Comensales
                        Registrados</span>
                    <p class="text-3xl font-black db-color-primary mt-2">{{ $totalClientes }}</p>
                    <span class="text-xs db-text-muted mt-2 block font-medium">Clientes con cuenta digital</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                <div class="db-card p-6 rounded-3xl shadow-xl flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold db-text-white mb-1">Métodos de Pago</h3>
                        <p class="text-xs db-text-muted mb-6">Canales preferidos por los comensales.</p>
                    </div>
                    <div class="relative w-full h-64 flex items-center justify-center">
                        <canvas id="paymentsChart"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-2 db-card p-6 rounded-3xl shadow-xl flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold db-text-white mb-1">🔥 Los más vendidos de la cocina</h3>
                        <p class="text-xs db-text-muted mb-4">Platillos y bebidas con mayor demanda acumulada en órdenes
                            válidas.</p>
                    </div>

                    <div class="space-y-4 flex-1 flex flex-col justify-center">
                        @forelse($platosMasVendidos as $index => $plato)
                            <div
                                class="flex items-center justify-between p-3 db-card-inner rounded-2xl db-row-hover transition">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="w-7 h-7 flex items-center justify-center rounded-xl text-xs font-black border 
                                        {{ $index === 0 ? 'db-rank-top1' : '' }}
                                        {{ $index === 1 ? 'db-rank-top2' : '' }}
                                        {{ $index === 2 ? 'db-rank-top3' : '' }}">
                                        {{ $index + 1 }}
                                    </span>

                                    <div class="w-12 h-12 rounded-xl db-card-inner overflow-hidden shrink-0">
                                        @if ($plato->image)
                                            <img src="{{ \Illuminate\Support\Str::startsWith($plato->image, 'http') ? $plato->image : asset('storage/' . $plato->image) }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xl">🍔</div>
                                        @endif
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-bold db-text-white">{{ $plato->name }}</h4>
                                        <p class="text-xs db-text-muted font-mono">
                                            ${{ number_format($plato->price, 2) }}</p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <span class="text-xs font-bold px-3 py-1 db-rank-top1 rounded-xl font-mono">
                                        {{ $plato->total_unidades }} vendidos
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center db-text-muted italic text-sm py-8">
                                Aún no se registran platos vendidos mediante la caja directa.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="db-card rounded-3xl overflow-hidden shadow-xl mb-8">
                <div class="p-6 pb-2">
                    <h3 class="text-lg font-bold db-text-white">Últimas Transacciones en Tiempo Real</h3>
                    <p class="text-xs db-text-muted mt-0.5">Auditoría rápida de las 5 comandas más recientes procesadas.
                    </p>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="db-table-header text-xs uppercase tracking-wider font-semibold db-text-muted">
                                <th class="py-3 px-6">Factura</th>
                                <th class="py-3 px-6">Cliente</th>
                                <th class="py-3 px-6">Método</th>
                                <th class="py-3 px-6">Monto Total</th>
                                <th class="py-3 px-6">Estado Caja</th>
                            </tr>
                        </thead>
                        <tbody class="db-text-muted text-sm">
                            @forelse($ultimasFacturas as $factura)
                                <tr class="db-row-hover db-table-row transition duration-150">
                                    <td class="py-3 px-6 font-mono db-color-primary font-bold">
                                        {{ $factura->numero_factura ?? 'FAC-' . $factura->id }}
                                    </td>
                                    <td class="py-3 px-6">
                                        <span
                                            class="db-text-white block font-medium">{{ $factura->cliente_nombre ?? 'Cliente de Paso' }}</span>
                                        <span
                                            class="db-text-muted text-xs">{{ $factura->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="text-xs db-card-inner px-2 py-1 rounded-lg db-text-white">
                                            {{ $factura->metodo_pago }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 db-text-white font-bold">
                                        ${{ number_format($factura->total, 2) }}
                                    </td>
                                    <td class="py-3 px-6">
                                        @if ($factura->reporte && $factura->reporte->status === 'activo')
                                            <span
                                                class="inline-flex items-center gap-1 text-xs db-badge-active px-2.5 py-0.5 rounded-full">
                                                <span
                                                    class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                Activo
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 text-xs db-badge-canceled px-2.5 py-0.5 rounded-full">
                                                Anulado
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center db-text-muted italic">No hay
                                        transacciones registradas hoy.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 db-card-inner border-t db-border-split text-right">
                    <a href="/admin/facturas" class="text-xs db-link-action font-bold transition">Ver todo el historial
                        de ventas →</a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('paymentsChart').getContext('2d');

            const labelsArray = @json($chartLabels);
            const dataArray = @json($chartData);

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labelsArray,
                    datasets: [{
                        data: dataArray,
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.6)', /* Emerald */
                            'rgba(245, 158, 11, 0.6)', /* Amber */
                            'rgba(34, 211, 238, 0.6)', /* Cyan */
                            'rgba(168, 85, 247, 0.6)' /* Purple */
                        ],
                        borderColor: '#0f172a',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                font: {
                                    family: 'Arial',
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
