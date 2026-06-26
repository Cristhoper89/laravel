<x-app-layout>
    <div class="admin-wrapper min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-6">

            <div class="admin-card border rounded-3xl p-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="admin-title text-4xl font-bold mb-2"><span>Panel de Administración</span> 🛠️</h2>
                    <p class="admin-subtitle">Gestiona el menú de comida, el stock disponible y los precios del sistema.</p>
                </div>
                <a href="{{ route('productos.create') }}" class="admin-btn-success font-bold px-6 py-3 rounded-2xl transition duration-300 shadow-lg flex items-center gap-2">
                    ➕ Agregar Platillo
                </a>
            </div>

            @if (session('success'))
                <div class="admin-alert-success mt-6 border rounded-2xl p-4 shadow-lg flex items-center gap-2">
                    <span>✨</span>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <div class="admin-card border rounded-3xl mt-8 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="admin-table w-full text-left border-collapse">
                        <thead>
                            <tr class="admin-table-th-row border-b text-sm font-semibold uppercase">
                                <th class="p-6">Platillo</th>
                                <th class="p-6">Proveedor</th>
                                <th class="p-6">Unidad</th>
                                <th class="p-6">Precio</th>
                                <th class="p-6">Inventario</th>
                                <th class="p-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($productos as $producto)
                                <tr class="admin-table-row transition duration-200">
                                    <td class="p-6 flex items-center gap-4">
                                        <div class="admin-img-container w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 border flex items-center justify-center">
                                            @if ($producto->image)
                                                <img src="{{ \Illuminate\Support\Str::startsWith($producto->image, 'http') ? $producto->image : (\Illuminate\Support\Str::startsWith($producto->image, '/storage') ? $producto->image : asset('storage/' . $producto->image)) }}"
                                                     alt="{{ $producto->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="admin-img-placeholder w-full h-full flex items-center justify-center text-xl" title="Sin imagen">
                                                    🍔
                                                </div>
                                            @endif
                                        </div>
                                        <span class="font-bold text-white text-base">{{ $producto->name }}</span>
                                    </td>
                                    
                                    <td class="py-4 px-4 text-sm">{{ $producto->proveedor->company_name ?? 'Sin Proveedor' }}</td>
                                    
                                    <td class="p-6">
                                        <span class="admin-badge-unit border px-3 py-1 rounded-full text-xs font-medium">
                                            {{ $producto->unit_of_measurement }}
                                        </span>
                                    </td>
                                    
                                    <td class="p-6 font-semibold admin-text-price">
                                        ${{ number_format($producto->price, 2) }}
                                    </td>
                                    
                                    <td class="p-6">
                                        <span class="{{ $producto->stock < 10 ? 'admin-stock-critical' : 'admin-stock-normal' }}">
                                            {{ $producto->stock }} u.
                                        </span>
                                    </td>
                                    
                                    <td class="p-6">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('productos.edit', $producto->id) }}" class="admin-action-btn btn-edit p-2 border rounded-xl transition duration-200" title="Editar">
                                                ✏️
                                            </a>
                                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este platillo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="admin-action-btn btn-delete p-2 border rounded-xl transition duration-200" title="Eliminar">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="admin-table-empty p-12 text-center">
                                        No hay platillos registrados en el menú todavía.
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