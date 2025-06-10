{{-- Componente para mostrar y gestionar las reservas activas del usuario --}}
{{-- Permite ver detalles de reservas y cancelarlas con confirmación de seguridad --}}

{{-- Contenedor principal con Alpine.js --}}
<div x-data="reservasActivas()" class="reservas-activas">
    
    {{-- ======= ESTADO DE CARGA ======= --}}
    {{-- Spinner que se muestra mientras cargan las reservas --}}
    <template x-if="loading">
        <div class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
        </div>
    </template>
    
    {{-- ======= ESTADO VACÍO ======= --}}
    {{-- Mensaje cuando el usuario no tiene reservas activas --}}
    <template x-if="!loading && reservas.length === 0">
        <div class="text-center py-12">
            {{-- Icono de advertencia --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <h3 class="text-xl font-semibold mb-2">No tienes reservas activas</h3>
            <p class="text-gray-500 mb-6">Realiza una reserva para ver tus clases programadas</p>
            {{-- Enlace para crear nueva reserva --}}
            <a href="/dashboard" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-md transition-colors">
                Reservar una clase
            </a>
        </div>
    </template>
    
    {{-- ======= LISTA DE RESERVAS ACTIVAS ======= --}}
    {{-- Solo se muestra si hay reservas para mostrar --}}
    <template x-if="!loading && reservas.length > 0">
        <div class="space-y-4">
            {{-- Iteración por cada reserva del usuario --}}
            <template x-for="(reserva, index) in reservas" :key="index">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    
                    {{-- ======= INFORMACIÓN DE LA RESERVA ======= --}}
                    <div class="p-6">
                        {{-- Nombre de la clase/asignatura --}}
                        <h3 class="text-xl font-semibold mb-4" x-text="reserva.clase"></h3>

                        {{-- Grid responsivo con detalles de la reserva --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- COLUMNA IZQUIERDA: Fecha y hora --}}
                            <div class="space-y-2">
                                {{-- Fecha de la clase --}}
                                <div class="flex items-center text-sm">
                                    {{-- Icono de calendario --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <span x-text="formatDate(reserva.fecha)"></span>
                                </div>

                                {{-- Horario de la clase con duración --}}
                                <div class="flex items-center text-sm">
                                    {{-- Icono de reloj --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    {{-- Hora inicio - hora fin (duración en minutos) --}}
                                    <span x-text="formatTime(reserva.hora) + ' - ' + formatTime(reserva.hora_fin) +' (' + reserva.duracion + ' minutos)'"></span>
                                </div>
                            </div>

                            {{-- COLUMNA DERECHA: Ubicación y asiento --}}
                            <div class="space-y-2">
                                {{-- Aula donde se imparte --}}
                                <div class="flex items-center text-sm">
                                    {{-- Icono de ubicación --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <span x-text="reserva.aula"></span>
                                </div>

                                {{-- Número de asiento asignado --}}
                                <div class="flex items-center text-sm font-medium text-purple-600">
                                    <span x-text="'Asiento: ' + reserva.asiento"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- ======= ACCIONES DE LA RESERVA ======= --}}
                    {{-- Footer con botones para gestionar la reserva --}}
                    <div class="bg-gray-50 px-6 py-3 flex justify-between">
                        {{-- Botón para cancelar la reserva --}}
                        <button 
                            @click="abrirModalCancelacion(reserva.id)" 
                            class="px-4 py-2 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 transition-colors">
                            Cancelar Reserva
                        </button>
                        {{-- Enlace para ver detalles completos --}}
                        <a :href="`/seleccion-asientos/confirmacion/${reserva.id}`">
                            <button class="px-4 py-2 text-sm bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors">
                                Ver Detalles
                            </button>
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- ======= MODAL DE CONFIRMACIÓN DE CANCELACIÓN ======= --}}
    {{-- Modal con temporizador de seguridad para evitar cancelaciones accidentales --}}
    <div 
        x-show="modalCancelacion" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        {{-- Controlar scroll del body cuando el modal está abierto --}}
        x-init="$watch('modalCancelacion', value => {
            if (value) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        })">
        
        {{-- Overlay de fondo --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        {{-- Contenedor centrado del modal --}}
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
            {{-- Overlay clickeable para cerrar --}}
            <div 
                x-show="modalCancelacion" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity" 
                @click="cerrarModalCancelacion">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            {{-- Contenido principal del modal --}}
            <div 
                x-show="modalCancelacion" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                {{-- CONTENIDO DEL MODAL --}}
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        {{-- Icono de advertencia --}}
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        {{-- Texto del modal --}}
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Cancelar reserva
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que deseas cancelar esta reserva?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- BOTONES DEL MODAL --}}
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    {{-- Botón Confirmar con temporizador de seguridad --}}
                    <button 
                        type="button" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        {{-- Deshabilitar durante los primeros 5 segundos --}}
                        :disabled="tiempoRestante > 0"
                        @click="confirmarCancelacion">
                        {{-- Mostrar contador regresivo --}}
                        <span x-show="tiempoRestante > 0" x-text="tiempoRestante + 's'"></span>
                        {{-- Mostrar "Confirmar" cuando se puede clickear --}}
                        <span x-show="tiempoRestante === 0">Confirmar</span>
                    </button>
                    {{-- Botón Cancelar --}}
                    <button 
                        type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        @click="cerrarModalCancelacion">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======= JAVASCRIPT CON ALPINE.JS ======= --}}
