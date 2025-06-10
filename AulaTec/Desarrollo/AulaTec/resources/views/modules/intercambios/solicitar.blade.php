{{-- Modal para solicitar intercambio de reserva --}}
{{-- Permite al usuario seleccionar una de sus reservas para intercambiar por la solicitada --}}

{{-- Componente Alpine.js con overlay completo --}}
<div x-data="availableReservations()" 
     x-init="init()" 
     class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    
    {{-- Contenedor principal del modal --}}
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto">
        <div class="p-4 sm:p-6">
            
            {{-- ======= HEADER CON TÍTULO Y BOTÓN CERRAR ======= --}}
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg sm:text-xl font-semibold">Solicitar Intercambio</h3>
                {{-- Botón cerrar con icono X --}}
                <button class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors" 
                        @click="hideSolicitarModal()" 
                        aria-label="Cerrar">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            {{-- ======= SECCIÓN RESERVA SOLICITADA ======= --}}
            {{-- Información de la reserva que el usuario quiere obtener --}}
            <div class="mb-4">
                <h4 class="font-medium mb-2 text-sm sm:text-base">Reserva solicitada:</h4>
                <div class="border rounded-md p-3 bg-gray-50">
                    {{-- Nombre de la asignatura (se llena dinámicamente) --}}
                    <p class="font-medium text-sm sm:text-base reserva-solicitada-asignatura"></p>
                    {{-- Detalles de fecha, hora y ubicación --}}
                    <div class="text-xs sm:text-sm space-y-1 mt-1">
                        {{-- Fila con fecha y hora (responsive flex) --}}
                        <div class="flex flex-wrap items-center">
                            {{-- Fecha de la clase --}}
                            <div class="flex items-center mr-2 mb-1 sm:mb-0">
                                <svg class="h-4 w-4 mr-1 flex-shrink-0 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="reserva-solicitada-fecha"></span>
                            </div>
                            {{-- Hora de la clase --}}
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-1 flex-shrink-0 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="reserva-solicitada-hora"></span>
                            </div>
                        </div>
                        {{-- Ubicación (aula y asiento) --}}
                        <div class="flex items-start">
                            <svg class="h-4 w-4 mr-1 flex-shrink-0 text-gray-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="reserva-solicitada-aula"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- ======= SECCIÓN SELECCIÓN DE RESERVA PROPIA ======= --}}
            {{-- Lista de reservas del usuario para ofrecer a cambio --}}
            <div class="mb-4">
                <h4 class="font-medium mb-2 text-sm sm:text-base">Selecciona tu reserva para intercambiar:</h4>
                {{-- Contenedor scrolleable para la lista --}}
                <div class="space-y-2 max-h-[200px] sm:max-h-[250px] overflow-y-auto pr-1 sm:pr-2">
                    
                    {{-- ESTADO DE CARGA: Spinner --}}
                    <template x-if="loading">
                        <div class="flex justify-center py-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                        </div>
                    </template>

                    {{-- ESTADO VACÍO: Sin reservas disponibles --}}
                    <template x-if="!loading && reservations.length === 0">
                        <div class="text-center py-4 text-gray-500">
                            No tienes reservas disponibles para intercambiar
                        </div>
                    </template>

                    {{-- LISTA DE RESERVAS DISPONIBLES --}}
                    {{-- Iteración por cada reserva del usuario --}}
                    <template x-for="reservation in reservations" :key="reservation.id">
                        <div class="border rounded-md p-2 sm:p-3 cursor-pointer hover:bg-gray-50 transition-colors active:bg-gray-100 relative"
                            {{-- Estilos dinámicos para reserva seleccionada --}}
                            :class="{'border-purple-600 bg-purple-50': selectedReservation === reservation.id}"
                            @click="selectReservation(reservation.id)">
                            
                            {{-- Icono de check para reserva seleccionada --}}
                            <svg x-show="selectedReservation === reservation.id" 
                                class="absolute top-2 right-2 h-5 w-5 text-purple-600" 
                                xmlns="http://www.w3.org/2000/svg" 
                                viewBox="0 0 20 20" 
                                fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            
                            {{-- Nombre de la asignatura --}}
                            <p class="font-medium text-sm sm:text-base" x-text="reservation.subject"></p>
                            {{-- Detalles de la reserva --}}
                            <div class="text-xs sm:text-sm space-y-1 mt-1">
                                {{-- Fecha y hora --}}
                                <div class="flex flex-wrap items-center">
                                    {{-- Fecha --}}
                                    <div class="flex items-center mr-2 mb-1 sm:mb-0">
                                        <svg class="h-4 w-4 mr-1 flex-shrink-0 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span x-text="formatDate(reservation.date)"></span>
                                    </div>
                                    {{-- Hora con duración --}}
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-1 flex-shrink-0 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="formatTime(reservation.time) + ' (' + reservation.duration + ' minutos)'"></span>
                                    </div>
                                </div>
                                {{-- Aula y asiento --}}
                                <div class="flex items-start">
                                    <svg class="h-4 w-4 mr-1 flex-shrink-0 text-gray-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span x-text="reservation.classroom + ', Asiento ' + reservation.asiento"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            {{-- ======= BOTONES DE ACCIÓN ======= --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3 mt-6">
                {{-- Botón Cancelar --}}
                <button type="button" 
                    class="w-full sm:w-auto px-4 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors" 
                    @click="hideSolicitarModal()">
                    Cancelar
                </button>
                {{-- Botón Solicitar (con estado de carga) --}}
                <button type="button" 
                    {{-- Deshabilitar si no hay reserva seleccionada o está procesando --}}
                    :disabled="!selectedReservation || submitting"
                    {{-- Clases dinámicas según estado --}}
                    :class="{
                        'opacity-50 cursor-not-allowed': !selectedReservation || submitting,
                        'hover:from-purple-700 hover:to-cyan-600': selectedReservation && !submitting
                    }"
                    class="w-full sm:w-auto px-4 py-2.5 bg-gradient-to-r from-purple-600 to-cyan-500 text-white rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors" 
                    @click="submitRequest">
                    {{-- Texto normal --}}
                    <span x-show="!submitting">Solicitar</span>
                    {{-- Texto con spinner durante procesamiento --}}
                    <span x-show="submitting" class="flex items-center justify-center">
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

