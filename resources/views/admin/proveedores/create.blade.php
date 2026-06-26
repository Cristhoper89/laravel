<x-app-layout>
    <div class="prv-wrapper min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-6">
            <div class="prv-card border rounded-3xl p-8 shadow-xl">

                <div class="mb-6">
                    <h3 class="prv-main-title text-2xl font-bold">Registrar Proveedor 🚚</h3>
                    <p class="prv-subtitle text-sm mt-1">Añade una nueva entidad comercial para la cadena de suministro.</p>
                </div>

                <form action="{{ route('proveedores.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block prv-label mb-2 text-sm font-medium">Nombre de la Empresa</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}"
                                placeholder="Ej. Carnes del Norte S.A.S."
                                class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                        </div>
                        <div>
                            <label class="block prv-label mb-2 text-sm font-medium">NIT (Número de Identificación Tributaria)</label>
                            <input type="text" name="nit" value="{{ old('nit') }}"
                                placeholder="Ej. 900.123.456-7"
                                class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block prv-label mb-2 text-sm font-medium">Nombre del Contacto</label>
                            <input type="text" name="contact_name" value="{{ old('contact_name') }}"
                                placeholder="Ej. Carlos Mendoza"
                                class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200"
                                required>
                        </div>
                        <div>
                            <label class="block prv-label mb-2 text-sm font-medium">Teléfono de Contacto</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                placeholder="Ej. +57 3001234567"
                                class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200">
                        </div>
                    </div>

                    <div>
                        <label class="block prv-label mb-2 text-sm font-medium">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="proveedor@empresa.com"
                            class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200">
                    </div>

                    <div>
                        <label class="block prv-label mb-2 text-sm font-medium">Dirección Comercial</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                            placeholder="Ej. Av. Principal 123, Pabellón B"
                            class="w-full prv-input border rounded-2xl p-3 focus:outline-none transition duration-200">
                    </div>

                    <div class="prv-divider-top pt-4">
                        <div class="prv-photo-section border rounded-2xl p-4 space-y-4"
                            x-data="{ tipoImagen: '{{ old('logo_url') || (isset($proveedor) && \Illuminate\Support\Str::startsWith($proveedor->image, 'http')) ? 'url' : 'file' }}' }">

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between prv-divider-bottom pb-3 gap-2">
                                <div>
                                    <label class="block prv-label text-sm font-medium">Logotipo o Imagen del Proveedor 🚚</label>
                                    <p class="prv-subtitle text-[11px] mt-0.5">Sube una imagen corporativa o vincula un enlace externo (JPG, PNG, WEBP).</p>
                                </div>

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

                            <div class="flex flex-col sm:flex-row items-center gap-4 prv-photo-preview-box p-4 rounded-xl border">

                                @if (isset($proveedor) && $proveedor->image)
                                    <div class="w-16 h-16 rounded-xl prv-avatar-placeholder overflow-hidden border flex-shrink-0 shadow-inner">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($proveedor->image, 'http') ? $proveedor->image : (\Illuminate\Support\Str::startsWith($proveedor->image, '/storage') ? $proveedor->image : asset('storage/' . $proveedor->image)) }}"
                                            alt="Logo actual" class="w-full h-full object-cover">
                                    </div>
                                @ graves
                                    <div class="w-16 h-16 rounded-xl prv-avatar-placeholder border flex items-center justify-center text-2xl flex-shrink-0">
                                        🏢
                                    </div>
                                @endif

                                <div class="flex-1 w-full">
                                    <div x-show="tipoImagen === 'file'" x-transition>
                                        <p class="text-[11px] prv-subtitle mb-2">Selecciona un archivo local.
                                            @if (isset($proveedor))
                                                Sube uno nuevo solo si deseas reemplazar el actual.
                                            @endif
                                        </p>

                                        <input type="file" :name="tipoImagen === 'file' ? 'image' : ''"
                                            :disabled="tipoImagen !== 'file'"
                                            class="w-full usr-file-input file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold transition duration-200">
                                    </div>

                                    <div x-show="tipoImagen === 'url'" x-transition class="hidden" :class="{ 'hidden': tipoImagen !== 'url' }">
                                        <p class="text-[11px] prv-subtitle mb-2">Pega la dirección URL directa del logotipo de la empresa proveedora.</p>

                                        <input type="url" :name="tipoImagen === 'url' ? 'image' : ''"
                                            :disabled="tipoImagen !== 'url'"
                                            value="{{ old('image', isset($proveedor) && \Illuminate\Support\Str::startsWith($proveedor->image, 'http') ? $proveedor->image : '') }}"
                                            placeholder="https://ejemplo.com/logos/proveedor.webp"
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
                            Guardar Proveedor
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>