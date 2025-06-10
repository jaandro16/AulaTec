<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <!-- Configuración básica de la página -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon de la aplicación -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Política de seguridad para forzar HTTPS -->
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- Título dinámico de la página -->
    <title>@yield('titulo')</title>

    <!-- Livewire Styles - Necesario para el funcionamiento de Livewire -->
    @livewireStyles

    <!-- Alpine.js - Framework JS ligero para interactividad -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Choices.js - Para mejorar selects -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <!-- Dependencias de estilos y frameworks CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Estilos personalizados para el toggle switch -->
    <style>
        .form-check-input:checked {
            background-color: rgb(147 51 234) !important;
            /* Color morado de Tailwind */
            border-color: rgb(147 51 234) !important;
        }

        .form-switch .form-check-input {
            width: 3.5em;
            height: 2em;
            cursor: pointer;
        }

        .form-switch .form-check-input:focus {
            border-color: rgb(147 51 234);
            box-shadow: 0 0 0 0.2rem rgba(147, 51, 234, 0.25);
        }
    </style>

    <!-- Flatpickr - Para selectores de fecha -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_purple.css">
    @stack('styles')

    <!-- Estilos del sidebar responsive -->
    <style>
        /* Estilos base para el sidebar */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            z-index: 40;
            background-color: white;
            overflow-y: auto;
            transition: transform 0.3s ease;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        /* Overlay para móvil */
        #sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 30;
            display: none;
        }

        /* Comportamiento responsivo */
        @media (max-width: 1023px) {
            #sidebar {
                transform: translateX(-100%);
            }

            body.sidebar-open #sidebar {
                transform: translateX(0);
            }

            body.sidebar-open #sidebar-overlay {
                display: block;
            }

            .main-content {
                width: 100%;
                margin-left: 0;
            }

            body.sidebar-open {
                overflow: hidden;
            }
        }

        @media (min-width: 1024px) {
            #sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px);
                transition: margin-left 0.3s ease, width 0.3s ease;
            }
        }
    </style>
</head>

<body class="h-full bg-gray-100">
    <!-- Contenedor principal de la aplicación -->
    <div class="min-h-screen">
        <!-- Header fijo con navegación -->
        <header class="fixed top-0 w-full bg-white/80 backdrop-blur-sm shadow-sm z-20">
            <div class="flex items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
                <!-- Logo de la aplicación -->
                <div class="flex items-center flex-shrink-0 space-x-2 py-2">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-600 to-cyan-500 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 9h6v6H9z"/>
                                <path d="M4 4v2m0 12v2m16-2v2m0-16v2M4 12H2m20 0h-2M12 4V2m0 20v-2" />
                            </svg>
                        </div>
                        <span class="font-bold text-xl">AulaTec</span>
                    </a>
                </div>

                <!-- Título visible en pantallas medianas y grandes -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        @if (auth()->user()->rol === 'profesor')
                            <a href="{{ route('admin.crear-profesor.edit') }}"
                                class="flex items-center space-x-2 text-gray-700 hover:text-purple-600 transition-colors"
                                title="Editar perfil de profesor">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-circle-user-round-icon lucide-circle-user-round">
                                    <path d="M18 20a6 6 0 0 0-12 0" />
                                    <circle cx="12" cy="10" r="4" />
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                <span class="text-sm font-medium">Perfil de Profesor</span>
                            </a>
                        @else
                            <span class="text-lg font-semibold text-gray-800">AulaTec</span>
                        @endif
                    @else
                        <span class="text-lg font-semibold text-gray-800">AulaTec</span>
                    @endauth
                </div>
                <!-- Botón móvil para abrir el menú -->
                <button type="button" id="mobile-menu-button"
                    class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500"
                    aria-label="Toggle sidebar">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </header>

        <!-- Overlay para el sidebar en dispositivos móviles -->
        <div id="sidebar-overlay"></div>

        <!-- Layout principal con sidebar y contenido -->
        <div class="flex h-full pt-14 sm:pt-16">
            <!-- Sidebar - Incluido desde shared.aside -->
            @include('shared.aside')

            <!-- Área principal de contenido -->
            <main class="main-content flex-1 transition-all duration-300 ease-in-out">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                    @yield('contenido')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts necesarios -->
    @livewireScripts

    <!-- Dependencias JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Script para la funcionalidad del sidebar responsive -->
    <script>
        // Inicialización cuando el DOM está listo
        document.addEventListener('DOMContentLoaded', function() {
            // Referencias a elementos del DOM
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const menuButton = document.getElementById('mobile-menu-button');
            const header = document.querySelector('header');
            let lastScroll = 0;

            // Verificación de seguridad para elementos requeridos
            if (!sidebar || !overlay || !menuButton) {
                console.error('Elementos del sidebar no encontrados');
                return;
            }

            // Función para alternar la visibilidad del sidebar
            function toggleSidebar() {
                document.body.classList.toggle('sidebar-open');
            }

            // Eventos para controlar el sidebar
            menuButton.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);

            // Cerrar sidebar con tecla Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
                    toggleSidebar();
                }
            });

            // Manejo de responsive
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024 && document.body.classList.contains('sidebar-open')) {
                    document.body.classList.remove('sidebar-open');
                }
            });

            // Control de sombra del header al hacer scroll
            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;
                if (currentScroll <= 0) {
                    header.classList.remove('shadow-md');
                    header.classList.add('shadow-sm');
                } else {
                    header.classList.remove('shadow-sm');
                    header.classList.add('shadow-md');
                }
                lastScroll = currentScroll;
            });
        });
    </script>

    <!-- Scripts adicionales específicos de la vista -->
    @stack('scripts')
</body>
</html>
