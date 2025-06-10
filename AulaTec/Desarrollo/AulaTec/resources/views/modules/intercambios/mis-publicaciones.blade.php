{{-- Vista para gestionar las publicaciones de intercambio del usuario actual --}}
{{-- Permite ver, cancelar publicaciones y gestionar solicitudes recibidas --}}

{{-- Componente Alpine.js con datos reactivos --}}
<div x-data="userExchanges()" x-init="loadExchanges()" class="space-y-4">
    
    {{-- ======= HEADER CON TÍTULO Y BOTÓN ======= --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h3 class="text-lg font-medium">Mis reservas publicadas para intercambio</h3>
        {{-- Botón para abrir modal de nueva publicación --}}
        <button class="bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white px-4 py-2 rounded-md" 
                onclick="showPublicarModal()">
            Publicar Reserva
        </button>
    </div>

    {{-- ======= GRID PRINCIPAL DE PUBLICACIONES ======= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- ESTADO DE CARGA: Spinner mientras cargan datos --}}
        <template x-if="loading">
            <div class="col-span-full flex justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
            </div>
        </template>

        {{-- ESTADO VACÍO: Cuando no hay publicaciones --}}
        <template x-if="!loading && exchanges.length === 0">
            <div class="col-span-full text-center py-12">
                {{-- Icono de advertencia --}}
                <svg class="h-12 w-12 mx-auto text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-xl font-semibold mb-2">No tienes reservas publicadas</h3>
                <p class="text-gray-500 mb-6">Publica una de tus reservas para intercambio</p>
            </div>
        </template>

        {{-- ======= LISTA DE PUBLICACIONES ======= --}}
        {{-- Iteración por cada intercambio publicado por el usuario --}}
        <template x-for="exchange in exchanges" :key="exchange.id">
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                
                {{-- INFORMACIÓN DE LA RESERVA PUBLICADA --}}
                <div class="p-4">
                    {{-- Nombre de la asignatura --}}
                    <h4 class="text-lg font-semibold" x-text="exchange.reservation.class_session.subject.name"></h4>
                    
                    {{-- Grid con detalles de fecha/hora y ubicación --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 mb-4">
                        
                        {{-- COLUMNA IZQUIERDA: Fecha y hora --}}
                        <div class="space-y-2">
                            {{-- Fecha de la clase --}}
                            <div class="flex items-center text-sm">
                                <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span x-text="formatDate(exchange.reservation.class_session.date)"></span>
                            </div>
                            {{-- Hora y duración de la clase --}}
                            <div class="flex items-center text-sm">
                                <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-text="formatTime(exchange.reservation.class_session.time_slot.start_time) + ' (' + exchange.reservation.class_session.time_slot.duration + ' minutos)'"></span>
                            </div>
                        </div>
                        
                        {{-- COLUMNA DERECHA: Ubicación y asiento --}}
                        <div class="space-y-2">
                            {{-- Aula donde se imparte --}}
                            <div class="flex items-center text-sm">
                                <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span x-text="exchange.reservation.class_session.classroom.name"></span>
                            </div>
                            {{-- Número de asiento (destacado en morado) --}}
                            <div class="flex items-center text-sm font-medium text-purple-600">
                                <span x-text="'Asiento: ' + exchange.reservation.asiento"></span>
                            </div>
                        </div>
                    </div>

                    {{-- MOTIVO DEL INTERCAMBIO --}}
                    <div class="bg-gray-50 p-3 rounded-md text-sm mb-4">
                        <p class="text-gray-600" x-text="'Motivo: ' + (exchange.motivo || 'No especificado')"></p>
                    </div>

                    {{-- ======= SOLICITUDES RECIBIDAS ======= --}}
                    {{-- Mostrar solo si hay solicitudes --}}
                    <div x-show="exchange.requests.length > 0">
                        <h4 class="font-medium mb-2">Solicitudes recibidas:</h4>
                        {{-- Iteración por cada solicitud de intercambio --}}
                        <template x-for="request in exchange.requests" :key="request.id">
                            <div class="border rounded-md p-3 mb-2">
                                {{-- Nombre del usuario que solicita --}}
                                <p class="font-medium" x-text="request.user.nombre + ' ' + request.user.apellido"></p>
                                {{-- Detalles de la reserva que ofrece a cambio --}}
                                <p class="text-sm text-gray-600 mb-2" 
                                   x-text="'Ofrece: ' + request.offered_reservation.subject + ' - ' + 
                                          formatDate(request.offered_reservation.date) + ' ' +
                                          formatTime(request.offered_reservation.time) + ' - ' +
                                          request.offered_reservation.classroom + ' - Asiento ' +
                                          request.offered_reservation.asiento">
                                </p>
                                {{-- BOTONES DE ACCIÓN PARA LA SOLICITUD --}}
                                <div class="flex space-x-2">
                                    {{-- Botón Aceptar (solo si está pendiente) --}}
                                    <button class="bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white px-3 py-1 text-sm rounded"
                                            x-show="request.estado === 'Pendiente'"
                                            @click="abrirModalConfirmacion('aceptar', request.id)">
                                        Aceptar
                                    </button>
                                    {{-- Botón Rechazar (solo si está pendiente) --}}
                                    <button class="border border-gray-300 text-gray-700 px-3 py-1 text-sm rounded hover:bg-gray-50"
                                            x-show="request.estado === 'Pendiente'"
                                            @click="abrirModalConfirmacion('rechazar', request.id)">
                                        Rechazar
                                    </button>
                                    {{-- Badge de estado Aceptada --}}
                                    <span x-show="request.estado === 'Aceptada'" 
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aceptada</span>
                                    {{-- Badge de estado Rechazada --}}
                                    <span x-show="request.estado === 'Rechazada'" 
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-8000">Rechazada</span>
                                </div>
                            </div>
                        </template>
                    </div>
                    {{-- Mensaje cuando no hay solicitudes --}}
                    <div x-show="exchange.requests.length === 0" class="text-gray-500 text-sm text-center py-2">
                        No hay solicitudes todavía
                    </div>
                </div>
                
                {{-- ======= FOOTER CON ACCIONES ======= --}}
                <div class="bg-gray-50 px-4 py-3 border-t">
                    {{-- Botón cancelar publicación (solo si no hay solicitudes aceptadas) --}}
                    <template x-if="exchange.requests.length === 0 || exchange.requests.some(r => r.estado === 'Pendiente')">
                        <button class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-100"
                                @click="abrirModalConfirmacion('cancelar', exchange.id)">
                            Cancelar Publicación
                        </button>
                    </template>
                    {{-- Estado de intercambio completado --}}
                    <template x-if="!exchange.active">
                        <div class="flex justify-center">
                            <template x-if="exchange.requests.some(r => r.estado === 'Aceptada')">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{-- Indicador visual de éxito --}}
                                    <svg class="w-2.5 h-2.5 mr-2 text-green-400 fill-current" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Intercambio realizado
                                </span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
    
    {{-- ======= MODAL DE CONFIRMACIÓN ======= --}}
    {{-- Modal reutilizable para confirmar acciones (cancelar, aceptar, rechazar) --}}
    <div 
        x-show="modalConfirmacion" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        {{-- Controlar scroll del body cuando el modal está abierto --}}
        x-init="$watch('modalConfirmacion', value => {
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
                        {{-- Icono de advertencia (color dinámico según acción) --}}
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                            :class="modalTipo === 'cancelar' || modalTipo === 'rechazar' ? 'bg-red-100' : 'bg-yellow-100'">
                            <svg class="h-6 w-6" 
                                :class="modalTipo === 'cancelar' || modalTipo === 'rechazar' ? 'text-red-600' : 'text-yellow-600'" 
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        {{-- Texto del modal (dinámico según acción) --}}
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{-- Título dinámico según tipo de acción --}}
                                <span x-text="modalTipo === 'cancelar' ? 'Cancelar publicación' : 
                                            modalTipo === 'aceptar' ? 'Aceptar intercambio' : 
                                            'Rechazar solicitud'"></span>
                            </h3>
                            <div class="mt-2">
                                {{-- Mensaje de confirmación dinámico --}}
                                <p class="text-sm text-gray-500">
                                    <span x-text="modalTipo === 'cancelar' ? 
                                        '¿Estás seguro de que deseas cancelar esta publicación de intercambio?' : 
                                        modalTipo === 'aceptar' ? 
                                        '¿Estás seguro de que deseas aceptar este intercambio?' :
                                        '¿Estás seguro de que deseas rechazar esta solicitud de intercambio?'">
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- BOTONES DEL MODAL --}}
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    {{-- Botón Confirmar (con estado de carga) --}}
                    <button 
                        type="button" 
                        class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white sm:ml-3 sm:w-auto sm:text-sm"
                        {{-- Clases dinámicas según tipo de acción y estado de procesamiento --}}
                        :class="{
                            'bg-red-600 hover:bg-red-700': modalTipo === 'cancelar' || modalTipo === 'rechazar',
                            'bg-yellow-600 hover:bg-yellow-700': modalTipo === 'aceptar',
                            'opacity-75 cursor-not-allowed': procesando
                        }"
                        @click="confirmarAccion"
                        :disabled="procesando"
                    >
                        {{-- Spinner de carga (visible durante procesamiento) --}}
                        <svg x-show="procesando" 
                            class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" 
                            xmlns="http://www.w3.org/2000/svg" 
                            fill="none" 
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{-- Texto dinámico del botón --}}
                        <span x-text="procesando ? 'Procesando...' : 'Confirmar'"></span>
                    </button>
                    {{-- Botón Cancelar --}}
                    <button 
                        type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        @click="cerrarModalConfirmacion"
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
 * Componente Alpine.js para gestionar las publicaciones de intercambio del usuario
 * 
 * Funcionalidades:
 * - Cargar y mostrar publicaciones del usuario
 * - Gestionar solicitudes de intercambio recibidas
 * - Cancelar publicaciones
 * - Aceptar/rechazar solicitudes
 */
