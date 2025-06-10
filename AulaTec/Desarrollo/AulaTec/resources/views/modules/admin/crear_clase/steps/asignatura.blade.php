{{-- Contenedor principal --}}
<div>
    {{-- Encabezado con título y stepper --}}
    <div class="p-4 sm:p-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Detalles de la Clase</h2>
                <p class="text-sm text-gray-500">Paso 1 - Información de la asignatura</p>
            </div>
            @include('modules.admin.crear_clase.partials.stepper')
        </div>
    </div>

    {{-- Contenedor del formulario --}}
    <div class="p-4 sm:p-6 space-y-6">
        {{-- Formulario con evento wire:submit --}}
        <form wire:submit.prevent="nextStep">
            {{-- Campo de selección de asignatura --}}
            <div class="space-y-2">
                <label for="asignatura" class="text-sm font-medium text-gray-700 flex items-center">
                    <i class="fas fa-book text-purple-500 mr-2"></i>
                    Asignatura
                </label>
                <div class="relative rounded-md shadow-sm">
                    <select id="asignatura" wire:model="asignatura"
                        class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm appearance-none">
                        <option class="text-gray-500">Selecciona una asignatura</option>
                        @foreach ($asignaturas as $asignatura)
                            <option value="{{ $asignatura->id }}">{{ $asignatura->name }} ({{$asignatura->code}})</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                @error('asignatura') 
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Campo de profesor (solo lectura) --}}
            <div class="space-y-2 mt-6">
                <label for="profesor" class="text-sm font-medium text-gray-700 flex items-center">
                    <i class="fas fa-user text-purple-500 mr-2"></i>
                    Profesor
                </label>
                <div class="relative rounded-md shadow-sm">
                    <input type="text"
                        class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed sm:text-sm"
                        value="{{ $profesor_nombre }}"
                        readonly>
                    <input type="hidden" wire:model="profesor">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <i class="fas fa-user-check text-gray-400"></i>
                    </div>
                </div>
            </div>

            {{-- Campo de fecha con flatpickr --}}
            <div class="space-y-2 mt-6">
                <label for="fecha_clase" class="text-sm font-medium text-gray-700 flex items-center">
                    <i class="fas fa-calendar text-purple-500 mr-2"></i>
                    Fecha de la clase
                </label>
                <div class="relative rounded-md shadow-sm">
                    <input type="text" 
                        id="fecha_clase" 
                        wire:model="fecha"
                        class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                        placeholder="Selecciona una fecha">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-calendar text-gray-400"></i>
                    </div>
                </div>
                @error('fecha') 
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Footer con botón de siguiente --}}
            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    Siguiente
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts para inicializar flatpickr --}}
@push('scripts')
<script>
    // Configuración del datepicker flatpickr
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#fecha_clase", {
            locale: "es",              // Idioma español
            dateFormat: "Y/m/d",       // Formato de fecha
            minDate: "today",          // Fecha mínima permitida
            disableMobile: true,       // Deshabilitar versión móvil
            theme: "material_purple",  // Tema visual
            position: "below",         // Posición del calendario
            monthSelectorType: "static",// Tipo de selector de mes
            showMonths: 1              // Número de meses mostrados
        });
    });
</script>
@endpush