{{-- Componente de historial con dos pestañas: Reservas y Ausencias --}}
{{-- Permite visualizar el historial completo y gestionar justificantes de faltas --}}

{{-- Inclusión del componente de notificaciones --}}
@include('components.notification')

{{-- Contenedor principal visible cuando la pestaña 'historial' está activa --}}
<div x-show="activeTab === 'historial'" x-cloak>
    {{-- Sub-navegación para alternar entre historial de reservas y ausencias --}}
    <div x-data="{ activeHistorialTab: 'reservas' }">
        {{-- ======= BOTONES DE NAVEGACIÓN ENTRE PESTAÑAS ======= --}}
        <div class="mb-4">
            <div class="inline-flex rounded-md shadow-sm">
                {{-- Botón para historial de reservas --}}
                <button @click="activeHistorialTab = 'reservas'" 
                        :class="activeHistorialTab === 'reservas' ? 'bg-white text-foreground' : 'bg-muted text-muted-foreground hover:text-foreground'" 
                        class="px-4 py-2 text-sm font-medium rounded-l-md border border-gray-200 focus:z-10 focus:ring-2 focus:ring-purple-500 focus:text-purple-700">
                    Historial de Reservas
                </button>
                {{-- Botón para historial de ausencias --}}
                <button @click="activeHistorialTab = 'justificaciones'" 
                        :class="activeHistorialTab === 'justificaciones' ? 'bg-white text-foreground' : 'bg-muted text-muted-foreground hover:text-foreground'" 
                        class="px-4 py-2 text-sm font-medium rounded-r-md border border-gray-200 focus:z-10 focus:ring-2 focus:ring-purple-500 focus:text-purple-700">
                    Historial de Ausencias
                </button>
            </div>
        </div>

        {{-- ======= PESTAÑA: HISTORIAL DE RESERVAS ======= --}}
        {{-- Muestra todas las reservas pasadas y futuras del usuario --}}
        <div x-show="activeHistorialTab === 'reservas'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-cloak>
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                {{-- Header de la sección --}}
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Historial de Reservas</h3>
                    <p class="text-sm text-gray-500 mt-1">Todas tus reservas pasadas y futuras</p>
                </div>
                
                {{-- Componente Alpine.js para gestionar el historial de reservas --}}
                <div x-data="{ 
                    // ======= ESTADO REACTIVO =======
                    reservas: [],           // Array de todas las reservas del usuario
                    loading: true,          // Estado de carga

                    /**
                     * Inicializa el componente cargando las reservas
                     */
                    init() {
                        this.cargarReservasHistorial();
                    },

                    /**
                     * Carga el historial completo de reservas desde la API
                     */
                    async cargarReservasHistorial() {
                        try {
                            const response = await fetch('/api/reservations/historial');
                            if (!response.ok) throw new Error('Error al cargar las reservas');
                            
                            this.reservas = await response.json();
                        } catch (error) {
                            console.error('Error:', error);
                        } finally {
                            this.loading = false;
                        }
                    }
                }" class="historial-reservas">
                    
                    {{-- ======= ESTADO DE CARGA ======= --}}
                    <div x-show="loading" class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                    </div>
        
                    {{-- ======= CONTENIDO PRINCIPAL ======= --}}
                    <div class="p-6" x-show="!loading">
                        {{-- Lista de reservas si hay datos --}}
                        <template x-if="reservas.length > 0">
                            <div class="space-y-4">
                                {{-- Iteración por cada reserva --}}
                                <template x-for="(reserva, index) in reservas" :key="index">
                                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        {{-- Layout responsivo para información de la reserva --}}
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                            
                                            {{-- ======= COLUMNA IZQUIERDA: DETALLES DE LA CLASE ======= --}}
                                            <div>
                                                {{-- Nombre de la clase --}}
                                                <h3 class="font-medium" x-text="reserva.clase"></h3>
                                                <div class="text-sm text-gray-500">
                                                    {{-- Profesor con icono de libro --}}
                                                    <div class="flex items-center mt-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-1">
                                                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                                                        </svg>
                                                        <span x-text="reserva.profesor"></span>
                                                    </div>
                                                    {{-- Aula con icono de ubicación --}}
                                                    <div class="flex items-center mt-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-1">
                                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                                            <circle cx="12" cy="10" r="3"></circle>
                                                        </svg>
                                                        <span x-text="reserva.aula"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- ======= COLUMNA DERECHA: FECHA, HORA Y ESTADO ======= --}}
                                            <div class="text-right">
                                                {{-- Fecha formateada con icono de calendario --}}
                                                <div class="flex items-center justify-end">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-1 text-gray-500">
                                                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                                        <line x1="16" x2="16" y1="2" y2="6"></line>
                                                        <line x1="8" x2="8" y1="2" y2="6"></line>
                                                        <line x1="3" x2="21" y1="10" y2="10"></line>
                                                    </svg>
                                                    {{-- Fecha en formato español --}}
                                                    <span x-text="new Date(reserva.fecha).toLocaleDateString('es-ES', {
                                                        day: 'numeric',
                                                        month: 'long',
                                                        year: 'numeric'
                                                    })"></span>
                                                </div>
                                                
                                                {{-- Asiento y hora --}}
                                                <div class="flex items-center justify-end mt-1 space-x-3">
                                                    {{-- Número de asiento destacado --}}
                                                    <span class="text-sm font-medium text-purple-600" x-text="'Asiento ' + reserva.asiento"></span>
                                                    {{-- Hora con icono de reloj --}}
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-1 text-gray-500">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                        </svg>
                                                        <span x-text="reserva.hora"></span>
                                                    </div>
                                                </div>
                                                
                                                {{-- Colores dinámicos según el estado de la reserva --}}
                                                <div class="mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                        :class="{
                                                            // ======= RESERVA COMPLETADA (VERDE) =======
                                                            // Estado: Estudiante asistió y se confirmó presencia
                                                            'bg-green-100 text-green-800': reserva.estado === 'Completada',
                                                            
                                                            // ======= RESERVA PENDIENTE (AMARILLO) =======
                                                            // Estado: No asistido PERO la clase aún no ha terminado
                                                            'bg-yellow-100 text-yellow-800': reserva.estado === 'No asistido' && (() => {
                                                                // Construir fecha de la reserva
                                                                const fechaReserva = new Date(reserva.fecha);
                                                                // Extraer hora y minutos de finalización
                                                                const [horaFin, minutosFin] = reserva.hora_fin.split(':').map(Number);
                                                                // Crear fecha completa con hora de fin de clase
                                                                const fechaHoraFin = new Date(fechaReserva.getFullYear(), fechaReserva.getMonth(), fechaReserva.getDate(), horaFin, minutosFin);
                                                                // Retornar true si la clase aún no ha terminado
                                                                return fechaHoraFin > new Date();
                                                            })(),
                                                            
                                                            // ======= AUSENCIA JUSTIFICADA (AZUL) =======
                                                            // Estado: No asistido PERO se subió un justificante válido
                                                            'bg-blue-100 text-blue-800': reserva.estado === 'No asistido' && reserva.justificado === true,
                                                            
                                                            // ======= FALTA INJUSTIFICADA (ROJO) =======
                                                            // Estado: No asistido Y la clase ya terminó Y no hay justificante
                                                            'bg-red-100 text-red-800': reserva.estado === 'No asistido' && !reserva.justificado && (() => {
                                                                // Construir fecha de la reserva
                                                                const fechaReserva = new Date(reserva.fecha);
                                                                // Extraer hora y minutos de finalización
                                                                const [horaFin, minutosFin] = reserva.hora_fin.split(':').map(Number);
                                                                // Crear fecha completa con hora de fin de clase
                                                                const fechaHoraFin = new Date(fechaReserva.getFullYear(), fechaReserva.getMonth(), fechaReserva.getDate(), horaFin, minutosFin);
                                                                // Retornar true si la clase ya terminó
                                                                return fechaHoraFin < new Date();
                                                            })()
                                                        }">
                                                        
                                                        {{-- ======= TEXTO DINÁMICO DEL BADGE ======= --}}
                                                        {{-- El texto mostrado cambia según las mismas condiciones que los colores --}}
                                                        <span x-text="
                                                            // ======= LÓGICA DE TEXTO =======
                                                            reserva.estado === 'Completada' ? 'Completada' :
                                                            
                                                            // Verificar si es 'No asistido' pero la clase aún no terminó
                                                            (reserva.estado === 'No asistido' && (() => {
                                                                const fechaReserva = new Date(reserva.fecha);
                                                                const [horaFin, minutosFin] = reserva.hora_fin.split(':').map(Number);
                                                                const fechaHoraFin = new Date(fechaReserva.getFullYear(), fechaReserva.getMonth(), fechaReserva.getDate(), horaFin, minutosFin);
                                                                return fechaHoraFin > new Date();  // Clase en curso o futura
                                                            })()) ? 'Pendiente' :
                                                            
                                                            // Verificar si tiene justificante aprobado
                                                            (reserva.estado === 'No asistido' && reserva.justificado === true) ? 'Ausencia Justificada' :
                                                            
                                                            // Caso por defecto: falta sin justificar
                                                            'No asistido'
                                                        "></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
        
                        {{-- Estado vacío --}}
                        <template x-if="reservas.length === 0">
                            <div class="text-center py-12">
                                <p class="text-gray-500">No hay reservas completadas</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======= PESTAÑA: HISTORIAL DE AUSENCIAS ======= --}}
        {{-- Gestión de justificantes para clases no asistidas --}}
        <div x-show="activeHistorialTab === 'justificaciones'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-cloak>
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                {{-- Header de la sección --}}
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Clases No Asistidas</h3>
                    <p class="text-sm text-gray-500">Registro de clases a las que no has asistido</p>
                </div>
                
                {{-- Componente Alpine.js para gestionar ausencias y justificantes --}}
                <div x-data="ausenciasHandler()" class="historial-faltas">
                    {{-- Estado de carga --}}
                    <div x-show="loading" class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                    </div>

                    {{-- Contenido principal --}}
                    <div class="p-6" x-show="!loading">
                        {{-- Lista de ausencias si hay datos --}}
                        <template x-if="reservas.length > 0">
                            <div class="space-y-4">
                                {{-- Iteración por cada ausencia --}}
                                <template x-for="(reserva, index) in reservas" :key="index">
                                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        {{-- Layout responsivo para información de la ausencia --}}
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                            
                                            {{-- ======= COLUMNA IZQUIERDA: DETALLES DE LA CLASE ======= --}}
                                            <div>
                                                {{-- Nombre de la clase --}}
                                                <h3 class="font-medium" x-text="reserva.clase"></h3>
                                                <div class="text-sm text-gray-500">
                                                    {{-- Profesor con icono --}}
                                                    <div class="flex items-center mt-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-1">
                                                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                                                        </svg>
                                                        <span x-text="reserva.profesor"></span>
                                                    </div>
                                                    {{-- Aula con icono --}}
                                                    <div class="flex items-center mt-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-1">
                                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                                            <circle cx="12" cy="10" r="3"></circle>
                                                        </svg>
                                                        <span x-text="reserva.aula"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- ======= COLUMNA DERECHA: FECHA, HORA Y ESTADO ======= --}}
                                            <div class="text-right">
                                                {{-- Fecha formateada --}}
                                                <div class="flex items-center justify-end">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-1 text-gray-500">
                                                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                                        <line x1="16" x2="16" y1="2" y2="6"></line>
                                                        <line x1="8" x2="8" y1="2" y2="6"></line>
                                                        <line x1="3" x2="21" y1="10" y2="10"></line>
                                                    </svg>
                                                    <span x-text="new Date(reserva.fecha).toLocaleDateString('es-ES', {
                                                        day: 'numeric',
                                                        month: 'long',
                                                        year: 'numeric'
                                                    })"></span>
                                                </div>
                                                
                                                {{-- Asiento y hora --}}
                                                <div class="flex items-center justify-end mt-1">
                                                    <span class="text-sm font-medium text-purple-600 mr-2" x-text="'Asiento ' + reserva.asiento"></span>
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-1 text-gray-500">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                        </svg>
                                                        <span x-text="reserva.hora"></span>
                                                    </div>
                                                </div>
                                                
                                                {{-- ======= BADGES DE ESTADO ======= --}}
                                                <div class="mt-2">
                                                    {{-- Badge si hay justificante pendiente --}}
                                                    <template x-if="reserva.justificante_path">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Pendiente de Justificación
                                                        </span>
                                                    </template>
                                                    {{-- Badge de no asistencia --}}
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        No Asistida
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- ======= SECCIÓN DE JUSTIFICANTES ======= --}}
                                        {{-- Permite subir y gestionar documentos de justificación --}}
                                        <div class="mt-4 pt-3 border-t border-gray-100">
                                            {{-- Header de la sección de justificante --}}
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                                <p class="text-sm font-medium text-gray-700">Justificante de falta de asistencia</p>
                                                <div class="flex-shrink-0">
                                                    {{-- ======= BOTÓN SUBIR (cuando no hay justificante) ======= --}}
                                                    <template x-if="!reserva.justificante_path">
                                                        <label for="justificante-file-upload" class="relative cursor-pointer">
                                                            <div class="group flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                                                {{-- Icono de subida --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-2 text-gray-500 group-hover:text-purple-500">
                                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                    <polyline points="17 8 12 3 7 8"></polyline>
                                                                    <line x1="12" x2="12" y1="3" y2="15"></line>
                                                                </svg>
                                                                Subir documento o imagen
                                                            </div>
                                                            {{-- Input file oculto --}}
                                                            <input 
                                                                id="justificante-file-upload" 
                                                                name="justificante" 
                                                                type="file" 
                                                                class="sr-only" 
                                                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                                                @change="subirJustificante(reserva.id, $event)"
                                                            >
                                                        </label>
                                                    </template>
                                                    
                                                    {{-- ======= BOTÓN DESCARGAR (cuando ya hay justificante) ======= --}}
                                                    <template x-if="reserva.justificante_path">
                                                        <a 
                                                            :href="'/storage/' + reserva.justificante_path"
                                                            target="_blank"
                                                            download
                                                            class="group flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-purple-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
                                                        >
                                                            {{-- Icono de descarga --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-2 text-purple-500">
                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                                <line x1="12" x2="12" y1="15" y2="3"></line>
                                                            </svg>
                                                            Descargar justificante
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                            
                                            {{-- ======= ZONA DE DRAG & DROP ======= --}}
                                            {{-- Área grande para arrastrar archivos o hacer click --}}
                                            <div class="mt-2">
                                                <div class="flex items-center justify-center w-full">
                                                    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                                        {{-- Estado cuando NO hay archivo --}}
                                                        <template x-if="!reserva.justificante_path">
                                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                {{-- Icono de documento --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 mb-2 text-gray-400">
                                                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                                </svg>
                                                                <p class="mb-1 text-sm text-gray-500"><span class="font-semibold">Haz clic para subir</span> o arrastra y suelta</p>
                                                                <p class="text-xs text-gray-500">PDF, DOC, JPG o PNG (Máx. 10MB)</p>
                                                            </div>
                                                        </template>
                                                        
                                                        {{-- Estado cuando SÍ hay archivo --}}
                                                        <template x-if="reserva.justificante_path">
                                                            <div class="flex items-center justify-center p-4">
                                                                <div class="flex items-center">
                                                                    {{-- Icono de archivo existente --}}
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 mr-3 text-purple-500">
                                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                                                        <polyline points="10 9 9 9 8 9"></polyline>
                                                                    </svg>
                                                                    <div class="flex flex-col">
                                                                        <span class="text-sm font-medium text-purple-600" x-text="reserva.justificante_nombre"></span>
                                                                        <span class="text-xs text-gray-500">Haz clic para cambiar el archivo</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                            
                                                        {{-- Input file para la zona de drop --}}
                                                        <input 
                                                            id="dropzone-file" 
                                                            type="file" 
                                                            class="hidden" 
                                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" 
                                                            @change="subirJustificante(reserva.id, $event)"
                                                        />
                                                    </label>
                                                </div>
                                            </div>
                                            {{-- Texto de ayuda --}}
                                            <p class="mt-2 text-xs text-gray-500">Sube un documento o imagen que justifique tu falta de asistencia.</p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Estado vacío --}}
                        <template x-if="reservas.length === 0">
                            <div class="text-center py-12">
                                <p class="text-gray-500">No hay clases pendientes de justificar</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======= JAVASCRIPT CON ALPINE.JS ======= --}}
<script>
/**
 * Componente Alpine.js para gestionar las ausencias y justificantes
 * 
 * Funcionalidades:
 * - Cargar clases no asistidas
 * - Subir documentos de justificación
 * - Descargar justificantes existentes
 * - Manejo de notificaciones de éxito/error
 */
function ausenciasHandler() {
    return {
        // ======= ESTADO REACTIVO =======
        reservas: [],           // Array de clases no asistidas
        loading: true,          // Estado de carga
        
        /**
         * Inicializa el componente cargando las faltas
         */
        init() {
            this.cargarFaltas();
        },

        /**
         * Carga las clases no asistidas desde la API
         */
        async cargarFaltas() {
            try {
                const response = await fetch('/api/reservations/missed');
                if (!response.ok) throw new Error('Error al cargar las faltas');
                
                this.reservas = await response.json();
            } catch (error) {
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        /**
         * Sube un archivo de justificante para una reserva específica
         * @param {number} reservaId - ID de la reserva a justificar
         * @param {Event} event - Evento del input file
         */
        async subirJustificante(reservaId, event) {
            const file = event.target.files[0];
            if (!file) return;

            // Preparar FormData para subida de archivo
            const formData = new FormData();
            formData.append('justificante', file);

            // Obtener token CSRF de forma segura
            const tokenElement = document.querySelector('meta[name="csrf-token"]');
            if (!tokenElement) {
                window.dispatchEvent(new CustomEvent('show-notification', { 
                    detail: { 
                        type: 'error', 
                        message: 'Error: Token de seguridad no encontrado. Por favor, recarga la página.' 
                    } 
                }));
                return;
            }

            try {
                // Enviar archivo al servidor
                const response = await fetch(`/reservations/${reservaId}/justificante`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': tokenElement.content
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al subir el justificante');
                }

                // Recargar lista para mostrar cambios
                await this.cargarFaltas();
                
                // Mostrar notificación de éxito
                window.dispatchEvent(new CustomEvent('show-notification', { 
                    detail: { 
                        type: 'success', 
                        message: 'Justificante subido correctamente' 
                    } 
                }));
            } catch (error) {
                console.error('Error:', error);
                // Mostrar notificación de error
                window.dispatchEvent(new CustomEvent('show-notification', { 
                    detail: { 
                        type: 'error', 
                        message: error.message || 'Error al subir el justificante' 
                    } 
                }));
            }
        }
    };
}
</script>