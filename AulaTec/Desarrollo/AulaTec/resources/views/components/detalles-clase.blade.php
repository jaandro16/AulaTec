{{-- Componente reutilizable para mostrar información detallada de una clase --}}
{{-- Recibe $classDetails con todas las relaciones cargadas (subject, teacher, timeSlot, classroom, reservations) --}}

{{-- Contenedor principal con espaciado vertical entre elementos --}}
<div class="space-y-4">
    {{-- ======= TÍTULO: NOMBRE DE LA ASIGNATURA ======= --}}
    {{-- Título principal responsive (más grande en desktop) --}}
    <h3 class="text-lg md:text-xl font-semibold">{{ $classDetails->subject->name }}</h3>

    {{-- ======= INFORMACIÓN DETALLADA DE LA CLASE ======= --}}
    {{-- Grid de información con iconos descriptivos --}}
    <div class="space-y-3">
        
        {{-- ======= INFORMACIÓN DEL PROFESOR ======= --}}
        {{-- Muestra nombre completo del docente que imparte la clase --}}
        <div class="flex items-start">
            {{-- Icono de usuario con tamaños responsivos --}}
            <i class="fas fa-user h-4 w-4 md:h-5 md:w-5 mr-2 md:mr-3 text-gray-500 mt-0.5"></i>
            <div>
                {{-- Etiqueta descriptiva --}}
                <p class="font-medium text-sm md:text-base">Profesor</p>
                {{-- Nombre completo: nombre + apellido --}}
                <p class="text-sm md:text-base text-gray-600">
                    {{ $classDetails->teacher->nombre }} {{ $classDetails->teacher->apellido }}
                </p>
            </div>
        </div>

        {{-- ======= FECHA DE LA CLASE ======= --}}
        {{-- Fecha formateada en español con Carbon --}}
        <div class="flex items-start">
            {{-- Icono de calendario --}}
            <i class="fas fa-calendar h-4 w-4 md:h-5 md:w-5 mr-2 md:mr-3 text-gray-500 mt-0.5"></i>
            <div>
                <p class="font-medium text-sm md:text-base">Fecha</p>
                {{-- Formato: "lunes, 15 de enero de 2025" usando Carbon con locale español --}}
                <p class="text-sm md:text-base text-gray-600">
                    {{ \Carbon\Carbon::parse($classDetails->date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
            </div>
        </div>

        {{-- ======= HORA DE INICIO ======= --}}
        {{-- Muestra la hora de comienzo de la clase --}}
        <div class="flex items-start">
            {{-- Icono de reloj --}}
            <i class="fas fa-clock h-4 w-4 md:h-5 md:w-5 mr-2 md:mr-3 text-gray-500 mt-0.5"></i>
            <div>
                <p class="font-medium text-sm md:text-base">Hora de Inicio</p>
                {{-- Formato HH:MM desde la relación timeSlot --}}
                <p class="text-sm md:text-base text-gray-600">
                    {{ $classDetails->timeSlot->start_time }}
                </p>
            </div>
        </div>

        {{-- ======= UBICACIÓN DEL AULA ======= --}}
        {{-- Nombre del aula donde se imparte la clase --}}
        <div class="flex items-start">
            {{-- Icono de ubicación/marcador --}}
            <i class="fas fa-map-marker-alt h-4 w-4 md:h-5 md:w-5 mr-2 md:mr-3 text-gray-500 mt-0.5"></i>
            <div>
                <p class="font-medium text-sm md:text-base">Ubicación</p>
                {{-- Nombre del aula desde la relación classroom --}}
                <p class="text-sm md:text-base text-gray-600">{{ $classDetails->classroom->name }}</p>
            </div>
        </div>

        {{-- ======= DISPONIBILIDAD DE ASIENTOS ======= --}}
        {{-- Sección con cálculos dinámicos y barra de progreso --}}
        <div class="flex items-start">
            {{-- Icono de usuarios/grupo --}}
            <i class="fas fa-users h-4 w-4 md:h-5 md:w-5 mr-2 md:mr-3 text-gray-500 mt-0.5"></i>
            <div class="w-full">
                <p class="font-medium text-sm md:text-base">Disponibilidad</p>
                
                {{-- ======= CÁLCULOS DE OCUPACIÓN ======= --}}
                @php
                    // Contar reservas existentes para esta clase
                    $asientosOcupados = $classDetails->reservations->count();
                    
                    // Capacidad máxima del aula
                    $capacidadTotal = $classDetails->classroom->capacity;
                    
                    // Asientos aún disponibles para reservar
                    $disponibles = $capacidadTotal - $asientosOcupados;
                    
                    // Porcentaje de ocupación actual
                    $porcentajeOcupado = ($asientosOcupados / $capacidadTotal) * 100;
                @endphp
                
                {{-- ======= TEXTO DE DISPONIBILIDAD ======= --}}
                {{-- Formato: "X de Y asientos disponibles" --}}
                <p class="text-sm md:text-base text-gray-600">
                    {{ $disponibles }} de {{ $capacidadTotal }} asientos disponibles
                </p>
                
                {{-- ======= BARRA DE PROGRESO VISUAL ======= --}}
                {{-- Barra horizontal que muestra visualmente la disponibilidad --}}
                <div class="w-full bg-gray-200 rounded-full h-2 md:h-2.5 mt-2">
                    {{-- Barra de progreso con gradiente (representa disponibilidad, no ocupación) --}}
                    <div class="bg-gradient-to-r from-purple-600 to-cyan-500 h-2 md:h-2.5 rounded-full transition-all duration-300" 
                         {{-- Ancho dinámico: 100% - porcentaje ocupado = porcentaje disponible --}}
                         style="width: {{ 100 - $porcentajeOcupado }}%">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>