{{-- Vista para gestionar las solicitudes de intercambio enviadas por el usuario actual --}}
{{-- Permite ver el estado de las solicitudes y cancelar las pendientes --}}

{{-- Componente Alpine.js con datos reactivos --}}
<div x-data="exchangeRequests()" x-init="loadRequests()" class="space-y-4">
    {{-- Título de la sección --}}
    <h3 class="text-lg font-medium mb-6">Mis solicitudes de intercambio</h3>

    {{-- ======= GRID PRINCIPAL DE SOLICITUDES ======= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- ESTADO DE CARGA: Spinner mientras cargan datos --}}
        <template x-if="loading">
            <div class="col-span-full flex justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
            </div>
        </template>

        {{-- ESTADO VACÍO: Cuando no hay solicitudes enviadas --}}
        <template x-if="!loading && requests.length === 0">
            <div class="col-span-full text-center py-12">
                {{-- Icono de chat/mensaje --}}
                <svg class="h-12 w-12 mx-auto text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="text-xl font-semibold mb-2">No tienes solicitudes de intercambio</h3>
                <p class="text-gray-500">Cuando solicites un intercambio, aparecerá aquí</p>
            </div>
        </template>
        
        {{-- ======= LISTA DE SOLICITUDES ENVIADAS ======= --}}
        {{-- Iteración por cada solicitud de intercambio del usuario --}}
        <template x-for="request in requests" :key="request.id">
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                
                {{-- HEADER CON ESTADO DE LA SOLICITUD --}}
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-semibold">Solicitud de Intercambio</h4>
                        {{-- Badge de estado dinámico con colores según el estado --}}
                        <span class="text-sm px-2 py-1 rounded-full" 
                            :class="{
                                'bg-yellow-100 text-yellow-800': request.estado === 'Pendiente',
                                'bg-green-100 text-green-800': request.estado === 'Aceptada',
                                'bg-red-100 text-red-800': request.estado === 'Rechazada'
                            }"
                            x-text="request.estado">
                        </span>
                    </div>
                    
                    {{-- ======= COMPARACIÓN DE RESERVAS ======= --}}
                    {{-- Grid para mostrar lado a lado las dos reservas involucradas --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 mb-4">
                        
                        {{-- COLUMNA IZQUIERDA: Reserva que ofrece el usuario --}}
                        <div class="border rounded-md p-4">
                            <h4 class="font-medium mb-2 text-purple-600">Tu reserva ofrecida</h4>
                            {{-- Nombre de la asignatura --}}
                            <p class="font-medium" x-text="request.offered_reservation.subject"></p>
                            {{-- Detalles de fecha, hora, ubicación y asiento --}}
                            <div class="space-y-1 mt-2 text-sm">
                                {{-- Fecha de la clase --}}
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span x-text="formatDate(request.offered_reservation.date)"></span>
                                </div>
                                {{-- Hora de la clase --}}
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="formatTime(request.offered_reservation.time)"></span>
                                </div>
                                {{-- Aula donde se imparte --}}
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span x-text="request.offered_reservation.classroom"></span>
                                </div>
                                {{-- Número de asiento --}}
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="'Asiento: ' + request.offered_reservation.asiento"></span>
                                </div>
                            </div>
                        </div>

                        {{-- COLUMNA DERECHA: Reserva que solicita a cambio --}}
                        <div class="border rounded-md p-4">
                            <h4 class="font-medium mb-2 text-cyan-600">Reserva solicitada</h4>
                            {{-- Nombre de la asignatura solicitada --}}
                            <p class="font-medium" x-text="request.requested_reservation.subject"></p>
                            {{-- Propietario de la reserva solicitada --}}
                            <p class="text-sm text-gray-600" x-text="'De: ' + request.requested_reservation.owner"></p>
                            {{-- Detalles de fecha, hora, ubicación y asiento --}}
                            <div class="space-y-1 mt-2 text-sm">
                                {{-- Fecha de la clase solicitada --}}
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span x-text="formatDate(request.requested_reservation.date)"></span>
                                </div>
                                {{-- Hora de la clase solicitada --}}
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="formatTime(request.requested_reservation.time)"></span>
                                </div>
                                {{-- Aula de la clase solicitada --}}
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span x-text="request.requested_reservation.classroom"></span>
                                </div>
                                {{-- Número de asiento solicitado --}}
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="'Asiento: ' + request.requested_reservation.asiento"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- ======= FOOTER CON ACCIONES ======= --}}
                <div class="bg-gray-50 px-4 py-3 border-t">
                    {{-- Botón cancelar solicitud (solo si está pendiente) --}}
                    <template x-if="request.estado === 'Pendiente'">
                        <button class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-100"
                                @click="abrirModalCancelacion(request.id)">
                            Cancelar Solicitud
                        </button>
                    </template>
                    {{-- Mensaje de éxito (solo si fue aceptada) --}}
                    <template x-if="request.estado === 'Aceptada'">
                        <p class="text-green-600 text-center">¡Intercambio completado! La reserva ha sido actualizada.</p>
                    </template>
                </div>
            </div>
        </template>
    </div>
    
    {{-- ======= MODAL DE CONFIRMACIÓN PARA CANCELAR ======= --}}
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
        })"
    >
        {{-- Overlay de fondo --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        {{-- Contenedor centrado del modal --}}
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                {{-- CONTENIDO DEL MODAL --}}
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        {{-- Icono de advertencia --}}
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        {{-- Texto del modal --}}
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Cancelar solicitud
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que deseas cancelar esta solicitud de intercambio?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- BOTONES DEL MODAL --}}
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    {{-- Botón Confirmar --}}
                    <button 
                        type="button" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                        @click="confirmarCancelacion"
                    >
                        Confirmar
                    </button>
                    {{-- Botón Cancelar --}}
                    <button 
                        type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        @click="cerrarModalCancelacion"
                    >
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
 * Componente Alpine.js para gestionar las solicitudes de intercambio del usuario
 * 
 * Funcionalidades:
 * - Cargar y mostrar solicitudes enviadas por el usuario
 * - Cancelar solicitudes pendientes
 * - Mostrar estado actualizado de cada solicitud
 */
