@extends('layouts.perfil')

@section('contenido')
    <div class="w-full max-w-7xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mi Perfil</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar con información básica -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="pt-6 px-6 pb-6">
                        <div class="flex flex-col items-center">
                            <div class="h-24 w-24 mb-4 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    width="48" 
                                    height="48" 
                                    viewBox="0 0 24 24" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    stroke-width="1.5" 
                                    stroke-linecap="round" 
                                    stroke-linejoin="round" 
                                    class="text-gray-400">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <!-- Nombre -->
                            <div class="text-center">
                                <h2 class="text-xl font-bold text-gray-900">
                                    {{ $usuario->nombre }} {{ $usuario->apellido }}
                                </h2>
                                <p class="text-gray-500 text-sm mt-1 break-words">{{ $usuario->email }}</p>
                            </div>
                            
                            <div class="mt-4 w-full">
                                <div class="bg-gray-100 p-3 rounded-lg mb-2">
                                    <p class="text-sm font-medium">Matrícula</p>
                                    <p class="text-lg">{{ $usuario->numero_matricula }}</p>
                                </div>
                                <div class="bg-gray-100 p-3 rounded-lg mb-2">
                                    <p class="text-sm font-medium">Carrera</p>
                                    <p class="text-lg {{ $usuario->carrera ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $usuario->carrera ?? 'Carrera de Ejemplo' }}
                                    </p>
                                </div>
                                <div class="bg-gray-100 p-3 rounded-lg">
                                    <p class="text-sm font-medium">Rol</p>
                                    <p class="text-lg">{{ $usuario->rol }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="lg:col-span-3">
                <div x-data="{ activeTab: 'cuenta' }">
                    <!-- Tabs -->
                    <div class="grid w-full grid-cols-3 mb-6  bg-gray-100 p-1 rounded-lg">
                        <button @click="activeTab = 'cuenta'" :class="activeTab === 'cuenta' ? 'bg-white text-foreground shadow-sm' : 'bg-muted text-muted-foreground hover:text-foreground'" class="flex items-center justify-center py-2 px-3 rounded-tl-md rounded-tr-md border-b-2 transition-colors" :style="activeTab === 'cuenta' ? 'border-color: hsl(262.1 83.3% 57.8%)' : 'border-color: transparent'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span class="hidden sm:inline">Cuenta</span>
                            <span class="sm:hidden font-medium">Cuenta</span>
                        </button>
                        <button @click="activeTab = 'seguridad'" :class="activeTab === 'seguridad' ? 'bg-white text-foreground shadow-sm' : 'bg-muted text-muted-foreground hover:text-foreground'" class="flex items-center justify-center py-2 px-3 border-b-2 transition-colors" :style="activeTab === 'seguridad' ? 'border-color: hsl(262.1 83.3% 57.8%)' : 'border-color: transparent'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <span class="hidden sm:inline">Seguridad</span>
                            <span class="sm:hidden font-medium">Seg.</span>
                        </button>
                        <button @click="activeTab = 'historial'" :class="activeTab === 'historial' ? 'bg-white text-foreground shadow-sm' : 'bg-muted text-muted-foreground hover:text-foreground'" class="flex items-center justify-center py-2 px-3 rounded-tr-md rounded-tl-md border-b-2 transition-colors" :style="activeTab === 'historial' ? 'border-color: hsl(262.1 83.3% 57.8%)' : 'border-color: transparent'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path><path d="M12 7v5l4 2"></path></svg>
                            <span class="hidden sm:inline">Historial</span>
                            <span class="sm:hidden font-medium">Hist.</span>
                        </button>
                    </div>

                    <!-- Pestaña de Cuenta -->
                    <div x-show="activeTab === 'cuenta'" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-cloak>
                        @include('modules.perfil.cuenta')
                    </div>

                    <!-- Pestaña de Seguridad -->
                    <div x-show="activeTab === 'seguridad'" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-cloak>
                        @include('modules.perfil.seguridad')
                    </div>
                    
                    <!-- Pestaña de Historial -->
                    <div x-show="activeTab === 'historial'" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-cloak>
                        @include('modules.perfil.historial')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection