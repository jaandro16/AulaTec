<?php

namespace App\Providers;

use App\Components\AsignaturaStepComponent;
use App\Components\CreateClassWizardComponent;
use App\Components\DatosStepComponent;
use App\Components\DescripcionStepComponent;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/**
 * Proveedor de servicios principal de la aplicación
 * Registra los componentes de Livewire y otros servicios
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios de la aplicación
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa los servicios de la aplicación
     * Registra los componentes de Livewire necesarios para el wizard
     */
    public function boot(): void
    {
        // Registrar componentes del wizard de creación de clase
        Livewire::component('create-class-wizard', CreateClassWizardComponent::class);
        Livewire::component('asignatura-step', AsignaturaStepComponent::class);
        Livewire::component('datos-step', DatosStepComponent::class);
        Livewire::component('descripcion-step', DescripcionStepComponent::class);
    }
}
