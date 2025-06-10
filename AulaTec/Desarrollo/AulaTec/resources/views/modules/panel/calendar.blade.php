{{-- Componente de calendario interactivo para selección de fechas --}}
{{-- Permite navegar entre meses y seleccionar fechas futuras --}}

{{-- Contenedor principal del calendario con Alpine.js --}}
<div class="calendario-container" x-data="calendario()">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        
        {{-- ======= HEADER DEL CALENDARIO ======= --}}
        {{-- Barra superior con navegación de mes y año --}}
        <div class="p-2 bg-purple-600 text-white flex justify-between items-center">
            {{-- Botón mes anterior --}}
            <button 
                @click="mesAnterior()" 
                class="p-2 rounded-full hover:bg-white/20 transition-colors"
                aria-label="Mes anterior">
                {{-- Icono flecha izquierda --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            {{-- Título con mes y año actual --}}
            <h2 class="text-xl font-bold" x-text="nombreMes + ' ' + año"></h2>
            {{-- Botón mes siguiente --}}
            <button 
                @click="mesSiguiente()" 
                class="p-2 rounded-full hover:bg-white/20 transition-colors"
                aria-label="Mes siguiente">
                {{-- Icono flecha derecha --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        {{-- ======= ENCABEZADOS DE DÍAS DE LA SEMANA ======= --}}
        {{-- Fila con nombres de días (Lun, Mar, Mié, etc.) --}}
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
            <template x-for="dia in diasSemana" :key="dia">
                <div class="py-2 text-center text-sm font-medium text-gray-500">
                    <span x-text="dia"></span>
                </div>
            </template>
        </div>

        {{-- ======= GRID DE DÍAS DEL CALENDARIO ======= --}}
        {{-- Cuadrícula 7x6 con todos los días visibles --}}
        <div class="grid grid-cols-7">
            <template x-for="(dia, index) in diasCalendario" :key="index">
                <div 
                    @click="seleccionarFecha(dia)"
                    {{-- Clases dinámicas según estado del día --}}
                    :class="{
                        'bg-gray-100': dia.mesActual,                                      // Días del mes actual
                        'bg-gray-50 text-gray-400': !dia.mesActual,                       // Días de meses anterior/siguiente
                        'bg-purple-100 border-purple-500': esFechaSeleccionada(dia),      // Día seleccionado
                        'cursor-pointer hover:bg-gray-200': dia.mesActual && !dia.pasado, // Días clickeables
                        'cursor-not-allowed opacity-50': dia.pasado && !permitirFechasPasadas // Días bloqueados
                    }"
                    class="h-9 p-1 border border-gray-100 relative flex items-center justify-center">
                    {{-- Número del día con estilo especial para hoy --}}
                    <span 
                        :class="{'text-purple-600 font-medium': esHoy(dia)}"
                        class="text-sm"
                        x-text="dia.numero">
                    </span>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- ======= JAVASCRIPT CON ALPINE.JS ======= --}}
<script>
/**
 * Componente Alpine.js para el calendario interactivo
 * 
 * Funcionalidades:
 * - Navegación entre meses y años
 * - Selección de fechas futuras
 * - Integración con store global de Alpine
 * - Marcado visual de fecha actual y seleccionada
 */
function calendario() {
    return {
        // ======= PROPIEDADES REACTIVAS =======
        fecha: new Date(),                  // Fecha base para mostrar el mes/año actual
        fechaSeleccionada: null,            // Fecha seleccionada por el usuario
        permitirFechasPasadas: false,       // Control para permitir/bloquear fechas pasadas
        
        // ======= DATOS ESTÁTICOS =======
        diasSemana: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'], // Nombres cortos de días
        meses: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], // Nombres de meses
        
        // ======= PROPIEDADES COMPUTADAS =======
        
        /**
         * Obtiene el año de la fecha actual del calendario
         */
        get año() {
            return this.fecha.getFullYear();
        },
        
        /**
         * Obtiene el mes (0-11) de la fecha actual del calendario
         */
        get mes() {
            return this.fecha.getMonth();
        },
        
        /**
         * Obtiene el nombre del mes actual en español
         */
        get nombreMes() {
            return this.meses[this.mes];
        },
        
        /**
         * Genera el array completo de días para mostrar en el calendario
         * Incluye días del mes anterior, actual y siguiente para llenar la cuadrícula 7x6
         */
        get diasCalendario() {
            let dias = [];
            
            // PASO 1: Calcular primer y último día del mes actual
            let primerDia = new Date(this.año, this.mes, 1);
            let ultimoDia = new Date(this.año, this.mes + 1, 0);
            
            // PASO 2: Determinar qué día de la semana empieza el mes (ajustado para Lunes=0)
            let diaSemana = primerDia.getDay();
            diaSemana = diaSemana === 0 ? 6 : diaSemana - 1; // Convertir Domingo(0) a 6, y restar 1 al resto
            
            // PASO 3: Agregar días del mes anterior para completar la primera semana
            let ultimoDiaMesAnterior = new Date(this.año, this.mes, 0).getDate();
            for (let i = diaSemana - 1; i >= 0; i--) {
                let fecha = new Date(this.año, this.mes - 1, ultimoDiaMesAnterior - i);
                dias.push({
                    fecha: fecha,
                    numero: ultimoDiaMesAnterior - i,
                    mesActual: false,
                    pasado: fecha < new Date(new Date().setHours(0, 0, 0, 0)) // Comparar solo fechas sin hora
                });
            }
            
            // PASO 4: Agregar todos los días del mes actual
            for (let i = 1; i <= ultimoDia.getDate(); i++) {
                let fecha = new Date(this.año, this.mes, i);
                dias.push({
                    fecha: fecha,
                    numero: i,
                    mesActual: true,
                    pasado: fecha < new Date(new Date().setHours(0, 0, 0, 0)) // Marcar como pasado si es anterior a hoy
                });
            }
            
            // PASO 5: Agregar días del mes siguiente hasta completar 42 casillas (6 semanas x 7 días)
            let diasRestantes = 42 - dias.length;
            for (let i = 1; i <= diasRestantes; i++) {
                let fecha = new Date(this.año, this.mes + 1, i);
                dias.push({
                    fecha: fecha,
                    numero: i,
                    mesActual: false,
                    pasado: fecha < new Date(new Date().setHours(0, 0, 0, 0))
                });
            }
            
            return dias;
        },
        
        // ======= MÉTODOS DE NAVEGACIÓN =======
        
        /**
         * Navega al mes anterior
         * Actualiza la fecha base para regenerar el calendario
         */
        mesAnterior() {
            this.fecha = new Date(this.año, this.mes - 1, 1);
        },
        
        /**
         * Navega al mes siguiente
         * Actualiza la fecha base para regenerar el calendario
         */
        mesSiguiente() {
            this.fecha = new Date(this.año, this.mes + 1, 1);
        },
        
        // ======= MÉTODOS DE SELECCIÓN =======
        
        /**
         * Selecciona una fecha del calendario
         * @param {Object} dia - Objeto día con propiedades fecha, numero, mesActual, pasado
         */
        seleccionarFecha(dia) {
            // No permitir selección de fechas pasadas (a menos que esté habilitado)
            if (dia.pasado && !this.permitirFechasPasadas) return;
            
            // Actualizar fecha seleccionada localmente
            this.fechaSeleccionada = dia.fecha;
            
            // Actualizar store global de Alpine para comunicación entre componentes
            Alpine.store('calendario').setFecha(dia.fecha);
            
            // Limpiar horario seleccionado al cambiar fecha
            Alpine.store('calendario').setHorario(null);
        },
        
        // ======= MÉTODOS DE ESTADO VISUAL =======
        
        /**
         * Verifica si un día es el día actual (hoy)
         * @param {Object} dia - Objeto día a verificar
         * @returns {boolean} - True si es hoy
         */
        esHoy(dia) {
            const hoy = new Date();
            return dia.fecha.getDate() === hoy.getDate() && 
                   dia.fecha.getMonth() === hoy.getMonth() && 
                   dia.fecha.getFullYear() === hoy.getFullYear();
        },
        
        /**
         * Verifica si un día está actualmente seleccionado
         * @param {Object} dia - Objeto día a verificar
         * @returns {boolean} - True si está seleccionado
         */
        esFechaSeleccionada(dia) {
            if (!this.fechaSeleccionada) return false;
            return dia.fecha.getDate() === this.fechaSeleccionada.getDate() && 
                   dia.fecha.getMonth() === this.fechaSeleccionada.getMonth() && 
                   dia.fecha.getFullYear() === this.fechaSeleccionada.getFullYear();
        }
    };
}
</script>