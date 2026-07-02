<x-app-layout>
    <div class="pos-wrapper min-h-screen py-10" x-data="{ tab: 'venta' }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="pos-card border rounded-3xl p-6 shadow-xl mb-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="pos-main-title text-3xl font-black tracking-tight">🏪 <span>Caja Registradora y Finanzas</span></h2>
                        <p class="pos-subtitle text-sm mt-1">Administra los flujos de dinero de la empresa en una sola interfaz.</p>
                    </div>

                    <div class="pos-tabs-container border p-1.5 rounded-2xl gap-1 shrink-0 flex">
                        <button @click="tab = 'venta'; setTimeout(() => document.getElementById('barcode-scanner-input')?.focus(), 100)"
                            :class="tab === 'venta' ? 'tab-active-cyan font-bold' : 'tab-inactive'"
                            class="px-4 py-2 text-xs uppercase tracking-wider border rounded-xl transition duration-200 flex items-center gap-1.5">
                            🛒 Caja Directa
                        </button>
                        <button @click="tab = 'movimiento'"
                            :class="tab === 'movimiento' ? 'tab-active-amber font-bold' : 'tab-inactive'"
                            class="px-4 py-2 text-xs uppercase tracking-wider border rounded-xl transition duration-200 flex items-center gap-1.5">
                            💰 Gastos / Otros
                        </button>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="pos-alert-danger border p-4 rounded-2xl mb-6 text-sm">
                    <strong>⚠️ Error:</strong> {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="pos-alert-success border p-4 rounded-2xl mb-6 text-sm flex items-center gap-2">
                    <span>✨</span><strong>Éxito:</strong> {{ session('success') }}
                </div>
            @endif

            <!-- PESTAÑA: CAJA DIRECTA -->
            <div x-show="tab === 'venta'" x-transition>
                <!-- CAMPO ESPÍA PARA CAPTURAR ESCÁNER -->
                <input type="text" id="barcode-scanner-input" class="absolute opacity-0 w-0 h-0 pointer-events-none" autofocus autocomplete="off">

                <form action="{{ route('admin.ventas.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        <div class="lg:col-span-2 space-y-6">
                            <div class="pos-card border rounded-3xl p-6 shadow-xl">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                                    <h3 class="pos-section-title text-sm font-bold uppercase tracking-wider">Agregar Productos a la Orden</h3>
                                    <span class="inline-flex items-center gap-1.5 bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 px-2.5 py-1 rounded-lg text-[11px] font-medium tracking-wide animate-pulse">
                                        ⚡ Lector Activo (Dispara en cualquier momento)
                                    </span>
                                </div>
                                <div class="flex flex-col lg:flex-row gap-4 items-end sm:items-stretch">

                                    <div class="flex-1 relative">
                                        <label class="block text-[11px] pos-input-label uppercase font-bold tracking-wider mb-1">Buscar Plato o Bebida</label>

                                        <div id="custom-select-trigger" class="w-full pos-select-trigger border rounded-xl px-4 py-3 text-sm cursor-pointer flex items-center justify-between transition">
                                            <span id="select-placeholder" class="pos-placeholder-text">-- Selecciona un producto --</span>
                                            <span class="pos-placeholder-arrow text-xs">▼</span>
                                        </div>

                                        <div id="custom-select-dropdown" class="hidden absolute left-0 right-0 mt-2 pos-dropdown-menu border rounded-2xl shadow-2xl z-50 max-h-80 flex flex-col overflow-hidden">
                                            <div class="p-2 pos-dropdown-search-box border-b">
                                                <input type="text" id="search-producto" placeholder="Escribe para buscar..."
                                                    class="w-full pos-dropdown-input border rounded-lg px-3 py-2 text-xs outline-none transition">
                                            </div>
                                            <div class="overflow-y-auto flex-1 divide-y" id="options-container">
                                                @foreach ($productos as $prod)
                                                    <div class="option-item flex items-center gap-3 px-4 py-2.5 cursor-pointer pos-option-row transition text-sm"
                                                        data-value="{{ $prod->id }}" data-precio="{{ $prod->price }}" data-stock="{{ $prod->stock }}" data-name="{{ $prod->name }}" data-barcode="{{ $prod->barcode ?? '' }}">

                                                        <div class="w-10 h-10 rounded-lg pos-option-img-box border flex items-center justify-center overflow-hidden shrink-0">
                                                            @if ($prod->image || $prod->imagen)
                                                                @php $itemFoto = $prod->image ?? $prod->imagen; @endphp
                                                                <img src="{{ \Illuminate\Support\Str::startsWith($itemFoto, 'http') ? $itemFoto : asset('storage/' . $itemFoto) }}"
                                                                    class="w-full h-full object-cover" alt="{{ $prod->name }}">
                                                            @else
                                                                <span class="text-lg">🍔</span>
                                                            @endif
                                                        </div>

                                                        <div class="flex-1 min-w-0">
                                                            <p class="pos-option-name font-medium truncate">{{ $prod->name }}</p>
                                                            <p class="text-xs pos-option-meta font-mono">
                                                                ${{ number_format($prod->price, 2) }} <span>|</span> Stock: {{ $prod->stock }} @if($prod->barcode) <span>|</span> 🏷️ {{ $prod->barcode }} @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <input type="hidden" id="selector-producto" value="">
                                    </div>

                                    <div class="w-full sm:w-32">
                                        <label class="block text-[11px] pos-input-label uppercase font-bold tracking-wider mb-1">Cantidad</label>
                                        <input type="number" id="cantidad-producto" value="1" min="1"
                                            class="w-full pos-input border rounded-xl px-4 py-3 text-sm text-center outline-none">
                                    </div>

                                    <div class="w-full sm:w-auto flex items-end">
                                        <button type="button" id="btn-agregar" class="w-full sm:w-auto pos-btn-add text-white px-6 py-3 rounded-xl font-medium text-sm transition h-[46px] shadow-lg">
                                            ＋ Agregar
                                        </button>
                                    </div>

                                </div>
                            </div>

                            <div class="pos-card border rounded-3xl shadow-xl overflow-hidden">
                                <div class="px-6 py-4 pos-table-header border-b">
                                    <h3 class="text-sm font-bold uppercase tracking-wider pos-section-title">Detalle del Consumo</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse pos-table">
                                        <thead>
                                            <tr class="border-b text-xs uppercase tracking-wider font-semibold pos-table-th-row">
                                                <th class="py-4 px-6">Producto</th>
                                                <th class="py-4 px-6 text-center">Precio</th>
                                                <th class="py-4 px-6 text-center">Cantidad</th>
                                                <th class="py-4 px-6 text-right">Subtotal</th>
                                                <th class="py-4 px-6 text-center">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="contenedor-items" class="divide-y text-sm pos-table-tbody">
                                            <tr id="fila-vacia">
                                                <td colspan="5" class="py-12 px-6 text-center pos-table-empty">
                                                    <div class="text-3xl mb-2">🍽️</div>
                                                    Agrega platos o bebidas para calcular el monto.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="pos-card border rounded-3xl p-6 shadow-xl space-y-6">

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 pos-section-title">Asociar Cliente Registrado</label>
                                    <select name="user_id" class="w-full pos-input border rounded-xl px-4 py-3 text-sm outline-none">
                                        <option value="">-- Cliente de Paso / Venta General --</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">
                                                👤 {{ $cliente->name }} ({{ $cliente->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[11px] pos-helper-text mt-1.5">Si se deja en blanco, la factura no se asociará a ninguna cuenta de usuario.</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider mb-2 pos-section-title">Método de Pago</label>
                                    <select name="metodo_pago" required class="w-full pos-input border rounded-xl px-4 py-3 text-sm outline-none">
                                        <option value="Efectivo">💵 Efectivo</option>
                                        <option value="Transferencia">📱 Transferencia Bancaria</option>
                                        <option value="Tarjeta">💳 Tarjeta de Débito / Crédito</option>
                                    </select>
                                </div>

                                <div class="pos-totals-box border p-4 rounded-2xl space-y-3">
                                    <div class="flex justify-between text-xs pos-helper-text">
                                        <span>Subtotal</span>
                                        <span id="txt-subtotal" class="font-mono">$0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t pos-totals-divider">
                                        <span class="text-sm font-bold text-white">Total a Cobrar</span>
                                        <span id="txt-total" class="text-xl font-black pos-text-total font-mono">$0.00</span>
                                    </div>
                                </div>

                                <button type="submit" class="w-full pos-btn-invoice text-white p-4 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                                    💼 Confirmar y Emitir Factura
                                </button>

                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- PESTAÑA: MOVIMIENTO -->
            <div x-show="tab === 'movimiento'" x-transition class="hidden" :class="{ 'hidden': tab !== 'movimiento' }">
                <div class="pos-card border rounded-3xl p-8 shadow-xl" x-data="{ tipo: 'egreso', concepto: 'pago_proveedor' }">

                    <form action="{{ route('admin.movimientos.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block pos-section-title text-xs font-bold uppercase tracking-wider mb-2">Tipo de Flujo</label>
                                <select name="tipo" x-model="tipo" class="w-full pos-input border rounded-xl p-3 text-sm focus:outline-none">
                                    <option value="egreso">🔴 Gasto / Salida (Egreso)</option>
                                    <option value="ingreso">🟢 Entrada Extra (Ingreso)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block pos-section-title text-xs font-bold uppercase tracking-wider mb-2">Concepto del Movimiento</label>
                                <select name="concepto" x-model="concepto" class="w-full pos-input border rounded-xl p-3 text-sm focus:outline-none">
                                    <optgroup label="Egresos Disponibles" x-show="tipo === 'egreso'">
                                        <option value="pago_proveedor">📦 Gasto en Producto (Suma Stock + Proveedor)</option>
                                        <option value="servicios">⚡ Servicios Públicos / Arriendo</option>
                                        <option value="nomina">👥 Préstamos / Nómina de Empleados</option>
                                        <option value="otros">🔺 Otros Gastos Varios</option>
                                    </optgroup>

                                    <optgroup label="Ingresos Disponibles" x-show="tipo === 'ingreso'">
                                        <option value="abono_empleado">🤝 Empleado paga dinero prestado</option>
                                        <option value="otros">🔹 Otros Ingresos de Caja</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <div x-show="tipo === 'egreso' && concepto === 'pago_proveedor'" x-transition class="p-5 pos-automation-box border rounded-2xl space-y-4">
                            <p class="pos-text-brand text-xs font-bold uppercase tracking-wide">📦 Automatización de Inventario</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block pos-helper-text text-xs mb-1">Proveedor Asociado</label>
                                    <select id="movProveedor" name="proveedor_id" class="w-full pos-input-nested border rounded-xl p-2.5 text-xs focus:outline-none">
                                        <option value="">Seleccione Proveedor...</option>
                                        @foreach ($proveedores as $prov)
                                            <option value="{{ $prov->id }}">{{ $prov->company_name ?? $prov->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block pos-helper-text text-xs mb-1">Producto a Surtir</label>
                                    <select id="movProducto" name="producto_id" class="w-full pos-input-nested border rounded-xl p-2.5 text-xs focus:outline-none">
                                        <option value="" data-proveedor="">Seleccione Producto...</option>
                                        @foreach ($productos as $prod)
                                            <option value="{{ $prod->id }}" data-proveedor="{{ $prod->supplier_id ?? '' }}">
                                                {{ $prod->name }} (Stock actual: {{ $prod->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block pos-helper-text text-xs mb-1">Cantidad a Sumar</label>
                                    <input type="number" name="cantidad_producto" placeholder="Ej: 20"
                                        class="w-full pos-input-nested border rounded-xl p-2.5 text-xs focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                <label class="block pos-section-title text-xs font-bold uppercase tracking-wider mb-2">Valor / Monto ($)</label>
                                <input type="number" step="0.01" name="monto" required placeholder="0.00"
                                    class="w-full pos-input border rounded-xl p-3 font-mono text-base font-bold focus:outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block pos-section-title text-xs font-bold uppercase tracking-wider mb-2">Descripción / Concepto Detallado</label>
                                <input type="text" name="descripcion" required placeholder="Ej: Compra de insumos o pago de servicios"
                                    class="w-full pos-input border rounded-xl p-3 text-sm focus:outline-none">
                            </div>
                        </div>

                        <button type="submit" class="w-full pos-btn-invoice font-black py-4 rounded-xl transition duration-200 text-sm uppercase tracking-wide shadow-lg flex items-center justify-center gap-2">
                            Guardar Movimiento y Sincronizar Reporte 💾
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectTrigger = document.getElementById('custom-select-trigger');
        const selectDropdown = document.getElementById('custom-select-dropdown');
        const searchInput = document.getElementById('search-producto');
        const optionsContainer = document.getElementById('options-container');
        const placeholderText = document.getElementById('select-placeholder');
        const hiddenInputProducto = document.getElementById('selector-producto');

        const btnAgregar = document.getElementById('btn-agregar');
        const inputCantidad = document.getElementById('cantidad-producto');
        const contenedorItems = document.getElementById('contenedor-items');
        const filaVacia = document.getElementById('fila-vacia');
        const txtSubtotal = document.getElementById('txt-subtotal');
        const txtTotal = document.getElementById('txt-total');

        const movProveedor = document.getElementById('movProveedor');
        const movProducto = document.getElementById('movProducto');
        
        // Elemento espía para capturar escáner
        const barcodeScannerInput = document.getElementById('barcode-scanner-input');

        let productoSeleccionadoData = null;

        // Mantener el foco en el input espía para escaneos continuos, excepto si se está editando otro input válido
        function focusScanner() {
            const activeEl = document.activeElement;
            const inputsIgnorados = ['search-producto', 'cantidad-producto', 'user_id', 'metodo_pago', 'proveedor_id', 'producto_id', 'cantidad_producto', 'monto', 'descripcion'];
            
            if (activeEl && inputsIgnorados.includes(activeEl.id) || activeEl.name && inputsIgnorados.includes(activeEl.name)) {
                return;
            }
            // Solo enfocar si estamos en la pestaña de venta
            const wrapper = document.querySelector('.pos-wrapper');
            if (wrapper && wrapper.__x && wrapper.__x.$data.tab === 'venta') {
                barcodeScannerInput?.focus();
            }
        }

        // Enfocar al inicio y recuperar foco tras hacer clics vagos en la pantalla
        setTimeout(focusScanner, 200);
        document.addEventListener('click', () => setTimeout(focusScanner, 150));

        // Lógica de captura y procesamiento del Código de Barras
        barcodeScannerInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Evitar cualquier submit indeseado por el Enter automático del lector
                
                const scannedCode = this.value.trim();
                this.value = ''; // Limpiar de inmediato para la siguiente lectura
                
                if (scannedCode === '') return;

                // Buscar el elemento en la lista que coincida con el atributo data-barcode
                const matchedOption = optionsContainer.querySelector(`.option-item[data-barcode="${scannedCode}"]`);

                if (matchedOption) {
                    const id = matchedOption.getAttribute('data-value');
                    const name = matchedOption.getAttribute('data-name');
                    const precio = parseFloat(matchedOption.getAttribute('data-precio'));
                    const stock = parseInt(matchedOption.getAttribute('data-stock'));

                    // Forzar la inyección directa al Detalle del Consumo (Cantidad fija de 1 por lectura)
                    inyectarProductoDirecto(id, name, precio, stock, 1);
                } else {
                    alert(`Código de barras "${scannedCode}" no asociado a ningún producto.`);
                }
            }
        });

        // Función Modularizada para Inyectar a Detalle Consumo directamente
        function inyectarProductoDirecto(prodId, prodNombre, prodPrecio, prodStock, cantidad) {
            if (prodStock <= 0) {
                alert(`El producto "${prodNombre}" no cuenta con stock disponible.`);
                return;
            }

            if (filaVacia && contenedorItems.contains(filaVacia)) filaVacia.remove();

            const filaExistente = document.querySelector(`tr[data-product-id="${prodId}"]`);
            if (filaExistente) {
                const inputCantExistente = filaExistente.querySelector('.input-cantidad-oculto');
                let nuevaCant = parseInt(inputCantExistente.value) + cantidad;

                if (nuevaCant > prodStock) {
                    alert(`Al acumular las unidades de "${prodNombre}" superas el stock disponible (${prodStock}).`);
                    return;
                }

                inputCantExistente.value = nuevaCant;
                filaExistente.querySelector('.txt-cantidad').textContent = nuevaCant;
                filaExistente.querySelector('.txt-subtotal-fila').textContent = `$${(prodPrecio * nuevaCant).toFixed(2)}`;
                calcularGranTotal();
                return;
            }

            const subtotalFila = prodPrecio * cantidad;
            const nuevaFila = document.createElement('tr');
            nuevaFila.setAttribute('data-product-id', prodId);
            nuevaFila.className = "pos-table-row-injected transition duration-150 struct-item-row";

            nuevaFila.innerHTML = `
                <td class="py-4 px-6 font-bold text-white">
                    ${prodNombre}
                    <input type="hidden" class="input-id-oculto" value="${prodId}">
                </td>
                <td class="py-4 px-6 text-center font-mono pos-helper-text">$${prodPrecio.toFixed(2)}</td>
                <td class="py-4 px-6 text-center">
                    <span class="txt-cantidad font-bold text-white">${cantidad}</span>
                    <input type="hidden" value="${cantidad}" class="input-cantidad-oculto">
                </td>
                <td class="py-4 px-6 text-right font-bold pos-text-row-subtotal font-mono txt-subtotal-fila">$${subtotalFila.toFixed(2)}</td>
                <td class="py-4 px-6 text-center">
                    <button type="button" class="pos-btn-remove-row transition btn-eliminar-fila">❌</button>
                </td>
            `;

            contenedorItems.appendChild(nuevaFila);

            reindexarProductos();
            calcularGranTotal();
        }

        // select trigger interactividad estándar manual
        selectTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            selectDropdown.classList.toggle('hidden');
            if (!selectDropdown.classList.contains('hidden')) {
                searchInput.focus();
            }
        });

        document.addEventListener('click', function() {
            selectDropdown.classList.add('hidden');
        });

        selectDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase().trim();
            const items = optionsContainer.querySelectorAll('.option-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                if (name.includes(query)) {
                    item.style.setProperty('display', 'flex', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
        });

        optionsContainer.addEventListener('click', function(e) {
            const row = e.target.closest('.option-item');
            if (!row) return;

            const id = row.getAttribute('data-value');
            const name = row.getAttribute('data-name');
            const precio = parseFloat(row.getAttribute('data-precio'));
            const stock = parseInt(row.getAttribute('data-stock'));

            productoSeleccionadoData = { id, name, precio, stock };

            hiddenInputProducto.value = id;
            placeholderText.textContent = `🍔 ${name} ($${precio.toFixed(2)})`;
            placeholderText.className = "text-white font-medium";

            selectDropdown.classList.add('hidden');
            searchInput.value = '';
            optionsContainer.querySelectorAll('.option-item').forEach(item => item.style.setProperty('display', 'flex', 'important'));
        });

        // Botón agregar manual
        btnAgregar.addEventListener('click', function() {
            if (!productoSeleccionadoData || !hiddenInputProducto.value) {
                alert('Por favor, selecciona un producto válido de la lista.');
                return;
            }

            const cantidad = parseInt(inputCantidad.value);
            if (cantidad <= 0 || isNaN(cantidad)) {
                alert('La cantidad debe ser mayor a cero.');
                return;
            }

            inyectarProductoDirecto(
                productoSeleccionadoData.id,
                productoSeleccionadoData.name,
                productoSeleccionadoData.precio,
                productoSeleccionadoData.stock,
                cantidad
            );

            hiddenInputProducto.value = "";
            placeholderText.textContent = "-- Selecciona un producto --";
            placeholderText.className = "pos-placeholder-text";
            inputCantidad.value = "1";
            productoSeleccionadoData = null;
        });

        contenedorItems.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-eliminar-fila')) {
                e.target.closest('tr').remove();
                if (contenedorItems.querySelectorAll('.struct-item-row').length === 0) {
                    contenedorItems.appendChild(filaVacia);
                }
                reindexarProductos();
                calcularGranTotal();
            }
        });

        function calcularGranTotal() {
            let totalAcumulado = 0;
            document.querySelectorAll('.struct-item-row').forEach(function(fila) {
                totalAcumulado += parseFloat(fila.querySelector('.txt-subtotal-fila').textContent.replace('$', ''));
            });
            txtSubtotal.textContent = `$${totalAcumulado.toFixed(2)}`;
            txtTotal.textContent = `$${(totalAcumulado * 1.19).toFixed(2)}`;
        }

        function reindexarProductos() {
            const filas = document.querySelectorAll('.struct-item-row');
            filas.forEach((fila, index) => {
                const inputId = fila.querySelector('.input-id-oculto');
                const inputCant = fila.querySelector('.input-cantidad-oculto');
                if (inputId && inputCant) {
                    inputId.setAttribute('name', `productos[${index}][id]`);
                    inputCant.setAttribute('name', `productos[${index}][cant]`);
                }
            });
        }

        if (movProveedor && movProducto) {
            movProveedor.addEventListener('change', function() {
                const proveedorId = this.value ? this.value.toString().trim() : "";
                Array.from(movProducto.options).forEach(option => {
                    if (option.value === "") { option.hidden = false; option.disabled = false; return; }
                    const prodDataProv = option.getAttribute('data-proveedor') ? option.getAttribute('data-proveedor').toString().trim() : "";
                    if (proveedorId === "" || prodDataProv === proveedorId) {
                        option.hidden = false; option.disabled = false;
                    } else {
                        option.hidden = true; option.disabled = true;
                    }
                });
                movProducto.value = "";
            });

            movProducto.addEventListener('change', function() {
                const opcionSeleccionada = this.options[this.selectedIndex];
                if (!opcionSeleccionada || opcionSeleccionada.value === "") return;
                const idProductoElegido = opcionSeleccionada.value;
                const prodDataProv = opcionSeleccionada.getAttribute('data-proveedor') ? opcionSeleccionada.getAttribute('data-proveedor').toString().trim() : "";
                if (prodDataProv) {
                    movProveedor.value = prodDataProv;
                    movProducto.value = idProductoElegido;
                }
            });
        }
    });
    </script>
</x-app-layout>