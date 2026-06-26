<x-app-layout>
    <div class="min-h-screen bg-slate-950 py-10">
        <div class="max-w-2xl mx-auto px-6">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-xl">
                
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-white">Editar Categoría ✏️</h3>
                    <p class="text-slate-400 text-sm mt-1">Modifica las propiedades o disponibilidad de la clasificación.</p>
                </div>
                
                <form action="{{ route('categorias.update', $categoria->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-slate-400 mb-2 text-sm font-medium">Nombre de la Categoría</label>
                        <input type="text" name="name" value="{{ old('name', $categoria->name) }}" placeholder="Ej. Bebidas calientes"
                               class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition duration-200" required>
                        @error('name')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-2 text-sm font-medium">Estado de la Categoría</label>
                        <select name="type" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-white focus:outline-none focus:border-emerald-500 transition duration-200" required>
                            <option value="activo" {{ old('type', $categoria->type) == 'activo' ? 'selected' : '' }}>Activo (Visible en el sistema)</option>
                            <option value="inactivo" {{ old('type', $categoria->type) == 'inactivo' ? 'selected' : '' }}>Inactivo (Oculto del menú/sistema)</option>
                        </select>
                        @error('type')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-4 pt-4 border-t border-slate-800/60">
                        <a href="{{ route('categorias.index') }}" 
                           class="bg-slate-950 border border-slate-800 text-slate-400 px-6 py-3 rounded-2xl hover:text-white hover:border-slate-700 transition duration-200 no-underline text-sm flex items-center">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-6 py-3 rounded-2xl transition duration-200 shadow-md text-sm">
                            Actualizar Categoría
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>