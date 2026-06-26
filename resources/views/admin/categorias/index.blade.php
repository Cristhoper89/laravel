<x-app-layout>
    <div class="cat-wrapper min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl cat-alert-success border flex items-center gap-3 text-sm font-medium">
                    <i class="fa-solid fa-circle-check text-lg"></i> 
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="cat-card border rounded-3xl p-6 mb-6 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold cat-main-title flex items-center gap-2">Categorías de Productos 🏷️</h1>
                    <p class="cat-subtitle text-sm mt-1">Organiza y clasifica los productos de tu menú o inventario.</p>
                </div>
                <a href="{{ route('categorias.create') }}" class="cat-btn-submit font-bold px-5 py-3 rounded-2xl transition duration-200 flex items-center gap-2 text-sm shadow-md no-underline">
                    <i class="fa-solid fa-plus"></i> Nueva Categoría
                </a>
            </div>

            <div class="cat-card border rounded-3xl p-6 shadow-xl">
                
                <form action="{{ url()->current() }}" method="GET" class="mb-6">
                    <div class="w-full sm:w-96 flex cat-search-container border rounded-2xl overflow-hidden transition duration-200">
                        <span class="cat-search-icon flex items-center justify-center pl-4 pr-2">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" 
                               class="w-full bg-transparent border-0 cat-search-input py-3 pr-4 focus:outline-none text-sm" 
                               placeholder="Buscar categoría por nombre..." value="{{ request('search') }}">
                    </div>
                </form>

                <div class="overflow-x-auto cat-table-border border rounded-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="cat-table-header border-b text-xs font-semibold tracking-wider uppercase">
                                <th class="p-4 text-center w-20">ID</th>
                                <th class="p-4">Nombre de la Categoría</th>
                                <th class="p-4 w-40">Estado</th>
                                <th class="p-4 text-center w-32">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="cat-table-body">
                            @forelse($categories as $categoria)
                                <tr class="cat-table-row transition duration-150">
                                    <td class="p-4 text-center cat-id-cell font-mono">#{{ $categoria->id }}</td>
                                    <td class="p-4 font-semibold cat-name-cell">{{ $categoria->name }}</td>
                                    <td class="p-4">
                                        @if($categoria->type === 'activo')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium cat-badge-active border">
                                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--color-success);"></span> Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium cat-badge-inactive border">
                                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--text-muted);"></span> Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('categorias.edit', $categoria->id) }}" 
                                               class="flex items-center justify-center w-9 h-9 cat-action-btn cat-edit-btn rounded-xl transition duration-150 shadow-inner" 
                                               title="Editar">
                                                <i class="fa-solid fa-pen-to-square text-base"></i>
                                            </a>
                                            
                                            <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="flex items-center justify-center w-9 h-9 cat-action-btn cat-delete-btn rounded-xl transition duration-150 shadow-inner" 
                                                        title="Eliminar">
                                                    <i class="fa-solid fa-trash text-base"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center cat-subtitle">
                                        <i class="fa-solid fa-tags text-3xl mb-2 block cat-empty-icon"></i>
                                        No se encontraron categorías registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 cat-divider-top gap-4">
                        <span class="cat-subtitle text-xs">
                            Mostrando {{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }} de {{ $categories->total() }} registros
                        </span>
                        <div class="cat-pagination-container">
                            {{ $categories->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>