<script>
/**
 * Componente Alpine.js para gestionar las reservas activas del usuario
 * 
 * Funcionalidades:
 * - Cargar y mostrar reservas activas
 * - Cancelar reservas con confirmación de seguridad
 * - Temporizador para prevenir cancelaciones accidentales
 * - Formateo de fechas y horas
 */
function reservasActivas() {
    return {
        // ======= ESTADO REACTIVO =======
        loading: true,                  // Estado de carga inicial
        reservas: [],                   // Array de reservas activas del usuario
        modalCancelacion: false,        // Visibilidad del modal de cancelación
        reservaIdSeleccionada: null,    // ID de la reserva a cancelar
        tiempoRestante: 5,              // Contador regresivo para habilitar confirmación
        temporizador: null,             // Referencia al intervalo del temporizador

        /**
         * Inicializa el componente cargando las reservas
         * Se ejecuta automáticamente al montar el componente
         */
        init() {
            this.cargarReservas();
        },

        /**
         * Carga las reservas activas del usuario desde la API
         * Maneja estados de carga y errores
         */
        async cargarReservas() {
            try {
                const response = await fetch('/api/user/reservations');
                if (!response.ok) throw new Error('Error al cargar las reservas');

                this.reservas = await response.json();
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        /**
         * Abre el modal de confirmación para cancelar una reserva
         * Inicia el temporizador de seguridad de 5 segundos
         * @param {number} reservaId - ID de la reserva a cancelar
         */
        abrirModalCancelacion(reservaId) {
            this.reservaIdSeleccionada = reservaId;
            this.tiempoRestante = 5;
            this.modalCancelacion = true;

            // Iniciar el temporizador de cuenta regresiva
            this.temporizador = setInterval(() => {
                if (this.tiempoRestante > 0) {
                    this.tiempoRestante--;
                } else {
                    // Detener temporizador cuando llega a 0
                    clearInterval(this.temporizador);
                }
            }, 1000);
        },

        /**
         * Cierra el modal de cancelación y limpia el estado
         * Detiene el temporizador si está activo
         */
        cerrarModalCancelacion() {
            this.modalCancelacion = false;
            this.reservaIdSeleccionada = null;

            // Detener el temporizador si está activo
            if (this.temporizador) {
                clearInterval(this.temporizador);
                this.temporizador = null;
            }
        },

        /**
         * Confirma y ejecuta la cancelación de la reserva
         * Solo permite proceder si el temporizador ha llegado a 0
         */
        async confirmarCancelacion() {
            // Validar que el temporizador de seguridad haya terminado
            if (this.tiempoRestante > 0) return;

            this.loading = true;
            this.modalCancelacion = false;

            try {
                const response = await fetch(`/reservations/${this.reservaIdSeleccionada}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) throw new Error('Error al cancelar la reserva');

                // Recargar la lista de reservas para reflejar los cambios
                await this.cargarReservas();
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
                this.reservaIdSeleccionada = null;

                // Detener el temporizador si está activo
                if (this.temporizador) {
                    clearInterval(this.temporizador);
                    this.temporizador = null;
                }
            }
        },

        /**
         * Formatea una fecha en español con día de la semana
         * @param {string} dateString - Fecha en formato string
         * @returns {string} Fecha formateada
         */
        formatDate(dateString) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('es-ES', options);
        },

        /**
         * Formatea una hora eliminando los segundos
         * @param {string} timeString - Hora en formato HH:MM:SS
         * @returns {string} Hora en formato HH:MM
         */
        formatTime(timeString) {
            return timeString.substring(0, 5);
        }
    };
}
</script>