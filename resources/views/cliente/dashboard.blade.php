<x-app-layout>

    <div class="min-h-screen welcome-body relative overflow-x-hidden py-10">

        <div class="max-w-7xl mx-auto px-6">

            <div class="feature-card border rounded-3xl p-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-4xl font-black text-white mb-3">
                        ¡Hola, {{ $cliente->name }}! 🍔
                    </h2>
                    <p class="text-slate-400 text-lg">
                        Bienvenido a nuestro sistema de pedidos. Revisa el menú de hoy abajo y arma tu orden ideal.
                    </p>
                </div>
                
                <a href="{{ route('carrito.index') }}" 
                   class="font-bold px-6 py-4 rounded-2xl transition duration-300 shadow-md flex items-center gap-2 text-sm whitespace-nowrap self-stretch md:self-auto justify-center no-underline"
                   style="background-color: var(--color-primary); color: var(--text-white);">
                    Ver mi Orden 🛒
                    @if(session('carrito') && count(session('carrito')) > 0)
                        <span class="bg-black/50 text-white text-xs px-2 py-0.5 rounded-full font-black">
                            {{ count(session('carrito')) }}
                        </span>
                    @endif
                </a>
            </div>

            @if(session('success'))
                <div class="mt-6 border rounded-2xl p-4 shadow-lg flex items-center gap-3 animate-fade-in bg-emerald-950/20 border-emerald-500/30 text-emerald-400">
                    <span>✨</span>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mt-6 border rounded-2xl p-4 shadow-lg flex items-center gap-3 bg-rose-950/20 border-rose-500/30 text-rose-400">
                    <span>⚠️</span>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <h3 class="text-2xl font-black text-white mt-10 mb-6 flex items-center gap-2">
                <span style="color: var(--color-primary);">🔥</span> Menú del Día
            </h3>

            {{-- Filtrar los productos para incluir únicamente los que tengan state activo --}}
            @php
                $productosActivos = $productos->filter(function($p) {
                    return (bool) $p->state;
                });
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

                @forelse($productosActivos as $producto)
                    <div class="feature-card border rounded-3xl overflow-hidden shadow-xl flex flex-col justify-between group transition duration-300">
                        
                        <div class="relative h-48 w-full bg-black/20 overflow-hidden">
                            @if($producto->image)
                                <img src="{{ Str::startsWith($producto->image, 'http') ? $producto->image : asset('storage/' . $producto->image) }}" 
                                     alt="{{ $producto->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-600 bg-black/10">
                                    🍽️ Sin imagen
                                </div>
                            @endif
                            
                            @if($producto->stock <= 0)
                                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center">
                                    <span class="bg-rose-500/20 border border-rose-500/50 text-rose-400 text-xs font-bold px-3 py-1 rounded-full">Agotado ❌</span>
                                </div>
                            @endif
                            
                            <span class="absolute top-4 right-4 bg-black/60 backdrop-blur-md border border-white/10 text-slate-300 text-xs font-semibold px-3 py-1 rounded-full">
                                {{ $producto->stock }} {{ $producto->unit_of_measurement }}
                            </span>
                        </div>

                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="text-xl font-bold text-white mb-1 transition duration-300" style="--tw-text-opacity: 1; color: inherit; font-weight: 800;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--text-main)'">
                                    {{ $producto->name }}
                                </h4>
                                <p class="text-slate-500 text-sm mb-4">
                                    Proveedor: {{ $producto->suppliers }}
                                </p>
                            </div>

                            <div class="mt-4">
                                <div class="flex items-baseline justify-between mb-4">
                                    <span class="text-slate-400 text-sm">Precio</span>
                                    <span class="text-2xl font-black" style="color: var(--color-success);">
                                        ${{ number_format($producto->price, 2) }}
                                    </span>
                                </div>

                                @if($producto->stock > 0)
                                    <a href="{{ route('carrito.add', $producto->id) }}" 
                                       class="w-full bg-black/40 border border-white/10 text-slate-200 hover:text-white transition duration-300 rounded-2xl py-3 font-semibold shadow-md flex items-center justify-center gap-2 text-center text-sm no-underline"
                                       onmouseover="this.style.backgroundColor='var(--color-primary)'; this.style.borderColor='var(--color-primary-hover)'"
                                       onmouseout="this.style.backgroundColor='rgba(0,0,0,0.4)'; this.style.borderColor='rgba(255,255,255,0.1)'">
                                        🛒 Agregar al Carrito
                                    </a>
                                @else
                                    <button disabled 
                                            class="w-full bg-white/5 border border-white/5 text-slate-600 rounded-2xl py-3 font-semibold cursor-not-allowed text-sm flex items-center justify-center gap-2">
                                        🚫 No disponible
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 xl:col-span-4 feature-card border rounded-3xl p-12 text-center">
                        <p class="text-slate-400 text-lg">
                            Anuncio: Por el momento la cocina no tiene productos con existencias disponibles.
                        </p>
                    </div>
                @endforelse

            </div>

        </div>

    </div>

</x-app-layout>