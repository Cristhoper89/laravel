<x-app-layout>
    <!-- Contenedor principal con fondo unificado del panel -->
    <div class="min-h-screen admin-wrapper py-10">
        <div class="max-w-3xl mx-auto px-6">
            <!-- Tarjeta del Formulario usando .admin-card -->
            <div class="admin-card border rounded-3xl p-8 shadow-xl">
                <h3 class="text-2xl font-bold admin-title mb-6">Nuevo Platillo 🍳</h3>

                <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    <!-- NOMBRE DEL PRODUCTO -->
                    <div>
                        <label class="block admin-subtitle mb-2 text-sm font-medium">Nombre del producto</label>
                        <!-- Usamos .usr-input para mantener la coherencia estética de las cajas de texto -->
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="Ej. Hamburguesa Especial"
                            class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                            required>
                        @error('name')
                            <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CÓDIGO DE BARRAS -->
                    <div>
                        <label class="block admin-subtitle mb-2 text-sm font-medium">Código de Barras 🛠️ (Escanea directamente aquí)</label>
                        <div class="relative">
                            <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}"
                                placeholder="Haz clic aquí y escanea el código de barras..."
                                class="w-full usr-input border rounded-2xl p-3 pl-10 focus:outline-none transition duration-200 font-mono tracking-wider">
                            <div class="absolute left-3 top-1.5 text-lg">
                                🏷️
                            </div>
                        </div>
                        <p class="admin-subtitle opacity-70 text-[11px] mt-1">Si usas un lector físico, selecciónalo y dispara sobre las barras. Deja en blanco si no aplica.</p>
                        @error('barcode')
                            <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PROVEEDOR Y CATEGORÍA -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block admin-subtitle mb-2 text-sm font-medium">Proveedor 🚚</label>
                            <select name="supplier_id"
                                class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                                <option value="" disabled selected>Selecciona un proveedor</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}"
                                        {{ old('supplier_id') == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block admin-subtitle mb-2 text-sm font-medium">Categoría 🏷️</label>
                            <select name="category_id"
                                class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                                <option value="" disabled selected>Selecciona una categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ old('category_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- UNIDAD DE MEDIDA -->
                    <div>
                        <label class="block admin-subtitle mb-2 text-sm font-medium">Unidad de Medida</label>
                        <select name="unit_of_measurement"
                            class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200">
                            <option value="Unidad" {{ old('unit_of_measurement') == 'Unidad' ? 'selected' : '' }}>Unidad</option>
                            <option value="Porción" {{ old('unit_of_measurement') == 'Porción' ? 'selected' : '' }}>Porción</option>
                            <option value="Lata" {{ old('unit_of_measurement') == 'Lata' ? 'selected' : '' }}>Lata</option>
                            <option value="Kilogramo" {{ old('unit_of_measurement') == 'Kilogramo' ? 'selected' : '' }}>Kilogramo</option>
                        </select>
                        @error('unit_of_measurement')
                            <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PRECIO Y STOCK -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block admin-subtitle mb-2 text-sm font-medium">Precio ($)</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                                placeholder="0.00"
                                class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                            @error('price')
                                <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block admin-subtitle mb-2 text-sm font-medium">Cantidad en Stock</label>
                            <input type="number" name="stock" value="{{ old('stock') }}" placeholder="0"
                                class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                            @error('stock')
                                <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- SECCIÓN DE LA IMAGEN CON ALPINE.JS -->
                    <div class="usr-photo-section border rounded-2xl p-4 space-y-4"
                        x-data="{ tipoImagen: '{{ old('image') && filter_var(old('image'), FILTER_VALIDATE_URL) ? 'url' : 'file' }}' }">

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between usr-divider-bottom pb-3 gap-2">
                            <div>
                                <label class="block admin-subtitle text-sm font-medium">Imagen del Producto 🖼️</label>
                                <p class="admin-subtitle text-[11px] mt-0.5">Selecciona cómo deseas cargar la imagen del platillo.</p>
                            </div>

                            <!-- Selector de tipo (File / URL) adaptado al diseño unificado -->
                            <div class="flex usr-toggle-container border p-1 rounded-xl gap-1 self-start sm:self-auto">
                                <button type="button" @click="tipoImagen = 'file'"
                                    :class="tipoImagen === 'file' ? 'toggle-active font-bold' : 'toggle-inactive'"
                                    class="px-3 py-1.5 text-xs rounded-lg transition duration-200">
                                    📁 Archivo Local
                                </button>
                                <button type="button" @click="tipoImagen = 'url'"
                                    :class="tipoImagen === 'url' ? 'toggle-active font-bold' : 'toggle-inactive'"
                                    class="px-3 py-1.5 text-xs rounded-lg transition duration-200">
                                    🌐 Enlace URL
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4 usr-preview-box p-4 rounded-xl border">
                            <div class="flex-1 w-full">
                                <!-- Input File Nativo adaptado -->
                                <div x-show="tipoImagen === 'file'" x-transition>
                                    <p class="text-[11px] admin-subtitle mb-2">Sube un archivo nuevo desde tu computadora.</p>
                                    <input type="file" :name="tipoImagen === 'file' ? 'image' : ''"
                                        class="w-full usr-file-input text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold transition duration-200">
                                </div>

                                <!-- Input URL -->
                                <div x-show="tipoImagen === 'url'" x-transition class="hidden"
                                    :class="{ 'hidden': tipoImagen !== 'url' }">
                                    <p class="text-[11px] admin-subtitle mb-2">Pega la dirección URL directa de la imagen de internet.</p>
                                    <input type="url" :name="tipoImagen === 'url' ? 'image' : ''"
                                        value="{{ old('image') && filter_var(old('image'), FILTER_VALIDATE_URL) ? old('image') : '' }}"
                                        placeholder="https://ejemplo.com/imagenes/platillo.jpg"
                                        class="w-full usr-input border rounded-xl p-2.5 focus:outline-none transition duration-200 text-sm">
                                </div>

                                @error('image')
                                    <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción Finales -->
                    <div class="flex justify-end gap-4 pt-4 usr-divider-top">
                        <a href="{{ route('admin.dashboard') }}"
                            class="usr-btn-cancel border px-6 py-3 rounded-2xl transition duration-200 no-underline text-sm flex items-center">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="admin-btn-success font-bold px-6 py-3 rounded-2xl transition duration-200 shadow-md text-sm">
                            Guardar Platillo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PREVENCIÓN DE ENVÍO ACCIDENTAL POR EL LECTOR DE CÓDIGO DE BARRAS -->
    <script>
        document.getElementById('barcode').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Detiene el submit accidental del formulario al escanear
                console.log("Código capturado con éxito: " + this.value);
            }
        });
    </script>
</x-app-layout>