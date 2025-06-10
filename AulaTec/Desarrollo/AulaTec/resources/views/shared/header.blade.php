{{-- Header compartido de la aplicación --}}
{{-- Barra de navegación principal con menú responsive y autenticación --}}

{{-- Header sticky con backdrop blur para efecto moderno --}}
<header class="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/80 backdrop-blur-sm">
    {{-- Contenedor principal con ancho máximo y padding responsivo --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flexbox para distribuir elementos horizontalmente --}}
        <div class="flex h-16 items-center justify-between">
            
            {{-- ======= SECCIÓN IZQUIERDA: LOGO Y NOMBRE ======= --}}
            {{-- Branding de la aplicación con enlace a home --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    {{-- Logo circular con gradiente de marca --}}
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-600 to-cyan-500 flex items-center justify-center">
                        {{-- Icono SVG del logo --}}
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 9h6v6H9z"/>
                            <path d="M4 4v2m0 12v2m16-2v2m0-16v2M4 12H2m20 0h-2M12 4V2m0 20v-2" />
                        </svg>
                    </div>
                    {{-- Nombre de la aplicación --}}
                    <span class="font-bold text-xl">AulaTec</span>
                </a>
            </div>

            {{-- ======= SECCIÓN CENTRAL: NAVEGACIÓN PRINCIPAL ======= --}}
            {{-- Menú de navegación (solo visible en desktop) --}}
            <nav class="hidden md:flex items-center space-x-6">
                {{-- ======= NAVEGACIÓN CONDICIONAL POR AUTENTICACIÓN ======= --}}
                {{-- Solo mostrar navegación si el usuario está logueado --}}
                @auth
                    {{-- ======= NAVEGACIÓN ESPECÍFICA PARA PROFESORES ======= --}}
                    @if(Auth::user()->rol === 'profesor')
                        {{-- Panel de administración de clases --}}
                        <a href="{{ route('admin.crear-clase.create') }}" 
                           {{-- Clases dinámicas para resaltar página activa --}}
                           class="text-sm font-medium transition-colors hover:text-purple-600 {{ request()->routeIs('admin.crear-clase.create') ? 'text-purple-600' : 'text-gray-700' }}">
                            Panel de Profesor
                        </a>
                        {{-- Perfil específico de profesor --}}
                        <a href="{{ route('admin.crear-profesor.edit') }}"
                           class="text-sm font-medium transition-colors hover:text-purple-600 {{ request()->routeIs('admin.crear-profesor.edit') ? 'text-purple-600' : 'text-gray-700' }}">
                            Mi Perfil
                        </a>
                    {{-- ======= NAVEGACIÓN PARA ESTUDIANTES Y OTROS ROLES ======= --}}
                    @else
                        {{-- Dashboard principal del estudiante --}}
                        <a href="{{ route('dashboard') }}" 
                           class="text-sm font-medium transition-colors hover:text-purple-600 {{ request()->routeIs('dashboard') ? 'text-purple-600' : 'text-gray-700' }}">
                            Panel
                        </a>
                        {{-- Perfil del estudiante --}}
                        <a href="{{ route('perfil.show') }}"
                           class="text-sm font-medium transition-colors hover:text-purple-600 {{ request()->routeIs('perfil.show') ? 'text-purple-600' : 'text-gray-700' }}">
                            Mi Perfil
                        </a>
                    @endif
                @endauth
            </nav>

            {{-- ======= SECCIÓN DERECHA: ACCIONES Y AUTENTICACIÓN ======= --}}
            <div class="flex items-center space-x-4">
                {{-- ======= BOTÓN DE CONTACTO ======= --}}
                {{-- Icono circular para acceder a página de contacto --}}
                <a href="{{ route('contacto.index') }}" class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                    {{-- Icono de sobre/email --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </a>

                {{-- ======= MENÚ DE AUTENTICACIÓN (DESKTOP) ======= --}}
                @auth
                    {{-- Usuario autenticado: mostrar botón de logout --}}
                    <div class="hidden md:flex items-center space-x-4">
                        {{-- Formulario para cerrar sesión --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Usuario no autenticado: mostrar login y registro --}}
                    <div class="hidden md:flex items-center space-x-4">
                        {{-- Botón de iniciar sesión (estilo secundario) --}}
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            Iniciar Sesión
                        </a>
                        {{-- Botón de registro (estilo primario con gradiente) --}}
                        <a href="{{ route('registro.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 rounded-lg transition-all duration-200">
                            Registrarse
                        </a>
                    </div>
                @endauth

                {{-- ======= BOTÓN DE MENÚ MÓVIL ======= --}}
                {{-- Solo visible en pantallas pequeñas --}}
                <button id="mobile-menu-button" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Toggle menu">
                    {{-- Icono de hamburguesa (3 líneas) --}}
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ======= MENÚ MÓVIL DESPLEGABLE ======= --}}
    {{-- Panel que se muestra/oculta en dispositivos móviles --}}
    <div id="mobile-menu" class="hidden md:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-4 bg-white border-t border-gray-200">
            {{-- ======= NAVEGACIÓN MÓVIL POR AUTENTICACIÓN ======= --}}
            @auth
                {{-- ======= ENLACES MÓVILES PARA PROFESORES ======= --}}
                @if(Auth::user()->rol === 'profesor')
                    <a href="{{ route('admin.crear-clase.create') }}" 
                       class="block py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.crear-clase.create') ? 'text-purple-600' : 'text-gray-700' }}">
                        Panel de Profesor
                    </a>
                    <a href="{{ route('admin.crear-profesor.edit') }}" 
                       class="block py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.crear-profesor.edit') ? 'text-purple-600' : 'text-gray-700' }}">
                        Mi Perfil
                    </a>
                {{-- ======= ENLACES MÓVILES PARA ESTUDIANTES ======= --}}
                @else
                    <a href="{{ route('dashboard') }}" 
                       class="block py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-purple-600' : 'text-gray-700' }}">
                        Panel
                    </a>
                    <a href="{{ route('perfil.show') }}" 
                       class="block py-2 text-sm font-medium transition-colors {{ request()->routeIs('perfil.show') ? 'text-purple-600' : 'text-gray-700' }}">
                        Mi Perfil
                    </a>
                @endif
                {{-- Logout en versión móvil --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left py-2 text-sm font-medium text-gray-700 hover:text-purple-600 transition-colors">
                        Cerrar Sesión
                    </button>
                </form>
            @else
                {{-- ======= ENLACES MÓVILES PARA NO AUTENTICADOS ======= --}}
                <a href="{{ route('login') }}" class="block py-2 text-sm font-medium text-gray-700 hover:text-purple-600 transition-colors">
                    Iniciar Sesión
                </a>
                <a href="{{ route('registro.create') }}" class="block py-2 text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors">
                    Registrarse
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- ======= JAVASCRIPT PARA MENÚ MÓVIL ======= --}}
<script>
    // ======= REFERENCIAS A ELEMENTOS DEL DOM =======
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    // Verificar que los elementos existen antes de agregar eventos
    if (mobileMenuButton && mobileMenu) {
        // ======= TOGGLE DEL MENÚ MÓVIL =======
        // Mostrar/ocultar menú al hacer click en el botón hamburguesa
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // ======= CERRAR MENÚ AL HACER CLICK FUERA =======
        // Mejora de UX: cerrar menú automáticamente
        document.addEventListener('click', (e) => {
            // Verificar si el click fue fuera del botón y del menú
            if (!mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    }
</script>