<?php

namespace App\Components;

use Spatie\LivewireWizard\Components\WizardComponent;
use Spatie\LivewireWizard\Support\State;

class CreateClassWizardComponent extends WizardComponent
{
    public function steps(): array
    {
        return [
            AsignaturaStepComponent::class,
            DatosStepComponent::class,
            DescripcionStepComponent::class,
        ];
    }

    public function stateClass(): string
    {
        return State::class;
    }
}
