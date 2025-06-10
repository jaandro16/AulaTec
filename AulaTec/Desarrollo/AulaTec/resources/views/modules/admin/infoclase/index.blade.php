@extends('layouts.admin')

@section('titulo', $titulo)
@section('contenido')

    {{-- ======= SECCIÓN SELECTOR DE CLASE ======= --}}
    {{-- Card con selector de clase y detalles de la sesión --}}
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900">Seleccionar Clase</h2>
            <p class="text-sm text-gray-600">Elige la clase para ver la distribución de asientos</p>
        </div>
        
        <div class="p-6">
            @if($clases->isEmpty())
                <div class="text-gray-500 text-sm">
                    No hay clases programadas próximamente.
                </div>
            @else
                <select id="clase-select" 
                        class="w-full md:w-[350px] rounded-md border border-gray-200 
                            bg-white text-gray-900 px-2 py-2
                            focus:ring-1 focus:ring-purple-500 focus:border-purple-500">
                    <option value="" disabled selected>Selecciona una clase</option>
                    @foreach($clases as $clase)
                        <option value="{{ $clase['id'] }}">
                            {{ $clase['nombre'] }} - {{ $clase['fecha']->format('d/m/Y') }} 
                            {{ $clase['hora_inicio'] }}-{{ $clase['hora_fin'] }} ({{ $clase['duracion'] }} min)
                        </option>
                    @endforeach
                </select>
            @endif

            {{-- Detalles de Clase --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($clases->isNotEmpty())
                    <div>
                        <h3 class="text-lg font-medium mb-4 text-gray-900">Detalles de la Clase</h3>
                        <div id="mensaje-seleccion" class="text-gray-500 text-sm pl-1">
                            Selecciona una clase para ver sus detalles
                        </div>
                        <div id="detalles-clase" class="space-y-3 hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-5 text-center">
                                    <i class="far fa-calendar text-gray-500"></i>
                                </div>
                                <span class="text-gray-700" id="clase-fecha"></span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-5 text-center">
                                    <i class="far fa-clock text-gray-500"></i>
                                </div>
                                <span class="text-gray-700" id="clase-hora"></span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-5 text-center">
                                    <i class="fas fa-map-marker-alt text-gray-500"></i>
                                </div>
                                <span class="text-gray-700" id="clase-aula"></span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-5 text-center">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>
                                <span class="text-gray-700" id="clase-profesor"></span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Estadísticas --}}
                    <div>
                        <h3 class="text-lg font-medium mb-4 text-gray-900">Estadísticas</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-md">
                                <p class="text-sm text-gray-600 mb-1">Asientos Ocupados</p>
                                <p class="text-2xl font-bold text-gray-900" id="asientos-ocupados">0/0</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <p class="text-sm text-gray-600 mb-1">Porcentaje de Ocupación</p>
                                <p class="text-2xl font-bold text-gray-900" id="porcentaje-ocupacion">0%</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ======= SECCIÓN DISTRIBUCIÓN DE ASIENTOS ======= --}}
    {{-- Visualización y gestión de asientos por filas --}}
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Distribución de Asientos</h2>
            <p class="text-sm text-gray-500">Visualización de asientos y estudiantes por fila</p>
        </div>

        <div class="p-6">
            {{-- ======= FILTROS DE BÚSQUEDA ======= --}}
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="relative flex-grow">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input type="text" 
                        placeholder="Buscar por asiento, estudiante o matrícula..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-md
                                focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div class="grid grid-cols-2 gap-4 md:w-80">
                    <select id="filtro-fila" class="w-full rounded-md border border-gray-200 px-2 py-2
                                            focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="todas">Todas las filas</option>
                        <option value="A">Fila A</option>
                        <option value="B">Fila B</option>
                        <option value="C">Fila C</option>
                        <option value="D">Fila D</option>
                    </select>

                    <select id="filtro-estado" class="w-full rounded-md border border-gray-200 px-2 py-2
                                            focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="todos">Todos los estados</option>
                        <option value="ocupado">Ocupados</option>
                        <option value="libre">Libres</option>
                    </select>
                </div>
            </div>

            {{-- ======= MENSAJE INICIAL ======= --}}
            <div id="mensaje-inicial" class="text-center py-12">
                <div class="mx-auto w-16 h-16 mb-4">
                    <svg class="w-full h-full text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2M16 11V7a4 4 0 0 0-8 0v4M12 15h.01"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-lg">
                    Selecciona una clase para conocer los asientos libres y ocupados
                </p>
            </div>

            {{-- ======= GRID DE ASIENTOS ======= --}}
            {{-- Organizado por filas A-D --}}
            <div class="space-y-6 hidden" id="seats-container">
                @foreach(['A', 'B', 'C', 'D'] as $fila)
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-md 
                                font-medium text-purple-600 mr-2">
                            {{ $fila }}
                        </div>
                        <span>Fila {{ $fila }}</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="fila-{{ $fila }}">
                        @for($i = 1; $i <= 8; $i++)
                            @if($fila === 'A' && $i <= 3)
                                @continue
                            @endif
                            @if(($fila === 'A' || $fila === 'D') && $i === 8)
                                <div class="bg-gray-100 rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                                    <div class="h-2 bg-gray-300"></div>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800">
                                                N/D
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-center h-16 text-gray-500">
                                            <p>No disponible</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div id="asiento-{{ $fila }}{{ $i }}" class="bg-white rounded-lg border border-green-200 shadow-sm overflow-hidden">
                                    <div class="h-2 bg-green-500"></div>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">
                                                Asiento {{ $fila }}{{ $i }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-center h-16 text-gray-500">
                                            <p>Asiento disponible</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

{{-- ======= SCRIPTS PARA GESTIÓN DE ASIENTOS ======= --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const detallesClase = document.getElementById('detalles-clase');
            const mensajeSeleccion = document.getElementById('mensaje-seleccion');
            const mensajeInicial = document.getElementById('mensaje-inicial');
            const seatsContainer = document.getElementById('seats-container');
            const searchInput = document.querySelector('input[type="text"]');
            const filtroFila = document.getElementById('filtro-fila');
            const filtroEstado = document.getElementById('filtro-estado');

            // Función de filtrado
            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedFila = filtroFila.value;
                const selectedEstado = filtroEstado.value;
                const seats = document.querySelectorAll('[id^="asiento-"]');

                seats.forEach(seat => {
                    let showBySearch = true;
                    let showByFila = true;
                    let showByEstado = true;

                    // Filtro por búsqueda
                    if (searchTerm) {
                        const seatContent = seat.textContent.toLowerCase();
                        showBySearch = seatContent.includes(searchTerm);
                    }

                    // Filtro por fila
                    if (selectedFila !== 'todas') {
                        showByFila = seat.id.includes(`asiento-${selectedFila}`);
                    }

                    // Filtro por estado
                    if (selectedEstado !== 'todos') {
                        const isOcupado = seat.classList.contains('border-purple-200');
                        showByEstado = (selectedEstado === 'ocupado') ? isOcupado : !isOcupado;
                    }

                    // Aplicar visibilidad
                    const shouldShow = showBySearch && showByFila && showByEstado;
                    const parentRow = seat.closest('.space-y-3');

                    if (shouldShow) {
                        seat.classList.remove('hidden');
                        if (parentRow) {
                            parentRow.classList.remove('hidden');
                        }
                    } else {
                        seat.classList.add('hidden');
                        if (parentRow) {
                            const visibleSeatsInRow = parentRow.querySelectorAll('[id^="asiento-"]:not(.hidden)').length;
                            if (visibleSeatsInRow === 0) {
                                parentRow.classList.add('hidden');
                            }
                        }
                    }
                });

                // Mostrar mensaje si no hay resultados
                const noResultsMessage = document.getElementById('no-results-message');
                const hasVisibleSeats = Array.from(seats).some(seat => !seat.classList.contains('hidden'));
                
                if (!hasVisibleSeats) {
                    if (!noResultsMessage) {
                        const message = document.createElement('div');
                        message.id = 'no-results-message';
                        message.className = 'text-center py-8 text-gray-500';
                        message.innerHTML = 'No se encontraron asientos con los filtros seleccionados';
                        seatsContainer.appendChild(message);
                    }
                } else if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            }

            // Event listeners para los filtros
            filtroFila.addEventListener('change', applyFilters);
            filtroEstado.addEventListener('change', applyFilters);

            // Modificar el event listener existente del input de búsqueda
            searchInput.addEventListener('input', debounce(function() {
                if (!seatsContainer.classList.contains('hidden')) {
                    applyFilters();
                }
            }, 300));

            // Función debounce para optimizar el rendimiento
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            document.getElementById('clase-select').addEventListener('change', function() {
                const claseId = this.value;

                mensajeInicial.classList.add('hidden');
                seatsContainer.classList.remove('hidden');
                
                fetch(`/infoclase/getDetalles/${claseId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Actualizar detalles existentes...
                        mensajeSeleccion.classList.add('hidden');
                        detallesClase.classList.remove('hidden');
                        
                        document.getElementById('clase-fecha').textContent = data.fecha;
                        document.getElementById('clase-hora').textContent = data.hora;
                        document.getElementById('clase-aula').textContent = data.aula;
                        document.getElementById('clase-profesor').textContent = data.profesor;
                        
                        document.getElementById('asientos-ocupados').textContent = 
                            `${data.estadisticas.ocupados}/${data.estadisticas.total}`;
                        document.getElementById('porcentaje-ocupacion').textContent = 
                            `${data.estadisticas.porcentaje}%`;

                        // Resetear todos los asientos a disponibles
                        document.querySelectorAll('[id^="asiento-"]').forEach(asiento => {
                            asiento.className = 'bg-white rounded-lg border border-green-200 shadow-sm overflow-hidden';
                            asiento.querySelector('.h-2').className = 'h-2 bg-green-500';
                            asiento.querySelector('.p-4').innerHTML = `
                                <div class="flex justify-between items-start mb-3">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">
                                        ${asiento.id.replace('asiento-', 'Asiento ')}
                                    </span>
                                </div>
                                <div class="flex items-center justify-center h-16 text-gray-500">
                                    <p>Asiento disponible</p>
                                </div>
                            `;
                        });

                        // Actualizar asientos ocupados
                        data.asientosOcupados.forEach(asientoData => {
                            const asientoElement = document.getElementById(`asiento-${asientoData.asiento}`);
                            if (asientoElement) {
                                asientoElement.className = 'bg-white rounded-lg border border-purple-200 shadow-sm overflow-hidden';
                                asientoElement.querySelector('.h-2').className = 'h-2 bg-purple-600';
                                asientoElement.querySelector('.p-4').innerHTML = `
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-purple-100 text-purple-800">
                                            Asiento ${asientoData.asiento}
                                        </span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                                <circle cx="12" cy="7" r="4"/>
                                            </svg>
                                            <span class="font-medium">${asientoData.estudiante.nombre}</span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <p>Matrícula: ${asientoData.estudiante.matricula}</p>
                                            <p class="truncate">${asientoData.estudiante.email}</p>
                                        </div>
                                    </div>
                                `;
                            }
                        });
                        filtroFila.value = 'todas';
                        filtroEstado.value = 'todos';
                        searchInput.value = '';
                        // Eliminar el mensaje de no resultados si existe
                        const noResultsMessage = document.getElementById('no-results-message');
                        if (noResultsMessage) {
                            noResultsMessage.remove();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // En caso de error, volver a mostrar el mensaje inicial
                        mensajeInicial.classList.remove('hidden');
                        seatsContainer.classList.add('hidden');
                    });
            });
        });
    </script>
@endpush