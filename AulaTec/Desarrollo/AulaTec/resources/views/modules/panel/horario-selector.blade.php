{{-- Componente selector de franjas horarias --}}
{{-- Se actualiza automáticamente cuando se selecciona una fecha en el calendario --}}

{{-- Contenedor principal con Alpine.js --}}
<div x-data="{ 
    // ======= ESTADO REACTIVO =======
    timeSlots: [],          // Array de franjas horarias disponibles
    selectedSlot: null,     // Franja horaria seleccionada actualmente
    loading: false,         // Estado de carga durante peticiones API
    error: null,            // Mensaje de error si falla la carga

    /**
     * Inicializa el componente y configura watchers
     * Se ejecuta automáticamente al montar el componente
     */
    init() {
        // Observar cambios en la fecha seleccionada del store global
        this.$watch('$store.calendario.fechaSeleccionada', (value) => {
            if (value) {
                const fecha = new Date(value);
                const diaSemana = fecha.getDay(); // 0=Domingo, 1=Lunes, ..., 6=Sábado
                
                // VALIDACIÓN: Si es sábado (6) o domingo (0), no hay clases
                if (diaSemana === 6 || diaSemana === 0) {
                    this.timeSlots = [];
                    this.selectedSlot = null;
                    this.error = 'No hay horarios disponibles los fines de semana';
                    return;
                }
                
                // Si es día laboral, limpiar error y cargar horarios
                this.error = null;
                this.loadTimeSlots();
            } else {
                // Si no hay fecha seleccionada, limpiar todo
                this.timeSlots = [];
                this.selectedSlot = null;
            }
        });
    },

    /**
     * Verifica si una franja horaria ya ha pasado
     * Solo aplica para el día actual, comparando con la hora actual
     * @param {Object} slot - Objeto de franja horaria con formatted_time
     * @returns {boolean} - True si la franja ya pasó
     */
    isTimeSlotPassed(slot) {
        const now = new Date();
        const today = new Date().setHours(0,0,0,0);
        const selectedDate = new Date(this.$store.calendario.fechaSeleccionada).setHours(0,0,0,0);
        
        // Solo verificar si la fecha seleccionada es hoy
        if (selectedDate === today) {
            // Extraer hora de inicio del formato 'HH:MM - HH:MM'
            const [hours, minutes] = slot.formatted_time.split(' - ')[0].split(':');
            const slotTime = new Date();
            slotTime.setHours(parseInt(hours), parseInt(minutes), 0);
            
            // Comparar con la hora actual
            return now > slotTime;
        }
        // Para fechas futuras, ninguna franja ha pasado
        return false;
    },

    /**
     * Carga las franjas horarias disponibles desde la API
     * Maneja estados de carga, error y validación de franjas pasadas
     */
    async loadTimeSlots() {
        this.loading = true;
        this.error = null;
        
        try {
            const response = await fetch('/api/time-slots');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            this.timeSlots = data.data || [];
            
            // LIMPIEZA: Resetear selección si el slot seleccionado ya no está disponible
            if (this.selectedSlot && this.isTimeSlotPassed(this.selectedSlot)) {
                this.selectedSlot = null;
                Alpine.store('calendario').setHorario(null);
            }
        } catch (error) {
            console.error('Error:', error);
            this.error = error.message;
            this.timeSlots = [];
        } finally {
            this.loading = false;
        }
    },

    /**
     * Selecciona una franja horaria específica
     * Actualiza el store global para comunicación con otros componentes
     * @param {Object} slot - Objeto de franja horaria a seleccionar
     */
    selectSlot(slot) {
        // No permitir selección de franjas que ya pasaron
        if (this.isTimeSlotPassed(slot)) {
            return;
        }
        // Actualizar selección local y store global
        this.selectedSlot = slot;
        Alpine.store('calendario').setHorario(slot);
    }
}">
    
    {{-- ======= ESTADO DE ERROR ======= --}}
    {{-- Mensaje de error (ej: fines de semana, fallos de API) --}}
    <div x-show="error" 
         class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" 
         role="alert">
        <span x-text="error"></span>
    </div>

    {{-- ======= ESTADO DE CARGA ======= --}}
    {{-- Spinner que se muestra mientras cargan las franjas horarias --}}
    <div x-show="loading" class="flex justify-center">
        <svg class="animate-spin h-8 w-8 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- ======= LISTA DE FRANJAS HORARIAS ======= --}}
    {{-- Solo se muestra si hay datos y no hay errores --}}
    <div x-show="!loading && !error && timeSlots.length > 0" 
        class="space-y-2">
        {{-- Iteración por cada franja horaria disponible --}}
        <template x-for="slot in timeSlots" :key="slot.id">
            <div>
                <button 
                    @click="selectSlot(slot)"
                    {{-- Clases dinámicas según estado de la franja --}}
                    :class="{
                        'bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white': selectedSlot?.id === slot.id && !isTimeSlotPassed(slot),
                        'hover:bg-gray-50 border-gray-300': selectedSlot?.id !== slot.id && !isTimeSlotPassed(slot),
                        'bg-gray-100 text-gray-400 cursor-not-allowed': isTimeSlotPassed(slot)
                    }"
                    {{-- Deshabilitar franjas que ya pasaron --}}
                    :disabled="isTimeSlotPassed(slot)"
                    class="w-full p-2 border rounded-md transition-all duration-200 text-left font-medium flex justify-between items-center">
                    {{-- Horario formateado (ej: "09:00 - 10:30") --}}
                    <span x-text="slot.formatted_time"></span>
                    {{-- Mensaje de advertencia para franjas pasadas --}}
                    <span x-show="isTimeSlotPassed(slot)" 
                        class="text-red-500 text-sm">
                        Las clases de esta franja horaria ya no están disponibles
                    </span>
                </button>
            </div>
        </template>
    </div>

    {{-- ======= ESTADO VACÍO ======= --}}
    {{-- Mensaje cuando no hay franjas horarias disponibles --}}
    <div x-show="!loading && !error && timeSlots.length === 0" 
         class="text-center py-8 text-gray-500">
        No hay horarios disponibles
    </div>
</div>