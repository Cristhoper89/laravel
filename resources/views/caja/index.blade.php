<x-app-layout>
    <div class="min-h-screen welcome-body py-10 relative overflow-x-hidden">
        <div class="max-w-xl mx-auto px-6">

            <!-- CONFIGURACIÓN DE HUELLA DIGITAL DIRECTA -->
            <div class="flex justify-end mb-4">
                <button onclick="abrirModalRegistroHuella()"
                    class="cfg-btn-secondary px-4 py-2 rounded-xl font-medium text-xs transition cursor-pointer flex items-center gap-2">
                    ⚙️ Configurar mi Huella Digital
                </button>
            </div>

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
                                <span class="text-slate-400">📝 Ventas a Crédito:</span>
                                <span
                                    class="font-bold text-amber-400">${{ number_format($totales['ventas_credito'], 2) }}</span>
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

                    <!-- Input oculto para pasarle al JS el valor calculado de cierre automático -->
                    <input type="hidden" id="montoCaja" value="{{ $totales['efectivo_esperado_final'] }}">

                    <button onclick="abrirModalConfirmacion('cerrar')"
                        class="w-full font-black py-4 rounded-2xl transition duration-200 shadow-md text-sm uppercase tracking-wide border-none text-white bg-rose-600 hover:bg-rose-700 cursor-pointer">
                        Cerrar Caja con Balance Esperado 🔒
                    </button>
                @else
                    <div class="bg-black/20 p-6 rounded-2xl border border-white/5 mb-6">
                        <p class="text-rose-400 text-sm mb-4 font-bold">🔴 ESTADO: CAJA CERRADA</p>
                        <div class="text-left">
                            <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Monto
                                Base de Apertura (Efectivo en Caja)</label>
                            <input type="number" id="montoCaja"
                                class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-white text-center focus:outline-none text-lg font-bold"
                                placeholder="0.00" value="0">
                        </div>
                    </div>
                    <button onclick="abrirModalConfirmacion('abrir')"
                        class="w-full font-black py-4 rounded-2xl transition duration-200 shadow-md text-sm uppercase tracking-wide border-none text-white cursor-pointer"
                        style="background-color: var(--color-primary, #b91c1c);">
                        Abrir Nueva Caja 🔓
                    </button>
                @endif
            </div>

            <!-- MODAL DE CONFIRMACIÓN (APERTURA / CIERRE) -->
            <div id="modalPassword"
                class="hidden fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
                <div
                    class="feature-card border rounded-3xl p-6 w-full max-w-sm text-center bg-slate-900 text-white border-white/10">
                    <div class="text-3xl mb-2">🔑 / ☝️</div>
                    <h3 id="modalTitulo" class="text-xl font-bold mb-2 text-white">Confirmar Apertura</h3>
                    <p class="text-slate-400 text-xs mb-4">Ingresa tu contraseña de administrador o usa tu huella
                        digital para autorizar el movimiento de caja:</p>

                    <!-- Campo de contraseña tradicional -->
                    <input type="password" id="confirmPassword"
                        class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-white text-center mb-4 focus:outline-none focus:border-cyan-500"
                        placeholder="••••••••">

                    <!-- BOTÓN DE HUELLA DIGITAL -->
                    <button type="button" onclick="autenticarConHuella()"
                        class="w-full mb-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl text-sm transition cursor-pointer flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/20">
                        <span>☝️ Autenticar con Huella</span>
                    </button>

                    <div class="flex gap-3">
                        <button onclick="document.getElementById('modalPassword').classList.add('hidden')"
                            class="w-1/2 bg-white/5 border border-white/5 text-slate-400 py-2 rounded-xl text-sm font-semibold cursor-pointer hover:bg-white/10">Cancelar</button>
                        <button onclick="enviarProcesarCaja()"
                            class="w-1/2 font-bold py-2 rounded-xl text-sm text-white cursor-pointer bg-cyan-500 hover:bg-cyan-600">Confirmar</button>
                    </div>
                </div>
            </div>

            <!-- MODAL: VERIFICAR CONTRASEÑA ANTES DE REGISTRAR HUELLA -->
            <div id="modalRegistroHuella"
                class="hidden fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
                <div
                    class="feature-card border rounded-3xl p-6 w-full max-w-sm text-center bg-slate-900 text-white border-white/10">
                    <div class="text-3xl mb-2">🛡️</div>
                    <h3 class="text-xl font-bold mb-2 text-white">Autorizar Registro</h3>
                    <p class="text-slate-400 text-xs mb-4">Por seguridad, confirma tu contraseña antes de vincular tu
                        huella digital:</p>

                    <input type="password" id="passwordParaHuella"
                        class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-white text-center mb-4 focus:outline-none focus:border-emerald-500"
                        placeholder="••••••••">

                    <div class="flex gap-3">
                        <button onclick="document.getElementById('modalRegistroHuella').classList.add('hidden')"
                            class="w-1/2 bg-white/5 border border-white/5 text-slate-400 py-2 rounded-xl text-sm font-semibold cursor-pointer hover:bg-white/10">Cancelar</button>
                        <button onclick="verificarYRegistrarHuella()"
                            class="w-1/2 font-bold py-2 rounded-xl text-sm text-white cursor-pointer bg-emerald-600 hover:bg-emerald-700">Verificar
                            Clave</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Script oficial del paquete compilado -->
    <script src="{{ asset('vendor/webauthn/webauthn.js') }}"></script>

    <script>
        let accionActual = '';

        function abrirModalConfirmacion(accion) {
            accionActual = accion;
            const monto = document.getElementById('montoCaja').value;

            if (monto === '' || monto < 0) {
                alert("Por favor, ingresa un monto base inicial válido.");
                return;
            }

            document.getElementById('modalTitulo').innerText = accion === 'abrir' ? 'Confirmar Apertura' :
                'Confirmar Cierre de Caja';
            document.getElementById('confirmPassword').value = '';
            document.getElementById('modalPassword').classList.remove('hidden');
        }

        // --- AUTENTICACIÓN BIOMÉTRICA (OPCIONAL) ---
        async function autenticarConHuella() {
            const monto = document.getElementById('montoCaja').value;

            try {
                const webauthn = new WebAuthn();
                const assertion = await webauthn.login();

                let responseProcesar = await fetch('/caja/procesar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        accion: accionActual,
                        monto: monto,
                        auth_type: 'biometric',
                        webauthn_assertion: assertion
                    })
                });

                let data = await responseProcesar.json();
                manejadorRespuestaServidor(responseProcesar.ok, data);

            } catch (error) {
                console.error(error);
                alert("Autenticación biométrica fallida o cancelada.");
            }
        }

        // --- AUTENTICACIÓN TRADICIONAL POR CONTRASEÑA ---
        async function enviarProcesarCaja() {
            const monto = document.getElementById('montoCaja').value;
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
                manejadorRespuestaServidor(response.ok, data);
            } catch (error) {
                console.error(error);
                alert("Error de red.");
            }
        }

        // --- FLUJO DE CONFIGURACIÓN Y REGISTRO DE NUEVA HUELLA ---
        function abrirModalRegistroHuella() {
            document.getElementById('passwordParaHuella').value = '';
            document.getElementById('modalRegistroHuella').classList.remove('hidden');
        }

        async function verificarYRegistrarHuella() {
            const password = document.getElementById('passwordParaHuella').value;

            if (!password) {
                alert("Debes ingresar tu contraseña para continuar.");
                return;
            }

            try {
                let responseValidar = await fetch('/caja/validar-contrasena', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        password: password
                    })
                });

                let dataValidar = await responseValidar.json();

                if (!responseValidar.ok || !dataValidar.success) {
                    alert(dataValidar.message || "Contraseña incorrecta.");
                    return;
                }

                document.getElementById('modalRegistroHuella').classList.add('hidden');

                // Inicializamos el flujo de registro nativo de asbiin/laravel-webauthn
                const webauthn = new WebAuthn();
                await webauthn.register();

                alert(
                    "¡Tu huella digital se ha registrado y vinculado con éxito! Ya puedes usarla para abrir o cerrar caja.");

            } catch (error) {
                console.error(error);
                alert("El registro biométrico falló o fue cancelado.");
            }
        }

        function manejadorRespuestaServidor(isOk, data) {
            if (isOk && data.success) {
                alert(data.message);
                document.getElementById('modalPassword').classList.add('hidden');
                window.location.reload();
            } else {
                alert(data.message || "Ocurrió un error.");
            }
        }
    </script>
</x-app-layout>
