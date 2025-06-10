{{-- Contenedor principal --}}
<div>
    {{-- Encabezado con título y stepper --}}
    <div class="p-4 sm:p-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Descripción de la Clase</h2>
                <p class="text-sm text-gray-500">Paso 3 - Información detallada de la clase</p>
            </div>
            @include('modules.admin.crear_clase.partials.stepper')
        </div>
    </div>

    {{-- Contenido principal del formulario --}}
    <div class="p-4 sm:p-6 space-y-6">
        {{-- Formulario de creación de clase --}}
        <form action="{{ route('admin.crear-clase.store') }}" method="POST">
            @csrf   
            {{-- Campo para la descripción general de la clase --}}
            <div class="space-y-2">
                <label for="descripcion" class="text-sm font-medium text-gray-700 flex items-center">
                    <i class="fas fa-book-open text-purple-500 mr-2"></i>
                    Descripción general
                </label>
                <div class="relative rounded-md shadow-sm">
                    <textarea id="descripcion" name="descripcion" rows="5"
                        class="block w-full pl-3 pr-3 py-2.5 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                        placeholder="Describe el contenido general de la clase...">{{ old('descripcion', $descripcion) }}</textarea>
                </div>
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Sección de consejos para redactar la descripción --}}
            <div class="mt-6 p-4 bg-purple-50 rounded-lg border border-purple-100">
                <h3 class="font-medium text-purple-800 mb-3 flex items-center">
                    <i class="fas fa-lightbulb text-purple-600 mr-2"></i>
                    Consejos para una buena descripción:
                </h3>
                <ul class="text-sm space-y-2 text-purple-700">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-0.5 mr-2"></i>
                        <span>Incluye información sobre el material necesario para la clase</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-0.5 mr-2"></i>
                        <span>Menciona si hay requisitos previos para asistir</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-0.5 mr-2"></i>
                        <span>Añade detalles sobre el contenido que se cubrirá</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-0.5 mr-2"></i>
                        <span>Especifica si hay tareas o evaluaciones asociadas</span>
                    </li>
                </ul>
            </div>

            {{-- Panel de resumen con los datos de la clase --}}
            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-medium text-gray-800 mb-3">Resumen de la clase:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Asignatura:</p>
                        <p class="font-medium">
                            @if ($asignaturas && $asignatura)
                                {{ optional($asignaturas->where('id', $asignatura)->first())->name ?? 'No seleccionada' }}
                            @else
                                No seleccionada
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Profesor:</p>
                        <p class="font-medium">{{ $profesor_nombre }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Fecha:</p>
                        <p class="font-medium">{{ $fecha ?? 'No especificada' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Aula:</p>
                        <p class="font-medium">
                            @if ($aulas && $aula)
                                {{ optional($aulas->where('id', $aula)->first())->name ?? 'No seleccionada' }}
                            @else
                                No seleccionada
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Botones de navegación entre pasos --}}
            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-between">
                <button type="button" wire:click="previousStep"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Anterior
                </button>
                <!-- Botón de submit tradicional -->
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Finalizar
                </button>
            </div>
        </form>
    </div>
</div>
