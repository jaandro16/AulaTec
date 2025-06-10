{{-- Footer compartido de la aplicación --}}
{{-- Contiene navegación, información de contacto y enlaces de soporte --}}

{{-- Footer principal con borde superior y fondo blanco --}}
<footer class="border-t border-gray-200 bg-white mt-auto">
    {{-- Contenedor principal con padding responsivo --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Grid responsivo: 1 columna en móvil, 4 columnas en desktop --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            
            {{-- ======= COLUMNA 1: BRANDING Y DESCRIPCIÓN ======= --}}
            {{-- Logo, nombre y descripción de la aplicación --}}
            <div class="md:col-span-1">
                {{-- Logo y nombre de la marca --}}
                <div class="flex items-center space-x-2 mb-4">
                    {{-- Logo circular con gradiente --}}
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-600 to-cyan-500 flex items-center justify-center">
                        {{-- Icono SVG del logo --}}
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 9h6v6H9z"/>
                            <path d="M4 4v2m0 12v2m16-2v2m0-16v2M4 12H2m20 0h-2M12 4V2m0 20v-2" />
                        </svg>
                    </div>
                    {{-- Nombre de la aplicación --}}
                    <span class="font-bold text-xl">AulaTec</span>
                </div>
                {{-- Descripción breve del sistema --}}
                <p class="text-sm text-gray-500">
                    Sistema de reservas para clases universitarias. Simplificando la asistencia a clases desde 2025.
                </p>
            </div>

            {{-- ======= COLUMNA 2: ENLACES RÁPIDOS ======= --}}
            {{-- Navegación principal del sitio --}}
            <div>
                <h3 class="font-semibold mb-4 text-gray-900">Enlaces rápidos</h3>
                <ul class="space-y-2 text-sm">
                    {{-- Enlace a página de inicio --}}
                    <li>
                        <a href="{{ route('home') }}" 
                           class="text-gray-500 hover:text-purple-600 transition-colors">
                            Inicio
                        </a>
                    </li>
                    
                    {{-- ======= ENLACES CONDICIONALES SEGÚN AUTENTICACIÓN ======= --}}
                    {{-- Solo mostrar si el usuario está logueado --}}
                    @auth
                        {{-- Enlace específico para profesores --}}
                        @if(Auth::user()->rol === 'profesor')
                            <li>
                                <a href="{{ route('admin.crear-clase.create') }}" 
                                   class="text-gray-500 hover:text-purple-600 transition-colors">
                                    Panel de Profesor
                                </a>
                            </li>
                        {{-- Enlace para estudiantes y otros roles --}}
                        @else
                            <li>
                                <a href="{{ route('dashboard') }}" 
                                   class="text-gray-500 hover:text-purple-600 transition-colors">
                                    Panel
                                </a>
                            </li>
                        @endif
                    @endauth
                    
                    {{-- Enlace a página de contacto (siempre visible) --}}
                    <li>
                        <a href="{{ route('contacto.index') }}" 
                           class="text-gray-500 hover:text-purple-600 transition-colors">
                            Contacto
                        </a>
                    </li>
                </ul>
            </div>

            {{-- ======= COLUMNA 3: SOPORTE Y AYUDA ======= --}}
            {{-- Enlaces a documentación y políticas --}}
            <div>
                <h3 class="font-semibold mb-4 text-gray-900">Soporte</h3>
                <ul class="space-y-2 text-sm">
                    {{-- Enlace a preguntas frecuentes --}}
                    <li>
                        <a href="#" 
                           class="text-gray-500 hover:text-purple-600 transition-colors">
                            Preguntas Frecuentes
                        </a>
                    </li>
                    {{-- Enlace a centro de ayuda --}}
                    <li>
                        <a href="#" 
                           class="text-gray-500 hover:text-purple-600 transition-colors">
                            Centro de Ayuda
                        </a>
                    </li>
                    {{-- Enlace a términos de servicio --}}
                    <li>
                        <a href="#" 
                           class="text-gray-500 hover:text-purple-600 transition-colors">
                            Términos de Servicio
                        </a>
                    </li>
                    {{-- Enlace a política de privacidad --}}
                    <li>
                        <a href="#" 
                           class="text-gray-500 hover:text-purple-600 transition-colors">
                            Política de Privacidad
                        </a>
                    </li>
                </ul>
            </div>

            {{-- ======= COLUMNA 4: INFORMACIÓN DE CONTACTO ======= --}}
            {{-- Datos de contacto con iconos descriptivos --}}
            <div>
                <h3 class="font-semibold mb-4 text-gray-900">Contacto</h3>
                <ul class="space-y-2 text-sm">
                    {{-- Correo electrónico con icono de sobre --}}
                    <li class="text-gray-500 flex items-start">
                        <svg class="w-4 h-4 mt-0.5 mr-2 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>info@aulatec.edu</span>
                    </li>
                    {{-- Teléfono con icono de teléfono --}}
                    <li class="text-gray-500 flex items-start">
                        <svg class="w-4 h-4 mt-0.5 mr-2 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>+34 912 345 678</span>
                    </li>
                    {{-- Dirección física con icono de ubicación --}}
                    <li class="text-gray-500 flex items-start">
                        <svg class="w-4 h-4 mt-0.5 mr-2 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Campus Universitario, 28001 Madrid</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ======= COPYRIGHT Y DERECHOS ======= --}}
        {{-- Separador visual y texto de derechos de autor --}}
        <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-500">
            {{-- Copyright dinámico con año actual --}}
            <p>© {{ date('Y') }} AulaTec. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>