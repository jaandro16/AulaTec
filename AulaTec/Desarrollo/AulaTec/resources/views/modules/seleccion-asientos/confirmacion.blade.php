{{-- Vista de confirmación de reserva exitosa --}}
{{-- Muestra detalles de la reserva, código QR y opciones de descarga/navegación --}}

{{-- Extiende el layout específico para selección de asientos --}}
@extends('layouts.asiento')

{{-- Título de la página para SEO y navegador --}}
@section('title', 'Confirmación de Reserva - Sistema de Reservas')

{{-- Contenido principal de la página --}}
@section('contenido')
{{-- Contenedor principal con Alpine.js para manejo de estado --}}
<div x-data="{ 
    // ======= ESTADO REACTIVO =======
    loading: true,              // Control de animación de carga inicial
    reserva: {                  // Datos de la reserva formateados desde el backend
        id: '{{ $reservation->id }}',
        clase: '{{ $reservation->classSession->subject->name }}',
        profesor: '{{ $reservation->classSession->teacher->nombre }} {{ $reservation->classSession->teacher->apellido }}',
        aula: '{{ $reservation->classSession->classroom->name }}',
        fecha: '{{ \Carbon\Carbon::parse($reservation->classSession->date)->format('d \d\e F \d\e Y') }}',
        hora: '{{ \Carbon\Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i') }}',
        hora_fin: '{{ \Carbon\Carbon::parse($reservation->classSession->timeSlot->end_time)->format('H:i') }}',
        duracion: '{{ \Carbon\Carbon::parse($reservation->classSession->timeSlot->start_time)->diffInMinutes($reservation->classSession->timeSlot->end_time) }}',
        asiento: '{{ $reservation->asiento }}',
        estado: '{{ $reservation->estado }}'
    }
}" 
{{-- Simular carga durante 2 segundos para mejor UX --}}
x-init="setTimeout(() => loading = false, 2000)"
class="container max-w-2xl mx-auto px-4 py-12">
    
    {{-- ======= ESTADO DE CARGA ======= --}}
    {{-- Spinner y mensaje mientras se "genera" la confirmación --}}
    <div x-show="loading" class="flex flex-col items-center justify-center space-y-4">
        {{-- Spinner circular animado --}}
        <div class="w-16 h-16 border-4 border-gray-300 border-t-purple-600 rounded-full animate-spin"></div>
        <p class="text-gray-600 font-medium">Generando confirmación...</p>
    </div>

    {{-- ======= CONTENIDO PRINCIPAL ======= --}}
    {{-- Tarjeta de confirmación con transición de entrada --}}
    <div x-show="!loading" 
         {{-- Animación suave de aparición --}}
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            
            {{-- ======= CABECERA DE CONFIRMACIÓN ======= --}}
            {{-- Header con gradiente y mensaje de éxito --}}
            <div class="text-center border-b pb-6 pt-6 px-6 bg-gradient-to-r from-purple-600 to-cyan-500">
                {{-- Icono de éxito en círculo blanco --}}
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                {{-- Título de confirmación --}}
                <h1 class="text-2xl font-bold text-white">¡Reserva Confirmada!</h1>
                {{-- Subtítulo explicativo --}}
                <p class="text-white opacity-90">Tu reserva ha sido procesada correctamente</p>
            </div>
            
            {{-- ======= CONTENIDO PRINCIPAL ======= --}}
            <div class="p-6 space-y-6">
                
                {{-- ======= DETALLES DE LA RESERVA ======= --}}
                {{-- Tabla con información completa de la reserva --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="font-semibold text-lg mb-3 border-b pb-2 border-gray-200">Detalles de la Reserva</h2>
                    <div class="space-y-2">
                        {{-- Nombre de la asignatura --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500">Clase:</span>
                            <span class="font-medium" x-text="reserva.clase"></span>
                        </div>
                        {{-- Profesor que imparte --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500">Profesor:</span>
                            <span class="font-medium" x-text="reserva.profesor"></span>
                        </div>
                        {{-- Fecha de la clase --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500">Fecha:</span>
                            <span class="font-medium" x-text="reserva.fecha"></span>
                        </div>
                        {{-- Hora de inicio --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500">Hora:</span>
                            <span class="font-medium" x-text="reserva.hora"></span>
                        </div>
                        {{-- Duración de la clase --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500">Duración:</span>
                            <span class="font-medium" x-text="reserva.duracion + ' minutos'"></span>
                        </div>
                        {{-- Aula asignada --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500">Aula:</span>
                            <span class="font-medium" x-text="reserva.aula"></span>
                        </div>
                        {{-- Número de asiento (destacado en púrpura) --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500">Asiento:</span>
                            <span class="font-medium text-purple-600" x-text="reserva.asiento"></span>
                        </div>
                    </div>
                </div>

                {{-- ======= CÓDIGO QR DE ASISTENCIA ======= --}}
                {{-- Sección centrada con el código QR para verificar asistencia --}}
                <div class="text-center">
                    <h2 class="font-semibold text-lg mb-2">Código QR de Asistencia</h2>
                    {{-- Información sobre el envío por email --}}
                    <p class="text-sm text-gray-500 mb-4">
                        Se ha enviado un correo electrónico con el código QR a tu dirección registrada.
                    </p>
                    {{-- Contenedor del código QR con sombra --}}
                    <div class="bg-white p-4 rounded-lg inline-block shadow-md">
                        <div class="w-40 h-40 mx-auto flex items-center justify-center">
                            <div class="w-full h-full">
                                {{-- Imagen del código QR generada en el backend --}}
                                {!! '<img src="' . $qrCode . '" alt="QR Code" class="w-full h-full">' !!}
                            </div>
                        </div>
                    </div>
                    {{-- Instrucciones de uso --}}
                    <p class="text-sm mt-2">Muestra este código al ingresar a la clase</p>
                </div>
                
                {{-- ======= INFORMACIÓN ADICIONAL ======= --}}
                {{-- Consejos e instrucciones importantes para el estudiante --}}
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-800">
                    <p class="font-medium mb-1">Información importante:</p>
                    <ul class="list-disc list-inside space-y-1 pl-2">
                        <li>Llega 10 minutos antes del inicio de la clase</li>
                        <li>Recuerda traer tu identificación estudiantil</li>
                        <li>No olvides cancelar tu reserva si no puedes asistir</li>
                    </ul>
                </div>
            </div>
            
            {{-- ======= FOOTER CON ACCIONES ======= --}}
            {{-- Botones de navegación y descarga --}}
            <div class="border-t border-gray-200 p-6 flex flex-col space-y-3">
                {{-- Botón para volver al dashboard principal --}}
                <a href="{{ route('dashboard') }}" class="w-full">
                    <button class="w-full py-2 px-4 bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white font-medium rounded-md shadow-sm">
                        Volver al Panel
                    </button>
                </a>
                {{-- Componente para descargar PDF de la reserva --}}
                <x-download-pdf :reservation="$reservation" />
            </div>
        </div>
    </div>
</div>
@endsection