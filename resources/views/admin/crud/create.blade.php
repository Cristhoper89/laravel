<x-app-layout>
    <div class="usr-wrapper min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-6">
            
            <div class="usr-card border rounded-3xl p-8 shadow-xl">
                
                <div class="mb-6">
                    <h3 class="usr-main-title text-2xl font-bold">Registrar Nuevo Usuario 👤</h3>
                    <p class="usr-subtitle text-sm mt-1">Crea una nueva cuenta de acceso asignándole un rol específico dentro de la plataforma.</p>
                </div>
                
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf 
                    
                    <div>
                        <label class="block usr-label mb-2 text-sm font-medium">Nombre Completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej. Juan Pérez"
                               class="w-full usr-input border rounded-2xl p-3 text-sm transition duration-200" required>
                        @error('name')
                            <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block usr-label mb-2 text-sm font-medium">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="juan@ejemplo.com"
                                   class="w-full usr-input border rounded-2xl p-3 text-sm transition duration-200" required>
                            @error('email')
                                <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block usr-label mb-2 text-sm font-medium">Rol del Sistema</label>
                            <select name="role" class="w-full usr-input border rounded-2xl p-3 text-sm transition duration-200">
                                <option value="cliente" {{ old('role') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('role')
                                <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block usr-label mb-2 text-sm font-medium">Contraseña de Acceso</label>
                        <input type="password" name="password" placeholder="Mínimo 8 caracteres"
                               class="w-full usr-input border rounded-2xl p-3 text-sm transition duration-200" required>
                        @error('password')
                            <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="usr-divider-top pt-4">
                        <div class="usr-subcard border rounded-2xl p-4 space-y-4" 
                             x-data="{ tipoImagen: '{{ old('photo') && filter_var(old('photo'), FILTER_VALIDATE_URL) ? 'url' : 'file' }}' }">
                            
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between usr-divider-bottom pb-3 gap-2">
                                <div>
                                    <label class="block usr-label text-sm font-medium">Foto de Perfil (Opcional) 👤</label>
                                    <p class="usr-helper-text text-[11px] mt-0.5">Sube un avatar o vincula una imagen externa (JPG, PNG, GIF).</p>
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

                            <div class="flex flex-col sm:flex-row items-center gap-4 usr-preview-box p-4 rounded-xl border">
                                
                                <div class="w-16 h-16 rounded-full usr-avatar-placeholder border flex items-center justify-center text-2xl flex-shrink-0">
                                    👤
                                </div>
                                
                                <div class="flex-1 w-full">
                                    <div x-show="tipoImagen === 'file'" x-transition>
                                        <p class="text-[11px] usr-helper-text mb-2">Selecciona un archivo de imagen desde tu dispositivo.</p>
                                        <input type="file" 
                                               :name="tipoImagen === 'file' ? 'photo' : ''" 
                                               class="w-full usr-file-input text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold transition duration-200">
                                    </div>

                                    <div x-show="tipoImagen === 'url'" x-transition class="hidden" :class="{ 'hidden': tipoImagen !== 'url' }">
                                        <p class="text-[11px] usr-helper-text mb-2">Pega la dirección URL directa de la foto de perfil.</p>
                                        <input type="url" 
                                               :name="tipoImagen === 'url' ? 'photo' : ''"
                                               value="{{ old('photo') && filter_var(old('photo'), FILTER_VALIDATE_URL) ? old('photo') : '' }}" 
                                               placeholder="https://ejemplo.com/avatares/usuario.jpg"
                                               class="w-full usr-input border rounded-xl p-2.5 transition duration-200 text-sm">
                                    </div>
                                    
                                    @error('photo')
                                        <p class="usr-text-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4 usr-divider-top">
                        <a href="{{ route('users.index') }}" 
                           class="usr-btn-cancel border px-6 py-3 rounded-2xl transition duration-200">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="usr-btn-submit font-bold px-6 py-3 rounded-2xl transition duration-200 shadow-md">
                            Crear Usuario
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>