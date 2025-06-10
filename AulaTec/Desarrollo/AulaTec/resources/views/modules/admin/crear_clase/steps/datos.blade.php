{{-- Vista para el paso 2 del formulario de creación de clase --}}
<div>
    {{-- Encabezado con título y stepper --}}
    <div class="p-4 sm:p-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Detalles del Aula</h2>
                <p class="text-sm text-gray-500">Paso 2 - Información del aula y horario</p>
            </div>
            @include('modules.admin.crear_clase.partials.stepper')
        </div>
    </div>

    {{-- Contenedor principal del formulario --}}
    <div class="p-4 sm:p-6 space-y-6">
        <form wire:submit.prevent="nextStep">
            {{-- Campo para seleccionar el aula --}}
            <div class="space-y-2">
                <label for="aula" class="text-sm font-medium text-gray-700 flex items-center">
                    <i class="fas fa-door-open text-purple-500 mr-2"></i>
                    Aula
                </label>
                <div class="relative rounded-md shadow-sm">
                    <select id="aula" wire:model.live="aula"
                        class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm appearance-none">
                        <option value="">Selecciona un aula</option>
                        @foreach ($aulas as $aula)
                            <option value="{{ $aula->id }}">{{ $aula->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                @error('aula')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Campo para seleccionar el horario --}}
            <div class="space-y-2 mt-6">
                <label for="horario" class="text-sm font-medium text-gray-700 flex items-center">
                    <i class="fas fa-clock text-purple-500 mr-2"></i>
                    Horario
                </label>
                <div class="relative rounded-md shadow-sm">
                    <select id="horario" wire:model="horario"
                        class="block w-full pl-3 pr-10 py-2.5 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm appearance-none">
                        <option value="">Selecciona un horario</option>
                        @foreach ($timeSlots as $timeSlot)
                            <option value="{{ $timeSlot->id }}">
                                {{ \Carbon\Carbon::parse($timeSlot->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($timeSlot->end_time)->format('H:i') }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                @error('horario')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Sección que muestra la capacidad del aula seleccionada --}}
            <div class="space-y-2 mt-6">
                <label class="text-sm font-medium text-gray-700 flex items-center">
                    <i class="fas fa-users text-purple-500 mr-2"></i>
                    Capacidad del Aula
                </label>
                <div class="relative rounded-md bg-gray-50 border border-gray-200 px-4 py-3">
                    <div class="flex items-center">
                        <i class="fas fa-user-friends text-purple-400 mr-3"></i>
                        @if($capacidad)
                            <span class="text-gray-900 font-medium">{{ $capacidad }} alumnos</span>
                        @else
                            <span class="text-gray-500 italic">Selecciona un aula para ver su capacidad</span>
                        @endif
                    </div>
                    @if($capacidad)
                        <p class="mt-1 text-xs text-gray-500">Capacidad máxima del aula seleccionada</p>
                    @endif
                </div>
            </div>

            {{-- Botones de navegación entre pasos --}}
            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-between">
                <button type="button" wire:click="previousStep"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Anterior
                </button>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    Siguiente
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
