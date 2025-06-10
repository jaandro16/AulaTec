@extends('layouts.admin')

@section('titulo', $titulo)

@section('contenido')
    {{-- Contenedor principal con Alpine.js para manejar el modal de visualización de justificantes --}}
    <div x-data="{ 
        // Estados del modal
        showModal: false, // Controla si el modal está visible
        currentPath: '', // Ruta del archivo que se está visualizando
        modalKey: 0, // Clave para forzar re-renderizado del contenido del modal
        
        // Función para cerrar el modal
        closeModal() {
            this.showModal = false;
            // Limpiar el contenido después de cerrar para evitar problemas de visualización
            setTimeout(() => {
                this.currentPath = '';
                this.modalKey++; // Incrementar para forzar re-renderizado
            }, 50);
        },
        
        // Función para abrir el modal con un archivo específico
        openModal(path) {
            this.currentPath = path;
            this.modalKey++; // Incrementar para nuevo contenido
            this.showModal = true;
        },
        
        // Función para detectar si el archivo es una imagen
        isImageFile(path) {
            if (!path) return false;
            return /\.(jpg|jpeg|png|gif|webp|bmp)$/i.test(path.toLowerCase());
        },
        
        // Función para detectar si el archivo es un PDF
        isPDFFile(path) {
            if (!path) return false;
            return path.toLowerCase().includes('.pdf');
        }
    }" x-init="$watch('showModal', value => {
        // Prevenir scroll del body cuando el modal está abierto
        if (value) {
            document.body.classList.add('overflow-hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    })">
        <div class="space-y-4 sm:space-y-6">
            {{-- Encabezado de la página --}}
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">{{ $titulo }}</h1>
                <p class="text-sm sm:text-base text-gray-500">Aquí se muestran los alumnos que han faltado a clases prácticas
                    y requieren
                    revisión de sus justificantes</p>
            </div>

            {{-- Tarjeta de filtros de búsqueda --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-4 sm:p-6">
                    <h2 class="text-lg font-semibold">Filtros de Búsqueda</h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-4">Filtra los justificantes por
                        diferentes
                        criterios</p>

                    {{-- Grid responsive para los filtros --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 items-start">                        
                        {{-- Campo de búsqueda por texto --}}
                        <div class="relative sm:col-span-2">
                            <i class="fas fa-search absolute left-2 top-2.5 h-4 w-4 text-gray-500 dark:text-gray-400"></i>
                            <input type="text" id="searchTerm" placeholder="Buscar por estudiante o matrícula..."
                                class="w-full pl-8 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        {{-- Dropdown selector de clase con Alpine.js --}}
                        <div x-data="{ open: false, selected: 'Todas las clases' }" class="relative w-full">
                            {{-- Botón que abre/cierra el dropdown --}}
                            <button @click="open = !open" type="button"
                                class="relative w-full py-2 px-3 inline-flex justify-between items-center gap-x-1.5 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[38px]">
                                <div class="flex items-center gap-x-2 truncate min-w-0 flex-1">
                                    <i class="fas fa-chalkboard-teacher text-gray-500 text-sm flex-shrink-0"></i>
                                    <span class="truncate text-left" x-text="selected"></span>
                                </div>
                                {{-- Icono de flecha que rota cuando se abre el dropdown --}}
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>

                            {{-- Lista desplegable de opciones de clase --}}
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-20 mt-1 w-full bg-white rounded-md shadow-lg py-1 border border-gray-200 max-h-60 overflow-y-auto">
                                
                                {{-- Opción para mostrar todas las clases --}}
                                <a @click="selected = 'Todas las clases'; filtrarTabla()" data-clase=""
                                    class="flex items-center gap-x-2 px-3 py-2 text-sm text-gray-800 hover:bg-purple-50 cursor-pointer">
                                    <i class="fas fa-chalkboard-teacher text-gray-500 text-sm"></i>
                                    <span class="truncate">Todas las clases</span>
                                </a>
                                
                                {{-- Iteración sobre las clases únicas disponibles --}}
                                @foreach ($clasesUnicas as $clase)
                                    <a @click="selected = '{{ $clase['nombre'] }}'; filtrarTabla()"
                                        data-clase="{{ $clase['id'] }}"
                                        class="flex items-center gap-x-2 px-3 py-2 text-sm text-gray-800 hover:bg-purple-50 cursor-pointer">
                                        <i class="fas fa-book text-gray-500 text-sm"></i>
                                        <span class="truncate">{{ $clase['nombre'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Dropdown selector de fecha con Alpine.js --}}
                        <div x-data="{ open: false, selected: 'Todas las fechas' }" class="relative w-full">
                            {{-- Botón que abre/cierra el dropdown de fechas --}}
                            <button @click="open = !open" type="button"
                                class="relative w-full py-2 px-3 inline-flex justify-between items-center gap-x-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[38px]">
                                <div class="flex items-center gap-x-2 truncate min-w-0 flex-1">
                                    <i class="fas fa-calendar text-gray-500 text-sm flex-shrink-0"></i>
                                    <span class="truncate text-left" x-text="selected"></span>
                                </div>
                                {{-- Icono de flecha animado --}}
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>

                            {{-- Lista desplegable de opciones de fecha --}}
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute z-20 mt-1 w-full bg-white rounded-md shadow-lg py-1 border border-gray-200 max-h-60 overflow-y-auto">
                                
                                {{-- Opción para mostrar todas las fechas --}}
                                <a @click="selected = 'Todas las fechas'; filtrarTabla()" data-fecha=""
                                    class="flex items-center gap-x-2 px-3 py-2 text-sm text-gray-800 hover:bg-purple-50 cursor-pointer">
                                    <i class="fas fa-calendar text-gray-500 text-sm"></i>
                                    <span class="truncate">Todas las fechas</span>
                                </a>
                                
                                {{-- Opción para filtrar por hoy --}}
                                <a @click="selected = 'Hoy'; filtrarTabla()" data-fecha="{{ date('Y-m-d') }}"
                                    class="flex items-center gap-x-2 px-3 py-2 text-sm text-gray-800 hover:bg-purple-50 cursor-pointer">
                                    <i class="fas fa-clock text-gray-500 text-sm"></i>
                                    <span class="truncate">Hoy</span>
                                </a>
                                
                                {{-- Iteración sobre las fechas únicas disponibles --}}
                                @foreach ($fechasUnicas as $fecha)
                                    <a @click="selected = '{{ $fecha['formato'] }}'; filtrarTabla()"
                                        data-fecha="{{ $fecha['fecha'] }}"
                                        class="flex items-center gap-x-2 px-3 py-2 text-sm text-gray-800 hover:bg-purple-50 cursor-pointer">
                                        <i class="fas fa-calendar-day text-gray-500 text-sm"></i>
                                        <span class="truncate">{{ $fecha['formato'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tarjeta principal con la tabla de justificantes --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-4 sm:p-6">
                    {{-- Encabezado de la tabla con botón de exportación --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 sm:mb-6">
                        <div>
                            <h2 class="text-lg font-semibold">Registro de Justificantes</h2>
                            <p class="text-xs sm:text-sm text-gray-500">Mostrando justificantes</p>
                        </div>
                        {{-- Botón para exportar datos a Excel --}}
                        <button onclick="exportarCSV(event)"
                            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">
                            <i class="fas fa-download mr-2"></i>
                            Exportar CSV
                        </button>
                    </div>

                    {{-- Contenedor scrolleable para la tabla en dispositivos pequeños --}}
                    <div class="overflow-x-auto rounded-md border">
                        <table class="min-w-full divide-y divide-gray-200">
                            {{-- Encabezados de la tabla --}}
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                        Estudiante
                                    </th>
                                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                        Matrícula
                                    </th>
                                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                        Clase
                                    </th>
                                    {{-- Columnas ocultas en dispositivos móviles --}}
                                    <th class="hidden md:table-cell px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                        Aula
                                    </th>
                                    <th class="hidden md:table-cell px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                        Asiento
                                    </th>
                                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                        Hora
                                    </th>
                                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                        Justificado
                                    </th>
                                    <th class="px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            
                            {{-- Cuerpo de la tabla --}}
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- Iteración sobre las reservas/justificantes --}}
                                @forelse ($reservas ?? [] as $reserva)
                                    {{-- Cada fila con datos para filtrado y estilo alternado --}}
                                    <tr class="{{ $loop->index % 2 == 0 ? 'bg-white' : 'bg-gray-100' }} hover:bg-gray-200 transition-colors duration-150"
                                        data-clase-id="{{ $reserva['clase_id'] ?? '' }}"
                                        data-fecha="{{ is_string($reserva['fecha']) ? date('Y-m-d', strtotime(str_replace('/', '-', $reserva['fecha']))) : '' }}">
                                        
                                        {{-- Columna: Nombre del estudiante --}}
                                        <td class="px-2 py-3 whitespace-nowrap text-sm">{{ $reserva['nombre'] }}</td>
                                        
                                        {{-- Columna: Matrícula del estudiante --}}
                                        <td class="px-2 py-3 whitespace-nowrap text-sm">{{ $reserva['matricula'] }}</td>
                                        
                                        {{-- Columna: Nombre de la clase --}}
                                        <td class="px-2 py-3 whitespace-nowrap text-sm">{{ $reserva['clase'] }}</td>
                                        
                                        {{-- Columna: Aula (oculta en móviles) --}}
                                        <td class="hidden md:table-cell px-2 py-3 whitespace-nowrap text-sm">
                                            {{ $reserva['aula'] }}</td>
                                        
                                        {{-- Columna: Número de asiento (oculta en móviles) --}}
                                        <td class="hidden md:table-cell px-2 py-3 whitespace-nowrap text-sm">
                                            {{ $reserva['asiento'] }}</td>
                                        
                                        {{-- Columna: Fecha de la clase --}}
                                        <td class="px-2 py-3 whitespace-nowrap text-sm">
                                            {{-- Manejo de diferentes formatos de fecha --}}
                                            @if (is_string($reserva['fecha']))
                                                {{ $reserva['fecha'] }}
                                            @elseif($reserva['fecha'] instanceof \Carbon\Carbon)
                                                {{ $reserva['fecha']->format('d/m/Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        
                                        {{-- Columna: Hora de la clase --}}
                                        <td class="px-2 py-3 whitespace-nowrap text-sm">
                                            {{ $reserva['hora'] }}
                                        </td>
                                        
                                        {{-- Columna: Estado de justificación con toggle switch --}}
                                        <td class="px-2 py-3 whitespace-nowrap text-sm">
                                            {{-- Formulario para cambiar estado de justificación --}}
                                            <form action="{{ route('justificantes.justificar', $reserva['id']) }}"
                                                method="POST" class="justificacion-form">
                                                @csrf
                                                {{-- Toggle switch personalizado --}}
                                                <label class="relative inline-flex items-center cursor-pointer toggle-container">
                                                    <input type="checkbox" name="justificado" value="1"
                                                        class="sr-only peer toggle-switch"
                                                        {{ $reserva['justificado'] == 1 ? 'checked' : '' }}>
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600 toggle-background">
                                                    </div>
                                                </label>
                                            </form>
                                        </td>
                                        
                                        {{-- Columna: Acciones (ver justificante) --}}
                                        <td class="px-2 py-3 whitespace-nowrap text-left text-sm">
                                            {{-- Mostrar botón de visualización si existe justificante --}}
                                            @if ($reserva['justificante_path'])
                                                <button
                                                    @click="openModal('{{ asset('storage/' . $reserva['justificante_path']) }}')"
                                                    class="inline-flex items-center justify-start px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-md hover:bg-purple-700 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-purple-500">
                                                    {{-- Icono de ojo --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd"
                                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Ver
                                                </button>
                                            @else
                                                {{-- Mostrar mensaje cuando no hay justificante --}}
                                                <span class="inline-flex items-center justify-start pl-0 pr-2.5 px-1 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                    {{-- Icono de ojo tachado --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-3.5 w-3.5 ml-0.5 mr-1" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                                                            clip-rule="evenodd" />
                                                        <path
                                                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                                    </svg>
                                                    Sin justificante
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Mensaje cuando no hay datos --}}
                                    <tr>
                                        <td colspan="9" class="px-2 py-4 text-center text-sm text-gray-500">
                                            No hay registros de justificantes disponibles
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal para visualizar justificantes (imágenes y PDFs) --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            {{-- Overlay de fondo oscuro --}}
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal()"></div>

            {{-- Contenedor del contenido del modal --}}
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white rounded-lg w-full mx-auto shadow-xl 
                           sm:w-11/12 md:w-4/5 lg:w-3/5 xl:w-2/5 
                           max-h-[90vh] overflow-hidden">

                    {{-- Encabezado del modal con título y botón de cerrar --}}
                    <div class="flex items-center justify-between p-3 sm:p-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Justificante</h3>
                        <button @click="closeModal()" type="button" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Cerrar</span>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Cuerpo del modal con visor de archivos --}}
                    <div class="p-3 sm:p-4 overflow-auto" style="max-height: calc(90vh - 130px);">
                        <div class="bg-gray-100 rounded-lg overflow-hidden">
                            {{-- Visor responsive con diferentes comportamientos según el dispositivo --}}
                            <div class="relative w-full" style="padding-top: min(70vh, 100%);" :key="modalKey">
                                
                                {{-- Visualización para pantallas grandes (desktop): muestra contenido directamente --}}
                                <div class="hidden md:block">
                                    {{-- Mostrar imagen si el archivo es una imagen --}}
                                    <div x-show="isImageFile(currentPath)" class="absolute inset-0 flex items-center justify-center bg-gray-50">
                                        <img :src="currentPath" 
                                            alt="Justificante" 
                                            class="max-w-full max-h-full object-contain"
                                            :key="'img-' + modalKey">
                                    </div>
                                    
                                    {{-- Mostrar PDF embebido si el archivo es un PDF --}}
                                    <object x-show="isPDFFile(currentPath)" 
                                            :data="currentPath" 
                                            type="application/pdf"
                                            class="absolute inset-0 w-full h-full" 
                                            style="border: none;"
                                            :key="'pdf-' + modalKey">
                                        {{-- Fallback para PDFs que no se pueden mostrar directamente --}}
                                        <div class="absolute inset-0 flex flex-col items-center justify-center p-6 bg-gray-50">
                                            <div class="text-center max-w-sm mx-auto">
                                                <i class="fas fa-file-pdf text-red-500 text-5xl mb-4"></i>
                                                <h4 class="text-lg font-medium text-gray-800 mb-2">Visualizador de PDF</h4>
                                                <p class="text-gray-600 mb-4">No se puede mostrar el PDF directamente en esta ventana</p>
                                                <a :href="currentPath" target="_blank"
                                                    class="inline-block w-full px-6 py-3 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 transition-colors">
                                                    <i class="fas fa-external-link-alt mr-2"></i>
                                                    Abrir PDF en nueva ventana
                                                </a>
                                            </div>
                                        </div>
                                    </object>
                                </div>

                                {{-- Visualización para pantallas pequeñas (móviles): siempre muestra mensaje de "abrir en nueva ventana" --}}
                                <div class="block md:hidden">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center p-6 bg-gray-50">
                                        <div class="text-center max-w-sm mx-auto">
                                            {{-- Icono dinámico según el tipo de archivo --}}
                                            <i :class="isPDFFile(currentPath) ? 'fas fa-file-pdf text-red-500' : 'fas fa-image text-blue-500'" 
                                            class="text-4xl mb-4"></i>
                                            
                                            {{-- Título dinámico según el tipo de archivo --}}
                                            <h4 class="text-lg font-medium text-gray-800 mb-2" 
                                                x-text="isPDFFile(currentPath) ? 'Visualizador de PDF' : 'Visualizador de Imágenes'"></h4>
                                            
                                            {{-- Mensaje explicativo dinámico --}}
                                            <p class="text-gray-600 mb-4 text-sm" 
                                            x-text="isPDFFile(currentPath) ? 'Para una mejor visualización, abre el PDF en una nueva ventana' : 'Para una mejor visualización, abre la imagen en una nueva ventana'"></p>
                                            
                                            {{-- Botón para abrir en nueva ventana con estilo dinámico --}}
                                            <a :href="currentPath" target="_blank"
                                                :class="isPDFFile(currentPath) ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'"
                                                class="inline-block w-full px-4 py-3 text-sm font-medium text-white rounded-md shadow-sm transition-colors">
                                                <i class="fas fa-external-link-alt mr-2"></i>
                                                <span x-text="isPDFFile(currentPath) ? 'Abrir PDF en nueva ventana' : 'Abrir imagen en nueva ventana'"></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    
                    {{-- Pie del modal con botones de acción --}}
                    <div class="flex justify-end gap-2 p-3 sm:p-4 border-t border-gray-200">
                        {{-- Botón para cerrar el modal --}}
                        <button type="button" @click="closeModal()"
                            class="px-3 py-1.5 sm:px-4 sm:py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            Cerrar
                        </button>
                        {{-- Botón para ver en tamaño completo en nueva ventana --}}
                        <a :href="currentPath" target="_blank"
                            class="px-3 py-1.5 sm:px-4 sm:py-2 text-sm font-medium text-white bg-purple-600 rounded-md shadow-sm hover:bg-purple-700">
                            Ver en tamaño completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Componente de notificaciones --}}
    <x-notification />
@endsection

@push('scripts')
    {{-- Librerías externas para exportación a Excel --}}
    <!-- Incluir ExcelJS para generar Excel con estilos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <!-- FileSaver.js para la descarga del archivo -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables globales para el sistema de filtrado
            let filtroClase = '';    // ID de la clase seleccionada para filtrar
            let filtroFecha = '';    // Fecha seleccionada para filtrar
            let terminoBusqueda = '';// Término de búsqueda por texto

            // Referencias a elementos del DOM
            const searchInput = document.getElementById('searchTerm');
            const rows = document.querySelectorAll('tbody tr');

            // Función principal para filtrar la tabla según los criterios activos
            window.filtrarTabla = function() {
                // Obtener valores actuales de los filtros desde los elementos seleccionados
                const claseElement = document.querySelector('a[data-clase].selected');
                const fechaElement = document.querySelector('a[data-fecha].selected');

                // Obtener filtro de clase desde elemento seleccionado o dropdown de Alpine.js
                if (claseElement) {
                    filtroClase = claseElement.getAttribute('data-clase');
                } else {
                    // Fallback: intentar obtener desde el dropdown de Alpine.js
                    const claseDropdown = document.querySelector('div[x-data*="selected"][data-clase]');
                    filtroClase = claseDropdown ? claseDropdown.getAttribute('data-clase') : '';
                }

                // Obtener filtro de fecha desde elemento seleccionado o dropdown de Alpine.js
                if (fechaElement) {
                    filtroFecha = fechaElement.getAttribute('data-fecha');
                } else {
                    // Fallback: intentar obtener desde el dropdown de Alpine.js
                    const fechaDropdown = document.querySelector('div[x-data*="selected"][data-fecha]');
                    filtroFecha = fechaDropdown ? fechaDropdown.getAttribute('data-fecha') : '';
                }

                // Aplicar filtros a cada fila de la tabla
                rows.forEach(row => {
                    // Obtener texto completo de la fila para búsqueda
                    const texto = row.textContent.toLowerCase();
                    const cumpleBusqueda = terminoBusqueda === '' || texto.includes(terminoBusqueda);

                    // Obtener atributos de datos para filtrado específico
                    const claseId = row.getAttribute('data-clase-id') || '';
                    const fechaAsist = row.getAttribute('data-fecha') || '';

                    // Verificar si la fila cumple con los criterios de filtrado
                    const cumpleClase = filtroClase === '' || claseId === filtroClase;
                    const cumpleFecha = filtroFecha === '' || fechaAsist.includes(filtroFecha);

                    // Mostrar u ocultar la fila según los criterios
                    row.style.display = (cumpleBusqueda && cumpleClase && cumpleFecha) ? '' : 'none';
                });

                // Actualizar contador de resultados mostrados
                const visibles = [...rows].filter(row => row.style.display !== 'none').length;
                const total = rows.length;
                const mensajeElemento = document.querySelector('.text-xs.sm\\:text-sm.text-gray-500');
                if (mensajeElemento) {
                    mensajeElemento.textContent = `Mostrando ${visibles} de ${total} justificantes`;
                }
            };

            // Agregar event listeners para marcar elementos seleccionados en dropdowns
            document.querySelectorAll('[data-clase], [data-fecha]').forEach(item => {
                item.addEventListener('click', function() {
                    // Determinar si es un filtro de clase o fecha
                    const isClase = this.hasAttribute('data-clase');
                    const selector = isClase ? '[data-clase]' : '[data-fecha]';

                    // Quitar clase 'selected' de otros elementos del mismo tipo
                    document.querySelectorAll(selector).forEach(el => {
                        el.classList.remove('selected');
                    });

                    // Marcar el elemento actual como seleccionado
                    this.classList.add('selected');
                });
            });

            // Event listener para búsqueda por texto en tiempo real
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    terminoBusqueda = this.value.toLowerCase();
                    filtrarTabla();
                });
            }

            // Asegurar que las filas tengan los atributos necesarios para filtrado
            rows.forEach(row => {
                if (!row.dataset.claseId || !row.dataset.fecha) {
                    const celdas = row.querySelectorAll('td');
                    if (celdas.length > 0) {
                        // Obtener nombre de clase (3ra columna) y buscar su ID correspondiente
                        const nombreClase = celdas[2]?.textContent.trim();
                        const claseEncontrada = Array.from(@json($clasesUnicas))
                            .find(c => c.nombre === nombreClase);
                        if (claseEncontrada) {
                            row.dataset.claseId = claseEncontrada.id;
                        }

                        // Obtener y formatear fecha (6ta columna)
                        const fecha = celdas[5]?.textContent.trim();
                        if (fecha) {
                            // Convertir formato dd/mm/yyyy a yyyy-mm-dd para filtrado consistente
                            const fechaParts = fecha.split('/');
                            if (fechaParts.length === 3) {
                                const fechaFormateada = `${fechaParts[2]}-${fechaParts[1]}-${fechaParts[0]}`;
                                row.dataset.fecha = fechaFormateada;
                            } else {
                                row.dataset.fecha = fecha;
                            }
                        }
                    }
                }
            });

            // Configuración de los toggle switches para justificación
            const toggles = document.querySelectorAll('.toggle-switch');
            let processingInProgress = false; // Prevenir múltiples submissions simultáneas

            toggles.forEach(toggle => {
                toggle.addEventListener('change', function(e) {
                    // Prevenir cambios mientras se procesa otra operación
                    if (processingInProgress) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    processingInProgress = true;
                    const toggleBackground = this.nextElementSibling;
                    toggleBackground.classList.add('processing'); // Agregar animación visual

                    // Deshabilitar todos los toggles durante el procesamiento
                    toggles.forEach(otherToggle => {
                        otherToggle.disabled = true;
                        const container = otherToggle.closest('.toggle-container');
                        if (container) {
                            container.classList.add('opacity-50', 'cursor-not-allowed');
                            container.style.pointerEvents = 'none';
                        }
                    });

                    // Enviar formulario después de una breve pausa para mostrar la animación
                    setTimeout(() => {
                        this.form.submit();
                    }, 200);
                });
            });

            // Event listeners para enlaces de filtros
            document.querySelectorAll('a[data-clase], a[data-fecha]').forEach(link => {
                link.addEventListener('click', function() {
                    // Determinar tipo de filtro y selector correspondiente
                    const isClase = this.hasAttribute('data-clase');
                    const selector = isClase ? 'a[data-clase]' : 'a[data-fecha]';

                    // Remover clase 'selected' de otros enlaces del mismo tipo
                    document.querySelectorAll(selector).forEach(el => {
                        el.classList.remove('selected');
                    });

                    // Marcar el enlace actual como seleccionado
                    this.classList.add('selected');

                    // Aplicar filtros inmediatamente
                    filtrarTabla();
                });
            });

            // Inicializar la tabla con el conteo inicial de resultados
            filtrarTabla();
        });

        // Función avanzada para exportar datos a Excel con estilos y formato profesional
        function exportarCSV(event) {
            // Manejo robusto del evento
            if (!event) event = window.event;
            const button = event.currentTarget || event.target;

            // Cambiar estado del botón a procesando
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
            button.disabled = true;

            // Crear y mostrar notificación de progreso
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 left-4 md:left-auto md:max-w-sm bg-white shadow-lg rounded-lg p-4 flex items-center z-50 transform transition-all duration-500 translate-y-[-100px]';

            notification.innerHTML = `
                <div class="shrink-0 mr-3 bg-purple-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 text-sm truncate">Preparando exportación</p>
                    <p class="text-sm text-gray-600 truncate">Generando archivo Excel con estilos...</p>
                </div>
            `;
            document.body.appendChild(notification);

            // Animación de entrada de la notificación
            setTimeout(() => notification.style.transform = 'translate(0, 0)', 10);

            // Iniciar proceso de exportación con retraso para mostrar animación
            setTimeout(async () => {
                try {
                    // Crear nuevo libro de Excel con metadatos
                    const workbook = new ExcelJS.Workbook();
                    workbook.creator = 'AulaTec';
                    workbook.created = new Date();

                    // Crear hoja de cálculo
                    const worksheet = workbook.addWorksheet('Justificantes');

                    // Extraer datos de la tabla HTML
                    const table = document.querySelector('table');
                    const rows = table.querySelectorAll('tr');

                    // Procesar encabezados de la tabla
                    const headers = [];
                    const headerCells = rows[0].querySelectorAll('th');
                    headerCells.forEach(header => {
                        // Solo incluir columnas visibles
                        if (window.getComputedStyle(header).display !== 'none') {
                            headers.push(header.innerText.trim());
                        }
                    });

                    // Añadir fila de encabezados al Excel
                    worksheet.addRow(headers);

                    // Aplicar formato profesional a los encabezados
                    const headerRow = worksheet.getRow(1);
                    headerRow.font = {
                        bold: true,
                        color: { argb: 'FFFFFFFF' } // Texto blanco
                    };
                    headerRow.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: { argb: '8366B3' } // Color morado de AulaTec
                    };
                    headerRow.alignment = {
                        vertical: 'middle',
                        horizontal: 'center'
                    };
                    headerRow.height = 22;

                    // Procesar filas de datos
                    let rowIndex = 2; // Comenzar en fila 2 (la 1 son encabezados)

                    rows.forEach((row, index) => {
                        if (index === 0) return; // Saltar fila de encabezados
                        if (window.getComputedStyle(row).display === 'none') return; // Saltar filas ocultas por filtros

                        const rowData = [];
                        const cells = row.querySelectorAll('td');
                        let hasJustificado = false;
                        let hasPendiente = false;

                        cells.forEach((cell, cellIndex) => {
                            let text;
                            
                            // Manejo especial para la columna de justificación (índice 7)
                            if (cellIndex === 7) {
                                const checkbox = cell.querySelector('input[type="checkbox"]');
                                if (checkbox) {
                                    const isChecked = checkbox.checked || checkbox.hasAttribute('checked');
                                    text = isChecked ? 'Sí' : 'No';
                                    
                                    // Marcar para aplicar estilos específicos
                                    if (isChecked) {
                                        hasJustificado = true;
                                    } else {
                                        hasPendiente = true;
                                    }
                                } else {
                                    text = 'No determinado';
                                }
                            } else {
                                // Procesamiento estándar para otras columnas
                                if (cell.querySelector('.truncate')) {
                                    text = cell.querySelector('.truncate').innerText.trim();
                                } else {
                                    text = cell.innerText.replace(/\s+/g, ' ').trim();
                                }
                            }

                            rowData.push(text);
                        });

                        // Añadir fila al Excel si tiene datos
                        if (rowData.length > 0) {
                            const excelRow = worksheet.addRow(rowData);

                            // Aplicar estilos a cada celda de la fila
                            excelRow.eachCell((cell, colNumber) => {
                                // Bordes para todas las celdas
                                cell.border = {
                                    top: { style: 'thin', color: { argb: 'DDDDDD' } },
                                    right: { style: 'thin', color: { argb: 'DDDDDD' } },
                                    bottom: { style: 'thin', color: { argb: 'DDDDDD' } },
                                    left: { style: 'thin', color: { argb: 'DDDDDD' } }
                                };

                                // Alineación vertical centrada
                                cell.alignment = { vertical: 'middle' };

                                // Filas alternadas con color de fondo gris claro
                                if (rowIndex % 2 === 0) {
                                    cell.fill = {
                                        type: 'pattern',
                                        pattern: 'solid',
                                        fgColor: { argb: 'F9FAFB' }
                                    };
                                }

                                // Estilos especiales para columna de estado de justificación
                                if (colNumber === 8) { // Columna de Justificado
                                    if (cell.value === 'Sí') {
                                        cell.font = {
                                            bold: true,
                                            color: { argb: '166534' } // Verde oscuro
                                        };
                                        cell.fill = {
                                            type: 'pattern',
                                            pattern: 'solid',
                                            fgColor: { argb: 'DCFCE7' } // Verde claro
                                        };
                                    } else if (cell.value === 'No') {
                                        cell.font = {
                                            bold: true,
                                            color: { argb: '854D0E' } // Naranja oscuro
                                        };
                                        cell.fill = {
                                            type: 'pattern',
                                            pattern: 'solid',
                                            fgColor: { argb: 'FEF9C3' } // Amarillo claro
                                        };
                                    }
                                }
                            });

                            rowIndex++;
                        }
                    });

                    // Ajuste automático del ancho de columnas
                    worksheet.columns.forEach(column => {
                        let maxLength = 0;
                        column.eachCell({ includeEmpty: true }, cell => {
                            if (cell.value) {
                                const length = cell.value.toString().length;
                                if (length > maxLength) maxLength = length;
                            }
                        });
                        // Establecer ancho mínimo de 10 y agregar padding
                        column.width = maxLength < 10 ? 10 : maxLength + 2;
                    });

                    // Generar buffer del archivo Excel
                    const buffer = await workbook.xlsx.writeBuffer();

                    // Crear nombre de archivo con fecha actual
                    const today = new Date();
                    const formattedDate = `${today.getDate()}-${today.getMonth() + 1}-${today.getFullYear()}`;

                    // Descargar el archivo
                    saveAs(new Blob([buffer]), `justificantes_${formattedDate}.xlsx`);

                    // Actualizar notificación a estado de éxito
                    notification.innerHTML = `
                        <div class="shrink-0 mr-3 bg-green-100 rounded-full p-2">
                            <svg class="w-5 h-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 text-sm truncate">Exportación completada</p>
                            <p class="text-sm text-gray-600 truncate">El archivo Excel se ha descargado correctamente.</p>
                        </div>
                    `;

                } catch (error) {
                    console.error('Error al exportar:', error);

                    // Notificación de error
                    notification.innerHTML = `
                        <div class="shrink-0 mr-3 bg-red-100 rounded-full p-2">
                            <svg class="w-5 h-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 text-sm truncate">Error en la exportación</p>
                            <p class="text-sm text-gray-600 truncate">No se pudo generar el archivo Excel. ${error.message}</p>
                        </div>
                    `;
                }

                // Restaurar botón a su estado original
                button.innerHTML = originalText;
                button.disabled = false;

                // Ocultar notificación después de 3 segundos
                setTimeout(() => {
                    notification.style.transform = 'translate(0, -100px)';
                    setTimeout(() => document.body.removeChild(notification), 500);
                }, 3000);

            }, 800); // Retraso de 800ms para mostrar animación
        }
    </script>
@endpush

@section('styles')
    <style>
        /* ======= ESTILOS RESPONSIVE PARA FILTROS ======= */
        /* Optimización para dispositivos móviles */
        @media (max-width: 640px) {
            /* Espaciado mejorado en contenedor de filtros para móviles */
            .grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4 {
                gap: 1rem;
            }
            
            /* Botones de filtro con altura mínima para facilitar interacción táctil */
            button[type="button"] {
                min-height: 44px; /* Cumple con pautas de accesibilidad táctil */
                font-size: 14px;
            }
            
            /* Iconos con tamaño fijo para consistencia visual */
            .fas {
                width: 16px;
                flex-shrink: 0;
            }
            
            /* Dropdown menus de ancho completo en móviles */
            div[x-show="open"] {
                left: 0;
                right: 0;
                width: 100%;
                max-height: 200px; /* Altura reducida en móvil para mejor UX */
            }
        }

        /* ======= ESTILOS PARA DROPDOWNS ======= */
        /* Mejora de visibilidad y usabilidad de menús desplegables */
        div[x-show="open"] {
            position: absolute;
            z-index: 20;
            margin-top: 0.25rem;
            width: 100%;
            background-color: white;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            padding: 0.25rem 0;
            max-height: 240px;
            overflow-y: auto;
        }

        /* Estilos para elementos individuales del dropdown */
        div[x-show="open"] a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            font-size: 0.875rem;
            color: #374151;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            min-height: 44px; /* Área táctil mínima para accesibilidad */
        }

        /* Estado hover para elementos del dropdown */
        div[x-show="open"] a:hover {
            background-color: #f3f0ff;
        }

        /* Estado seleccionado para elementos del dropdown */
        div[x-show="open"] a.selected {
            background-color: #f3f0ff;
            font-weight: 600;
            color: #6d28d9;
        }

        /* ======= ESTILOS PARA BOTONES DE FILTRO ======= */
        /* Transiciones suaves para interacciones */
        button[type="button"] {
            transition: all 0.15s ease-in-out;
        }

        /* Estados hover y focus para botones */
        button[type="button"]:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }

        button[type="button"]:focus {
            outline: none;
            ring: 2px;
            ring-color: #a855f7;
            border-color: #a855f7;
        }

        /* ======= UTILIDADES DE TEXTO ======= */
        /* Truncamiento mejorado de texto */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Prevenir deformación de elementos flex */
        .flex-shrink-0 {
            flex-shrink: 0;
        }

        .min-w-0 {
            min-width: 0;
        }

        /* ======= ANIMACIONES PARA TOGGLES ======= */
        /* Efecto visual durante procesamiento de toggles de justificación */
        .processing {
            position: relative;
            overflow: hidden;
        }

        .processing::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: pulse-loading 1.5s infinite;
            pointer-events: none;
        }

        /* Animación de pulso para indicar procesamiento */
        @keyframes pulse-loading {
            0% {
                left: -100%;
            }
            50% {
                left: 100%;
            }
            100% {
                left: 100%;
            }
        }

        /* Cursor de no permitido para elementos deshabilitados */
        .cursor-not-allowed {
            cursor: not-allowed !important;
        }

        /* ======= ESTILOS ESPECÍFICOS PARA EL MODAL EN MÓVILES ======= */
        /* Optimizaciones para visualización de justificantes en dispositivos móviles */
        @media (max-width: 768px) {
            /* Forzar mostrar fallback en móviles */
            .hidden.md\\:block {
                display: none !important;
            }
            
            .block.md\\:hidden {
                display: block !important;
            }
            
            /* Modal más compacto para móviles */
            .relative.bg-white.rounded-lg {
                width: 95% !important;
                max-height: 85vh !important;
            }
            
            /* Botones optimizados para interacción táctil */
            .block.md\\:hidden a[target="_blank"] {
                min-height: 48px;
                font-size: 16px;
                padding: 1rem 1.5rem;
            }
            
            /* Iconos apropiados para móvil */
            .block.md\\:hidden .text-4xl {
                font-size: 3rem !important;
            }
            
            /* Tipografía mejorada para lectura en móvil */
            .block.md\\:hidden .text-lg {
                font-size: 1.125rem !important;
            }
            
            .block.md\\:hidden .text-sm {
                font-size: 0.875rem !important;
                line-height: 1.4;
            }
        }

        /* Optimización para dispositivos táctiles sin hover */
        @media (hover: none) and (pointer: coarse) {
            /* Forzar fallback en dispositivos táctiles */
            .hidden.md\\:block {
                display: none !important;
            }
            
            .block.md\\:hidden {
                display: block !important;
            }
        }

        /* ======= RESPONSIVE DESIGN PARA BOTONES DEL MODAL ======= */
        /* Layout vertical para botones en móviles */
        @media (max-width: 640px) {
            .flex.justify-end.gap-2 {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .flex.justify-end.gap-2 button,
            .flex.justify-end.gap-2 a {
                width: 100%;
                text-align: center;
                min-height: 44px;
            }
        }
    </style>
@endsection