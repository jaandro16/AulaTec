@extends('layouts.admin')

@section('titulo', $titulo)

@section('contenido')
    {{-- ======= CONTENEDOR PRINCIPAL ======= --}}
    {{-- Contenedor máximo ancho con espaciado vertical uniforme --}}
    <div class="space-y-6 max-w-7xl mx-auto">
        {{-- ======= MENSAJES DE ESTADO ======= --}}
        {{-- Notificaciones de éxito o error con animación --}}
        @if (session('message'))
            {{-- Mensaje de éxito con borde verde y fade in --}}
            <div class="max-w-2xl mx-auto px-4 py-3 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-md shadow-sm animate-fadeIn" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <p class="font-medium">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            {{-- Listado de errores con borde rojo y fade in --}}
            <div class="max-w-2xl mx-auto px-4 py-3 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md shadow-sm animate-fadeIn">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mt-0.5 mr-3 text-red-500"></i>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ======= HEADER DE LA PÁGINA ======= --}}
        {{-- Encabezado con título y descripción --}}
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                    {{ $titulo }}
                </h1>
                <p class="mt-1 text-sm sm:text-base text-gray-500">
                    Configura los detalles para crear una nueva clase
                </p>
            </div>
        </div>

        {{-- ======= WIZARD DE CREACIÓN ======= --}}
        {{-- Card principal que contiene el componente Livewire del wizard --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <livewire:create-class-wizard />
        </div>
    </div>
@endsection

{{-- ======= ESTILOS ADICIONALES ======= --}}
{{-- Inclusión de dependencias CSS y estilos personalizados --}}
@push('styles')
    {{-- Librerías externas --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_purple.css">
    
    {{-- Estilos personalizados para animaciones y responsive --}}
    <style>
        /* Animaciones personalizadas */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out forwards;
        }
        
        /* Mejoras responsivas */
        @media (max-width: 640px) {
            .responsive-padding {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
@endpush

{{-- ======= SCRIPTS NECESARIOS ======= --}}
{{-- Scripts para el selector de fecha --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
@endpush
