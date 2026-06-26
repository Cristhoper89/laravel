<x-app-layout>
    <div class="usr-wrapper min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-6">
            <div class="usr-card border rounded-3xl p-8 shadow-xl">
                
                <div class="mb-6">
                    <h3 class="usr-main-title text-2xl font-bold">Editar Usuario 👤</h3>
                    <p class="usr-subtitle text-sm mt-1">Modifica el perfil de: <span class="usr-highlight font-semibold">{{ $user->name }}</span></p>
                </div>
                
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block usr-label mb-2 text-sm font-medium">Nombre Completo</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                               class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200" required>
                        @error('name')
                            <p class="usr-error-text text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block usr-label mb-2 text-sm font-medium">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200" required>
                            @error('email')
                                <p class="usr-error-text text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block usr-label mb-2 text-sm font-medium">Rol del Sistema</label>
                            <select name="role" class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200">
                                <option value="cliente" {{ old('role', $user->role) == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('role')
                                <p class="usr-error-text text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block usr-label mb-2 text-sm font-medium">Contraseña (Opcional)</label>
                        <input type="password" name="password" placeholder="Dejar en blanco para mantener la actual"
                               class="w-full usr-input border rounded-2xl p-3 focus:outline-none transition duration-200">
                        @error('password')
                            <p class="usr-error-text text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="usr-divider-top pt-4">
                        <div class="usr-photo-section border rounded-2xl p-4 space-y-4" 
                             x-data="{ tipoImagen: '{{ \Illuminate\Support\Str::startsWith($user->photo, 'http') ? 'url' : 'file' }}' }">
                            
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between usr-divider-bottom pb-3 gap-2">
                                <div>
                                    <label class="block usr-label text-sm font-medium">Foto de Perfil 🖼️</label>
                                    <p class="usr-subtitle text-[11px] mt-0.5">Elige si deseas usar un archivo local o un enlace web externo.</p>
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

                            <div class="flex flex-col sm:flex-row items-center gap-4 usr-photo-preview-box p-4 rounded-xl border">
                                
                                <div class="w-16 h-16 rounded-full usr-avatar-placeholder overflow-hidden border flex-shrink-0 shadow-inner">
                                    @if($user->photo)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($user->photo, 'http') ? $user->photo : (\Illuminate\Support\Str::startsWith($user->photo, '/storage') ? $user->photo : asset('storage/' . $user->photo)) }}" 
                                             alt="Avatar actual" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full usr-avatar-default flex items-center justify-center font-bold text-white uppercase">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-1 w-full">
                                    <div x-show="tipoImagen === 'file'" x-transition>
                                        <p class="text-[11px] usr-subtitle mb-2">Sube un archivo nuevo solo si deseas reemplazar la imagen de perfil actual.</p>
                                        <input type="file" 
                                               :name="tipoImagen === 'file' ? 'photo' : ''" 
                                               class="w-full usr-file-input file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold transition duration-200">
                                    </div>

                                    <div x-show="tipoImagen === 'url'" x-transition class="hidden" :class="{ 'hidden': tipoImagen !== 'url' }">
                                        <p class="text-[11px] usr-subtitle mb-2">Modifica o introduce una nueva dirección web directa para el avatar.</p>
                                        <input type="url" 
                                               :name="tipoImagen === 'url' ? 'photo' : ''"
                                               value="{{ old('photo', \Illuminate\Support\Str::startsWith($user->photo, 'http') ? $user->photo : '') }}" 
                                               placeholder="https://ejemplo.com/avatares/usuario.jpg"
                                               class="w-full usr-input border rounded-xl p-2.5 focus:outline-none transition duration-200 text-sm">
                                    </div>
                                    
                                    @error('photo')
                                        <p class="usr-error-text text-xs mt-1">{{ $message }}</p>
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
                            Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>