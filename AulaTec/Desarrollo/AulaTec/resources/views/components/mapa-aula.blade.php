{{-- Componente interactivo para seleccionar asientos en un aula --}}
{{-- Recibe $asientosOcupados: array con IDs de asientos ya reservados --}}

{{-- Props del componente con valor por defecto --}}
@props(['asientosOcupados' => []])

{{-- ======= CONTENEDOR PRINCIPAL ======= --}}
{{-- Componente Alpine.js con datos reactivos --}}
<div x-data="mapaAula" class="flex flex-col items-center">
    
    {{-- ======= PIZARRA ======= --}}
    {{-- Referencia visual de la orientación del aula --}}
    <div class="mb-6 w-full max-w-3xl bg-gray-200 p-2 text-center rounded-md">
        Pizarra
    </div>

    {{-- ======= GRID DE ASIENTOS ======= --}}
    {{-- Grid de 8 columnas con ancho máximo responsivo --}}
    <div class="grid gap-1 sm:gap-2 mx-auto" style="grid-template-columns: repeat(8, 1fr); max-width: 500px;">
        {{-- Bucle por cada fila del aula (A, B, C, D) --}}
        <template x-for="fila in filas">
            {{-- Contenedor para los asientos de una fila --}}
            <div class="contents">
                {{-- Bucle por cada asiento en la fila actual --}}
                <template x-for="asiento in getAsientosPorFila(fila)">
                    {{-- Contenedor del asiento con span condicional para mesa profesor --}}
                    <div :class="{'col-span-3': asiento.tipo === 'profesor'}">
                        
                        {{-- ======= MESA DEL PROFESOR ======= --}}
                        {{-- Elemento especial que ocupa 3 columnas --}}
                        <template x-if="asiento.tipo === 'profesor'">
                            <div :class="{
                                'w-30 text-xs': isMobile,
                                'w-40 text-sm': !isMobile,
                                'h-9 sm:h-12': true
                            }" class="bg-gray-300 rounded-md flex items-center justify-center">
                                Mesa del profesor
                            </div>
                        </template>
                        
                        {{-- ======= ASIENTOS NORMALES ======= --}}
                        {{-- Botones clickeables para seleccionar asiento --}}
                        <template x-if="!asiento.tipo">
                            <button 
                                :class="{
                                    // Tamaños responsivos según dispositivo
                                    'w-9 h-9 text-xs': isMobile,
                                    'w-12 h-12': !isMobile,
                                    // Estados visuales según disponibilidad y selección
                                    'bg-purple-600 text-white hover:bg-purple-700': selectedSeat === asiento.id,
                                    'bg-green-500 text-white hover:bg-green-600': asiento.disponible && selectedSeat !== asiento.id,
                                    'bg-gray-400 text-gray-100 cursor-not-allowed': !asiento.disponible
                                }"
                                class="rounded-md flex items-center justify-center font-medium transition-colors"
                                {{-- Evento de click para seleccionar asiento --}}
                                @click="selectSeat(asiento)"
                                {{-- Deshabilitar si no está disponible --}}
                                :disabled="!asiento.disponible"
                                {{-- Mostrar ID del asiento como texto --}}
                                x-text="asiento.id"
                            ></button>
                        </template>

                        {{-- ======= ESPACIOS SIN MESA ======= --}}
                        {{-- Posiciones donde no hay asientos disponibles --}}
                        <template x-if="asiento.tipo === 'no-mesa'">
                            <div class="bg-gray-100 rounded-md border-2 border-dashed border-gray-200 w-9 h-9 sm:w-12 sm:h-12 flex items-center justify-center text-gray-400 text-xs">
                                N/D
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- ======= INFORMACIÓN DEL ASIENTO SELECCIONADO ======= --}}
    {{-- Panel que aparece cuando se selecciona un asiento --}}
    <div x-show="selectedSeat" 
         {{-- Animaciones de entrada suaves --}}
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         {{-- Animaciones de salida --}}
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="mt-4 text-center bg-white rounded-lg shadow-sm border border-gray-200 p-3">
        <p class="font-medium">
            Asiento seleccionado: 
            {{-- ID del asiento en color púrpura --}}
            <span class="text-purple-600 font-bold" x-text="selectedSeat"></span>
        </p>
    </div>
</div>

