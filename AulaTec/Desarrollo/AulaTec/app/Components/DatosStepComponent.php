<?php

namespace App\Components;

use App\Models\Classroom;
use Spatie\LivewireWizard\Components\StepComponent;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\Session;


class DatosStepComponent extends StepComponent
{
    public $aula;
    public $horario;
    public $capacidad;
    public $aulas;
    public $timeSlots;



    protected $rules = [
        'aula' => 'required',
        'horario' => 'required',
        'capacidad' => 'required'
    ];

    public function stepInfo(): array
    {
        return [
            'label' => 'Datos de Aula',
            'icon' => 'fa-clock'
        ];
    }
    public function mount()
    {
        $this->aulas = Classroom::all();
        $this->timeSlots = TimeSlot::all();
    }
    public function nextStep()
    {
        $this->validate();
    
        // Usar Session facade para almacenar en el estado
        Session::put('datos', [
            'aula' => $this->aula,
            'horario' => $this->horario,
            'capacidad' => $this->capacidad
        ]);
        parent::nextStep();
    }
    public function updatedAula($value)
    {
        if ($value) {
            $aula = Classroom::find($value);
            $this->capacidad = $aula ? $aula->capacity : null;
        } else {
            $this->capacidad = null;
        }
    }

    public function render()
    {
        return view('modules.admin.crear_clase.steps.datos', [
            'aulas' => $this->aulas,
            'timeSlots' => $this->timeSlots
    ]);
    }
}
