{{-- Modal para publicar una reserva para intercambio --}}
{{-- Permite al usuario seleccionar una de sus reservas activas y especificar motivo --}}

{{-- Contenedor principal del modal con fondo blanco y sombra --}}
<div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto sm:mx-4">
    
    {{-- ======= HEADER DEL MODAL ======= --}}
    <div class="p-4 sm:p-6">
        <div class="flex justify-between items-center mb-4">
            {{-- Título del modal --}}
            <h3 class="text-base sm:text-lg font-semibold">Publicar reserva para intercambio</h3>
            {{-- Botón cerrar (X) --}}
            <button 
                class="text-gray-500 hover:text-gray-700 p-1" 
                onclick="hidePublicarModal()">
                {{-- Icono X para cerrar --}}
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        {{-- ======= FORMULARIO CON ALPINE.JS ======= --}}
        {{-- Componente reactivo para manejar la selección de reserva --}}
        <div x-data="reservaSelector()">
            <form>
                
                {{-- ======= SELECTOR DE RESERVA ======= --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="reserva">
                        Selecciona una reserva
                    </label>
                    {{-- Select que se llena dinámicamente con las reservas del usuario --}}
                    <select id="reserva" 
                        class="w-full border-2 border-gray-300 rounded-md
                            focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 
                            hover:border-gray-400 transition-colors duration-200
                            bg-white py-2 px-3 text-gray-800 text-sm sm:text-base"
                        x-init="loadReservations()"
                        x-model="selectedReservation">
                        {{-- Opción por defecto --}}
                        <option value="" class="text-gray-600" >Selecciona una reserva</option>
                        {{-- Iteración dinámica de reservas disponibles --}}
                        <template x-for="reserva in reservations" :key="reserva.id">
                            <option :value="reserva.id" 
                                    class="text-gray-800 truncate"
                                    {{-- Tooltip con información completa --}}
                                    :title="`${reserva.subject} - ${formatDate(reserva.date)} ${formatTime(reserva.time)}`"
                                    {{-- Texto truncado para evitar desbordamiento --}}
                                    x-text="truncateOptionText(`${reserva.subject} - ${formatDate(reserva.date)} ${formatTime(reserva.time)}`)">
                            </option>
                        </template>
                    </select>
                </div>
                
                {{-- ======= DETALLES DE LA RESERVA SELECCIONADA ======= --}}
                {{-- Sección que aparece solo cuando se selecciona una reserva --}}
                <div id="detalles-reserva" 
                    x-show="selectedReservation !== ''"
                    x-transition
                    class="border rounded-md p-2 sm:p-3 bg-gray-50 mb-4">
                    <h4 class="font-medium mb-2 text-sm sm:text-base">Detalles de la reserva:</h4>
                    <div class="text-xs sm:text-sm space-y-1">
                        
                        {{-- Fecha de la clase --}}
                        <div class="flex items-center">
                            {{-- Icono de calendario --}}
                            <svg class="h-4 w-4 mr-2 text-gray-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="break-words" x-text="getCurrentReservation().date ? formatDate(getCurrentReservation().date) : ''"></span>
                        </div>
                        
                        {{-- Hora y duración de la clase --}}
                        <div class="flex items-center">
                            {{-- Icono de reloj --}}
                            <svg class="h-4 w-4 mr-2 text-gray-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{-- Hora con duración fija de 90 minutos --}}
                            <span class="break-words" x-text="getCurrentReservation().time ? formatTime(getCurrentReservation().time) + ' (90 minutos)' : ''"></span>
                        </div>
                        
                        {{-- Aula donde se imparte la clase --}}
                        <div class="flex items-center">
                            {{-- Icono de ubicación --}}
                            <svg class="h-4 w-4 mr-2 text-gray-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{-- Nombre del aula con fallback --}}
                            <span class="break-words" x-text="getCurrentReservation().classroom || 'Aula sin especificar'"></span>
                        </div>
                    </div>
                </div>
            
                {{-- ======= CAMPO MOTIVO DEL INTERCAMBIO ======= --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="motivo">
                        Motivo del intercambio
                    </label>
                    {{-- Textarea para explicar el motivo --}}
                    <textarea 
                        id="motivo" 
                        x-model="motivo"
                        rows="3"
                        class="w-full border-2 border-gray-300 rounded-md
                            focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 
                            hover:border-gray-400 transition-colors duration-200
                            bg-white py-2 px-3 text-gray-800 text-sm sm:text-base
                            placeholder-gray-400" 
                        placeholder="Explica por qué deseas intercambiar esta reserva">
                    </textarea>
                </div>
                
                {{-- ======= BOTONES DE ACCIÓN ======= --}}
                <div class="flex flex-col sm:flex-row sm:justify-end gap-2">
                    {{-- Botón Cancelar --}}
                    <button type="button" 
                        class="w-full sm:w-auto order-2 sm:order-1 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" 
                        @click="hidePublicarModal()">
                        Cancelar
                    </button>
                    {{-- Botón Publicar (deshabilitado si faltan datos) --}}
                    <button type="button" 
                        class="w-full sm:w-auto order-1 sm:order-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white rounded-md text-sm font-medium transition-colors"
                        @click="publicarIntercambio()"
                        {{-- Deshabilitar si no hay reserva seleccionada o motivo --}}
                        :disabled="!selectedReservation || !motivo"
                        :class="{'opacity-50 cursor-not-allowed': !selectedReservation || !motivo}">
                        Publicar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======= JAVASCRIPT CON ALPINE.JS ======= --}}
<script>
/**
 * Componente Alpine.js para la selección y publicación de reservas para intercambio
 * 
 * Funcionalidades:
 * - Cargar reservas activas del usuario
 * - Mostrar detalles de la reserva seleccionada
 * - Validar y publicar intercambio
 * - Verificar duplicados antes de publicar
 */
function reservaSelector() {
    return {
        // ======= ESTADO REACTIVO =======
        reservations: [],           // Array de reservas disponibles del usuario
        selectedReservation: '',    // ID de la reserva seleccionada
        motivo: '',                 // Motivo del intercambio
        loading: false,             // Estado de carga
        error: null,                // Error en caso de fallo

        /**
         * Carga las reservas activas del usuario desde la API
         * Solo incluye reservas futuras que no han sido intercambiadas
         */
        async loadReservations() {
            this.loading = true;
            try {
                const response = await fetch('/api/reservations/active');
                if (!response.ok) throw new Error('Error al cargar las reservas');
                
                const data = await response.json();
                if (data.status === 'success') {
                    this.reservations = data.data;
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
         * Obtiene los datos completos de la reserva actualmente seleccionada
         * @returns {Object} Objeto con los datos de la reserva o objeto vacío
         */
        getCurrentReservation() {
            return this.reservations.find(r => r.id.toString() === this.selectedReservation.toString()) || {};
        },

        /**
         * Formatea una fecha en español con día de la semana
         * @param {string} dateString - Fecha en formato string
         * @returns {string} Fecha formateada o string vacío
         */
        formatDate(dateString) {
            if (!dateString) return '';
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('es-ES', options);
        },

        /**
         * Formatea una hora eliminando los segundos
         * @param {string} timeString - Hora en formato HH:MM:SS
         * @returns {string} Hora en formato HH:MM o string vacío
         */
        formatTime(timeString) {
            if (!timeString) return '';
            return timeString.substring(0, 5);
        },

        /**
         * Trunca el texto de las opciones del select para evitar desbordamiento
         * @param {string} text - Texto a truncar
         * @returns {string} Texto truncado con "..." si es necesario
         */
        truncateOptionText(text) {
            const maxLength = 40; // Longitud máxima para el select
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        },

        /**
         * Publica una reserva para intercambio
         * Incluye validación de duplicados y manejo de errores
         */
        async publicarIntercambio() {
            // Validar que hay datos requeridos
            if (!this.selectedReservation || !this.motivo) return;

            try {
                // PASO 1: Verificar si la reserva ya existe en intercambios
                const checkResponse = await fetch(`/api/exchanges/check-reservation/${this.selectedReservation}`);
                const checkData = await checkResponse.json();

                if (!checkResponse.ok) {
                    throw new Error(checkData.message || 'Error al verificar la reserva');
                }

                // Si ya existe, mostrar error
                if (checkData.exists) {
                    throw new Error('Esta reserva ya está publicada o ha sido intercambiada anteriormente');
                }

                // PASO 2: Si no existe, proceder con la publicación
                const response = await fetch('/api/exchanges', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        reservation_id: this.selectedReservation,
                        motivo: this.motivo
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // PASO 3: Limpiar formulario y cerrar modal
                    this.selectedReservation = '';
                    this.motivo = '';
                    hidePublicarModal();
                    
                    // PASO 4: Recargar la lista de intercambios disponibles
                    if (typeof window.loadActiveExchanges === 'function') {
                        window.loadActiveExchanges();
                    }
                    
                    // PASO 5: Mostrar notificación de éxito
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            message: 'Tu reserva ha sido publicada correctamente para intercambio'
                        }
                    }));
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                // Mostrar notificación de error
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        message: error.message || 'Ha ocurrido un error al publicar la reserva'
                    }
                }));

                // Cerrar modal si la reserva ya existe (evitar confusión)
                if (error.message.includes('ya está publicada') || error.message.includes('ha sido intercambiada')) {
                    this.selectedReservation = '';
                    this.motivo = '';
                    hidePublicarModal();
                }
            }
        }
    }
}
</script>