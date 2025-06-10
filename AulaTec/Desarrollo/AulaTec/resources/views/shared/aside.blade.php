{{-- Sidebar principal --}}
<aside id="sidebar" class="border-r border-gray-200 flex flex-col">
    {{-- Cabecera del sidebar (solo visible en móvil) --}}
    <div class="lg:hidden flex items-center justify-between p-4 border-b border-gray-200">
        <a href="{{ route('home') }}" class="flex items-center space-x-3">
            {{-- <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-600 to-cyan-500"></div> --}}
            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-600 to-cyan-500 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 9h6v6H9z"/>
                    <path d="M4 4v2m0 12v2m16-2v2m0-16v2M4 12H2m20 0h-2M12 4V2m0 20v-2" />
                </svg>
            </div>
            <span class="font-bold text-xl">AulaTec</span>
        </a>
        <button id="close-sidebar-button"
            class="rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Logo (solo visible en desktop) --}}
    <div class="hidden lg:flex items-center space-x-2 px-7 py-3 border-b border-gray-200">
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            {{-- <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-600 to-cyan-500"></div> --}}
            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-600 to-cyan-500 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 9h6v6H9z"/>
                    <path d="M4 4v2m0 12v2m16-2v2m0-16v2M4 12H2m20 0h-2M12 4V2m0 20v-2" />
                </svg>
            </div>
            <span class="font-bold text-xl">AulaTec</span>
        </a>
    </div>

    {{-- Navegación principal --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3">
        <div class="space-y-1">
            {{-- Crear Clase --}}
            <a href="{{ route('admin.crear-clase.create') }}"
                class="flex items-center w-full px-3 py-2.5 text-base sm:text-md font-medium rounded-md transition-colors no-underline hover:no-underline
               {{ request()->routeIs('admin.crear-clase.create') ? 'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 mr-3 {{ request()->routeIs('admin.crear-clase.create') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }}">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M8 12h8" />
                    <path d="M12 8v8" />
                </svg>
                <span class="truncate">Crear Clase</span>
            </a>

            {{-- Escaner QR --}}
            <a href="{{ route('admin.admin-qr.index') }}"
                class="flex items-center w-full px-3 py-2.5 text-base sm:text-md font-medium rounded-md transition-colors no-underline hover:no-underline
               {{ request()->routeIs('admin.admin-qr.index') ? 'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 mr-3 {{ request()->routeIs('admin.admin-qr.index') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }}">
                    <rect width="5" height="5" x="3" y="3" rx="1" />
                    <rect width="5" height="5" x="16" y="3" rx="1" />
                    <rect width="5" height="5" x="3" y="16" rx="1" />
                    <path d="M21 16h-3a2 2 0 0 0-2 2v3" />
                    <path d="M21 21v.01" />
                    <path d="M12 7v3a2 2 0 0 1-2 2H7" />
                    <path d="M3 12h.01" />
                    <path d="M12 3h.01" />
                    <path d="M12 16v.01" />
                    <path d="M16 12h1" />
                    <path d="M21 12v.01" />
                    <path d="M12 21v-1" />
                </svg>
                <span class="truncate">Escaner QR</span>
            </a>

            {{-- Control de Asientos --}}
            <a href="{{ route('admin.infoclase.index') }}"
                class="flex items-center w-full px-3 py-2.5 text-base sm:text-md font-medium rounded-md transition-colors no-underline hover:no-underline
               {{ request()->routeIs('admin.infoclase.index') ? 'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 mr-3 {{ request()->routeIs('admin.infoclase.index') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }}">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
                <span class="truncate">Control de Asientos</span>
            </a>

            {{-- Registro de Asistencia --}}
            <a href="{{ route('admin.asistencias.index') }}"
                class="flex items-center w-full px-3 py-2.5 text-base sm:text-md font-medium rounded-md transition-colors no-underline hover:no-underline
               {{ request()->routeIs('admin.asistencias.index') ? 'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 mr-3 {{ request()->routeIs('admin.asistencias.index') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }}">
                    <path d="M8 2v4" />
                    <path d="M16 2v4" />
                    <rect width="18" height="18" x="3" y="4" rx="2" />
                    <path d="M3 10h18" />
                </svg>
                <span class="truncate">Registro de Asistencia</span>
            </a>

            {{-- Justificantes --}}
            <a href="{{ route('admin.justificantes.index') }}"
                class="flex items-center w-full px-3 py-2.5 text-base sm:text-md font-medium rounded-md transition-colors no-underline hover:no-underline
               {{ request()->routeIs('admin.justificantes.index') ? 'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 mr-3 {{ request()->routeIs('admin.justificantes.index') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }}">
                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                    <path d="M12 11h4" />
                    <path d="M12 16h4" />
                    <path d="M8 11h.01" />
                    <path d="M8 16h.01" />
                </svg>
                <span class="truncate">Justificantes</span>
            </a>

            {{-- Crear profesor --}}
            <a href="{{ route('admin.crear-profesor.create') }}"
                class="flex items-center w-full px-3 py-2.5 text-base sm:text-md font-medium rounded-md transition-colors no-underline hover:no-underline
               {{ request()->routeIs('admin.crear-profesor.create') ? 'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round"
                    class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 mr-3 {{ request()->routeIs('admin.crear-profesor.create') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }}">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="19" x2="19" y1="8" y2="14" />
                    <line x1="22" x2="16" y1="11" y2="11" />
                </svg>
                <span class="truncate">Crear Profesor</span>
            </a>

            {{-- Editar perfil --}}
            <a href="{{ route('admin.crear-profesor.edit') }}"
                class="flex items-center w-full px-3 py-2.5 text-base sm:text-md font-medium rounded-md transition-colors no-underline hover:no-underline
               {{ request()->routeIs('admin.crear-profesor.edit') ? 'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 mr-3 {{ request()->routeIs('admin.crear-profesor.edit') ? 'text-purple-600' : 'text-gray-400 group-hover:text-purple-600' }}">
                    <path d="M11.5 15H7a4 4 0 0 0-4 4v2" />
                    <path
                        d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                    <circle cx="10" cy="7" r="4" />
                </svg> <span class="truncate">Editar Profesor</span>
            </a>
        </div>
    </nav>

    {{-- Footer con botón de cerrar sesión --}}
    <div class="p-3 sm:p-4 border-t border-gray-200 mt-auto">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="mr-2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Cerrar Sesión
            </button>
        </form>
    </div>
</aside>

@push('scripts')
    <script>
        // Asignar evento al botón de cerrar en móvil
        document.addEventListener('DOMContentLoaded', function() {
            const closeButton = document.getElementById('close-sidebar-button');
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    document.body.classList.remove('sidebar-open');
                });
            }
        });
    </script>
@endpush
