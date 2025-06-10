<?php

namespace App\Components;

use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Spatie\LivewireWizard\Components\StepComponent;
use Illuminate\Support\Facades\Session;

class AsignaturaStepComponent extends StepComponent
{
    public $asignatura ;
    public $profesor ;
    public $profesor_nombre;
    public $fecha ;
    public $asignaturas;
    


    protected $rules = [
        'asignatura' => 'required',
        'profesor' => 'required',
        'fecha' => 'required'
    ];

    public function stepInfo(): array
    {
        return [
            'label' => 'Asignatura',
            'icon' => 'fa-book'
        ];
    }
    
    public function mount()
    {
        $this->asignaturas = Subject::all();
        // Obtener datos del profesor autenticado
        $user = Auth::user();
        $this->profesor = $user->id;
        $this->profesor_nombre = $user->nombre . ' ' . $user->apellido;
    }

    public function nextStep()
    {
        $this->validate();

        Session::put('asignatura', [
            'asignatura' => $this->asignatura,
            'profesor' => $this->profesor,
            'profesor_nombre' => $this->profesor_nombre,
            'fecha' => $this->fecha
        ]);

        parent::nextStep();
    }

    public function render()
    {
        return view('modules.admin.crear_clase.steps.asignatura', [
            'asignaturas' => $this->asignaturas
        ]);
    }
}
