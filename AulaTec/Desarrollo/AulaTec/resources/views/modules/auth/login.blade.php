{{-- Vista del formulario de inicio de sesión que extiende el layout principal --}}
@extends('layouts.main')

@section('contenido')
{{-- Contenedor principal centrado con ancho máximo para móviles --}}
<div class="container max-w-md mx-auto px-4 py-12">
    {{-- Card principal del formulario con sombra y bordes redondeados --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg">
        
        {{-- ======= HEADER DEL FORMULARIO ======= --}}
        {{-- Título y descripción del formulario --}}
        <div class="p-6 space-y-1">
            <h2 class="text-2xl font-bold text-center">Iniciar sesión</h2>
            <p class="text-center text-gray-500">Ingresa tus credenciales para acceder al sistema</p>
        </div>

        {{-- ======= FORMULARIO DE LOGIN ======= --}}
        <div class="p-6 pt-0">
            {{-- Formulario que envía datos por POST a la ruta de autenticación --}}
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4" id="loginForm">
                @csrf {{-- Token CSRF para seguridad --}}
                
                {{-- CAMPO EMAIL --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                    <input type="email" 
                           id="email" 
                           name="email"
                           {{-- Clases dinámicas: borde rojo si hay error de validación --}}
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="tu.correo@universidad.edu"
                           value=""
                           required>
                    {{-- Mostrar mensaje de error específico del campo email --}}
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CAMPO CONTRASEÑA --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium">Contraseña</label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           placeholder="*********"
                           {{-- Clases dinámicas: borde rojo si hay error de validación --}}
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('password') border-red-500 @enderror"
                           required>
                    {{-- Mostrar mensaje de error específico del campo contraseña --}}
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ======= OPCIONES ADICIONALES ======= --}}
                {{-- Checkbox "Recordarme" y enlace de recuperación --}}
                <div class="flex items-center justify-between">
                    {{-- Checkbox para mantener sesión activa --}}
                    {{-- <div class="flex items-center space-x-2">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember"
                               class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <label for="remember" class="text-sm">Recordarme</label>
                    </div> --}}
                    {{-- Enlace para recuperar contraseña (placeholder) --}}
                    {{-- <a href="#" 
                       class="text-sm text-purple-600 hover:underline">
                        ¿Olvidaste tu contraseña?
                    </a> --}}
                </div>

                {{-- BOTÓN DE ENVÍO --}}
                {{-- Botón con gradiente y estado de carga manejado por JavaScript --}}
                <button type="submit" 
                        id="submitBtn"
                        class="w-full py-2 px-4 bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2">
                    Iniciar sesión
                </button>
            </form>
        </div>

        {{-- ======= FOOTER CON ENLACE DE REGISTRO ======= --}}
        {{-- Sección separada para usuarios que no tienen cuenta --}}
        <div class="p-6 border-t border-gray-200 ">
            <p class="text-center text-sm text-gray-500">
                ¿No tienes cuenta? 
                {{-- Enlace que redirige al formulario de registro --}}
                <a href="{{ route('registro.create') }}" class="text-purple-600 hover:underline">
                    Regístrate
                </a>
            </p>
        </div>
    </div>
</div>

{{-- ======= JAVASCRIPT PARA UX MEJORADA ======= --}}
<script>
    // Obtener referencias a los elementos del formulario
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Agregar listener para el evento de envío del formulario
    form.addEventListener('submit', function(e) {
        // Cambiar el texto del botón para mostrar estado de carga
        submitBtn.textContent = 'Autenticando...';
        // Deshabilitar el botón para evitar múltiples envíos
        submitBtn.disabled = true;
    });
</script>
@endsection