{{-- Vista principal del home que extiende el layout base --}}
@extends('layouts.main')

@section('contenido')
{{-- Contenedor principal con overflow controlado --}}
<div class="w-full overflow-x-hidden">
    
    {{-- ======= SECCIÓN HERO ======= --}}
    {{-- Sección principal con título y llamada a la acción --}}
    <section class="py-8 sm:py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Layout flex responsivo: vertical en móvil, horizontal en desktop --}}
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-12">
                
                {{-- COLUMNA IZQUIERDA: Contenido textual --}}
                <div class="w-full lg:w-1/2 space-y-6 text-center lg:text-left">
                    {{-- Título principal con gradiente --}}
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-cyan-500 leading-tight">
                        Sistema de Reservas Universitario
                    </h1>
                    {{-- Descripción del sistema --}}
                    <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto lg:mx-0">
                        Reserva tus clases de forma sencilla y rápida. Elige tu asiento preferido y recibe confirmación instantánea.
                    </p>
                    
                    {{-- BOTONES DE ACCIÓN SEGÚN ESTADO DE AUTENTICACIÓN --}}
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        {{-- Si el usuario NO está autenticado --}}
                        @guest
                            {{-- Botón principal: Registrarse --}}
                            <a href="{{ route('registro.create') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 rounded-lg shadow-sm transition-all duration-200 transform hover:scale-105">
                                Registrarse
                            </a>
                            {{-- Botón secundario: Iniciar sesión --}}
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200">
                                Iniciar Sesión
                            </a>
                        {{-- Si el usuario SÍ está autenticado --}}
                        @else
                            {{-- Botón diferenciado según rol del usuario --}}
                            @if(Auth::user()->rol === 'profesor')
                                {{-- Para profesores: ir al panel administrativo --}}
                                <a href="{{ route('admin.crear-clase.create') }}" 
                                   class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 rounded-lg shadow-sm transition-all duration-200">
                                    Panel de Profesor
                                </a>
                            @else
                                {{-- Para alumnos: ir al dashboard de estudiante --}}
                                <a href="{{ route('dashboard') }}" 
                                   class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 rounded-lg shadow-sm transition-all duration-200">
                                    Mi Panel
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>
                
                {{-- COLUMNA DERECHA: Imagen representativa --}}
                <div class="w-full lg:w-1/2 max-w-lg lg:max-w-none">
                    <div class="relative mx-auto lg:mx-0">
                        {{-- Efecto de glow detrás de la imagen --}}
                        <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-cyan-500 rounded-lg blur opacity-25"></div>
                        {{-- Contenedor de la imagen con shadow --}}
                        <div class="relative bg-white p-3 rounded-lg shadow-xl">
                            <img src="{{ asset('images/UniReservas.png') }}" 
                                 alt="Imagen representativa del campus universitario"
                                 class="w-full h-auto rounded-lg object-cover"
                                 loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======= SECCIÓN "CÓMO FUNCIONA" ======= --}}
    {{-- Explicación del proceso en 3 pasos --}}
    <section class="py-12 lg:py-16 ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Título de la sección --}}
            <h2 class="text-2xl sm:text-3xl font-bold mb-8 lg:mb-12 text-center text-gray-900">¿Cómo funciona?</h2>
            {{-- Grid responsivo para las cards de pasos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                
                {{-- PASO 1: Registro --}}
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 transition-all duration-200 hover:shadow-xl hover:-translate-y-1">
                    {{-- Icono del paso --}}
                    <div class="flex justify-center mb-4">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    {{-- Título y descripción del paso --}}
                    <h3 class="text-xl font-semibold mb-2 text-center">Regístrate</h3>
                    <p class="text-gray-600 text-center">
                        Crea tu cuenta con tus datos académicos y personales para acceder al sistema.
                    </p>
                </div>

                {{-- PASO 2: Selección de fecha y hora --}}
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 transition-all duration-200 hover:shadow-xl hover:-translate-y-1">
                    <div class="flex justify-center mb-4">
                        <div class="p-3 bg-cyan-100 rounded-full">
                            {{-- Icono de calendario --}}
                            <svg class="h-8 w-8 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-center">Selecciona fecha y hora</h3>
                    <p class="text-gray-600 text-center">
                        Elige en el calendario interactivo la fecha y hora de la clase que deseas reservar.
                    </p>
                </div>

                {{-- PASO 3: Selección de asiento --}}
                {{-- Ocupa columnas completas en responsive para centrar en layouts impares --}}
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 transition-all duration-200 hover:shadow-xl hover:-translate-y-1 md:col-span-2 lg:col-span-1">
                    <div class="flex justify-center mb-4">
                        <div class="p-3 bg-purple-100 rounded-full">
                            {{-- Icono de ubicación --}}
                            <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-center">Elige tu asiento</h3>
                    <p class="text-gray-600 text-center">
                        Selecciona tu asiento preferido en el mapa interactivo del aula y confirma tu reserva.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ======= SECCIÓN CTA (CALL TO ACTION) ======= --}}
    {{-- Llamada final a la acción con fondo degradado --}}
    <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Contenedor con gradiente de fondo --}}
            <div class="bg-gradient-to-r from-purple-600 to-cyan-500 rounded-xl p-8 text-white text-center">
                <h2 class="text-2xl sm:text-3xl font-bold mb-4">¿Listo para comenzar?</h2>
                
                {{-- BOTONES FINALES SEGÚN ESTADO DE AUTENTICACIÓN --}}
                @guest
                    {{-- Para usuarios no registrados: promover registro --}}
                    <p class="text-lg sm:text-xl mb-6">Regístrate ahora y comienza a reservar tus clases de manera eficiente.</p>
                    <a href="{{ route('registro.create') }}" 
                       class="inline-flex items-center px-6 py-3 text-base font-medium text-purple-600 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200">
                        Crear cuenta
                    </a>
                @else
                    {{-- Para usuarios autenticados: dirigir al panel correspondiente --}}
                    @if(Auth::user()->rol === 'profesor')
                        <p class="text-lg sm:text-xl mb-6">Accede a tu panel de profesor para gestionar tus clases.</p>
                        <a href="{{ route('admin.crear-clase.create') }}" 
                           class="inline-flex items-center px-6 py-3 text-base font-medium text-purple-600 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200">
                            Panel de Profesor
                        </a>
                    @else
                        <p class="text-lg sm:text-xl mb-6">Accede a tu panel para gestionar tus reservas.</p>
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-6 py-3 text-base font-medium text-purple-600 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200">
                            Mi Panel
                        </a>
                    @endif
                @endguest
            </div>
        </div>
    </section>
</div>
@endsection