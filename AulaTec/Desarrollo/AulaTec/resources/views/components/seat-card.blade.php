{{-- Componente reutilizable para mostrar información de un asiento individual --}}
{{-- Recibe: fila, numero, ocupado (boolean), estudiante (array con datos del usuario) --}}

{{-- Props del componente con tipos específicos --}}
@props(['fila', 'numero', 'ocupado', 'estudiante'])

{{-- ======= TARJETA PRINCIPAL DEL ASIENTO ======= --}}
{{-- Contenedor con bordes dinámicos según estado de ocupación --}}
<div class="bg-white rounded-lg border {{ $ocupado ? 'border-purple-200' : 'border-green-200' }} shadow-sm overflow-hidden">
    
    {{-- ======= BARRA SUPERIOR DE COLOR ======= --}}
    {{-- Indicador visual: púrpura=ocupado, verde=disponible --}}
    <div class="h-2 {{ $ocupado ? 'bg-purple-600' : 'bg-green-500' }}"></div>
    
    {{-- ======= CONTENIDO PRINCIPAL ======= --}}
    <div class="p-4">
        {{-- ======= HEADER: ID DEL ASIENTO Y MENÚ ======= --}}
        {{-- Fila superior con identificador y botón de opciones --}}
        <div class="flex justify-between items-start mb-3">
            {{-- ======= BADGE DEL ASIENTO ======= --}}
            {{-- Etiqueta con colores dinámicos según disponibilidad --}}
            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                       {{ $ocupado ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                {{-- Formato: "Asiento A1", "Asiento B5", etc. --}}
                Asiento {{ $fila }}{{ $numero }}
            </span>
            
            {{-- ======= BOTÓN DE OPCIONES ======= --}}
            {{-- Solo visible cuando el asiento está ocupado --}}
            @if($ocupado)
            <button class="h-8 w-8 rounded-md hover:bg-gray-100 flex items-center justify-center">
                {{-- Icono de tres puntos (menú de opciones) --}}
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    {{-- Tres círculos horizontales para representar menú --}}
                    <circle cx="12" cy="12" r="1"/>
                    <circle cx="19" cy="12" r="1"/>
                    <circle cx="5" cy="12" r="1"/>
                </svg>
            </button>
            @endif
        </div>

        {{-- ======= CONTENIDO CONDICIONAL SEGÚN OCUPACIÓN ======= --}}
        
        {{-- ======= INFORMACIÓN DEL ESTUDIANTE (OCUPADO) ======= --}}
        @if($ocupado)
        <div class="space-y-2">
            {{-- ======= NOMBRE DEL ESTUDIANTE ======= --}}
            {{-- Fila con icono de usuario y nombre --}}
            <div class="flex items-center">
                {{-- Icono de perfil de usuario --}}
                <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    {{-- Path de figura humana: círculo (cabeza) + rectángulo (cuerpo) --}}
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                {{-- Nombre completo del estudiante --}}
                <span class="font-medium">{{ $estudiante['nombre'] }}</span>
            </div>
            
            {{-- ======= DATOS ADICIONALES DEL ESTUDIANTE ======= --}}
            {{-- Información secundaria en texto más pequeño --}}
            <div class="text-sm text-gray-500">
                {{-- Número de matrícula universitaria --}}
                <p>Matrícula: {{ $estudiante['matricula'] }}</p>
                {{-- Email con truncate para evitar desbordamiento --}}
                <p class="truncate">{{ $estudiante['email'] }}</p>
            </div>
        </div>
        
        {{-- ======= ESTADO DISPONIBLE (NO OCUPADO) ======= --}}
        @else
        {{-- ======= MENSAJE DE DISPONIBILIDAD ======= --}}
        {{-- Área centrada con mensaje neutral --}}
        <div class="flex items-center justify-center h-16 text-gray-500">
            <p>Asiento disponible</p>
        </div>
        @endif
    </div>
</div>