@extends('layouts.admin')

@section('titulo', $titulo)

@section('contenido')
    {{-- ======= HEADER PRINCIPAL ======= --}}
    {{-- Título y descripción de la página --}}
    <div class="space-y-4 sm:space-y-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">{{ $titulo }}</h1>
            <p class="text-sm sm:text-base text-gray-500">Consulta y gestiona el registro de asistencias de los estudiantes.</p>
        </div>

        {{-- ======= SECCIÓN FILTROS ======= --}}
        {{-- Card de filtros con buscador y selectores --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-4 sm:p-6">
                <h2 class="text-lg font-semibold">Filtros de Búsqueda</h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-4">Filtra las asistencias por diferentes
                    criterios</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 items-start">
                    {{-- Buscador --}}
                    <div class="relative sm:col-span-2">
                        <i class="fas fa-search absolute left-2 top-2.5 h-4 w-4 text-gray-500 dark:text-gray-400"></i>
                        <input type="text" id="searchTerm" placeholder="Buscar por estudiante o matrícula..."
                            class="w-full pl-8 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    {{-- Selector de Clase --}}
                    <div x-data="{ open: false, selected: 'Todas las clases' }" class="relative w-full">
                        <button @click="open = !open" type="button"
                            class="relative w-full py-2 px-3 inline-flex justify-between items-center gap-x-1.5 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[38px]">
                            <div class="flex items-center gap-x-2 truncate min-w-0 flex-1">
                                <i class="fas fa-chalkboard-teacher text-gray-500 text-sm flex-shrink-0"></i>
                                <span class="truncate text-left" x-text="selected"></span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-20 mt-1 w-full bg-white rounded-md shadow-lg py-1 border border-gray-200 max-h-60 overflow-y-auto">
                            <a @click="selected = 'Todas las clases'; filtrarTabla()" data-clase=""
                                class="flex items-center gap-x-2 px-3 py-2 text-sm text-gray-800 hover:bg-purple-50 cursor-pointer">
                                <i class="fas fa-chalkboard-teacher text-gray-500 text-sm"></i>
                                <span class="truncate">Todas las clases</span>
                            </a>
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

                    {{-- Selector de Fecha --}}
                    <div x-data="{ open: false, selected: 'Todas las fechas' }" class="relative w-full">
                        <button @click="open = !open" type="button"
                            class="relative w-full py-2 px-3 inline-flex justify-between items-center gap-x-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 min-h-[38px]">
                            <div class="flex items-center gap-x-2 truncate min-w-0 flex-1">
                                <i class="fas fa-calendar text-gray-500 text-sm flex-shrink-0"></i>
                                <span class="truncate text-left" x-text="selected"></span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute z-20 mt-1 w-full bg-white rounded-md shadow-lg py-1 border border-gray-200 max-h-60 overflow-y-auto">
                            <a @click="selected = 'Todas las fechas'; filtrarTabla()" data-fecha=""
                                class="flex items-center gap-x-2 px-3 py-2 text-sm text-gray-800 hover:bg-purple-50 cursor-pointer">
                                <i class="fas fa-calendar text-gray-500 text-sm"></i>
                                <span class="truncate">Todas las fechas</span>
                            </a>
                            <a @click="selected = 'Hoy'; filtrarTabla()" data-fecha="{{ date('Y-m-d') }}"
                                class="flex items-center gap-x-2 px-3 py-2 text-sm text-gray-800 hover:bg-purple-50 cursor-pointer">
                                <i class="fas fa-clock text-gray-500 text-sm"></i>
                                <span class="truncate">Hoy</span>
                            </a>
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

        {{-- ======= TABLA DE ASISTENCIAS ======= --}}
        {{-- Listado de asistencias con filtros y exportación --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-4 sm:p-6">
                {{-- Header de tabla con título y botón exportar --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 sm:mb-6">
                    <div>
                        <h2 class="text-lg font-semibold">Registro de Asistencias</h2>
                        <p class="text-xs sm:text-sm text-gray-500">Mostrando asistencias</p>
                    </div>
                    <button onclick="exportarCSV(event)"
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">
                        <i class="fas fa-download mr-2"></i>
                        Exportar CSV
                    </button>
                </div>

                {{-- Tabla responsive con datos de asistencia --}}
                <div class="overflow-x-auto rounded-md border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    Estudiante
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    Matrícula
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    Clase
                                </th>
                                <th
                                    class="hidden sm:table-cell px-3 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    Aula
                                </th>
                                <th
                                    class="hidden sm:table-cell px-3 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    Asiento
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-2 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    Hora
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($reservas as $reserva)
                                <tr class="{{ $loop->index % 2 == 0 ? 'bg-white' : 'bg-gray-200' }}"
                                    data-clase-id="{{ $reserva['clase_id'] ?? '' }}"
                                    data-fecha="{{ is_string($reserva['fecha']) ? $reserva['fecha'] : ($reserva['fecha'] instanceof \Carbon\Carbon ? $reserva['fecha']->format('Y-m-d') : '') }}">
                                    <td class="px-3 py-3 whitespace-nowrap text-sm font-medium">
                                        {{ $reserva['nombre'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm">
                                        {{ $reserva['matricula'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm">
                                        {{ $reserva['clase'] ?? 'N/A' }}
                                    </td>
                                    <td class="hidden sm:table-cell px-3 py-3 whitespace-nowrap text-sm">
                                        {{ $reserva['aula'] ?? 'N/A' }}
                                    </td>
                                    <td class="hidden sm:table-cell px-3 py-3 whitespace-nowrap text-sm">
                                        {{ $reserva['asiento'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm">
                                        @if (is_string($reserva['fecha']))
                                            {{ $reserva['fecha'] }}
                                        @elseif($reserva['fecha'] instanceof \Carbon\Carbon)
                                            {{ $reserva['fecha']->format('d/m/Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm">
                                        {{ $reserva['hora'] }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm">
                                        <div class="flex items-center">
                                            @if ($reserva['estado'] === 'Completada')
                                                <span
                                                    class="inline-flex items-center gap-2 px-3 w-36 h-8 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21.801 10A10 10 0 1 1 17 3.335" />
                                                        <path d="m9 11 3 3L22 4" />
                                                    </svg>
                                                    <div class="flex-1 truncate">
                                                        <span class="hidden sm:inline">Completado</span>
                                                        <span class="sm:hidden">Completado</span>
                                                    </div>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 px-3 w-32 h-8 rounded-full text-xs sm:text-sm font-medium bg-yellow-100 text-yellow-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                        <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z" />
                                                        <path d="M9 17h6" />
                                                        <path d="M9 13h6" />
                                                    </svg>
                                                    <div class="flex-1 truncate">
                                                        <span class="hidden sm:inline">Justificado</span>
                                                        <span class="sm:hidden">Justificado</span>
                                                    </div>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-4 text-center text-sm text-gray-500">
                                        No hay registros de asistencia disponibles
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- ======= SCRIPTS DE FUNCIONALIDAD ======= --}}
@push('scripts')
    {{-- Scripts para exportación y manejo de datos --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <script>
        // Script para gestión de filtros y exportación
        document.addEventListener('DOMContentLoaded', function() {
            // Variables para filtrado
            let filtroClase = '';
            let filtroFecha = '';
            let terminoBusqueda = '';
            
            const searchInput = document.getElementById('searchTerm');
            const rows = document.querySelectorAll('tbody tr');
            
            // Función para filtrar la tabla
            window.filtrarTabla = function() {
                // Obtener valores actuales de filtros
                const claseElement = document.querySelector('a[data-clase].selected');
                const fechaElement = document.querySelector('a[data-fecha].selected');
                
                if (claseElement) {
                    filtroClase = claseElement.getAttribute('data-clase');
                } else {
                    // Intentar obtener desde el dropdown de Alpine.js
                    const claseDropdown = document.querySelector('div[x-data*="selected"][data-clase]');
                    filtroClase = claseDropdown ? claseDropdown.getAttribute('data-clase') : '';
                }
                
                if (fechaElement) {
                    filtroFecha = fechaElement.getAttribute('data-fecha');
                } else {
                    // Intentar obtener desde el dropdown de Alpine.js
                    const fechaDropdown = document.querySelector('div[x-data*="selected"][data-fecha]');
                    filtroFecha = fechaDropdown ? fechaDropdown.getAttribute('data-fecha') : '';
                }
                
                // Aplicar filtros a cada fila
                rows.forEach(row => {
                    const texto = row.textContent.toLowerCase();
                    const cumpleBusqueda = terminoBusqueda === '' || texto.includes(terminoBusqueda);
                    
                    const claseId = row.getAttribute('data-clase-id') || '';
                    const fechaAsist = row.getAttribute('data-fecha') || '';
                    
                    const cumpleClase = filtroClase === '' || claseId === filtroClase;
                    const cumpleFecha = filtroFecha === '' || fechaAsist.includes(filtroFecha);
                    
                    row.style.display = (cumpleBusqueda && cumpleClase && cumpleFecha) ? '' : 'none';
                });
                
                // Actualizar mensaje sobre resultados
                const visibles = [...rows].filter(row => row.style.display !== 'none').length;
                const total = rows.length;
                const mensajeElemento = document.querySelector('.text-xs.sm\\:text-sm.text-gray-500');
                if (mensajeElemento) {
                    mensajeElemento.textContent = `Mostrando ${visibles} de ${total} asistencias`;
                }
            };
            
            // Marcar elemento seleccionado en los dropdowns
            document.querySelectorAll('[data-clase], [data-fecha]').forEach(item => {
                item.addEventListener('click', function() {
                    // Quitar selección previa
                    const isClase = this.hasAttribute('data-clase');
                    const selector = isClase ? '[data-clase]' : '[data-fecha]';
                    
                    document.querySelectorAll(selector).forEach(el => {
                        el.classList.remove('selected');
                    });
                    
                    // Añadir clase seleccionada
                    this.classList.add('selected');
                });
            });
            
            // Evento para búsqueda por texto
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    terminoBusqueda = this.value.toLowerCase();
                    filtrarTabla();
                });
            }
            
            // Asegurarse de que las filas tienen los atributos necesarios para filtrar
            rows.forEach(row => {
                if (!row.dataset.claseId || !row.dataset.fecha) {
                    const celdas = row.querySelectorAll('td');
                    if (celdas.length > 0) {
                        // Nombre de clase (3ra columna)
                        const nombreClase = celdas[2]?.textContent.trim();
                        // Buscar el ID correspondiente al nombre
                        const claseEncontrada = Array.from(@json($clasesUnicas))
                            .find(c => c.nombre === nombreClase);
                        if (claseEncontrada) {
                            row.dataset.claseId = claseEncontrada.id;
                        }
                        
                        // Fecha (6ta columna)
                        const fecha = celdas[5]?.textContent.trim();
                        if (fecha) {
                            // Convertir formato dd/mm/yyyy a yyyy-mm-dd para filtrado
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
            
            // Al hacer clic en enlaces de filtros, marcarlos como seleccionados
            document.querySelectorAll('a[data-clase], a[data-fecha]').forEach(link => {
                link.addEventListener('click', function() {
                    // Remover clase 'selected' de otros enlaces del mismo tipo
                    const isClase = this.hasAttribute('data-clase');
                    const selector = isClase ? 'a[data-clase]' : 'a[data-fecha]';
                    
                    document.querySelectorAll(selector).forEach(el => {
                        el.classList.remove('selected');
                    });
                    
                    // Añadir clase 'selected' al enlace actual
                    this.classList.add('selected');
                    
                    // Aplicar filtro
                    filtrarTabla();
                });
            });
            
            // Inicializar la tabla para mostrar conteo inicial
            filtrarTabla();
        });

        // Función mejorada para exportar Excel con estilos
        function exportarCSV(event) {
            // Corregir el manejo del evento
            if (!event) event = window.event;
            const button = event.currentTarget || event.target;

            // Guardar el texto original y cambiar el botón
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
            button.disabled = true;

            // Crear notificación
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

            // Mostrar notificación con animación
            setTimeout(() => notification.style.transform = 'translate(0, 0)', 10);

            // Iniciar proceso de exportación con pequeño retraso para mostrar animación
            setTimeout(async () => {
                try {
                    // Crear libro de Excel
                    const workbook = new ExcelJS.Workbook();
                    workbook.creator = 'AulaTec';
                    workbook.created = new Date();

                    // Crear hoja de cálculo
                    const worksheet = workbook.addWorksheet('Asistencias');

                    // Obtener datos de la tabla
                    const table = document.querySelector('table');
                    const rows = table.querySelectorAll('tr');

                    // Procesar encabezados
                    const headers = [];
                    const headerCells = rows[0].querySelectorAll('th');
                    headerCells.forEach(header => {
                        if (window.getComputedStyle(header).display !== 'none') {
                            headers.push(header.innerText.trim());
                        }
                    });

                    // Añadir encabezados al Excel
                    worksheet.addRow(headers);

                    // Dar formato a la fila de encabezados
                    const headerRow = worksheet.getRow(1);
                    headerRow.font = {
                        bold: true,
                        color: {
                            argb: 'FFFFFFFF'
                        }
                    };
                    headerRow.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: {
                            argb: '8366B3'
                        } // Morado AulaTec
                    };
                    headerRow.alignment = {
                        vertical: 'middle',
                        horizontal: 'center'
                    };
                    headerRow.height = 22;

                    // Procesar filas de datos
                    let rowIndex = 2; // Comienza en 2 porque la fila 1 son los encabezados

                    rows.forEach((row, index) => {
                        if (index === 0) return; // Saltar encabezados
                        if (window.getComputedStyle(row).display === 'none')
                            return; // Saltar filas ocultas

                        const rowData = [];
                        const cells = row.querySelectorAll('td');
                        let hasCompletado = false;
                        let hasJustificado = false;

                        cells.forEach(cell => {
                            // Obtener solo el texto limpio
                            let text = '';

                            if (cell.querySelector('.truncate')) {
                                text = cell.querySelector('.truncate').innerText.trim();
                            } else {
                                text = cell.innerText.replace(/\s+/g, ' ').trim();
                            }

                            // Detectar estados para aplicar estilos después
                            if (text.includes('Completado')) hasCompletado = true;
                            if (text.includes('Justificado')) hasJustificado = true;

                            rowData.push(text);
                        });

                        // Añadir fila al Excel
                        if (rowData.length > 0) {
                            const excelRow = worksheet.addRow(rowData);

                            // Aplicar estilos a la fila
                            excelRow.eachCell((cell, colNumber) => {
                                // Bordes para todas las celdas
                                cell.border = {
                                    top: {
                                        style: 'thin',
                                        color: {
                                            argb: 'DDDDDD'
                                        }
                                    },
                                    right: {
                                        style: 'thin',
                                        color: {
                                            argb: 'DDDDDD'
                                        }
                                    },
                                    bottom: {
                                        style: 'thin',
                                        color: {
                                            argb: 'DDDDDD'
                                        }
                                    },
                                    left: {
                                        style: 'thin',
                                        color: {
                                            argb: 'DDDDDD'
                                        }
                                    }
                                };

                                // Alineación
                                cell.alignment = {
                                    vertical: 'middle'
                                };

                                // Filas alternadas con color de fondo
                                if (rowIndex % 2 === 0) {
                                    cell.fill = {
                                        type: 'pattern',
                                        pattern: 'solid',
                                        fgColor: {
                                            argb: 'F9FAFB'
                                        } // Gris muy claro
                                    };
                                }

                                // Estilos para la columna de estado (última columna)
                                if (colNumber === rowData.length && (hasCompletado ||
                                        hasJustificado)) {
                                    if (hasCompletado) {
                                        cell.font = {
                                            bold: true,
                                            color: {
                                                argb: '166534'
                                            }
                                        }; // Verde oscuro
                                        cell.fill = {
                                            type: 'pattern',
                                            pattern: 'solid',
                                            fgColor: {
                                                argb: 'DCFCE7'
                                            } // Verde claro
                                        };
                                    } else if (hasJustificado) {
                                        cell.font = {
                                            bold: true,
                                            color: {
                                                argb: '854D0E'
                                            }
                                        }; // Naranja oscuro
                                        cell.fill = {
                                            type: 'pattern',
                                            pattern: 'solid',
                                            fgColor: {
                                                argb: 'FEF9C3'
                                            } // Amarillo claro
                                        };
                                    }
                                }
                            });

                            // Incrementar contador de filas
                            rowIndex++;
                        }
                    });

                    // Ajustar ancho de columnas automáticamente
                    worksheet.columns.forEach(column => {
                        let maxLength = 0;
                        column.eachCell({
                            includeEmpty: true
                        }, cell => {
                            if (cell.value) {
                                const length = cell.value.toString().length;
                                if (length > maxLength) maxLength = length;
                            }
                        });
                        column.width = maxLength < 10 ? 10 : maxLength + 2;
                    });

                    // Generar el Excel
                    const buffer = await workbook.xlsx.writeBuffer();

                    // Obtener la fecha actual formateada
                    const today = new Date();
                    const formattedDate = `${today.getDate()}-${today.getMonth() + 1}-${today.getFullYear()}`;

                    // Descargar el archivo Excel
                    saveAs(new Blob([buffer]), `asistencias_${formattedDate}.xlsx`);

                    // Actualizar la notificación a éxito
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

                // Restaurar el botón a su estado original
                button.innerHTML = originalText;
                button.disabled = false;

                // Ocultar la notificación después de un tiempo
                setTimeout(() => {
                    notification.style.transform = 'translate(0, -100px)';
                    setTimeout(() => document.body.removeChild(notification), 500);
                }, 3000);

            }, 800);
        }
    </script>
@endpush

{{-- ======= ESTILOS PERSONALIZADOS ======= --}}
@section('styles')
    {{-- Estilos personalizados para la vista de asistencias --}}
    <style>
        /* ======= ESTILOS RESPONSIVE PARA FILTROS ======= */
        /* Asegurar que los dropdowns se muestren correctamente en móviles */
        @media (max-width: 640px) {
            /* Contenedor de filtros más espacioso en móvil */
            .grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4 {
                gap: 1rem;
            }
            
            /* Botones de filtro con altura fija en móvil */
            button[type="button"] {
                min-height: 44px; /* Tamaño mínimo para touch */
                font-size: 14px;
            }
            
            /* Iconos con tamaño fijo */
            .fas {
                width: 16px;
                flex-shrink: 0;
            }
            
            /* Dropdown menus con ancho total en móvil */
            div[x-show="open"] {
                left: 0;
                right: 0;
                width: 100%;
                max-height: 200px; /* Altura máxima reducida en móvil */
            }
        }

        /* ======= ESTILOS PARA DROPDOWNS ======= */
        /* Mejorar visibilidad y usabilidad */
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

        /* Items del dropdown */
        div[x-show="open"] a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            font-size: 0.875rem;
            color: #374151;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            min-height: 44px; /* Área táctil mínima */
        }

        div[x-show="open"] a:hover {
            background-color: #f3f0ff;
        }

        div[x-show="open"] a.selected {
            background-color: #f3f0ff;
            font-weight: 600;
            color: #6d28d9;
        }

        /* ======= ESTILOS PARA BOTONES DE FILTRO ======= */
        /* Asegurar alineación consistente */
        button[type="button"] {
            transition: all 0.15s ease-in-out;
        }

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

        /* ======= TRUNCATE MEJORADO ======= */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Asegurar que los flex items no se deformen */
        .flex-shrink-0 {
            flex-shrink: 0;
        }

        .min-w-0 {
            min-width: 0;
        }

        /* ======= ESTILOS ESPECÍFICOS PARA TABLA DE ASISTENCIAS ======= */
        /* Asegurar que la tabla se vea bien en todos los dispositivos */
        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db #f3f4f6;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Estados de asistencia más visibles en móvil */
        @media (max-width: 640px) {
            .inline-flex.items-center.gap-2 {
                width: auto;
                min-width: 100px;
                justify-content: center;
                font-size: 0.75rem;
            }
        }
    </style>
@endsection