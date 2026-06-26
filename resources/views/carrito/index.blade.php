<x-app-layout>
    <div class="min-h-screen welcome-body relative overflow-x-hidden py-10">
        <div class="max-w-5xl mx-auto px-6">

            <div class="feature-card border rounded-3xl p-8 shadow-xl mb-8 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-black text-white mb-2">Tu Carrito de Pedidos 🛒</h2>
                    <p class="text-slate-400 text-sm">Revisa tus platillos, ajusta las porciones y confirma tu orden.</p>
                </div>
                <a href="/cliente/dashboard" class="db-link-action font-semibold text-sm transition duration-200 no-underline flex items-center gap-1">
                    ← Volver al Menú
                </a>
            </div>

            @if(session('success'))
            <div class="mb-6 bg-emerald-950/20 border border-emerald-500/30 text-emerald-400 rounded-2xl p-4 shadow-md flex items-center gap-2">
                <span>✨</span>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-rose-950/20 border border-rose-500/30 text-rose-400 rounded-2xl p-4 shadow-md flex items-center gap-2">
                <span>⚠️</span>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
            @endif

            @if(count($carrito) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-4">
                    @php $subtotalGeneral = 0; @endphp
                    @foreach($carrito as $id => $item)
                    @php
                    $totalPlato = $item['price'] * $item['cantidad'];
                    $subtotalGeneral += $totalPlato;
                    @endphp
                    
                    <div class="feature-card border rounded-2xl p-4 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4 transition duration-200">
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="h-16 w-16 bg-black/20 rounded-xl overflow-hidden flex-shrink-0 border border-white/5">
                                @if($item['image'])
                                <img src="{{ Str::startsWith($item['image'], 'http') ? $item['image'] : asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-lg bg-black/10">🍽️</div>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-lg">{{ $item['name'] }}</h4>
                                <p class="text-slate-500 text-xs">Precio unitario: <span class="font-bold" style="color: var(--color-success);">${{ number_format($item['price'], 2) }}</span></p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-white/5">
                            <form action="{{ route('carrito.update') }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @php method_field('PATCH'); @endphp
                                <input type="hidden" name="id" value="{{ $id }}">
                                
                                <input type="number" name="cantidad" value="{{ $item['cantidad'] }}" min="1"
                                    class="w-16 bg-black/40 border border-white/10 text-white rounded-xl p-2 text-center text-sm focus:outline-none transition duration-200"
                                    style="--tw-border-opacity: 1;"
                                    onfocus="this.style.borderColor='var(--color-primary)'"
                                    onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                                
                                <button type="submit" title="Actualizar cantidad" 
                                        class="p-2 bg-black/20 hover:text-white rounded-xl transition duration-200 border border-white/10"
                                        onmouseover="this.style.backgroundColor='var(--color-primary)'; this.style.borderColor='var(--color-primary-hover)'"
                                        onmouseout="this.style.backgroundColor='rgba(0,0,0,0.2)'; this.style.borderColor='rgba(255,255,255,0.1)'">
                                    🔄
                                </button>
                            </form>

                            <div class="text-right min-w-[80px]">
                                <p class="text-white font-black text-sm">${{ number_format($totalPlato, 2) }}</p>
                            </div>

                            <form action="{{ route('carrito.remove') }}" method="POST" onsubmit="return confirm('¿Remover este platillo de la orden?')">
                                @csrf
                                @php method_field('DELETE'); @endphp
                                <input type="hidden" name="id" value="{{ $id }}">
                                <button type="submit" class="text-rose-400 hover:text-rose-500 transition duration-200 p-2 text-sm">
                                    ❌
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="feature-card border rounded-3xl p-6 shadow-xl h-fit sticky top-6">
                    <h3 class="text-xl font-bold text-white mb-4 pb-2 border-b border-white/5">Resumen de Cuenta</h3>

                    @php
                    $iva = $subtotalGeneral * 0.19;
                    $totalGeneral = $subtotalGeneral + $iva;
                    @endphp

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal</span>
                            <span class="text-white font-semibold">${{ number_format($subtotalGeneral, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Impuesto (19%)</span>
                            <span class="text-white font-semibold">${{ number_format($iva, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400 pb-2">
                            <span>Servicio</span>
                            <span class="font-bold" style="color: var(--color-success);">Gratis</span>
                        </div>
                        <div class="flex justify-between text-base pt-3 border-t border-white/5">
                            <span class="text-slate-300 font-bold">Total General</span>
                            <span class="text-2xl font-black" style="color: var(--color-success);">${{ number_format($totalGeneral, 2) }}</span>
                        </div>
                    </div>

                    <form action="{{ route('facturas.store') }}" method="POST" class="mt-6 space-y-4">
                        @csrf

                        @foreach($carrito as $id => $item)
                        <input type="hidden" name="productos[{{ $id }}][id]" value="{{ $id }}">
                        <input type="hidden" name="productos[{{ $id }}][cantidad]" value="{{ $item['cantidad'] }}">
                        @endforeach

                        <div>
                            <label class="block text-slate-400 mb-2 text-xs font-medium uppercase tracking-wider">Método de Pago</label>
                            <select name="metodo_pago" 
                                    class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-slate-300 text-sm focus:outline-none transition duration-200"
                                    onfocus="this.style.borderColor='var(--color-primary)'"
                                    onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                                <option value="Efectivo" class="bg-slate-900">Efectivo 💵</option>
                                <option value="Tarjeta" class="bg-slate-900">Tarjeta de Crédito/Débito 💳</option>
                                <option value="Transferencia" class="bg-slate-900">Transferencia Bancaria 🏦</option>
                            </select>
                        </div>

                        <button type="submit" 
                                class="w-full font-black py-4 rounded-2xl transition duration-200 shadow-md text-center block text-sm uppercase tracking-wide border-none"
                                style="background-color: var(--color-primary); color: var(--text-white);"
                                onmouseover="this.style.backgroundColor='var(--color-primary-hover)'"
                                onmouseout="this.style.backgroundColor='var(--color-primary)'">
                            Confirmar y Pagar Orden 🧾
                        </button>
                    </form>
                </div>

            </div>
            @else
            <div class="feature-card border rounded-3xl p-16 text-center shadow-xl">
                <div class="text-5xl mb-4">🛒</div>
                <h3 class="text-xl font-bold text-white mb-2">Tu orden está vacía</h3>
                <p class="text-slate-400 text-sm max-w-sm mx-auto mb-6">Parece que aún no has seleccionado ningún platillo de nuestro menú del día.</p>
                <a href="/cliente/dashboard" 
                   class="inline-block border font-bold px-6 py-3 rounded-xl transition duration-200 text-sm no-underline bg-black/40 border-white/10 text-white"
                   onmouseover="this.style.backgroundColor='var(--color-primary)'; this.style.borderColor='var(--color-primary-hover)'"
                   onmouseout="this.style.backgroundColor='rgba(0,0,0,0.4)'; this.style.borderColor='rgba(255,255,255,0.1)'">
                    Explorar el Menú 🍳
                </a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>