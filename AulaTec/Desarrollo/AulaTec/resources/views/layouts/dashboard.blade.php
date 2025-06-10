<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Reservas') - Sistema de Reservas</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Reset CSS para normalizar navegadores -->
    <style>
        /* Reset básico */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Open Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
        }
        
        /* Normalizar elementos */
        img, picture, video, canvas, svg {
            display: block;
            max-width: 100%;
        }
        
        input, button, textarea, select {
            font: inherit;
        }
        
        /* Prevenir overflow */
        #root, #__next {
            isolation: isolate;
        }
        
        /* Chrome específico */
        @media screen and (-webkit-min-device-pixel-ratio:0) {
            body {
                -webkit-font-smoothing: antialiased;
            }
        }
    </style>

    <!-- Tailwind CSS con versión específica y configuración -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Open Sans', 'Helvetica Neue', 'sans-serif'],
                    },
                    container: {
                        center: true,
                        padding: {
                            DEFAULT: '1rem',
                            sm: '2rem',
                            lg: '4rem',
                            xl: '5rem',
                            '2xl': '6rem',
                        },
                        screens: {
                            sm: '640px',
                            md: '768px',
                            lg: '1024px',
                            xl: '1280px',
                            '2xl': '1400px',
                        }
                    },
                    colors: {
                        purple: {
                            50: "#f5f3ff",
                            100: "#ede9fe",
                            200: "#ddd6fe",
                            300: "#c4b5fd",
                            400: "#a78bfa",
                            500: "#8b5cf6",
                            600: "#7c3aed",
                            700: "#6d28d9",
                            800: "#5b21b6",
                            900: "#4c1d95",
                            950: "#2e1065",
                        },
                        cyan: {
                            50: "#ecfeff",
                            100: "#cffafe",
                            200: "#a5f3fc",
                            300: "#67e8f9",
                            400: "#22d3ee",
                            500: "#06b6d4",
                            600: "#0891b2",
                            700: "#0e7490",
                            800: "#155e75",
                            900: "#164e63",
                            950: "#083344",
                        },
                    },
                }
            },
            corePlugins: {
                preflight: true, // Importante: activa el reset de Tailwind
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="flex flex-col min-h-screen">
    <!-- ======= Header ======= -->
    @include('shared.header')
    <!-- End Header -->
    
    <x-notification />
    
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-gray-900">Panel de Reservas</h1>

            <div x-data="{ activeTab: '{{ $activeTab ?? 'reservar' }}' }">
                <div class="grid w-full max-w-md grid-cols-3 mb-6 bg-gray-100 rounded-lg border border-gray-200 p-1">
                    <button @click="activeTab = 'reservar'" :class="activeTab === 'reservar' ? 'bg-white text-foreground shadow-sm' : 'bg-muted text-muted-foreground hover:text-foreground'" class="flex items-center justify-center py-2 px-3 rounded-tl-md rounded-tr-md border-b-2 transition-colors" :style="activeTab === 'reservar' ? 'border-color: hsl(262.1 83.3% 57.8%)' : 'border-color: transparent'">
                        <span class="sm:inline font-medium text-sm">Reservar Clase</span>
                    </button>
                    <button @click="activeTab = 'mis-reservas'" :class="activeTab === 'mis-reservas' ? 'bg-white text-foreground shadow-sm' : 'bg-muted text-muted-foreground hover:text-foreground'" class="flex items-center justify-center py-2 px-3 border-b-2 transition-colors" :style="activeTab === 'mis-reservas' ? 'border-color: hsl(262.1 83.3% 57.8%)' : 'border-color: transparent'">
                        <span class="sm:inline font-medium text-sm">Mis Reservas</span>
                    </button>
                    <button @click="activeTab = 'intercambios'" :class="activeTab === 'intercambios' ? 'bg-white text-foreground shadow-sm' : 'bg-muted text-muted-foreground hover:text-foreground'" class="flex items-center justify-center py-2 px-3 rounded-tr-md rounded-tl-md border-b-2 transition-colors" :style="activeTab === 'intercambios' ? 'border-color: hsl(262.1 83.3% 57.8%)' : 'border-color: transparent'">
                        <span class="sm:inline font-medium text-sm">Intercambios</span>
                    </button>
                </div>

                <!-- Tab Reservar -->
                <div x-show="activeTab === 'reservar'" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-xl font-bold text-gray-900">Selecciona una fecha</h2>
                                <p class="text-sm text-gray-500">Elige el día en que deseas asistir a clase</p>
                            </div>
                            <div class="p-6">
                                @include('modules.panel.calendar')
                            </div>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-xl font-bold text-gray-900">Horarios disponibles</h2>
                                <p class="text-sm text-gray-500">Selecciona una fecha para ver los horarios disponibles</p>
                            </div>
                            <div class="p-6">
                                <!-- Mensaje cuando no hay fecha seleccionada -->
                                <div x-show="!$store.calendario.fechaSeleccionada" 
                                     class="flex justify-center items-center h-64 text-gray-400">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm">Selecciona una fecha en el calendario</p>
                                    </div>
                                </div>
                        
                                <!-- Horarios cuando hay fecha seleccionada -->
                                <div x-show="$store.calendario.fechaSeleccionada" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100">
                                    @include('modules.panel.horario-selector')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="$store.calendario.fechaSeleccionada && $store.calendario.horarioSeleccionado" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-900">Clases disponibles</h2>
                            <p class="text-sm text-gray-500">Selecciona una clase para reservar</p>
                        </div>
                        <div class="p-6">
                            @include('modules.panel.clases-list')
                        </div>
                    </div>
                </div>

                <!-- Tab Mis Reservas -->
                <div x-show="activeTab === 'mis-reservas'">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-900">Mis reservas activas</h2>
                            <p class="text-sm text-gray-500">Gestiona tus reservas actuales</p>
                        </div>
                        <div class="p-6">
                            @include('modules.panel.reservas-activas')
                        </div>
                    </div>
                </div>

                <!-- Tab Intercambios -->
                <div x-show="activeTab === 'intercambios'">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-900">Intercambio de Reservas</h2>
                            <p class="text-sm text-gray-500">Intercambia tus reservas con otros estudiantes</p>
                        </div>
                        <div class="p-6">
                            @include('modules.intercambios.index')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ======= Footer ======= -->
    @include('shared.footer')
    <!-- End Footer -->

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('calendario', {
                fechaSeleccionada: null,
                horarioSeleccionado: null,
                setFecha(fecha) {
                    this.fechaSeleccionada = fecha;
                },
                setHorario(horario) {
                    this.horarioSeleccionado = horario;
                }
            });
        });
    </script>
</body>
</html>