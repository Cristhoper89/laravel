<x-app-layout>
    <div class="cat-wrapper min-h-screen py-10">
        <div class="max-w-2xl mx-auto px-6">
            <div class="cat-card border rounded-3xl p-8 shadow-xl">
                
                <div class="mb-6">
                    <h3 class="text-2xl font-bold cat-main-title">Nueva Categoría 🏷️</h3>
                    <p class="cat-subtitle text-sm mt-1">Crea una clasificación para agrupar tus productos o servicios.</p>
                </div>
                
                <form action="{{ route('categorias.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block cat-subtitle mb-2 text-sm font-medium">Nombre de la Categoría</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej. Bebidas, Entradas, Postres..."
                               class="w-full cat-input border rounded-2xl p-3 focus:outline-none transition duration-200" required>
                        @error('name')
                            <p class="cat-error-text text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block cat-subtitle mb-2 text-sm font-medium">Estado Inicial</label>
                        <select name="type" class="w-full cat-input border rounded-2xl p-3 focus:outline-none transition duration-200" required>
                            <option value="activo" {{ old('type') == 'activo' ? 'selected' : '' }}>Activo (Visible en el sistema)</option>
                            <option value="inactivo" {{ old('type') == 'inactivo' ? 'selected' : '' }}>Inactivo (Oculto del menú/sistema)</option>
                        </select>
                        @error('type')
                            <p class="cat-error-text text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-4 pt-4 cat-divider-top">
                        <a href="{{ route('categorias.index') }}" 
                           class="cat-btn-cancel border px-6 py-3 rounded-2xl transition duration-200 no-underline text-sm flex items-center">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="cat-btn-submit font-bold px-6 py-3 rounded-2xl transition duration-200 shadow-md text-sm">
                            Guardar Categoría
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>