function exchangeRequests() {
    return {
        // ======= ESTADO REACTIVO =======
        requests: [],               // Array de solicitudes enviadas por el usuario
        loading: true,              // Estado de carga inicial
        modalCancelacion: false,    // Visibilidad del modal de cancelación
        requestToCancel: null,      // ID de la solicitud a cancelar

        /**
         * Carga las solicitudes de intercambio enviadas por el usuario actual
         */
        async loadRequests() {
            try {
                const response = await fetch('/api/exchange-requests/my-requests');
                const data = await response.json();
                
                if (data.status === 'success') {
                    this.requests = data.data;
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                // Emitir evento de notificación global
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        message: 'Error al cargar las solicitudes'
                    }
                }));
            } finally {
                this.loading = false;
            }
        },

        /**
         * Formatea una fecha en español con día de la semana
         */
        formatDate(dateString) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('es-ES', options);
        },

        /**
         * Formatea una hora eliminando los segundos
         */
        formatTime(timeString) {
            return timeString.substring(0, 5);
        },

        /**
         * Abre el modal de confirmación para cancelar una solicitud
         * @param {number} requestId - ID de la solicitud a cancelar
         */
        abrirModalCancelacion(requestId) {
            this.requestToCancel = requestId;
            this.modalCancelacion = true;
        },

        /**
         * Cierra el modal de cancelación y limpia el estado
         */
        cerrarModalCancelacion() {
            this.modalCancelacion = false;
            // Limpiar el estado después de un pequeño delay para evitar parpadeos
            setTimeout(() => {
                this.requestToCancel = null;
            }, 200);
        },

        /**
         * Confirma y ejecuta la cancelación de una solicitud de intercambio
         */
        async confirmarCancelacion() {
            try {
                const response = await fetch(`/api/exchange-requests/${this.requestToCancel}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Remover la solicitud de la lista local
                    this.requests = this.requests.filter(request => request.id !== this.requestToCancel);
                    
                    // Mostrar notificación de éxito
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            message: 'Solicitud cancelada correctamente'
                        }
                    }));
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        message: error.message || 'Error al cancelar la solicitud'
                    }
                }));
            } finally {
                // Cerrar modal independientemente del resultado
                this.cerrarModalCancelacion();
            }
        }
    }
}
</script>