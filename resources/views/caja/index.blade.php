<x-app-layout>
    <div class="min-h-screen welcome-body py-10 relative overflow-x-hidden">
        <div class="max-w-xl mx-auto px-6">

            <div class="feature-card border rounded-3xl p-8 shadow-xl text-center bg-slate-900 text-white">
                <h2 class="text-3xl font-black text-white mb-6">Control de Caja 🏦</h2>

                @if ($cajaAbierta)
                    <div class="text-left mb-6 bg-black/30 p-5 rounded-2xl border border-white/5">
                        <p class="text-emerald-400 text-sm mb-4 font-bold text-center">🟢 ESTADO: CAJA ABIERTA POR
                            {{ strtoupper($cajaAbierta->user->name) }}</p>
                        <p class="text-xs text-slate-400 mb-4 text-center">Iniciada el:
                            {{ $cajaAbierta->created_at->format('d/m/Y H:i A') }}</p>

                        <hr class="border-white/10 mb-4">

                        <!-- TABLA DE DESGLOSE DE ARQUEO -->
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-400">💵 Monto Base Inicial:</span>
                                <span
                                    class="font-bold text-white">${{ number_format($cajaAbierta->monto_apertura, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">🟢 Ventas en Efectivo (+):</span>
                                <span class="font-bold text-emerald-400">+
                                    ${{ number_format($totales['ventas_efectivo'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">💳 Ventas con Tarjeta:</span>
                                <span
                                    class="font-bold text-sky-400">${{ number_format($totales['ventas_tarjeta'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">🌐 Ventas por Plataforma:</span>
                                <span
                                    class="font-bold text-indigo-400">${{ number_format($totales['ventas_plataforma'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">🏦 Transferencias:</span>
                                <span
                                    class="font-bold text-amber-400">${{ number_format($totales['ventas_transferencia'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">🔴 Gastos / Salidas (-):</span>
                                <span class="font-bold text-rose-400">-
                                    ${{ number_format($totales['gastos'], 2) }}</span>
                            </div>

                            <hr class="border-white/10 my-4">

                            <div
                                class="flex justify-between text-base bg-white/5 p-3 rounded-xl border border-white/10">
                                <span class="font-black text-slate-300">💰 Efectivo Esperado Real:</span>
                                <span
                                    class="font-black text-emerald-400">${{ number_format($totales['efectivo_esperado_final'], 2) }}</span>
                            </div>

                            <div class="text-center text-[11px] text-slate-500 mt-2">
                                Total de ventas brutas registradas en el turno:
                                ${{ number_format($totales['total_ventas_acumulado'], 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Input oculto con la suma de cierre recomendada para la acción del JS -->
                    <input type="hidden" id="montoCaja" value="{{ $totales['monto_cierre_calculado'] }}">

                    {{-- Validación de Rol para Cerrar Caja --}}
                    @if (auth()->user()->role === 'cajero2')
                        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold text-center flex items-center justify-center gap-2">
                            <span>🔒</span> Sin permisos para realizar el cierre de caja.
                        </div>
                    @else
                        <button onclick="abrirModalConfirmacion('cerrar')"
                            class="w-full font-black py-4 rounded-2xl transition duration-200 shadow-md text-sm uppercase tracking-wide border-none text-white bg-rose-600 hover:bg-rose-700 cursor-pointer">
                            Cerrar Caja con Balance Esperado 🔒
                        </button>
                    @endif

                @else
                    <div class="bg-black/20 p-6 rounded-2xl border border-white/5 mb-6">
                        <p class="text-rose-400 text-sm mb-4 font-bold">🔴 ESTADO: CAJA CERRADA</p>
                        
                        @if (auth()->user()->role !== 'cajero2')
                            <div class="text-left">
                                <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">
                                    Monto Base de Apertura (Efectivo en Caja)
                                </label>
                                <input type="number" id="montoCaja"
                                    class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-white text-center focus:outline-none text-lg font-bold"
                                    placeholder="0.00" value="0">
                            </div>
                        @endif
                    </div>

                    {{-- Validación de Rol para Abrir Caja --}}
                    @if (auth()->user()->role === 'cajero2')
                        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold text-center flex items-center justify-center gap-2">
                            <span>🔒</span> Sin permisos para aperturar nueva caja.
                        </div>
                    @else
                        <button onclick="abrirModalConfirmacion('abrir')"
                            class="w-full font-black py-4 rounded-2xl transition duration-200 shadow-md text-sm uppercase tracking-wide border-none text-white cursor-pointer"
                            style="background-color: var(--color-primary, #b91c1c);">
                            Abrir Nueva Caja 🔓
                        </button>
                    @endif
                @endif
            </div>

            <!-- MODAL DE CONFIRMACIÓN CON CONTRASEÑA -->
            <div id="modalPassword"
                class="hidden fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
                <div
                    class="feature-card border rounded-3xl p-6 w-full max-w-sm text-center bg-slate-900 text-white border-white/10">
                    <div class="text-3xl mb-2">🔑</div>
                    <h3 id="modalTitulo" class="text-xl font-bold mb-2 text-white">Confirmar Acción</h3>
                    <p class="text-slate-400 text-xs mb-4">Ingresa tu contraseña para autorizar el movimiento de caja:</p>

                    <input type="password" id="confirmPassword"
                        class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-white text-center mb-4 focus:outline-none focus:border-cyan-500"
                        placeholder="••••••••">

                    <div class="flex gap-3">
                        <button onclick="document.getElementById('modalPassword').classList.add('hidden')"
                            class="w-1/2 bg-white/5 border border-white/5 text-slate-400 py-2 rounded-xl text-sm font-semibold cursor-pointer hover:bg-white/10">Cancelar</button>
                        <button onclick="enviarProcesarCaja()"
                            class="w-1/2 font-bold py-2 rounded-xl text-sm text-white cursor-pointer bg-cyan-500 hover:bg-cyan-600">Confirmar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        let accionActual = '';

        function abrirModalConfirmacion(accion) {
            accionActual = accion;
            const inputMonto = document.getElementById('montoCaja');
            const monto = inputMonto ? inputMonto.value : 0;

            if (monto === '' || monto < 0) {
                alert("Por favor, ingresa un monto base inicial válido.");
                return;
            }

            document.getElementById('modalTitulo').innerText = accion === 'abrir' ? 'Confirmar Apertura' : 'Confirmar Cierre de Caja';
            document.getElementById('confirmPassword').value = '';
            document.getElementById('modalPassword').classList.remove('hidden');
        }

        async function enviarProcesarCaja() {
            const inputMonto = document.getElementById('montoCaja');
            const monto = inputMonto ? inputMonto.value : 0;
            const password = document.getElementById('confirmPassword').value;

            if (!password) {
                alert("La contraseña es obligatoria.");
                return;
            }

            try {
                let response = await fetch('/caja/procesar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        accion: accionActual,
                        monto: monto,
                        auth_type: 'password',
                        password: password
                    })
                });

                let data = await response.json();
                
                if (response.ok && data.success) {
                    alert(data.message);
                    document.getElementById('modalPassword').classList.add('hidden');
                    window.location.reload();
                } else {
                    alert(data.message || "Ocurrió un error o no tienes permisos.");
                }
            } catch (error) {
                console.error(error);
                alert("Error de red al intentar procesar la caja.");
            }
        }
    </script>
</x-app-layout>