{{-- ======= JAVASCRIPT: LÓGICA DEL COMPONENTE ======= --}}
<script>
    // ======= INICIALIZACIÓN DE ALPINE.JS =======
    document.addEventListener('alpine:init', () => {
        // Registrar componente Alpine con datos reactivos
        Alpine.data('mapaAula', () => ({
            
            // ======= CONFIGURACIÓN ESTÁTICA DEL AULA =======
            // Array con todos los asientos y sus propiedades
            asientos: [
                // ======= PRIMERA FILA (A): Mesa profesor + 4 asientos + 1 espacio vacío =======
                { id: 'PROF', fila: 'A', columna: '1-3', tipo: 'profesor' },
                { id: 'A4', fila: 'A', columna: 4, disponible: true },
                { id: 'A5', fila: 'A', columna: 5, disponible: true },
                { id: 'A6', fila: 'A', columna: 6, disponible: true },
                { id: 'A7', fila: 'A', columna: 7, disponible: true },
                { id: 'A8', fila: 'A', columna: 8, tipo: 'no-mesa' },
                
                // ======= SEGUNDA FILA (B): 8 asientos completos =======
                { id: 'B1', fila: 'B', columna: 1, disponible: true },
                { id: 'B2', fila: 'B', columna: 2, disponible: true },
                { id: 'B3', fila: 'B', columna: 3, disponible: true },
                { id: 'B4', fila: 'B', columna: 4, disponible: true },
                { id: 'B5', fila: 'B', columna: 5, disponible: true },
                { id: 'B6', fila: 'B', columna: 6, disponible: true },
                { id: 'B7', fila: 'B', columna: 7, disponible: true },
                { id: 'B8', fila: 'B', columna: 8, disponible: true },
                
                // ======= TERCERA FILA (C): 8 asientos completos =======
                { id: 'C1', fila: 'C', columna: 1, disponible: true },
                { id: 'C2', fila: 'C', columna: 2, disponible: true },
                { id: 'C3', fila: 'C', columna: 3, disponible: true },
                { id: 'C4', fila: 'C', columna: 4, disponible: true },
                { id: 'C5', fila: 'C', columna: 5, disponible: true },
                { id: 'C6', fila: 'C', columna: 6, disponible: true },
                { id: 'C7', fila: 'C', columna: 7, disponible: true },
                { id: 'C8', fila: 'C', columna: 8, disponible: true },
                
                // ======= CUARTA FILA (D): 7 asientos + 1 espacio vacío =======
                { id: 'D1', fila: 'D', columna: 1, disponible: true },
                { id: 'D2', fila: 'D', columna: 2, disponible: true },
                { id: 'D3', fila: 'D', columna: 3, disponible: true },
                { id: 'D4', fila: 'D', columna: 4, disponible: true },
                { id: 'D5', fila: 'D', columna: 5, disponible: true },
                { id: 'D6', fila: 'D', columna: 6, disponible: true },
                { id: 'D7', fila: 'D', columna: 7, disponible: true },
                { id: 'D8', fila: 'D', columna: 8, tipo: 'no-mesa' }
            ],
            
            // ======= ESTADO REACTIVO =======
            asientosOcupados: @json($asientosOcupados), // Array de asientos ocupados desde el backend
            isMobile: window.innerWidth < 640,           // Detección de dispositivo móvil
            selectedSeat: null,                          // Asiento actualmente seleccionado
            filas: ['A', 'B', 'C', 'D'],                // Array de filas del aula
            
            // ======= INICIALIZACIÓN DEL COMPONENTE =======
            init() {
                // Actualizar disponibilidad según asientos ocupados del backend
                this.asientos = this.asientos.map(asiento => ({
                    ...asiento,
                    disponible: !this.asientosOcupados.includes(asiento.id)
                }));
    
                // Escuchar cambios de tamaño de ventana para responsividad
                window.addEventListener('resize', () => {
                    this.isMobile = window.innerWidth < 640;
                });
                
                // Sincronizar selección con variable global para otros componentes
                this.$watch('selectedSeat', value => {
                    window.selectedSeat = value;
                });
            },
            
            // ======= MÉTODO: FILTRAR ASIENTOS POR FILA =======
            // Devuelve solo los asientos que pertenecen a una fila específica
            getAsientosPorFila(fila) {
                return this.asientos.filter(asiento => asiento.fila === fila);
            },
            
            // ======= MÉTODO: SELECCIONAR ASIENTO =======
            // Maneja el click en un asiento para seleccionarlo
            selectSeat(asiento) {
                // Solo permitir selección si está disponible y no es elemento especial
                if (asiento.disponible && !asiento.tipo) {
                    this.selectedSeat = asiento.id;
                    window.selectedSeat = asiento.id; // Sincronizar con variable global
                }
            }
        }))
    })
</script>