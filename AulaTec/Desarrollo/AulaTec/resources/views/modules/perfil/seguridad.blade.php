{{-- Componente de seguridad para cambio de contraseña --}}
{{-- Formulario con validación en tiempo real y botones para mostrar/ocultar contraseñas --}}

{{-- Contenedor principal con Alpine.js para manejo de estado --}}
<div x-data="{ 
    // ======= ESTADO REACTIVO =======
    loading: false,                     // Estado de carga durante el envío del formulario
    formData: {                         // Datos del formulario sincronizados con inputs
        password: '',                   // Contraseña actual del usuario
        newPassword: '',                // Nueva contraseña deseada
        confirmPassword: ''             // Confirmación de la nueva contraseña
    },
    showPasswords: {                    // Control de visibilidad para cada campo de contraseña
        current: false,                 // Toggle para mostrar/ocultar contraseña actual
        new: false,                     // Toggle para mostrar/ocultar nueva contraseña
        confirm: false                  // Toggle para mostrar/ocultar confirmación
    }
}" class="bg-white rounded-lg border border-gray-200 shadow-sm">
    
    {{-- ======= HEADER DE LA SECCIÓN ======= --}}
    {{-- Título y descripción del componente --}}
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Seguridad de la Cuenta</h3>
        <p class="text-sm text-gray-500 mt-1">Actualiza tu contraseña y configura la seguridad de tu cuenta</p>
    </div>
    
    {{-- ======= CONTENIDO DEL FORMULARIO ======= --}}
    <div class="p-6">
        {{-- Formulario que envía datos al backend mediante método PUT --}}
        <form action="{{ route('perfil.update-password') }}" 
              method="POST" 
              {{-- Activar loading al enviar formulario --}}
              @submit="loading = true">
            @csrf
            @method('PUT')
            
            {{-- ======= CAMPOS DEL FORMULARIO ======= --}}
            <div class="space-y-6">
                
                {{-- ======= CONTRASEÑA ACTUAL ======= --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-900">
                        Contraseña actual <span class="text-red-500">*</span>
                    </label>
                    {{-- Contenedor con input y botón de mostrar/ocultar --}}
                    <div class="relative">
                        <input :type="showPasswords.current ? 'text' : 'password'" 
                               id="password" 
                               name="password"
                               x-model="formData.password" 
                               required
                               {{-- Clases dinámicas para estados de error --}}
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm bg-white text-gray-900 placeholder-gray-500 focus:border-purple-500 transition-colors @error('password') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Ingresa tu contraseña actual">
                        {{-- Botón toggle para mostrar/ocultar contraseña --}}
                        <button type="button" 
                                @click="showPasswords.current = !showPasswords.current"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                            {{-- Icono de ojo (mostrar) --}}
                            <svg x-show="!showPasswords.current" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{-- Icono de ojo tachado (ocultar) --}}
                            <svg x-show="showPasswords.current" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                    {{-- Mensaje de error del backend para contraseña actual --}}
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            {{-- Icono de advertencia --}}
                            <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- ======= NUEVA CONTRASEÑA ======= --}}
                <div class="space-y-2">
                    <label for="newPassword" class="block text-sm font-medium text-gray-900">
                        Nueva contraseña <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPasswords.new ? 'text' : 'password'" 
                               id="newPassword" 
                               name="newPassword"
                               x-model="formData.newPassword" 
                               required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm bg-white text-gray-900 placeholder-gray-500 focus:border-purple-500 transition-colors @error('newPassword') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Mínimo 8 caracteres">
                        {{-- Botón toggle para nueva contraseña --}}
                        <button type="button" 
                                @click="showPasswords.new = !showPasswords.new"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                            {{-- Iconos de mostrar/ocultar (mismos que el campo anterior) --}}
                            <svg x-show="!showPasswords.new" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPasswords.new" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                    {{-- Mensaje de error del backend para nueva contraseña --}}
                    @error('newPassword')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    {{-- Texto de ayuda con requisitos de contraseña --}}
                    <p class="text-xs text-gray-500">La contraseña debe tener al menos 8 caracteres</p>
                </div>

                {{-- ======= CONFIRMAR NUEVA CONTRASEÑA ======= --}}
                <div class="space-y-2">
                    <label for="confirmPassword" class="block text-sm font-medium text-gray-900">
                        Confirmar nueva contraseña <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPasswords.confirm ? 'text' : 'password'" 
                               id="confirmPassword" 
                               name="confirmPassword"
                               x-model="formData.confirmPassword" 
                               required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm bg-white text-gray-900 placeholder-gray-500 focus:border-purple-500 transition-colors @error('confirmPassword') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Confirma tu nueva contraseña">
                        {{-- Botón toggle para confirmación de contraseña --}}
                        <button type="button" 
                                @click="showPasswords.confirm = !showPasswords.confirm"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                            {{-- Iconos de mostrar/ocultar (mismos que los campos anteriores) --}}
                            <svg x-show="!showPasswords.confirm" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPasswords.confirm" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                    {{-- Mensaje de error del backend para confirmación de contraseña --}}
                    @error('confirmPassword')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- ======= BOTÓN DE ENVÍO ======= --}}
            {{-- Separador visual y botón de acción --}}
            <div class="border-t border-gray-200 mt-6 pt-6 flex justify-end">
                <button type="submit" 
                        {{-- Validación frontend: deshabilitar si faltan campos o está cargando --}}
                        :disabled="loading || !formData.password || !formData.newPassword || !formData.confirmPassword" 
                        class="inline-flex items-center justify-center px-6 py-2 bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white rounded-lg text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{-- Estado normal: mostrar texto e icono de candado --}}
                    <span x-show="!loading" class="flex items-center">
                        {{-- Icono de candado cerrado --}}
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Cambiar Contraseña
                    </span>
                    {{-- Estado de carga: mostrar spinner y texto de procesamiento --}}
                    <span x-show="loading" class="flex items-center">
                        {{-- Spinner animado --}}
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Actualizando...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>