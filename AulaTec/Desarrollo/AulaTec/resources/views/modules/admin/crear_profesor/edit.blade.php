{{-- Extendemos el layout de administrador --}}
@extends('layouts.admin')

{{-- Definimos el título de la página --}}
@section('titulo', 'Editar Profesor')

{{-- Contenido principal de la página --}}
@section('contenido')
<div class="space-y-4 sm:space-y-6">
    {{-- Encabezado de la página --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Editar Profesor</h1>
        <p class="text-sm sm:text-base text-gray-500">Modifica los datos del profesor.</p>
    </div>

    {{-- Formulario de información personal --}}
    <div x-data="{ 
        editando: false,
        formData: {
            nombre: '{{ $usuario->nombre }}',
            apellido: '{{ $usuario->apellido }}',
            email: '{{ $usuario->email }}'
        }
    }" class="bg-white rounded-lg border border-gray-200 shadow-sm">
        {{-- Cabecera del formulario --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div class="pr-4 sm:pr-0">
                    <h3 class="text-lg font-semibold">Información Personal</h3>
                    <p class="text-sm text-gray-500">Gestiona tu información personal y de contacto</p>
                </div>
                <button @click="editando = !editando" 
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium border px-4 py-2 transition-all duration-200 hover:bg-gray-100 hover:border-gray-300 hover:shadow-sm">
                    <span x-text="editando ? 'Cancelar' : 'Editar'"></span>
                </button>
            </div>
        </div>

        {{-- Cuerpo del formulario --}}
        <div class="p-6">
            {{-- Formulario para actualizar datos personales --}}
            <form action="{{ route('perfil.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Grid de campos del formulario --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Campo Nombre --}}
                    <div class="space-y-2">
                        <label for="nombre" class="text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" 
                            id="nombre" 
                            name="nombre" 
                            :disabled="!editando"
                            x-model="formData.nombre" 
                            required
                            :class="{'text-gray-500': !editando}"
                            class="w-full rounded-md border p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    </div>

                    {{-- Campo Apellidos --}}
                    <div class="space-y-2">
                        <label for="apellido" class="text-sm font-medium text-gray-700">Apellidos</label>
                        <input type="text" 
                            id="apellido" 
                            name="apellido"
                            :disabled="!editando"
                            x-model="formData.apellido" 
                            required
                            :class="{'text-gray-500': !editando}"
                            class="w-full rounded-md border p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    </div>

                    {{-- Campo Email --}}
                    <div class="space-y-2 md:col-span-2">
                        <label for="email" class="text-sm font-medium text-gray-700">Dirección de correo electrónico</label>
                        <input type="email" 
                            id="email" 
                            name="email"
                            :disabled="!editando"
                            x-model="formData.email" 
                            required
                            :class="{'text-gray-500': !editando}"
                            class="w-full rounded-md border p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    </div>
                </div>

                {{-- Botón de guardar cambios --}}
                <div x-show="editando" class="border-t border-gray-200 mt-6 pt-6 flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white shadow h-9 px-4 py-2">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Formulario de cambio de contraseña --}}
    <div x-data="{ 
        formDataPassword: {
            password: '',
            newPassword: '',
            confirmPassword: ''
        }
    }" class="bg-white rounded-lg border border-gray-200 shadow-sm mt-6">
        {{-- Cabecera del formulario de contraseña --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div>
                    <h3 class="text-lg font-semibold">Seguridad</h3>
                    <p class="text-sm text-gray-500">Actualiza tu contraseña</p>
                </div>
            </div>
        </div>

        {{-- Cuerpo del formulario de contraseña --}}
        <div class="p-6">
            {{-- Formulario para actualizar contraseña --}}
            <form action="{{ route('profesor.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    {{-- Campo contraseña actual --}}
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium text-gray-700">Contraseña actual</label>
                        <input type="password" 
                            id="password" 
                            name="password" 
                            x-model="formDataPassword.password"
                            required
                            class="w-full rounded-md border p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                        @error('password')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo nueva contraseña --}}
                    <div class="space-y-2">
                        <label for="newPassword" class="text-sm font-medium text-gray-700">Nueva contraseña</label>
                        <input type="password" 
                            id="newPassword" 
                            name="newPassword"
                            x-model="formDataPassword.newPassword"
                            required
                            class="w-full rounded-md border p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                        @error('newPassword')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo confirmar contraseña --}}
                    <div class="space-y-2">
                        <label for="confirmPassword" class="text-sm font-medium text-gray-700">Confirmar nueva contraseña</label>
                        <input type="password" 
                            id="confirmPassword" 
                            name="confirmPassword"
                            x-model="formDataPassword.confirmPassword"
                            required
                            class="w-full rounded-md border p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                        @error('confirmPassword')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botón actualizar contraseña --}}
                    <div class="border-t border-gray-200 mt-6 pt-6 flex justify-end">
                        <button type="submit" 
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-gradient-to-r from-purple-600 to-cyan-500 hover:from-purple-700 hover:to-cyan-600 text-white shadow h-9 px-4 py-2">
                            Actualizar Contraseña
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection