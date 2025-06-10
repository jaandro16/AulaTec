{{-- Componente para mostrar la lista de clases disponibles --}}
{{-- Se actualiza automáticamente cuando se selecciona fecha y horario --}}

{{-- Contenedor principal con Alpine.js --}}
<div x-data="clasesList()" class="space-y-1">
    
    {{-- ======= ESTADO DE CARGA ======= --}}
    {{-- Spinner que se muestra mientras cargan las clases --}}
    <div x-show="loading" class="flex justify-center">
        <svg class="animate-spin h-8 w-8 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- ======= ESTADO DE ERROR ======= --}}
    {{-- Mensaje de error si falla la carga de clases --}}
    <div x-show="error" 
         class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" 
         role="alert">
        <span x-text="error"></span>
    </div>

    {{-- ======= LISTA DE CLASES DISPONIBLES ======= --}}
    {{-- Solo se muestra si hay clases para mostrar --}}
    <template x-if="clasesDisponibles.length > 0">
        <div>
            {{-- Iteración por cada clase disponible --}}
            <template x-for="(clase, index) in clasesDisponibles" :key="clase.id">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden transition-all hover:shadow-md mb-4">
                    
                    {{-- ======= INFORMACIÓN DE LA CLASE ======= --}}
                    <div class="p-6">
                        {{-- Nombre de la asignatura --}}
                        <h3 class="text-xl font-semibold mb-2" x-text="clase.nombre"></h3>
                        {{-- Detalles de la clase --}}
                        <div class="space-y-2 text-sm text-gray-500">
                            {{-- Profesor que imparte la clase --}}
                            <div class="flex items-center">
                                {{-- Icono de persona --}}
                                <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span x-text="'Profesor: ' + clase.profesor"></span>
                            </div>
                            {{-- Aula donde se imparte --}}
                            <div class="flex items-center">
                                {{-- Icono de edificio --}}
                                <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span x-text="'Aula: ' + clase.aula"></span>
                            </div>
                            {{-- Disponibilidad de asientos --}}
                            <div class="flex items-center">
                                {{-- Icono de grupo de personas --}}
                                <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span x-text="'Asientos disponibles: ' + clase.asientosDisponibles + ' de ' + clase.capacidad"></span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- ======= BOTÓN DE ACCIÓN ======= --}}
                    {{-- Footer con botón para seleccionar asiento --}}
                    <div class="bg-gray-50 px-6 py-3">
                        <button 
                            @click="seleccionarClase(clase)"
                            {{-- Deshabilitar si no hay asientos disponibles --}}
                            :disabled="clase.asientosDisponibles === 0"
                            {{-- Estilo visual para botón deshabilitado --}}
                            :class="{'opacity-50 cursor-not-allowed': clase.asientosDisponibles === 0}"
                            class="w-full bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white py-2 px-4 rounded-md transition-colors">
                            {{-- Texto dinámico según disponibilidad --}}
                            <span x-text="clase.asientosDisponibles === 0 ? 'Clase llena' : 'Seleccionar asiento'"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </template>
    
    {{-- ======= ESTADO VACÍO ======= --}}
    {{-- Mensaje cuando no hay clases disponibles para la fecha/horario seleccionado --}}
    <template x-if="clasesDisponibles.length === 0">
        <div class="text-center py-12 text-gray-500">
            <p class="mb-2">No hay clases disponibles para esta fecha y horario</p>
            <p>Por favor, selecciona otra fecha u horario</p>
        </div>
    </template>

    {{-- ======= JAVASCRIPT CON ALPINE.JS ======= --}}
    <script>
    /**
     * Componente Alpine.js para la lista de clases disponibles
     * 
     * Funcionalidades:
     * - Carga automática cuando cambia fecha y horario
     * - Mostrar detalles de cada clase
     * - Navegación a selección de asientos
     * - Manejo de estados de carga y error
     */
    function clasesList() {
        return {
            // ======= ESTADO REACTIVO =======
            clasesDisponibles: [],      // Array de clases disponibles para fecha/horario
            loading: false,             // Estado de carga
            error: null,                // Mensaje de error

            /**
             * Inicializa el componente y configura watchers
             * Se ejecuta automáticamente al montar el componente
             */
            init() {
                // Observar cambios en el horario seleccionado del store global
                this.$watch('$store.calendario.horarioSeleccionado', (value) => {
                    // Solo cargar si hay fecha Y horario seleccionados
                    if (this.$store.calendario.fechaSeleccionada && value) {
                        this.loadClases();
                    } else {
                        // Limpiar lista si falta algún parámetro
                        this.clasesDisponibles = [];
                    }
                });
            },
            
            /**
             * Carga las clases disponibles desde la API
             * Basado en la fecha y horario seleccionados en el store
             */
            async loadClases() {
                this.loading = true;
                this.error = null;
                
                try {
                    // PASO 1: Formatear la fecha para la API (YYYY-MM-DD)
                    const fecha = new Date(this.$store.calendario.fechaSeleccionada);
                    const fechaFormateada = fecha.getFullYear() + '-' + 
                        String(fecha.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(fecha.getDate()).padStart(2, '0');
                    
                    // PASO 2: Hacer petición a la API con parámetros de fecha y horario
                    const response = await fetch(`/api/available-classes?date=${fechaFormateada}&time_slot_id=${this.$store.calendario.horarioSeleccionado.id}`);
                    
                    if (!response.ok) throw new Error('Error al cargar las clases');
                    
                    // PASO 3: Procesar respuesta JSON
                    const data = await response.json();
                    if (data.status === 'success') {
                        this.clasesDisponibles = data.data;
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.error = error.message;
                    this.clasesDisponibles = [];
                } finally {
                    this.loading = false;
                }
            },
            
            /**
             * Navega a la página de selección de asientos para la clase elegida
             * @param {Object} clase - Objeto clase con token único
             */
            seleccionarClase(clase) {
                // Redirigir usando el token único de la clase
                window.location.href = `/seleccion-asientos/${clase.token}`;
            }
        };
    }
    </script>
</div>