{{-- ======= JAVASCRIPT CON ALPINE.JS ======= --}}
<script>
/**
 * Componente Alpine.js para solicitar intercambio de reservas
 * 
 * Funcionalidades:
 * - Cargar reservas disponibles del usuario para intercambio
 * - Permitir selección de reserva a ofrecer
 * - Enviar solicitud de intercambio
 * - Manejar estados de carga y validaciones
 */
function availableReservations() {
    return {
        // ======= ESTADO REACTIVO =======
        reservations: [],           // Array de reservas del usuario disponibles para intercambio
        selectedReservation: null,  // ID de la reserva seleccionada por el usuario
        loading: true,              // Estado de carga inicial
        error: null,                // Mensaje de error si falla la carga
        exchangePostId: null,       // ID del intercambio al que se responde
        submitting: false,          // Estado de envío de solicitud

        /**
         * Inicializa el componente
         * Obtiene el ID del intercambio y carga las reservas
         */
        async init() {
            // Obtener ID del intercambio desde variable global
            this.exchangePostId = window.exchangePostId;
            // console.log('Initialize with exchangePostId:', this.exchangePostId);
            await this.loadReservations();
        },

        /**
         * Envía la solicitud de intercambio al servidor
         * Incluye validaciones y manejo de estados duplicados
         */
        async submitRequest() {
            // console.log('Submit with state:', {
            //     selectedReservation: this.selectedReservation,
            //     exchangePostId: this.exchangePostId || window.exchangePostId
            // });

            const postId = this.exchangePostId || window.exchangePostId;

            // Validar que hay datos requeridos
            if (!this.selectedReservation || !postId) {
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        message: 'Por favor, selecciona una reserva para intercambiar'
                    }
                }));
                return;
            }

            this.submitting = true;

            try {
                const response = await fetch('/api/exchange-requests', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        exchange_post_id: postId,
                        reservation_id: this.selectedReservation
                    })
                });

                const data = await response.json();
                
                // Manejar solicitudes duplicadas (código 422)
                if (response.status === 422) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            message: 'Ya has solicitado este intercambio anteriormente'
                        }
                    }));
                    // Cerrar modal incluso en caso de duplicado
                    window.hideSolicitarModal();
                    return;
                }

                if (data.status === 'success') {
                    // Notificar éxito
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            message: 'Solicitud de intercambio enviada correctamente'
                        }
                    }));
                    
                    // Cerrar modal y actualizar lista principal
                    window.hideSolicitarModal();
                    if (typeof window.loadActiveExchanges === 'function') {
                        window.loadActiveExchanges();
                    }
                } else {
                    throw new Error(data.message || 'Error al procesar la solicitud');
                }
            } catch (error) {
                console.error('Error submitting request:', error);
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        message: error.message || 'Error al enviar la solicitud'
                    }
                }));
            } finally {
                this.submitting = false;
            }
        },

        /**
         * Carga las reservas del usuario disponibles para intercambio
         * Filtra automáticamente las reservas pasadas
         */
        async loadReservations() {
            try {
                const response = await fetch('/api/reservations/available-for-exchange');
                if (!response.ok) throw new Error('Error al cargar las reservas');
                
                const data = await response.json();
                if (data.status === 'success') {
                    const now = new Date();
                    // Filtrar reservas futuras o de hoy que no han empezado
                    this.reservations = data.data.filter(reservation => {
                        const reservationDate = new Date(reservation.date);
                        
                        // Si es un día futuro, incluir
                        if (reservationDate > now) return true;
                        
                        // Si es hoy, verificar la hora
                        if (reservationDate.toDateString() === now.toDateString()) {
                            const [hours, minutes] = reservation.time.split(':');
                            const reservationTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), hours, minutes);
                            // Incluir solo si la hora no ha pasado
                            return reservationTime > now;
                        }
                        
                        return false;
                    });
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                this.error = error.message;
            } finally {
                this.loading = false;
            }
        },

        /**
         * Selecciona una reserva de la lista
         * @param {number} id - ID de la reserva a seleccionar
         */
        selectReservation(id) {
            this.selectedReservation = id;
        }
    }
}
</script>