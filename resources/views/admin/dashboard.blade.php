<x-app-layout>
    <div class="admin-wrapper min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-6">

            <!-- Header principal del Panel -->
            <div class="admin-card border rounded-3xl p-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="admin-title text-4xl font-bold mb-2"><span>Control de Inventario e Insumos</span> 📦</h2>
                    <p class="admin-subtitle">Gestiona el stock de ingredientes, insumos de cocina, unidades y proveedores.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('productos.create') }}" class="admin-btn-success font-bold px-6 py-3 rounded-2xl transition duration-300 shadow-lg flex items-center gap-2">
                        ➕ Registrar Insumo
                    </a>
                </div>
            </div>

            <!-- Notificaciones / Alertas de Sistema -->
            @if (session('success'))
                <div class="admin-alert-success mt-6 border rounded-2xl p-4 shadow-lg flex items-center gap-2">
                    <span>✨</span>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Tarjetas de Métricas (KPIs) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
                <div class="admin-card border rounded-2xl p-5 shadow-md flex items-center gap-4">
                    <div class="text-3xl p-3 bg-gray-800 rounded-xl">📦</div>
                    <div>
                        <p class="text-xs admin-subtitle uppercase tracking-wider font-semibold">Total Insumos</p>
                        <h3 class="text-2xl font-bold text-white">{{ $totalProductos }}</h3>
                    </div>
                </div>

                <div class="admin-card border rounded-2xl p-5 shadow-md flex items-center gap-4">
                    <div class="text-3xl p-3 bg-green-950/40 rounded-xl text-green-400">✅</div>
                    <div>
                        <p class="text-xs admin-subtitle uppercase tracking-wider font-semibold">Activos</p>
                        <h3 class="text-2xl font-bold text-green-400">{{ $totalActivos }}</h3>
                    </div>
                </div>

                <div class="admin-card border rounded-2xl p-5 shadow-md flex items-center gap-4">
                    <div class="text-3xl p-3 bg-red-950/40 rounded-xl text-red-400">⚠️</div>
                    <div>
                        <p class="text-xs admin-subtitle uppercase tracking-wider font-semibold">Bajo Stock (<=10)</p>
                        <h3 class="text-2xl font-bold text-red-400">{{ $bajoStock }}</h3>
                    </div>
                </div>

                <div class="admin-card border rounded-2xl p-5 shadow-md flex items-center gap-4">
                    <div class="text-3xl p-3 bg-gray-800/80 rounded-xl text-gray-400">🚫</div>
                    <div>
                        <p class="text-xs admin-subtitle uppercase tracking-wider font-semibold">Desactivados</p>
                        <h3 class="text-2xl font-bold text-gray-400">{{ $totalInactivos }}</h3>
                    </div>
                </div>
            </div>

            <!-- Navegación de Pestañas (Filtro Activos / Inactivos) -->
            <div class="flex items-center gap-3 mt-8 border-b border-gray-800 pb-3">
                <a href="{{ route('admin.dashboard', ['tab' => 'activos']) }}" 
                   class="px-5 py-2 rounded-xl text-sm font-semibold transition duration-200 {{ $tab !== 'inactivos' ? 'bg-red-600/20 text-red-400 border border-red-500/30' : 'text-gray-400 hover:text-white' }}">
                    Activos ({{ $totalActivos }})
                </a>
                <a href="{{ route('admin.dashboard', ['tab' => 'inactivos']) }}" 
                   class="px-5 py-2 rounded-xl text-sm font-semibold transition duration-200 {{ $tab === 'inactivos' ? 'bg-red-600/20 text-red-400 border border-red-500/30' : 'text-gray-400 hover:text-white' }}">
                    Inactivos ({{ $totalInactivos }})
                </a>
            </div>

            <!-- Tabla de Control de Inventario -->
            <div class="admin-card border rounded-3xl mt-6 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="admin-table w-full text-left border-collapse">
                        <thead>
                            <tr class="admin-table-th-row border-b text-sm font-semibold uppercase">
                                <th class="p-6">Insumo / Materia Prima</th>
                                <th class="p-6">Categoría</th>
                                <th class="p-6">Proveedor</th>
                                <th class="p-6">Unidad</th>
                                <th class="p-6">Precio</th>
                                <th class="p-6">Stock Actual</th>
                                <th class="p-6 text-center">Estado</th>
                                <th class="p-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($productos as $producto)
                                <tr class="admin-table-row transition duration-200 {{ $producto->state === false ? 'opacity-60 bg-black/20' : '' }}">
                                    <!-- Insumo / Imagen -->
                                    <td class="p-6 flex items-center gap-4">
                                        <div class="admin-img-container w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 border flex items-center justify-center">
                                            @if ($producto->image)
                                                <img src="{{ \Illuminate\Support\Str::startsWith($producto->image, 'http') ? $producto->image : (\Illuminate\Support\Str::startsWith($producto->image, '/storage') ? $producto->image : asset('storage/' . $producto->image)) }}"
                                                     alt="{{ $producto->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="admin-img-placeholder w-full h-full flex items-center justify-center text-xl" title="Insumo de cocina">
                                                    📦
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="font-bold text-white text-base block">{{ $producto->name }}</span>
                                            <span class="text-xs admin-subtitle">
                                                {{ $producto->barcode ? 'Código: ' . $producto->barcode : 'ID: #' . str_pad($producto->id, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Categoría -->
                                    <td class="py-4 px-6 text-sm">
                                        <span class="admin-badge-unit border px-3 py-1 rounded-full text-xs font-medium">
                                            {{ $producto->categoria?->name ?? $producto->category?->name ?? 'Sin Categoría' }}
                                        </span>
                                    </td>

                                    <!-- Proveedor -->
                                    <td class="py-4 px-6 text-sm">
                                        {{ $producto->proveedor?->company_name ?? $producto->supplier?->company_name ?? 'Sin Proveedor' }}
                                    </td>
                                    
                                    <!-- Unidad de Medida -->
                                    <td class="p-6">
                                        <span class="admin-badge-unit border px-3 py-1 rounded-full text-xs font-medium">
                                            {{ $producto->unit_of_measurement ?? 'Unidad' }}
                                        </span>
                                    </td>
                                    
                                    <!-- Precio -->
                                    <td class="p-6 font-semibold admin-text-price">
                                        ${{ number_format($producto->price, 2) }}
                                    </td>
                                    
                                    <!-- Stock de Inventario -->
                                    <td class="p-6">
                                        <span class="{{ $producto->stock <= 10 ? 'admin-stock-critical' : 'admin-stock-normal' }}">
                                            {{ $producto->stock }}
                                        </span>
                                    </td>

                                    <!-- Estado del Producto -->
                                    <td class="p-6 text-center">
                                        @if ($producto->state ?? true)
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-500/10 text-green-400 border border-green-500/20">
                                                Activo
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-500/10 text-red-400 border border-red-500/20">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- Botones de Acción -->
                                    <td class="p-6">
                                        <div class="flex items-center justify-center gap-3">
                                            @if ($producto->state ?? true)
                                                <!-- Editar -->
                                                <a href="{{ route('productos.edit', $producto->id) }}" class="admin-action-btn btn-edit p-2 border rounded-xl transition duration-200" title="Editar Insumo">
                                                    ✏️
                                                </a>

                                                <!-- Desactivar -->
                                                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas desactivar este insumo?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="admin-action-btn btn-delete p-2 border rounded-xl transition duration-200" title="Desactivar Insumo">
                                                        🚫
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Reactivar -->
                                                <form action="{{ route('productos.reactivar', $producto->id) }}" method="POST" onsubmit="return confirm('¿Deseas reactivar este insumo?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="p-2 border rounded-xl bg-green-950/30 text-green-400 border-green-800 hover:bg-green-900/50 transition duration-200" title="Reactivar Insumo">
                                                        🔄 Reactivar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="admin-table-empty p-12 text-center">
                                        No hay insumos {{ $tab === 'inactivos' ? 'inactivos' : 'activos' }} registrados en el inventario.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>