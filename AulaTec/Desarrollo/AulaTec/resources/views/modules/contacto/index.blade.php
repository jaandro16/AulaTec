{{-- Vista de la página de contacto que extiende el layout principal --}}
@extends('layouts.main')

@section('contenido')
{{-- Contenedor principal con ancho máximo y padding responsivo --}}
<div class="w-full max-w-7xl mx-auto px-4 py-12">
    <div class="max-w-6xl mx-auto">
        
        {{-- ======= SECCIÓN HEADER ======= --}}
        {{-- Título principal y descripción de la página --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-cyan-500">
                Contacto
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                ¿Tienes alguna pregunta o sugerencia? Estamos aquí para ayudarte.
            </p>
        </div>

        {{-- ======= GRID PRINCIPAL DE CONTENIDO ======= --}}
        {{-- Layout responsivo: 1 columna en móvil, 3 columnas en desktop --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            
            {{-- SIDEBAR IZQUIERDO: Información de contacto --}}
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    
                    {{-- Card de información de contacto --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Información de Contacto</h3>
                            <div class="space-y-6">
                                
                                {{-- Email --}}
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 p-3 bg-purple-100 rounded-full mr-4">
                                        {{-- Icono de email --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-1">Email</h4>
                                        <p class="text-sm text-gray-600 break-words">info@aulatec.edu</p>
                                    </div>
                                </div>

                                {{-- Teléfono --}}
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 p-3 bg-cyan-100 rounded-full mr-4">
                                        {{-- Icono de teléfono --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-1">Teléfono</h4>
                                        <p class="text-sm text-gray-600">+34 912 345 678</p>
                                    </div>
                                </div>

                                {{-- Dirección --}}
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 p-3 bg-purple-100 rounded-full mr-4">
                                        {{-- Icono de ubicación --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-1">Dirección</h4>
                                        <p class="text-sm text-gray-600">C. de José Gutiérrez Abascal, 2, Chamartín, 28006 Madrid</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card de horarios de atención --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Horario de Atención</h3>
                            <div class="space-y-3">
                                {{-- Lunes a Viernes --}}
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Lunes - Viernes:</span>
                                    <span class="text-sm font-medium text-gray-900">9:00 - 18:00</span>
                                </div>
                                {{-- Sábado --}}
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Sábado:</span>
                                    <span class="text-sm font-medium text-gray-900">10:00 - 14:00</span>
                                </div>
                                {{-- Domingo (cerrado) --}}
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Domingo:</span>
                                    <span class="text-sm font-medium text-red-500">Cerrado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ======= FORMULARIO DE CONTACTO ======= --}}
            {{-- Ocupa 2 columnas del grid en desktop --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        {{-- Título del formulario --}}
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Envíanos un mensaje</h2>
                            <p class="text-gray-600">Completa el formulario y te responderemos lo antes posible</p>
                        </div>

                        {{-- ======= MENSAJES DE ESTADO ======= --}}
                        {{-- Mensaje de éxito --}}
                        @if(session('success'))
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg" role="alert">
                                <div class="flex items-center">
                                    {{-- Icono de check --}}
                                    <svg class="h-5 w-5 text-green-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        {{-- Mensaje de error --}}
                        @if(session('error'))
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert">
                                <div class="flex items-center">
                                    {{-- Icono de error --}}
                                    <svg class="h-5 w-5 text-red-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                                </div>
                            </div>
                        @endif

                        {{-- ======= FORMULARIO ======= --}}
                        <form action="{{ route('contacto.enviar') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            {{-- Grid para nombre y email en la misma fila --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Campo Nombre --}}
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-medium text-gray-900">
                                        Nombre completo <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        id="name" 
                                        name="name" 
                                        type="text" 
                                        placeholder="Tu nombre completo" 
                                        required 
                                        value="{{ old('name') }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                    >
                                    {{-- Mensaje de error específico del campo --}}
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-600 flex items-center">
                                            <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                
                                {{-- Campo Email --}}
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-medium text-gray-900">
                                        Correo electrónico <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        id="email" 
                                        name="email" 
                                        type="email" 
                                        placeholder="tu.correo@ejemplo.com" 
                                        required 
                                        value="{{ old('email') }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                    >
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-600 flex items-center">
                                            <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Campo Asunto --}}
                            <div class="space-y-2">
                                <label for="subject" class="block text-sm font-medium text-gray-900">
                                    Asunto <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    id="subject" 
                                    name="subject" 
                                    type="text" 
                                    placeholder="Asunto de tu mensaje" 
                                    required 
                                    value="{{ old('subject') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('subject') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                >
                                @error('subject')
                                    <p class="mt-1 text-xs text-red-600 flex items-center">
                                        <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Campo Mensaje --}}
                            <div class="space-y-2">
                                <label for="message" class="block text-sm font-medium text-gray-900">
                                    Mensaje <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="message" 
                                    name="message" 
                                    placeholder="Escribe tu mensaje aquí..." 
                                    rows="6" 
                                    required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors resize-none @error('message') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-xs text-red-600 flex items-center">
                                        <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Botón de envío --}}
                            <div class="pt-4">
                                <button 
                                    type="submit" 
                                    class="w-full inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white rounded-lg text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200"
                                >
                                    {{-- Icono de envío --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                    </svg>
                                    Enviar mensaje
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======= SECCIÓN MAPA ======= --}}
        {{-- Mapa embebido de Google Maps --}}
        <div class="mb-12">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                {{-- Contenedor con aspect ratio fijo --}}
                <div class="aspect-[21/9] w-full">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d194477.19235766798!2d-3.9171535716960784!3d40.393205264375425!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd4228ee58976437%3A0xe0887483b829f649!2sEscuela%20T%C3%A9cnica%20Superior%20de%20Ingenieros%20Industriales%20UPM!5e0!3m2!1ses!2ses!4v1745833214510!5m2!1ses!2ses"
                        width="100%"
                        height="100%"
                        style="border: 0"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="w-full h-full"
                        title="Mapa de ubicación - Escuela Técnica Superior de Ingenieros Industriales UPM"
                    ></iframe>
                </div>
            </div>
        </div>

        {{-- ======= SECCIÓN FAQ ======= --}}
        {{-- Preguntas frecuentes --}}
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Preguntas Frecuentes</h2>
            <p class="text-lg text-gray-600 mb-8 max-w-3xl mx-auto">
                Encuentra respuestas rápidas a las preguntas más comunes sobre nuestro sistema de reservas
            </p>
            
            {{-- Grid de preguntas frecuentes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- FAQ 1: Cómo reservar --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start text-left">
                            <div class="flex-shrink-0 p-2 bg-purple-100 rounded-lg mr-4">
                                {{-- Icono de pregunta --}}
                                <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Cómo puedo reservar una clase?</h3>
                                <p class="text-gray-600">
                                    Para reservar una clase, debes iniciar sesión en tu cuenta, seleccionar la fecha y hora deseada, y
                                    elegir un asiento disponible en el aula.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FAQ 2: Cancelación --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start text-left">
                            <div class="flex-shrink-0 p-2 bg-cyan-100 rounded-lg mr-4">
                                {{-- Icono de reloj --}}
                                <svg class="h-5 w-5 text-cyan-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Puedo cancelar una reserva?</h3>
                                <p class="text-gray-600">
                                    Sí, puedes cancelar una reserva hasta 24 horas antes de la clase. Para hacerlo, ve a "Mis Reservas" en
                                    tu panel y selecciona la opción de cancelar.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FAQ 3: Intercambio de asientos --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start text-left">
                            <div class="flex-shrink-0 p-2 bg-purple-100 rounded-lg mr-4">
                                {{-- Icono de intercambio --}}
                                <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Cómo funciona el intercambio de asientos?</h3>
                                <p class="text-gray-600">
                                    El sistema permite intercambiar tu reserva con otros estudiantes. Publica tu reserva para intercambio
                                    y espera a que alguien la solicite, o busca reservas disponibles para intercambiar.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FAQ 4: Soporte técnico --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start text-left">
                            <div class="flex-shrink-0 p-2 bg-cyan-100 rounded-lg mr-4">
                                {{-- Icono de soporte --}}
                                <svg class="h-5 w-5 text-cyan-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Qué hago si tengo problemas técnicos?</h3>
                                <p class="text-gray-600">
                                    Si experimentas problemas técnicos, puedes contactarnos a través de este formulario o llamarnos
                                    directamente al número de soporte técnico.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection