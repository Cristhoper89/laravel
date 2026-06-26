<x-app-layout>
    <div class="usr-wrapper min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl usr-alert-success border flex items-center gap-3 text-sm font-medium">
                    <i class="fa-solid fa-circle-check text-lg"></i> 
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="usr-card border rounded-3xl p-6 mb-6 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="usr-main-title text-2xl font-bold flex items-center gap-2">Gestionar Usuarios 👥</h1>
                    <p class="usr-subtitle text-sm mt-1">
                        Agrega, edita, elimina o cambia los roles de los usuarios registrados en la plataforma.
                    </p>
                </div>
                <a href="{{ route('users.create') }}" class="usr-btn-submit font-bold px-5 py-3 rounded-2xl transition duration-200 flex items-center gap-2 text-sm shadow-md no-underline">
                    <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
                </a>
            </div>

            <div class="usr-card border rounded-3xl p-6 shadow-xl">
                
                <form action="{{ url()->current() }}" method="GET" class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                        
                        <div class="w-full sm:w-96 flex usr-search-box border rounded-2xl overflow-hidden transition duration-200">
                            <span class="usr-search-icon flex items-center justify-center pl-4 pr-2">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" name="search" class="w-full bg-transparent border-0 text-white placeholder-slate-600 py-3 pr-4 focus:outline-none text-sm" placeholder="Buscar por nombre o correo..." value="{{ request('search') }}">
                        </div>
                        
                        <div class="w-full sm:w-48">
                            <select name="role" class="w-full usr-input border rounded-2xl p-3 text-sm cursor-pointer transition duration-200" onchange="this.form.submit()">
                                <option value="">Todos los roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="cliente" {{ request('role') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto usr-table-container border rounded-2xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="usr-table-header border-b text-xs font-semibold tracking-wider uppercase">
                                <th class="p-4 text-center w-20">ID</th>
                                <th class="p-4 w-24">Foto</th>
                                <th class="p-4">Usuario</th>
                                <th class="p-4">Correo Electrónico</th>
                                <th class="p-4 text-center w-40">Rol asignado</th>
                                <th class="p-4 text-center w-32">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm usr-table-body">
                            @forelse($users as $user)
                                <tr class="usr-table-row transition duration-150">
                                    <td class="p-4 text-center usr-id-text font-mono">#{{ $user->id }}</td>
                                    
                                    <td class="p-4">
                                        <div class="w-10 h-10 rounded-full overflow-hidden border usr-avatar-ring">
                                            @if($user->photo)
                                                <img src="{{ \Illuminate\Support\Str::startsWith($user->photo, 'http') ? $user->photo : asset('storage/' . $user->photo) }}" 
                                                     class="w-full h-full object-cover" alt="Avatar">
                                            @else
                                                <div class="w-full h-full usr-avatar-default flex items-center justify-center text-xs font-bold text-white uppercase">
                                                    {{ substr($user->name, 0, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td class="p-4 font-semibold text-white">{{ $user->name }}</td>
                                    <td class="p-4 usr-subtitle">{{ $user->email }}</td>
                                    
                                    <td class="p-4 text-center">
                                        @if($user->role == 'admin')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-semibold usr-badge-admin border">
                                                Administrador
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-semibold usr-badge-client border">
                                                Cliente
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('users.edit', $user->id) }}" 
                                               class="flex items-center justify-center w-9 h-9 usr-action-btn btn-edit rounded-xl transition duration-150 shadow-inner" 
                                               title="Editar">
                                                <i class="fa-solid fa-pen-to-square text-base block"></i>
                                            </a>
                                            
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este usuario?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="flex items-center justify-center w-9 h-9 usr-action-btn btn-delete rounded-xl transition duration-150 shadow-inner" 
                                                        title="Eliminar">
                                                    <i class="fa-solid fa-trash text-base block"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center usr-subtitle">
                                        <i class="fa-solid fa-folder-open text-3xl mb-2 block text-slate-600"></i>
                                        No se encontraron usuarios registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 usr-divider-top gap-4">
                        <span class="usr-subtitle text-xs">
                            Mostrando {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} de {{ $users->total() }} registros
                        </span>
                        <div class="usr-pagination">
                            {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>