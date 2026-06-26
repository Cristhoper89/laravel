<x-app-layout>
    <div class="min-h-screen bg-slate-950 py-10" x-data="{ tab: 'venta' }">
        <div class="max-w-4xl mx-auto px-6">

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-xl mb-8">
                <h2 class="text-3xl font-bold text-white mb-2">Control de Caja Registradora 🏪</h2>
                <p class="text-slate-400 text-sm">Gestiona las ventas directas, inyecciones de efectivo y egresos/gastos de La Cabaña.</p>
                
                <div class="flex border-b border-slate-800 mt-6 gap-2">
                    <button @click="tab = 'venta'" 
                            :class="tab === 'venta' ? 'border-cyan-500 text-cyan-400 bg-cyan-500/5' : 'border-transparent text-slate-400 hover:text-slate-200'"
                            class="px-5 py-3 font-bold text-sm border-b-2 transition duration-200 rounded-t-xl">
                        🛒 Registrar Venta Directa
                    </button>
                    <button @click="tab = 'movimiento'" 
                            :class="tab === 'movimiento' ? 'border-amber-500 text-amber-400 bg-amber-500/5' : 'border-transparent text-slate-400 hover:text-slate-200'"
                            class="px-5 py-3 font-bold text-sm border-b-2 transition duration-200 rounded-t-xl">
                        💰 Gastos e Ingresos Varios
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-emerald-950/50 border border-emerald-500/30 text-emerald-400 rounded-2xl p-4 shadow-lg flex items-center gap-2">
                    <span>✨</span><p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <div x-show="tab === 'venta'" x-transition class="space-y-6">
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-xl">
                    <h3 class="text-white font-bold mb-4">Nueva Venta Directa</h3>
                    <p class="text-slate-500 text-xs">El comportamiento regular: genera factura y se guarda en reportes como 'entrance'.</p>
                </div>
            </div>

            <div x-show="tab === 'movimiento'" x-transition class="hidden" :class="{ 'hidden': tab !== 'movimiento' }">
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-xl" x-data="{ tipo: 'egreso', concepto: 'otros' }">
                    
                    <form action="{{ route('admin.movimientos.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Tipo de Flujo</label>
                                <select name="tipo" x-model="tipo" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 text-sm focus:outline-none focus:border-amber-500">
                                    <option value="egreso">🔴 Gasto / Salida (Egreso)</option>
                                    <option value="ingreso">🟢 Entrada Extra (Ingreso)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Concepto</label>
                                <select name="concepto" x-model="concepto" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 text-sm focus:outline-none focus:border-amber-500">
                                    <template x-if="tipo === 'egreso'">
                                        <optgroup label="Egresos Disponibles">
                                            <option value="pago_proveedor">📦 Pago a Proveedor (Compra Stock)</option>
                                            <option value="servicios">⚡ Servicios Públicos / Arriendo</option>
                                            <option value="nomina">👥 Pago Diarios / Nómina</option>
                                            <option value="otros">🔺 Otros Gastos</option>
                                        </optgroup>
                                    </template>
                                    
                                    <template x-if="tipo === 'ingreso'">
                                        <optgroup label="Ingresos Disponibles">
                                            <option value="abono_empleado">🤝 Abono de Empleado (Préstamo)</option>
                                            <option value="otros">🔹 Otros Ingresos Varios</option>
                                        </optgroup>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div x-show="tipo === 'egreso' && concepto === 'pago_proveedor'" x-transition class="p-5 bg-slate-950 border border-slate-800 rounded-2xl space-y-4">
                            <p class="text-amber-400 text-xs font-bold uppercase tracking-wide">📦 Automatización de Inventario</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-slate-500 text-xs mb-1">Proveedor</label>
                                    <select name="proveedor_id" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2.5 text-slate-300 text-xs">
                                        <option value="">Seleccione Proveedor...</option>
                                        @foreach($proveedores as $prov)
                                            <option value="{{ $prov->id }}">{{ $prov->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-500 text-xs mb-1">Producto a Surtir</label>
                                    <select name="producto_id" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2.5 text-slate-300 text-xs">
                                        <option value="">Seleccione Producto...</option>
                                        @foreach($productos as $prod)
                                            <option value="{{ $prod->id }}">{{ $prod->name }} (Stock: {{ $prod->stock }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-500 text-xs mb-1">Cantidad que Recibe</label>
                                    <input type="number" name="cantidad_producto" placeholder="Ej: 50" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2.5 text-slate-300 text-xs focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Monto / Dinero ($)</label>
                                <input type="number" step="0.01" name="monto" required placeholder="0.00" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-amber-400 font-mono text-base font-bold focus:outline-none focus:border-amber-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Descripción o Justificación</label>
                                <input type="text" name="descripcion" required placeholder="Ej: Pago de verduras semanales o Abono de Carlos" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 text-sm focus:outline-none focus:border-amber-500">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-slate-950 font-black py-4 rounded-xl transition duration-200 text-sm uppercase tracking-wide shadow-lg shadow-amber-500/10">
                            Guardar Movimiento en Caja y Reportes 💾
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>