function userExchanges() {
    return {
        // ======= ESTADO REACTIVO =======
        exchanges: [],              // Array de publicaciones del usuario
        loading: true,              // Estado de carga inicial
        modalConfirmacion: false,   // Visibilidad del modal de confirmación
        exchangeToCancel: null,     // ID del intercambio a cancelar
        requestToAccept: null,      // ID de la solicitud a aceptar
        modalTipo: '',              // Tipo de acción del modal (cancelar, aceptar, rechazar)
        procesando: false,          // Estado de procesamiento de acciones

        /**
         * Carga las publicaciones de intercambio del usuario actual
         * Filtra automáticamente las reservas pasadas
         */
        async loadExchanges() {
            try {
                const response = await fetch('/api/exchanges/user-posts');
                const data = await response.json();
                
                if (data.status === 'success') {
                    const now = new Date();
                    // Filtrar las publicaciones por fecha y hora
                    this.exchanges = data.data.filter(exchange => {
                        const reservationDate = new Date(exchange.reservation.class_session.date);
                        
                        // Si es un día futuro, incluir
                        if (reservationDate > now) return true;
                        
                        // Si es hoy, verificar la hora
                        if (reservationDate.toDateString() === now.toDateString()) {
                            const [hours, minutes] = exchange.reservation.class_session.time_slot.start_time.split(':');
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
                // Emitir evento de notificación global
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        message: 'Error al cargar las publicaciones'
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
         * Abre el modal de confirmación para diferentes acciones
         * @param {string} tipo - Tipo de acción: 'cancelar', 'aceptar', 'rechazar'
         * @param {number} id - ID del intercambio o solicitud
         */
        abrirModalConfirmacion(tipo, id) {
            // Limpiar cualquier estado anterior
            this.exchangeToCancel = null;
            this.requestToAccept = null;
            this.requestToReject = null;
            this.modalTipo = '';

            // Establecer nuevo estado según el tipo de acción
            this.modalTipo = tipo;
            if (tipo === 'cancelar') {
                this.exchangeToCancel = id;
            } else if (tipo === 'aceptar') {
                this.requestToAccept = id;
            } else if (tipo === 'rechazar') {
                this.requestToReject = id;
            }
            this.modalConfirmacion = true;
        },

        /**
         * Cierra el modal de confirmación y limpia el estado
         */
        cerrarModalConfirmacion() {
            // Primero ocultar el modal
            this.modalConfirmacion = false;
            // Limpiar el estado después de un pequeño delay para evitar parpadeos
            setTimeout(() => {
                this.exchangeToCancel = null;
                this.requestToAccept = null;
                this.modalTipo = '';
                this.procesando = false;
            }, 200);
        },  
        
        /**
         * Cancela una publicación de intercambio
         */
        async confirmarCancelacion() {
            if (this.procesando) return;
            this.procesando = true;
            try {
                const response = await fetch(`/api/exchanges/${this.exchangeToCancel}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Remover de la lista local
                    this.exchanges = this.exchanges.filter(exchange => exchange.id !== this.exchangeToCancel);
                    // Mostrar notificación de éxito
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            message: 'Publicación cancelada correctamente'
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
                        message: error.message || 'Error al cancelar la publicación'
                    }
                }));
            } finally {
                this.procesando = false;
                this.cerrarModalConfirmacion();
            }
        },

        /**
         * Acepta una solicitud de intercambio
         */
        async confirmarAceptacion() {
            if (this.procesando) return;
            this.procesando = true;
            
            try {
                const response = await fetch(`/api/exchange-requests/${this.requestToAccept}/accept`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al procesar el intercambio');
                }

                if (data.status === 'success') {
                    // Recargar los intercambios para reflejar cambios
                    await this.loadExchanges();
                    
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            message: 'Intercambio aceptado correctamente'
                        }
                    }));
                } else {
                    throw new Error(data.message || 'Error al aceptar el intercambio');
                }
            } catch (error) {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        message: error.message || 'Error al aceptar el intercambio'
                    }
                }));
            } finally {
                this.procesando = false;
                this.cerrarModalConfirmacion();
            }
        },

        /**
         * Rechaza una solicitud de intercambio
         */
        async confirmarRechazo() {
            if (this.procesando) return;
            this.procesando = true;
            try {
                const response = await fetch(`/api/exchange-requests/${this.requestToReject}/reject`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Recargar intercambios para actualizar estados
                    await this.loadExchanges();
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            message: 'Solicitud rechazada correctamente'
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
                        message: error.message || 'Error al rechazar la solicitud'
                    }
                }));
            } finally {
                this.procesando = false;
                this.cerrarModalConfirmacion();
            }
        },

        /**
         * Ejecuta la acción confirmada según el tipo seleccionado
         */
        async confirmarAccion() {
            const tipo = this.modalTipo;
            if (tipo === 'cancelar') {
                await this.confirmarCancelacion();
            } else if (tipo === 'aceptar') {
                await this.confirmarAceptacion();
            } else if (tipo === 'rechazar') {
                await this.confirmarRechazo();
            }
        }
    }
}
</script>