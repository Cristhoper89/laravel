<x-app-layout>
    <div class="min-h-screen bg-slate-950 py-10">
        <div class="max-w-3xl mx-auto px-6">

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-xl">

                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-white">Editar Platillo 📝</h3>
                    <p class="text-slate-400 text-sm mt-1">Modifica los detalles del producto seleccionado: <span
                            class="text-amber-400 font-semibold">{{ $producto->name }}</span></p>
                </div>

                <form action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- NOMBRE DEL PRODUCTO -->
                    <div>
                        <label class="block text-slate-400 mb-2 text-sm font-medium">Nombre del producto</label>
                        <input type="text" name="name" value="{{ old('name', $producto->name) }}"
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-white focus:outline-none focus:border-amber-500 transition duration-200"
                            required>
                        @error('name')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NUEVO CAMPO: CÓDIGO DE BARRAS -->
                    <div>
                        <label class="block text-slate-400 mb-2 text-sm font-medium">Código de Barras 🛠️ (Escanea directamente aquí)</label>
                        <div class="relative">
                            <input type="text" id="barcode" name="barcode" value="{{ old('barcode', $producto->barcode) }}"
                                placeholder="Haz clic aquí y escanea el código de barras..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 pl-10 text-white focus:outline-none focus:border-amber-500 transition duration-200 font-mono tracking-wider">
                            <div class="absolute left-3 top-1.5 text-lg">
                                🏷️
                            </div>
                        </div>
                        <p class="text-slate-500 text-[11px] mt-1">Si usas un lector físico, selecciónalo y dispara sobre las barras. Deja en blanco si no aplica.</p>
                        @error('barcode')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PROVEEDOR Y CATEGORÍA -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-slate-400 mb-2 text-sm font-medium">Proveedor 🚚</label>
                            <select name="supplier_id"
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-slate-300 focus:outline-none focus:border-amber-500 transition duration-200"
                                required>
                                <option value="" disabled>Selecciona un proveedor</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}"
                                        {{ old('supplier_id', $producto->supplier_id) == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-400 mb-2 text-sm font-medium">Categoría 🏷️</label>
                            <select name="category_id"
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-slate-300 focus:outline-none focus:border-amber-500 transition duration-200"
                                required>
                                <option value="" disabled>Selecciona una categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ old('category_id', $producto->category_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- UNIDAD DE MEDIDA -->
                    <div>
                        <label class="block text-slate-400 mb-2 text-sm font-medium">Unidad de Medida</label>
                        <select name="unit_of_measurement"
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-slate-300 focus:outline-none focus:border-amber-500 transition duration-200">
                            <option value="Unidad"
                                {{ old('unit_of_measurement', $producto->unit_of_measurement) == 'Unidad' ? 'selected' : '' }}>
                                Unidad</option>
                            <option value="Porción"
                                {{ old('unit_of_measurement', $producto->unit_of_measurement) == 'Porción' ? 'selected' : '' }}>
                                Porción</option>
                            <option value="Lata"
                                {{ old('unit_of_measurement', $producto->unit_of_measurement) == 'Lata' ? 'selected' : '' }}>
                                Lata</option>
                            <option value="Kilogramo"
                                {{ old('unit_of_measurement', $producto->unit_of_measurement) == 'Kilogramo' ? 'selected' : '' }}>
                                Kilogramo</option>
                        </select>
                        @error('unit_of_measurement')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PRECIO Y STOCK -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-slate-400 mb-2 text-sm font-medium">Precio ($)</label>
                            <input type="number" step="0.01" name="price"
                                value="{{ old('price', $producto->price) }}"
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-white focus:outline-none focus:border-amber-500 transition duration-200"
                                required>
                            @error('price')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-400 mb-2 text-sm font-medium">Cantidad en Stock</label>
                            <input type="number" name="stock" value="{{ old('stock', $producto->stock) }}"
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-white focus:outline-none focus:border-amber-500 transition duration-200"
                                required>
                            @error('stock')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- MULTIMEDIA (ALPINEX DATA) -->
                    <div class="border-t border-slate-800/60 pt-4">
                        <div class="bg-slate-950/40 border border-slate-800/80 rounded-2xl p-4 space-y-4"
                            x-data="{ tipoImagen: '{{ \Illuminate\Support\Str::startsWith($producto->image, 'http') ? 'url' : 'file' }}' }">

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800/60 pb-3 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-sm font-medium">Imagen del Producto 🖼️</label>
                                    <p class="text-slate-500 text-[11px] mt-0.5">Modifica el recurso multimedia del platillo.</p>
                                </div>

                                <div class="flex bg-slate-950 border border-slate-800 p-1 rounded-xl gap-1 self-start sm:self-auto">
                                    <button type="button" @click="tipoImagen = 'file'"
                                        :class="tipoImagen === 'file' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition duration-200">
                                        📁 Archivo Local
                                    </button>
                                    <button type="button" @click="tipoImagen = 'url'"
                                        :class="tipoImagen === 'url' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-slate-200'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition duration-200">
                                        🌐 Enlace URL
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-4 bg-slate-950 p-4 rounded-xl border border-slate-900">
                                <div class="w-20 h-20 rounded-xl bg-slate-900 overflow-hidden border border-slate-800 flex-shrink-0 shadow-inner">
                                    @if ($producto->image)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($producto->image, 'http') ? $producto->image : (\Illuminate\Support\Str::startsWith($producto->image, '/storage') ? $producto->image : asset('storage/' . $producto->image)) }}"
                                            alt="Imagen actual" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-amber-500/20 to-orange-600/20 flex items-center justify-center text-xl">
                                            🍔
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 w-full">
                                    <div x-show="tipoImagen === 'file'" x-transition>
                                        <p class="text-[11px] text-slate-500 mb-2">Sube un archivo nuevo solo si deseas reemplazar la imagen actual.</p>
                                        <input type="file" name="image_file"
                                            class="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-900 file:text-amber-400 hover:file:bg-slate-800 transition duration-200">
                                    </div>

                                    <div x-show="tipoImagen === 'url'" x-transition class="hidden" :class="{ 'hidden': tipoImagen !== 'url' }">
                                        <p class="text-[11px] text-slate-500 mb-2">Modifica o pega una nueva dirección URL para la imagen del producto.</p>
                                        <input type="url" name="image_url"
                                            value="{{ old('image_url', \Illuminate\Support\Str::startsWith($producto->image, 'http') ? $producto->image : '') }}"
                                            placeholder="https://ejemplo.com/imagenes/platillo.jpg"
                                            class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-white focus:outline-none focus:border-amber-500 transition duration-200 text-sm">
                                    </div>

                                    @error('image_file')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    @error('image_url')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ACCIONES -->
                    <div class="flex justify-end gap-4 pt-4 border-t border-slate-800/60">
                        <a href="{{ route('admin.dashboard') }}"
                            class="bg-slate-950 border border-slate-800 text-slate-400 px-6 py-3 rounded-2xl hover:text-white hover:border-slate-700 transition duration-200 no-underline text-sm flex items-center">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-6 py-3 rounded-2xl transition duration-200 shadow-md text-sm">
                            Actualizar Platillo
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- PREVENCIÓN DE ENVÍO ACCIDENTAL POR EL LECTOR DE CÓDIGO -->
    <script>
        document.getElementById('barcode').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Detiene el submit si se dispara desde este input
                console.log("Código capturado con éxito: " + this.value);
            }
        });
    </script>
</x-app-layout>