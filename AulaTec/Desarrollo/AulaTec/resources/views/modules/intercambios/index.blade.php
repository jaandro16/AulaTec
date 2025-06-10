{{-- Vista principal del sistema de intercambios de reservas --}}
{{-- Permite a los estudiantes ver, publicar y solicitar intercambios --}}

{{-- ======= CONTENEDOR PRINCIPAL ======= --}}
<div class="mx-auto ">
    
    {{-- ======= NAVEGACIÓN POR PESTAÑAS ======= --}}
    {{-- Sistema de tabs para organizar diferentes vistas del intercambio --}}
    <div class="grid w-full max-w-md grid-cols-3 mb-6 bg-gray-100 rounded-lg border border-gray-200 p-1">
        
        {{-- TAB 1: Reservas disponibles para intercambio --}}
        <button class="flex items-center justify-center py-2 px-3 rounded-tl-md rounded-tr-md border-b-2 bg-white text-foreground shadow-sm" 
                id="tab-disponibles"
                style="border-color: hsl(262.1 83.3% 57.8%)">
            <span class="sm:inline font-medium text-sm">Disponibles</span>
        </button>
        
        {{-- TAB 2: Intercambios publicados por el usuario actual --}}
        <button class="flex items-center justify-center py-2 px-3 border-b-2 bg-muted text-muted-foreground hover:text-foreground" 
                id="tab-mis-publicaciones"
                style="border-color: transparent">
            <span class="sm:inline font-medium text-sm">Mis Publicaciones</span>
        </button>
        
        {{-- TAB 3: Solicitudes de intercambio enviadas por el usuario --}}
        <button class="flex items-center justify-center py-2 px-3 rounded-tr-md rounded-tl-md border-b-2 bg-muted text-muted-foreground hover:text-foreground" 
                id="tab-mis-solicitudes"
                style="border-color: transparent">
            <span class="sm:inline font-medium text-sm">Mis Solicitudes</span>
        </button>
    </div>

    {{-- ======= CONTENIDO DE LAS PESTAÑAS ======= --}}
    <div>
        
        {{-- PESTAÑA DISPONIBLES (visible por defecto) --}}
        <div id="content-disponibles">
            
            {{-- Header con título y botón de publicar --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <h3 class="text-lg font-medium">Reservas disponibles para intercambio</h3>
                {{-- Botón para abrir modal de publicación --}}
                <button class="bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white px-4 py-2 rounded-md"
                        onclick="showPublicarModal()">
                    Publicar Reserva
                </button>
            </div>

            {{-- ======= SECCIÓN DE FILTROS ======= --}}
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                
                {{-- Campo de búsqueda textual --}}
                <div class="relative flex-grow">
                    {{-- Icono de búsqueda --}}
                    <svg class="absolute left-3 top-3 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    {{-- Input de búsqueda con función de filtrado en tiempo real --}}
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Buscar por clase, usuario o aula..." 
                        class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md"
                        oninput="filterExchanges()"
                    >
                </div>
                
                {{-- Filtro por asignatura --}}
                <div class="w-full md:w-64">
                    {{-- Select que se llena dinámicamente con las asignaturas --}}
                    <select class="w-full border border-gray-300 rounded-md px-3 py-2" id="subject-filter">
                        <option value="">Todas las asignaturas</option>
                        {{-- Las asignaturas se cargarán dinámicamente --}}
                    </select>
                </div>
            </div>

            {{-- Contenedor grid para las tarjetas de intercambio --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="exchanges-container">
                <!-- Las tarjetas se cargarán dinámicamente aquí -->
            </div>
        </div>

        {{-- PESTAÑA MIS PUBLICACIONES (oculta por defecto) --}}
        <div id="content-mis-publicaciones" class="hidden">
            {{-- Incluir vista parcial con las publicaciones del usuario --}}
            @include('modules.intercambios.mis-publicaciones')
        </div>

        {{-- PESTAÑA MIS SOLICITUDES (oculta por defecto) --}}
        <div id="content-mis-solicitudes" class="hidden">
            {{-- Incluir vista parcial con las solicitudes del usuario --}}
            @include('modules.intercambios.mis-solicitudes')
        </div>
    </div>
</div>

{{-- ======= MODALES ======= --}}

{{-- Modal para solicitar intercambio (overlay completo) --}}
<div id="modal-solicitar-intercambio" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    {{-- Prevenir cierre al hacer clic en el contenido del modal --}}
    <div onclick="event.stopPropagation()">
        {{-- Incluir formulario de solicitud --}}
        @include('modules.intercambios.solicitar')
    </div>
</div>

{{-- Modal para publicar intercambio (overlay completo) --}}
<div id="modal-publicar-intercambio" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    {{-- Prevenir cierre al hacer clic en el contenido del modal --}}
    <div onclick="event.stopPropagation()">
        {{-- Incluir formulario de publicación --}}
        @include('modules.intercambios.publicar')
    </div>
</div>

{{-- ======= JAVASCRIPT PARA FUNCIONALIDAD ======= --}}
<script>
    // ======= MANEJO DE PESTAÑAS =======
    
    // Evento para tab "Disponibles"
    document.getElementById('tab-disponibles').addEventListener('click', function() {
        // Mostrar contenido de disponibles y ocultar otros
        document.getElementById('content-disponibles').classList.remove('hidden');
        document.getElementById('content-mis-publicaciones').classList.add('hidden');
        document.getElementById('content-mis-solicitudes').classList.add('hidden');
        
        // Actualizar estilos del botón activo
        this.classList.remove('bg-muted', 'text-muted-foreground');
        this.classList.add('bg-white', 'text-foreground', 'shadow-sm');
        this.style.borderColor = 'hsl(262.1 83.3% 57.8%)';
        
        // Resetear estilos de los otros botones
        document.getElementById('tab-mis-publicaciones').classList.remove('bg-white', 'text-foreground', 'shadow-sm');
        document.getElementById('tab-mis-publicaciones').classList.add('bg-muted', 'text-muted-foreground');
        document.getElementById('tab-mis-publicaciones').style.borderColor = 'transparent';
        
        document.getElementById('tab-mis-solicitudes').classList.remove('bg-white', 'text-foreground', 'shadow-sm');
        document.getElementById('tab-mis-solicitudes').classList.add('bg-muted', 'text-muted-foreground');
        document.getElementById('tab-mis-solicitudes').style.borderColor = 'transparent';
    });
    
    // Evento para tab "Mis Publicaciones"
    document.getElementById('tab-mis-publicaciones').addEventListener('click', function() {
        // Cambiar visibilidad de contenidos
        document.getElementById('content-disponibles').classList.add('hidden');
        document.getElementById('content-mis-publicaciones').classList.remove('hidden');
        document.getElementById('content-mis-solicitudes').classList.add('hidden');
        
        // Actualizar estilos del botón activo
        this.classList.remove('bg-muted', 'text-muted-foreground');
        this.classList.add('bg-white', 'text-foreground', 'shadow-sm');
        this.style.borderColor = 'hsl(262.1 83.3% 57.8%)';
        
        // Resetear estilos de los otros botones
        document.getElementById('tab-disponibles').classList.remove('bg-white', 'text-foreground', 'shadow-sm');
        document.getElementById('tab-disponibles').classList.add('bg-muted', 'text-muted-foreground');
        document.getElementById('tab-disponibles').style.borderColor = 'transparent';
        
        document.getElementById('tab-mis-solicitudes').classList.remove('bg-white', 'text-foreground', 'shadow-sm');
        document.getElementById('tab-mis-solicitudes').classList.add('bg-muted', 'text-muted-foreground');
        document.getElementById('tab-mis-solicitudes').style.borderColor = 'transparent';
    });

    // Evento para tab "Mis Solicitudes"
    document.getElementById('tab-mis-solicitudes').addEventListener('click', function() {
        // Cambiar visibilidad de contenidos
        document.getElementById('content-disponibles').classList.add('hidden');
        document.getElementById('content-mis-publicaciones').classList.add('hidden');
        document.getElementById('content-mis-solicitudes').classList.remove('hidden');
        
        // Actualizar estilos del botón activo
        this.classList.remove('bg-muted', 'text-muted-foreground');
        this.classList.add('bg-white', 'text-foreground', 'shadow-sm');
        this.style.borderColor = 'hsl(262.1 83.3% 57.8%)';
        
        // Resetear estilos de los otros botones
        document.getElementById('tab-disponibles').classList.remove('bg-white', 'text-foreground', 'shadow-sm');
        document.getElementById('tab-disponibles').classList.add('bg-muted', 'text-muted-foreground');
        document.getElementById('tab-disponibles').style.borderColor = 'transparent';
        
        document.getElementById('tab-mis-publicaciones').classList.remove('bg-white', 'text-foreground', 'shadow-sm');
        document.getElementById('tab-mis-publicaciones').classList.add('bg-muted', 'text-muted-foreground');
        document.getElementById('tab-mis-publicaciones').style.borderColor = 'transparent';
    });

    // ======= INICIALIZACIÓN AL CARGAR LA PÁGINA =======
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar datos iniciales
        loadSubjects();               // Llenar select de asignaturas
        loadActiveExchanges();        // Cargar intercambios disponibles
        
        // Configurar eventos de filtrado
        document.getElementById('subject-filter').addEventListener('change', function() {
            filterExchanges();
        });

        document.getElementById('search-input').addEventListener('input', function() {
            filterExchanges();
        });
    });

    // Variable global para almacenar todas las reservas cargadas
    let allExchanges = [];

    // ======= FUNCIONES DE CARGA DE DATOS =======
    
    /**
     * Carga las asignaturas disponibles desde la API
     * y las añade al select de filtros
     */
    function loadSubjects() {
        fetch('/api/subjects')
            .then(response => response.json())
            .then(subjects => {
                const select = document.getElementById('subject-filter');
                // Crear option para cada asignatura
                subjects.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = subject.name;
                    select.appendChild(option);
                });
            })
            .catch(error => console.error('Error cargando asignaturas:', error));
    }

    /**
     * Carga todas las reservas activas disponibles para intercambio
     */
    function loadActiveExchanges() {
        fetch('/api/exchanges/active')
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || 'Error del servidor');
                    });
                }
                return response.json();
            })
            .then(exchanges => {
                allExchanges = exchanges;    // Guardamos todas las reservas
                filterExchanges();           // Aplicamos el filtro inicial
            })
            .catch(error => {
                console.error('Error completo:', error);
                // Mostrar mensaje de error en el contenedor
                document.getElementById('exchanges-container').innerHTML = `
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500">Error al cargar las reservas: ${error.message}</p>
                    </div>`;
            });
    }

    // ======= FUNCIÓN DE FILTRADO =======
    
    /**
     * Filtra y muestra las reservas según los criterios seleccionados
     * Aplica filtros de asignatura y búsqueda textual
     */
    function filterExchanges() {
        const selectedSubject = document.getElementById('subject-filter').value;
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const container = document.getElementById('exchanges-container');
        
        let filteredExchanges = allExchanges;
        
        // Filtrar por asignatura si se seleccionó una
        if (selectedSubject) {
            filteredExchanges = filteredExchanges.filter(exchange => 
                exchange.reservation.class_session.subject.id.toString() === selectedSubject
            );
        }

        // Filtrar por término de búsqueda
        if (searchTerm) {
            filteredExchanges = filteredExchanges.filter(exchange => {
                // Campos donde buscar: asignatura, usuario, aula, motivo, asiento
                const searchData = [
                    exchange.reservation.class_session.subject.name,
                    exchange.reservation.user.fullName,
                    exchange.reservation.class_session.classroom.name,
                    exchange.motivo || '',
                    exchange.reservation.asiento.toString()
                ].map(text => text.toLowerCase());

                // Verificar si algún campo contiene el término buscado
                return searchData.some(text => text.includes(searchTerm));
            });
        }

        // Mostrar mensaje si no hay resultados
        if (!filteredExchanges || filteredExchanges.length === 0) {
            const message = (selectedSubject || searchTerm)
                ? 'No hay reservas disponibles para los filtros seleccionados'
                : 'No hay reservas disponibles para intercambiar';

            container.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <div class="flex flex-col items-center justify-center">
                        {{-- Icono de bandeja vacía --}}
                        <svg class="h-12 w-12 text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" 
                            />
                        </svg>
                        <p class="text-gray-500 text-lg">${message}</p>
                        <p class="text-gray-400 text-sm mt-2">${
                            (selectedSubject || searchTerm)
                                ? 'Prueba con otros términos de búsqueda o filtros'
                                : 'Vuelve más tarde para ver nuevas publicaciones'
                        }</p>
                    </div>
                </div>`;
            return;
        }

        // Generar HTML para cada intercambio disponible
        container.innerHTML = filteredExchanges.map(exchange => {
            return `
                <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4">
                        {{-- Título: nombre de la asignatura --}}
                        <h4 class="text-lg font-semibold">${exchange.reservation.class_session.subject.name}</h4>
                        
                        {{-- Detalles de la clase --}}
                        <div class="mt-2 space-y-2">
                            {{-- Fecha --}}
                            <div class="flex items-center text-sm">
                                <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>${formatDate(exchange.reservation.class_session.date)}</span>
                            </div>
                            {{-- Hora y duración --}}
                            <div class="flex items-center text-sm">
                                <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>${exchange.reservation.class_session.time_slot.start_time} (${exchange.reservation.class_session.time_slot.duration} minutos)</span>
                            </div>
                            {{-- Ubicación del aula --}}
                            <div class="flex items-center text-sm">
                                <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>${exchange.reservation.class_session.classroom.name}</span>
                            </div>
                            {{-- Número de asiento (destacado) --}}
                            <div class="flex items-center text-sm font-medium text-purple-600">
                                <span>Asiento: ${exchange.reservation.asiento}</span>
                            </div>
                        </div>
                        
                        {{-- Información del publicador --}}
                        <div class="bg-gray-50 p-3 rounded-md text-sm mt-4 mb-4">
                            <p class="font-medium mb-1">Publicado por: ${exchange.reservation.user.fullName}</p>
                            <p class="text-gray-600">Motivo: ${exchange.motivo || 'No especificado'}</p>
                        </div>
                    </div>
                    
                    {{-- Botón de acción para solicitar intercambio --}}
                    <div class="bg-gray-50 px-4 py-3 border-t">
                        <button class="w-full bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white px-4 py-2 rounded-md flex items-center justify-center" 
                            onclick="(() => {
                                window.exchangePostId = ${exchange.id};
                                showSolicitarModal(${JSON.stringify(exchange).replace(/"/g, '&quot;')});
                            })()"
                            data-exchange-id="${exchange.id}">
                            {{-- Icono de intercambio --}}
                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Solicitar Intercambio
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    // ======= FUNCIONES AUXILIARES =======
    
    /**
     * Formatea una fecha en español con día de la semana
     */
    function formatDate(dateString) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    /**
     * Formatea una hora en formato HH:MM
     */
    function formatTime(timeString) {
        return timeString.substring(0, 5);
    }

    // ======= MANEJO DE MODALES =======
    
    /**
     * Muestra el modal para publicar una reserva
     */
    function showPublicarModal() {
        document.getElementById('modal-publicar-intercambio').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevenir scroll del body
    }

    /**
     * Oculta el modal de publicar reserva
     */
    function hidePublicarModal() {
        document.getElementById('modal-publicar-intercambio').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restaurar scroll del body
    }

    // Cerrar modal de publicar al hacer clic fuera de él
    document.getElementById('modal-publicar-intercambio').addEventListener('click', function(e) {
        if (e.target === this) {
            hidePublicarModal();
        }
    });

    // Cerrar modal de publicar con la tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('modal-publicar-intercambio').classList.contains('hidden')) {
            hidePublicarModal();
        }
    });

    /**
     * Muestra el modal para solicitar intercambio
     * @param {Object} exchangeData - Datos del intercambio seleccionado
     */
    function showSolicitarModal(exchangeData) {
        // Almacenar ID del intercambio globalmente
        window.exchangePostId = exchangeData.id;
        
        // Actualizar contenido del modal con los datos del intercambio
        document.querySelector('#modal-solicitar-intercambio .reserva-solicitada-asignatura').textContent = 
            exchangeData.reservation.class_session.subject.name;
        document.querySelector('#modal-solicitar-intercambio .reserva-solicitada-fecha').textContent = 
            formatDate(exchangeData.reservation.class_session.date);
        document.querySelector('#modal-solicitar-intercambio .reserva-solicitada-hora').textContent = 
            `${formatTime(exchangeData.reservation.class_session.time_slot.start_time)} (${exchangeData.reservation.class_session.time_slot.duration} minutos)`;
        document.querySelector('#modal-solicitar-intercambio .reserva-solicitada-aula').textContent = 
            `${exchangeData.reservation.class_session.classroom.name}, Asiento ${exchangeData.reservation.asiento}`;

        // Mostrar modal
        document.getElementById('modal-solicitar-intercambio').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Oculta el modal de solicitar intercambio
     */
    function hideSolicitarModal() {
        document.getElementById('modal-solicitar-intercambio').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restaurar scroll del body
    }

    // Cerrar modal de solicitar al hacer clic fuera de él
    document.getElementById('modal-solicitar-intercambio').addEventListener('click', function(e) {
        if (e.target === this) {
            hideSolicitarModal();
        }
    });

    // Cerrar modal de solicitar con la tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('modal-solicitar-intercambio').classList.contains('hidden')) {
            hideSolicitarModal();
        }
    });
</script>