{{-- Componente de perfil de usuario para editar información personal --}}
{{-- Permite visualizar y modificar datos del usuario con interfaz editable --}}

{{-- Contenedor principal con Alpine.js para manejo de estado de edición --}}
<div x-data="{ 
    // ======= ESTADO REACTIVO =======
    editando: false,                                    // Control para modo edición/visualización
    formData: {                                         // Datos del formulario sincronizados con la base de datos
        nombre: '{{ $usuario->nombre }}',               // Nombre del usuario desde BD
        apellido: '{{ $usuario->apellido }}',           // Apellidos del usuario desde BD
        email: '{{ $usuario->email }}',                 // Email del usuario desde BD
        numero_matricula: '{{ $usuario->numero_matricula }}', // Matrícula del usuario desde BD
        carrera: '{{ $usuario->carrera ?? 'Carrera de Ejemplo' }}' // Carrera con fallback si es null
    }
}" class="bg-white rounded-lg border border-gray-200 shadow-sm">
    
    {{-- ======= HEADER CON TÍTULO Y BOTÓN DE EDICIÓN ======= --}}
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-start">
            {{-- Información descriptiva del formulario --}}
            <div class="flex-1 mr-4">
                <h3 class="text-lg font-semibold text-gray-900">Información Personal</h3>
                <p class="text-sm text-gray-500 mt-1">Gestiona tu información personal y de contacto</p>
            </div>
            {{-- Botón toggle para activar/desactivar modo edición --}}
            <button @click="editando = !editando" 
                    class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 flex-shrink-0">
                {{-- Icono de editar (mostrado cuando NO está editando) --}}
                <svg x-show="!editando" class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{-- Icono de cerrar (mostrado cuando SÍ está editando) --}}
                <svg x-show="editando" class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{-- Texto dinámico del botón según el estado --}}
                <span x-text="editando ? 'Cancelar' : 'Editar'"></span>
            </button>
        </div>
    </div>

    {{-- ======= CONTENIDO DEL FORMULARIO ======= --}}
    <div class="p-6">
        {{-- Formulario que envía datos al backend mediante método PUT --}}
        <form action="{{ route('perfil.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            {{-- Grid responsivo para organizar los campos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- ======= CAMPO NOMBRE ======= --}}
                <div class="space-y-2">
                    <label for="nombre" class="block text-sm font-medium text-gray-900">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           :disabled="!editando"                           {{-- Solo editable cuando editando=true --}}
                           x-model="formData.nombre"                       {{-- Sincronizado con estado Alpine --}}
                           required
                           {{-- Clases dinámicas según estado de edición --}}
                           :class="editando ? 'bg-white text-gray-900 border-gray-300 focus:border-purple-500 focus:ring-purple-500' : 'bg-gray-50 text-gray-500 border-gray-200 cursor-not-allowed'"
                           class="w-full rounded-lg border px-3 py-2 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2">
                </div>

                {{-- ======= CAMPO APELLIDOS ======= --}}
                <div class="space-y-2">
                    <label for="apellido" class="block text-sm font-medium text-gray-900">
                        Apellidos <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="apellido" 
                           name="apellido"
                           :disabled="!editando"
                           x-model="formData.apellido" 
                           required
                           :class="editando ? 'bg-white text-gray-900 border-gray-300 focus:border-purple-500 focus:ring-purple-500' : 'bg-gray-50 text-gray-500 border-gray-200 cursor-not-allowed'"
                           class="w-full rounded-lg border px-3 py-2 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2">
                </div>

                {{-- ======= CAMPO EMAIL ======= --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-900">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email"
                           :disabled="!editando"
                           x-model="formData.email" 
                           required
                           :class="editando ? 'bg-white text-gray-900 border-gray-300 focus:border-purple-500 focus:ring-purple-500' : 'bg-gray-50 text-gray-500 border-gray-200 cursor-not-allowed'"
                           class="w-full rounded-lg border px-3 py-2 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2">
                </div>

                {{-- ======= CAMPO MATRÍCULA ======= --}}
                <div class="space-y-2">
                    <label for="numero_matricula" class="block text-sm font-medium text-gray-900">
                        Matrícula <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="numero_matricula" 
                           name="numero_matricula"
                           :disabled="!editando"
                           x-model="formData.numero_matricula" 
                           required
                           :class="editando ? 'bg-white text-gray-900 border-gray-300 focus:border-purple-500 focus:ring-purple-500' : 'bg-gray-50 text-gray-500 border-gray-200 cursor-not-allowed'"
                           class="w-full rounded-lg border px-3 py-2 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2">
                </div>

                {{-- ======= CAMPO CARRERA ======= --}}
                {{-- Ocupa 2 columnas en desktop para mayor espacio --}}
                <div class="space-y-2 md:col-span-2">
                    <label for="carrera" class="block text-sm font-medium text-gray-900">
                        Carrera
                    </label>
                    <input type="text" 
                           id="carrera" 
                           name="carrera"
                           :disabled="!editando"
                           x-model="formData.carrera" 
                           :class="editando ? 'bg-white text-gray-900 border-gray-300 focus:border-purple-500 focus:ring-purple-500' : 'bg-gray-50 text-gray-500 border-gray-200 cursor-not-allowed'"
                           class="w-full rounded-lg border px-3 py-2 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                           placeholder="Ingresa tu carrera">
                </div>
            </div>

            {{-- ======= BOTÓN DE GUARDAR CAMBIOS ======= --}}
            {{-- Solo visible cuando está en modo edición --}}
            <div x-show="editando" 
                 {{-- Animación de entrada suave --}}
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="border-t border-gray-200 mt-6 pt-6 flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-2 bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white rounded-lg text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200">
                    {{-- Icono de check para confirmación --}}
                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>