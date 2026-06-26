<x-app-layout>
    <div class="prv-wrapper min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl prv-alert-success border flex items-center gap-3 text-sm font-medium">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="prv-card border rounded-3xl p-6 mb-6 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold prv-main-title flex items-center gap-2">Gestión de Proveedores 📦</h1>
                    <p class="prv-subtitle text-sm mt-1">Administra las empresas y contactos que surten los productos de tu inventario.</p>
                </div>
                <a href="{{ route('proveedores.create') }}" class="prv-btn-submit font-bold px-5 py-3 rounded-2xl transition duration-200 flex items-center gap-2 text-sm shadow-md no-underline">
                    <i class="fa-solid fa-truck-ramp-box"></i> Nuevo Proveedor
                </a>
            </div>

            <div class="prv-card border rounded-3xl p-6 shadow-xl">

                <form action="{{ url()->current() }}" method="GET" class="mb-6">
                    <div class="w-full sm:w-96 flex prv-search-container border rounded-2xl overflow-hidden transition duration-200">
                        <span class="prv-search-icon flex items-center justify-center pl-4 pr-2">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search"
                            class="w-full bg-transparent border-0 prv-search-input py-3 pr-4 focus:outline-none text-sm"
                            placeholder="Buscar por empresa, contacto o NIT..." value="{{ request('search') }}">
                    </div>
                </form>

                <div class="overflow-x-auto prv-table-border border rounded-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="prv-table-header border-b text-xs font-semibold tracking-wider uppercase">
                                <th class="p-4 text-center w-20">ID</th>
                                <th class="p-4 w-24">Imagen</th>
                                <th class="p-4">Empresa / Proveedor</th>
                                <th class="p-4">Contacto</th>
                                <th class="p-4">Teléfono</th>
                                <th class="p-4">Correo Electrónico</th>
                                <th class="p-4 text-center w-32">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y prv-table-divide text-sm prv-table-body">
                            @forelse($proveedores as $proveedor)
                                <tr class="prv-table-row transition duration-150">
                                    <td class="p-4 text-center prv-id-cell font-mono">#{{ $proveedor->id }}</td>

                                    <td class="p-4">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden border prv-avatar-box flex-shrink-0 flex items-center justify-center shadow-inner">
                                            @if ($proveedor->image)
                                                <img src="{{ \Illuminate\Support\Str::startsWith($proveedor->image, 'http') ? $proveedor->image : asset('storage/' . $proveedor->image) }}"
                                                    class="w-full h-full object-cover"
                                                    alt="Logo de {{ $proveedor->company_name }}">
                                            @else
                                                <i class="fa-solid fa-truck prv-avatar-icon text-xs"></i>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="p-4">
                                        <div class="font-semibold prv-company-title">{{ $proveedor->company_name }}</div>
                                        <div class="text-xs prv-subtitle font-mono mt-0.5">NIT: {{ $proveedor->nit }}</div>
                                    </td>
                                    <td class="p-4 font-medium prv-contact-text">{{ $proveedor->contact_name }}</td>
                                    <td class="p-4 prv-mono-text font-mono">{{ $proveedor->phone ?? 'N/A' }}</td>
                                    <td class="p-4 prv-info-text">{{ $proveedor->email ?? 'N/A' }}</td>
                                    
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('proveedores.edit', $proveedor->id) }}"
                                                class="flex items-center justify-center w-9 h-9 prv-action-btn prv-edit-btn rounded-xl transition duration-150 shadow-inner"
                                                title="Editar">
                                                <i class="fa-solid fa-pen-to-square text-base"></i>
                                            </a>

                                            <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" class="inline m-0"
                                                onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center justify-center w-9 h-9 prv-action-btn prv-delete-btn rounded-xl transition duration-150 shadow-inner"
                                                    title="Eliminar">
                                                    <i class="fa-solid fa-trash text-base"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center prv-subtitle">
                                        <i class="fa-solid fa-boxes-packing text-3xl mb-2 block prv-empty-icon"></i>
                                        No se encontraron proveedores registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($proveedores->hasPages())
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 prv-divider-top gap-4">
                        <span class="prv-subtitle text-xs">
                            Mostrando {{ $proveedores->firstItem() ?? 0 }}-{{ $proveedores->lastItem() ?? 0 }} de
                            {{ $proveedores->total() }} registros
                        </span>
                        <div class="prv-pagination-container">
                            {{ $proveedores->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>