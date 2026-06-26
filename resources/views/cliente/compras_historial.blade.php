<x-app-layout>
    <div class="min-h-screen welcome-body relative overflow-x-hidden py-10">
        <div class="max-w-4xl mx-auto px-6">

            <div class="feature-card border rounded-3xl p-8 shadow-xl mb-8 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-black text-white mb-2">Mis Compras 🧾</h2>
                    <p class="text-slate-400 text-sm">Revisa el historial de tus pedidos, facturas y antojos guardados.</p>
                </div>
                <a href="/cliente/dashboard" class="db-link-action font-semibold text-sm transition duration-200 no-underline flex items-center gap-1">
                    ← Ir al Menú
                </a>
            </div>

            @if($facturas->count() > 0)
                <div class="space-y-4">
                    @foreach($facturas as $factura)
                        <div class="feature-card border rounded-2xl shadow-md overflow-hidden transition duration-200">
                            
                            <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 cursor-pointer select-none" 
                                 onclick="toggleFactura('{{ $factura->id }}')">
                                
                                <div class="space-y-1">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-mono bg-black/40 border border-white/10 text-white px-2.5 py-1 rounded-md font-bold">
                                            {{ $factura->numero_factura }}
                                        </span>
                                        <span class="text-xs text-slate-500 font-medium">
                                            📅 {{ $factura->created_at->format('d/m/Y h:i A') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400">
                                        Método de pago: <span class="text-slate-300 font-medium">{{ $factura->metodo_pago }}</span>
                                    </p>
                                </div>

                                <div class="flex items-center gap-4 self-end sm:self-auto">
                                    <div class="text-right">
                                        <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Pagado</p>
                                        <p class="text-xl font-black font-mono" style="color: var(--color-success);">
                                            ${{ number_format($factura->total, 2) }}
                                        </p>
                                    </div>
                                    <span id="flecha-{{ $factura->id }}" class="text-slate-500 text-sm transition-transform duration-200">▼</span>
                                </div>
                            </div>

                            <div id="detalles-{{ $factura->id }}" class="hidden border-t border-white/5 bg-black/20 p-6">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Platillos Ordenados</h4>
                                
                                <div class="divide-y divide-white/5">
                                    @foreach($factura->detalles as $detalle)
                                        <div class="py-3 flex justify-between items-center text-sm">
                                            <div class="flex items-center gap-3">
                                                <span class="font-bold px-2 py-0.5 rounded-lg text-xs" style="color: var(--color-primary); background-color: rgba(255, 255, 255, 0.05);">
                                                    x{{ $detalle->cantidad }}
                                                </span>
                                                <p class="text-slate-200 font-medium">
                                                    {{ $detalle->producto->name ?? 'Platillo Eliminado' }}
                                                </p>
                                            </div>
                                            <p class="font-mono font-bold text-slate-400">
                                                ${{ number_format($detalle->total_linea ?? ($detalle->precio_unitario * $detalle->cantidad), 2) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 pt-4 border-t border-white/5 flex flex-col items-end gap-1 text-xs text-slate-500 font-mono">
                                    <div>Subtotal: <span class="text-slate-400">${{ number_format($factura->subtotal, 2) }}</span></div>
                                    <div>IVA (19%): <span class="text-slate-400">${{ number_format($factura->impuesto, 2) }}</span></div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="feature-card border rounded-3xl p-16 text-center shadow-xl">
                    <div class="text-5xl mb-4">🍽️</div>
                    <h3 class="text-xl font-bold text-white mb-2">No tienes compras registradas</h3>
                    <p class="text-slate-400 text-sm max-w-sm mx-auto mb-6">Tus facturas y pedidos aparecerán aquí automáticamente en cuanto realices tu primer pedido.</p>
                    <a href="/cliente/dashboard" class="inline-block font-bold px-6 py-3 rounded-xl transition duration-200 text-sm no-underline" style="background-color: var(--color-primary); color: var(--text-white);">
                        Ordenar algo rico ahora 🍳
                    </a>
                </div>
            @endif

        </div>
    </div>

    <script>
        function toggleFactura(id) {
            const contenedor = document.getElementById(`detalles-${id}`);
            const flecha = document.getElementById(`flecha-${id}`);
            
            // Leemos la variable del tema en caliente directamente desde los estilos cargados
            const colorActivo = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim();

            if (contenedor.classList.contains('hidden')) {
                contenedor.classList.remove('hidden');
                flecha.style.transform = 'rotate(180deg)';
                flecha.style.color = colorActivo;
            } else {
                contenedor.classList.add('hidden');
                flecha.style.transform = 'rotate(0deg)';
                flecha.style.color = '#64748b'; // color gris neutro slate-500
            }
        }
    </script>
</x-app-layout>