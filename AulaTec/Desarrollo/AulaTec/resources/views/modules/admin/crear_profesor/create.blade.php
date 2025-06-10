{{-- Extendemos del layout principal de administrador --}}
@extends('layouts.admin')

{{-- Definimos el título de la página --}}
@section('titulo', 'Crear nuevo profesor')

{{-- Contenido principal de la página --}}
@section('contenido')
<div class="space-y-4 sm:space-y-6">
    {{-- Sección de alertas para mostrar mensajes de éxito o error --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Encabezado de la página --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Crear Nuevo Profesor</h1>
        <p class="text-sm sm:text-base text-gray-500">Registra un nuevo profesor en el sistema.</p>
    </div>

    {{-- Tarjeta del formulario --}}
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-4 sm:p-6">
            <h2 class="text-lg font-semibold mb-4">Datos del Profesor</h2>

            {{-- Formulario de registro --}}
            <form action="{{route('admin.crear-profesor.store')}}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Grid de campos del formulario --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Campo Nombre --}}
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}"  placeholder="Nombre del profesor" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        @error('nombre')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Apellido --}}
                    <div>
                        <label for="apellido" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                        <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}" required placeholder="Apellido del profesor"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        @error('apellido')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Email del profesor"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Contraseña --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <input type="password" id="password" name="password" required  placeholder="Contraseña del profesor"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Confirmar Contraseña --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirma la contraseña"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                {{-- Botón de envío --}}
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        Registrar Profesor
                        <i class="fas fa-user-plus ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Scripts específicos de la página --}}
@push('scripts')
<script>
// Script para manejar la validación del formulario y las alertas
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');

    // Limpiar formulario si hay mensaje de éxito
    if (document.querySelector('.bg-green-100')) {
        form.reset();
    }

    // Validación de coincidencia de contraseñas
    form.addEventListener('submit', function(e) {
        if (password.value !== confirmation.value) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            confirmation.focus();
        }
    });

    // Auto-ocultar alertas después de 3 segundos
    const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
});
</script>
@endpush