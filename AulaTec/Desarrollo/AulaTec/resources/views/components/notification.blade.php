{{-- Componente global de notificaciones para mostrar mensajes de éxito/error --}}
{{-- Se incluye en todas las páginas para feedback visual al usuario --}}

{{-- ======= CONTENEDOR PRINCIPAL CON ALPINE.JS ======= --}}
{{-- Componente reactivo con estado y funciones para gestionar notificaciones --}}
<div x-data="{ 
    // ======= ESTADO REACTIVO =======
    show: false,        // Controla si la notificación está visible
    message: '',        // Texto del mensaje a mostrar
    type: 'success',    // Tipo de notificación: 'success' o 'error'
    
    // ======= FUNCIÓN: CONTROL DE SCROLL ======= 
    // Bloquea/desbloquea el scroll del body cuando se muestra la notificación
    toggleScroll() {
        if (this.show) {
            // Bloquear scroll cuando se muestra la notificación
            document.body.style.overflow = 'hidden';
        } else {
            // Restaurar scroll cuando se oculta
            document.body.style.overflow = 'auto';
        }
    }
}" 
    {{-- Solo mostrar cuando show=true --}}
    x-show="show"
    
    {{-- ======= INICIALIZACIÓN DEL COMPONENTE ======= --}}
    x-init="
        // ======= LISTENER PARA EVENTOS JAVASCRIPT =======
        // Escuchar eventos personalizados disparados desde JavaScript
        window.addEventListener('show-notification', (event) => {
            type = event.detail.type;       // Extraer tipo del evento
            message = event.detail.message; // Extraer mensaje del evento
            show = true;                     // Mostrar notificación
            toggleScroll();                  // Bloquear scroll
            
            // ======= AUTO-OCULTAR DESPUÉS DE 1.5 SEGUNDOS =======
            setTimeout(() => {
                show = false;                // Ocultar notificación
                toggleScroll();              // Restaurar scroll
            }, 1500);
        });

        {{-- ======= NOTIFICACIONES DESDE SESSION FLASH ======= --}}
        {{-- Mostrar mensajes enviados desde el backend vía redirect()->with() --}}
        
        {{-- Mensaje de éxito desde Laravel --}}
        @if (session()->has('success'))
            type = 'success';                        // Tipo verde con check
            message = '{{ session('success') }}';    // Mensaje desde session
            show = true;                             // Mostrar inmediatamente
            toggleScroll();                          // Bloquear scroll
            
            // Auto-ocultar después de 1.5 segundos
            setTimeout(() => { 
                show = false;
                toggleScroll();
            }, 1500);
        @endif
        
        {{-- Mensaje de error desde Laravel --}}
        @if (session()->has('error'))
            type = 'error';                         // Tipo rojo con X
            message = '{{ session('error') }}';     // Mensaje desde session
            show = true;                            // Mostrar inmediatamente
            toggleScroll();                         // Bloquear scroll
            
            // Auto-ocultar después de 1.5 segundos
            setTimeout(() => {
                show = false;
                toggleScroll();
            }, 1500);
        @endif
    "
    
    {{-- ======= ANIMACIONES DE ENTRADA ======= --}}
    {{-- Transición suave al aparecer la notificación --}}
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"  {{-- Estado inicial: invisible y pequeña --}}
    x-transition:enter-end="opacity-100 transform scale-100" {{-- Estado final: visible y tamaño normal --}}
    
    {{-- ======= ANIMACIONES DE SALIDA ======= --}}
    {{-- Transición suave al desaparecer la notificación --}}
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform scale-100" {{-- Estado inicial: visible y normal --}}
    x-transition:leave-end="opacity-0 transform scale-90"      {{-- Estado final: invisible y pequeña --}}
    
    {{-- ======= OVERLAY DE FONDO ======= --}}
    {{-- Cubre toda la pantalla con efecto blur --}}
    class="fixed inset-0 flex items-center justify-center z-50"
    style="backdrop-filter: blur(8px);"
>    
    {{-- ======= TARJETA DE NOTIFICACIÓN ======= --}}
    {{-- Contenedor visual con colores dinámicos según el tipo --}}
    <div :class="{
        // ======= ESTILO PARA ÉXITO (VERDE) =======
        'bg-green-50/90 text-green-800 border-green-200': type === 'success',
        
        // ======= ESTILO PARA ERROR (ROJO) =======
        'bg-red-50/90 text-red-800 border-red-200': type === 'error'
    }" class="relative rounded-lg border p-4 flex items-center shadow-lg max-w-sm mx-auto backdrop-blur-sm">
        
        {{-- ======= CONTENEDOR DEL ICONO ======= --}}
        {{-- Icono con colores dinámicos según el tipo --}}
        <div :class="{
            'text-green-400': type === 'success',  // Verde para éxito
            'text-red-400': type === 'error'       // Rojo para error
        }" class="flex-shrink-0">
            
            {{-- ======= ICONO DE ÉXITO ======= --}}
            {{-- Círculo con check mark --}}
            <template x-if="type === 'success'">
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                    {{-- Path del círculo con check: checkmark dentro de círculo --}}
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </template>
            
            {{-- ======= ICONO DE ERROR ======= --}}
            {{-- Círculo con X --}}
            <template x-if="type === 'error'">
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                    {{-- Path del círculo con X: cruz dentro de círculo --}}
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </template>
        </div>
        
        {{-- ======= TEXTO DEL MENSAJE ======= --}}
        {{-- Contenido dinámico del mensaje con margen izquierdo --}}
        <p class="ml-3 text-sm font-medium" x-text="message"></p>
    </div>
</div>