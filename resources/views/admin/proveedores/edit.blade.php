<x-app-layout>
    <div class="prv-wrapper min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-6">
            <div class="prv-card border rounded-3xl p-8 shadow-xl">

                <div class="mb-6">
                    <h3 class="text-2xl font-bold prv-main-title">Editar Proveedor 📝</h3>
                    <p class="prv-subtitle text-sm mt-1">Modificando los datos de: <span class="font-semibold"
                            style="color: var(--color-success);">{{ $proveedor->company_name }}</span></p>
                </div>

                <form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block prv-label mb-2 text-sm font-medium">Nombre de la Empresa</label>
                            <input type="text" name="company_name"
                                value="{{ old('company_name', $proveedor->company_name) }}"
                                class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                            @error('company_name')
                                <p class="prv-error-text text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block prv-label mb-2 text-sm font-medium">NIT (Número de Identificación
                                Tributaria)</label>
                            <input type="text" name="nit" value="{{ old('nit', $proveedor->nit) }}"
                                class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                            @error('nit')
                                <p class="prv-error-text text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block prv-label mb-2 text-sm font-medium">Nombre del Contacto</label>
                            <input type="text" name="contact_name"
                                value="{{ old('contact_name', $proveedor->contact_name) }}"
                                class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                            @error('contact_name')
                                <p class="prv-error-text text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block prv-label mb-2 text-sm font-medium">Teléfono</label>
                            <input type="text" name="phone" value="{{ old('phone', $proveedor->phone) }}"
                                class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200">
                            @error('phone')
                                <p class="prv-error-text text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block prv-label mb-2 text-sm font-medium">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $proveedor->email) }}"
                            class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200">
                        @error('email')
                            <p class="prv-error-text text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block prv-label mb-2 text-sm font-medium">Dirección Comercial</label>
                        <input type="text" name="address" value="{{ old('address', $proveedor->address) }}"
                            class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200">
                        @error('address')
                            <p class="prv-error-text text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="prv-divider-top pt-4">
                        <div class="prv-photo-section border rounded-2xl p-4 space-y-4" x-data="{ tipoImagen: '{{ \Illuminate\Support\Str::startsWith($proveedor->image, 'http') ? 'url' : 'file' }}' }">

                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-between prv-divider-bottom pb-3 gap-2">
                                <div>
                                    <label class="block prv-label text-sm font-medium">Logotipo o Imagen del Proveedor
                                        🖼️</label>
                                    <p class="prv-subtitle text-[11px] mt-0.5">Modifica o reemplaza el recurso visual
                                        del proveedor.</p>
                                </div>

                                <div class="flex prv-input border p-1 rounded-xl gap-1 self-start sm:self-auto">
                                    <button type="button" @click="tipoImagen = 'file'"
                                        :class="tipoImagen === 'file' ? 'bg-emerald-500 text-slate-950 font-bold' :
                                            'prv-subtitle hover:text-white'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition duration-200">
                                        📁 Archivo Local
                                    </button>
                                    <button type="button" @click="tipoImagen = 'url'"
                                        :class="tipoImagen === 'url' ? 'bg-emerald-500 text-slate-950 font-bold' :
                                            'prv-subtitle hover:text-white'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition duration-200">
                                        🌐 Enlace URL
                                    </button>
                                </div>
                            </div>

                            <div
                                class="flex flex-col sm:flex-row items-center gap-4 prv-wrapper p-4 rounded-xl border border-slate-900">

                                <div
                                    class="w-16 h-16 rounded-xl overflow-hidden border prv-photo-preview-box flex-shrink-0 flex items-center justify-center shadow-inner">
                                    @if ($proveedor->image)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($proveedor->image, 'http') ? $proveedor->image : (\Illuminate\Support\Str::startsWith($proveedor->image, '/storage') ? $proveedor->image : asset('storage/' . $proveedor->image)) }}"
                                            class="w-full h-full object-cover" alt="Logo actual">
                                    @else
                                        <div class="prv-avatar-placeholder text-xl">
                                            🚚
                                        </div>
                                    @endif
                                </div>

                                <div x-show="tipoImagen === 'file'" x-transition>
                                    <p class="text-[11px] prv-subtitle mb-2">Sube un archivo nuevo solo si deseas
                                        reemplazar el logotipo actual.</p>
                                    <input type="file" :name="tipoImagen === 'file' ? 'image' : ''"
                                        class="w-full prv-file-input transition duration-200">
                                </div>

                                <div x-show="tipoImagen === 'url'" x-transition class="hidden"
                                    :class="{ 'hidden': tipoImagen !== 'url' }">
                                    <p class="text-[11px] prv-subtitle mb-2">Modifica o introduce una nueva dirección
                                        URL directa para el logotipo.</p>
                                    <input type="url" :name="tipoImagen === 'url' ? 'image' : ''"
                                        value="{{ old('image', \Illuminate\Support\Str::startsWith($proveedor->image, 'http') ? $proveedor->image : '') }}"
                                        placeholder="https://ejemplo.com/logos/proveedor.jpg"
                                        class="w-full prv-input border rounded-xl p-2.5 focus:outline-none transition duration-200 text-sm">
                                </div>

                                @error('image')
                                    <p class="prv-error-text text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>
            </div>

            <div class="flex justify-end gap-4 pt-4 prv-divider-top">
                <a href="{{ route('proveedores.index') }}"
                    class="prv-btn-cancel border px-6 py-3 rounded-2xl transition duration-200 no-underline text-sm flex items-center">
                    Cancelar
                </a>
                <button type="submit"
                    class="prv-btn-submit font-bold px-6 py-3 rounded-2xl transition duration-200 shadow-md text-sm">
                    Actualizar Proveedor
                </button>
            </div>
            </form>

        </div>
    </div>
    </div>
</x-app-layout>
