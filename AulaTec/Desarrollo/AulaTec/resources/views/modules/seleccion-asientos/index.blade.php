{{-- Vista principal para la selección de asientos --}}
{{-- Permite al usuario ver detalles de la clase, elegir asiento y confirmar la reserva --}}

{{-- Extiende el layout específico para selección de asientos --}}
@extends('layouts.asiento')

{{-- Título de la página para SEO y navegador --}}
@section('title', 'Selección de Asiento - Sistema de Reservas')

{{-- Contenido principal de la página --}}
@section('contenido')

{{-- ======= VARIABLE GLOBAL JAVASCRIPT ======= --}}
{{-- Variable para almacenar el asiento seleccionado, accesible desde cualquier componente --}}
<script>
    window.selectedSeat = null;
</script>

{{-- ======= CONTENEDOR PRINCIPAL ======= --}}
<div class="container mx-auto px-4 py-8">
    {{-- Componente de notificaciones para mostrar mensajes de éxito/error --}}
    @include('components.notification')
    
    {{-- Título principal de la página --}}
    <h1 class="text-2xl md:text-3xl font-bold mb-4 md:mb-6">Selección de Asiento</h1>

    {{-- Layout vertical con espaciado entre secciones --}}
    <div class="flex flex-col space-y-6">
        
        {{-- ======= SECCIÓN: DETALLES DE LA CLASE ======= --}}
        {{-- Información completa sobre la clase seleccionada --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            {{-- Header de la sección --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold">Detalles de la Clase</h2>
                <p class="text-sm text-gray-500">Información sobre la clase seleccionada</p>
            </div>
            
            {{-- Contenido de la sección --}}
            <div class="p-6">
                {{-- Componente con detalles formatados de la clase --}}
                @include('components.detalles-clase', ['classDetails' => $classDetails])

                {{-- ======= INDICADOR DE ASIENTO SELECCIONADO ======= --}}
                {{-- Solo visible cuando se ha seleccionado un asiento --}}
                <div x-data="{ selectedSeat: window.selectedSeat }" 
                     x-show="selectedSeat" 
                     {{-- Animación suave de aparición --}}
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="mt-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
                    <p class="font-medium">
                        Asiento seleccionado: <span x-text="selectedSeat" class="text-purple-600"></span>
                    </p>
                </div>
            </div>
        </div>

        {{-- ======= SECCIÓN: MAPA DEL AULA ======= --}}
        {{-- Interfaz interactiva para seleccionar asientos --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            {{-- Header de la sección --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold">Mapa del Aula</h2>
                <p class="text-sm text-gray-500">Selecciona un asiento disponible en el mapa</p>
            </div>
            
            {{-- Contenedor del mapa con ancho mínimo para móviles --}}
            <div class="p-6">
                <div class="min-w-[320px]">
                    {{-- Componente del mapa interactivo con asientos ocupados --}}
                    @include('components.mapa-aula', ['asientosOcupados' => $asientosOcupados])
                </div>
            </div>
            
            {{-- ======= LEYENDA DEL MAPA ======= --}}
            {{-- Explicación visual de los colores de los asientos --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-wrap gap-4">
                    {{-- Asiento disponible (verde) --}}
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded-sm mr-2"></div>
                        <span class="text-sm">Disponible</span>
                    </div>
                    {{-- Asiento ocupado (gris) --}}
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-gray-400 rounded-sm mr-2"></div>
                        <span class="text-sm">Ocupado</span>
                    </div>
                    {{-- Asiento seleccionado (púrpura) --}}
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-purple-600 rounded-sm mr-2"></div>
                        <span class="text-sm">Seleccionado</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======= SECCIÓN: BOTÓN DE CONFIRMACIÓN ======= --}}
        {{-- Acción final para procesar la reserva --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-6">
                {{-- Botón con lógica Alpine.js para confirmar reserva --}}
                <button 
                    x-data="{
                        // ======= ESTADO LOCAL =======
                        loading: false,         // Control de estado de carga durante el proceso
                        
                        /**
                         * Función asíncrona para confirmar la reserva
                         * Valida que hay asiento seleccionado y envía datos al backend
                         */
                        async confirmar() {
                            // Validar que se ha seleccionado un asiento
                            if (!window.selectedSeat) {
                                alert('Por favor, selecciona un asiento primero');
                                return;
                            }
                            
                            // Activar estado de carga
                            this.loading = true;
                            
                            try {
                                // ======= PETICIÓN AL BACKEND =======
                                // Enviar datos de la reserva al servidor
                                const response = await fetch(`/seleccion-asientos/{{ $token }}/confirmar`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                    },
                                    body: JSON.stringify({
                                        asiento: window.selectedSeat    // Asiento seleccionado
                                    })
                                });

                                // Parsear respuesta JSON del servidor
                                const data = await response.json();

                                // ======= MANEJO DE ERRORES =======
                                if (!response.ok) {
                                    // Disparar evento personalizado para mostrar notificación de error
                                    window.dispatchEvent(new CustomEvent('show-notification', {
                                        detail: {
                                            message: data.message,
                                            type: 'error'
                                        }
                                    }));
                                    throw new Error(data.message);
                                }

                                // ======= ÉXITO =======
                                // Redirigir a página de confirmación si todo está bien
                                if (data.status === 'success') {
                                    window.location.href = data.redirect;
                                }
                            } catch (error) {
                                console.error(error);
                            } finally {
                                // Siempre desactivar estado de carga al finalizar
                                this.loading = false;
                            }
                        }
                    }"
                    @click="confirmar"
                    {{-- Clases dinámicas según estado de carga --}}
                    :class="{
                        'opacity-75 cursor-wait': loading,                          // Estilo durante carga
                        'hover:from-purple-700 hover:to-cyan-600': !loading        // Hover solo cuando no está cargando
                    }"
                    class="w-full py-3 md:py-4 bg-gradient-to-r from-purple-600 to-cyan-500 text-white font-medium rounded-md transition-all duration-200"
                >
                    {{-- ======= TEXTO DEL BOTÓN ======= --}}
                    {{-- Estado normal: mostrar texto de confirmación --}}
                    <span x-show="!loading" x-text="window.selectedSeat ? 'Confirmar Reserva' : 'Confirmar Reserva'"></span>
                    
                    {{-- Estado de carga: mostrar spinner y texto de procesamiento --}}
                    <span x-show="loading" class="flex items-center justify-center">
                        {{-- Spinner animado --}}
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Procesando...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection