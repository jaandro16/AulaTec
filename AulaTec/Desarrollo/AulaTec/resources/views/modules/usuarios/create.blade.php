{{-- Vista de registro de nuevos usuarios --}}
{{-- Formulario completo con validación frontend y backend para crear cuentas de estudiantes --}}

{{-- Extiende el layout principal de la aplicación --}}
@extends('layouts.main')

{{-- Contenido principal de la página --}}
@section('contenido')
{{-- Contenedor centrado con ancho máximo para formulario --}}
<div class="container max-w-md mx-auto px-4 py-12">
    {{-- Tarjeta principal del formulario con sombra --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg">
        
        {{-- ======= HEADER DEL FORMULARIO ======= --}}
        {{-- Título y descripción del registro --}}
        <div class="p-6 space-y-1">
            <h2 class="text-2xl font-bold text-center">Crear cuenta</h2>
            <p class="text-center text-gray-500">Ingresa tus datos para registrarte en el sistema</p>
        </div>

        {{-- ======= FORMULARIO DE REGISTRO ======= --}}
        <div class="p-6 pt-0">
            {{-- Formulario que envía datos al backend mediante POST --}}
            <form action="{{ route('registro.store') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- ======= CAMPOS DE NOMBRE Y APELLIDOS ======= --}}
                {{-- Grid de 2 columnas para datos personales --}}
                <div class="grid grid-cols-2 gap-4">
                    {{-- Campo: Nombre --}}
                    <div class="space-y-2">
                        <label for="nombre" class="block text-sm font-medium">Nombre</label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               {{-- Clases dinámicas para estado de error --}}
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('nombre') border-red-500 @enderror"
                               placeholder="Tu nombre"
                               {{-- Preservar valor en caso de error de validación --}}
                               value="{{ old('nombre') }}"
                               required>
                        {{-- Mensaje de error del backend --}}
                        @error('nombre')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo: Apellidos --}}
                    <div class="space-y-2">
                        <label for="apellido" class="block text-sm font-medium">Apellidos</label>
                        <input type="text" 
                               id="apellido" 
                               name="apellido" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('apellido') border-red-500 @enderror"
                               placeholder="Tus apellidos"
                               value="{{ old('apellido') }}"
                               required>
                        @error('apellido')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ======= CAMPO: CORREO ELECTRÓNICO ======= --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="tu.correo@universidad.edu"
                           value="{{ old('email') }}"
                           required>
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ======= CAMPO: NÚMERO DE MATRÍCULA ======= --}}
                {{-- Identificador único del estudiante en la universidad --}}
                <div class="space-y-2">
                    <label for="numero_matricula" class="block text-sm font-medium">Número de matrícula</label>
                    <input type="text" 
                           id="numero_matricula" 
                           name="numero_matricula" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('numero_matricula') border-red-500 @enderror"
                           placeholder="Ej: A12345"
                           value="{{ old('numero_matricula') }}"
                           required>
                    @error('numero_matricula')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ======= CAMPO: CONTRASEÑA ======= --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium">Contraseña</label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           placeholder="*********"  
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent @error('password') border-red-500 @enderror"
                           required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ======= CAMPO: CONFIRMACIÓN DE CONTRASEÑA ======= --}}
                {{-- Campo para evitar errores de tipeo en la contraseña --}}
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-medium">Confirmar contraseña</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           placeholder="*********" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                           required>
                </div>

                {{-- ======= BOTÓN DE ENVÍO ======= --}}
                {{-- Botón principal para procesar el registro --}}
                <button type="submit" 
                        class="w-full py-2 px-4 bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2"
                        id="submitBtn">
                    Registrarse
                </button>
            </form>
        </div>

        {{-- ======= FOOTER CON ENLACE A LOGIN ======= --}}
        {{-- Enlace para usuarios que ya tienen cuenta --}}
        <div class="p-6 border-t border-gray-200">
            <p class="text-center text-sm text-gray-500">
                ¿Ya tienes cuenta? 
                <a href="{{ route('login') }}" class="text-purple-600 hover:underline">
                    Iniciar sesión
                </a>
            </p>
        </div>
    </div>
</div>

{{-- ======= JAVASCRIPT PARA VALIDACIÓN FRONTEND ======= --}}
<script>
    // ======= REFERENCIAS A ELEMENTOS DEL DOM =======
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');

    /**
     * Event listener para el envío del formulario
     * Realiza validaciones básicas antes de enviar al servidor
     */
    form.addEventListener('submit', function(e) {
        // Prevenir envío automático para validar primero
        e.preventDefault();
        
        // ======= VALIDACIONES BÁSICAS =======
        // Obtener valores de los campos críticos
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const matricula = document.getElementById('numero_matricula').value;

        // Validar que las contraseñas coincidan
        if (password !== confirmPassword) {
            alert('Las contraseñas no coinciden');
            return;
        }

        // Validar longitud mínima de matrícula
        if (matricula.length < 5) {
            alert('El número de matrícula debe tener al menos 5 caracteres');
            return;
        }

        // ======= CAMBIAR ESTADO DEL BOTÓN ======= 
        // Feedback visual durante el procesamiento
        submitBtn.textContent = 'Procesando...';
        submitBtn.disabled = true;
        
        // Enviar formulario al servidor si todas las validaciones pasan
        form.submit();
    });
</script>
@endsection