<?php

namespace App\Components;

use App\Models\Classroom;
use Spatie\LivewireWizard\Components\StepComponent;
use App\Models\ClassSession;
use App\Models\Subject;
use Illuminate\Support\Facades\Session;



class DescripcionStepComponent extends StepComponent
{
    public $descripcion;
    public $asignaturas;
    public $aulas;
    public $profesor;
    public $profesor_nombre;
    public $fecha;
    public $asignatura;
    public $aula;

    protected $rules = [
        'descripcion' => 'nullable|min:10'
    ];

    public function stepInfo(): array
    {
        return [
            'label' => 'Descripción',
            'icon' => 'fa-pencil'
        ];
    }

    public function mount()
    {
        $this->asignaturas = Subject::all();
        $this->aulas = Classroom::all();

        $asignaturaData = Session::get('asignatura', []);
        $datosData = Session::get('datos', []);

        $this->asignatura = $asignaturaData['asignatura'] ?? null;
        $this->profesor = $asignaturaData['profesor'] ?? null;
        $this->profesor_nombre = $asignaturaData['profesor_nombre'] ?? null;
        $this->fecha = $asignaturaData['fecha'] ?? null;
        $this->aula = $datosData['aula'] ?? null;
    }

    // public function nextStep()
    // {
    //     $this->validate();
    //     try {
    //         $asignaturaData = Session::get('asignatura');
    //         $datosData = Session::get('datos');

    //         // Log datos antes de crear
    //         logger()->info('Intentando crear clase:', [
    //             'asignatura' => $asignaturaData,
    //             'datos' => $datosData,
    //             'descripcion' => $this->descripcion
    //         ]);

    //         $datosClase = [
    //             'subject_id' => $asignaturaData['asignatura'],
    //             'user_id' => $asignaturaData['profesor'],
    //             'classroom_id' => $datosData['aula'],
    //             'time_slot_id' => $datosData['horario'],
    //             'date' => $asignaturaData['fecha'],
    //             'description' => $this->descripcion
    //         ];

    //         // Crear clase y verificar
    //         $clase = ClassSession::create($datosClase);

    //         if (!$clase) {
    //             throw new \Exception('Error al crear la clase: no se pudo guardar en la base de datos');
    //         }

    //         // Log después de crear exitosamente
    //         logger()->info('Clase creada exitosamente:', ['clase_id' => $clase->id]);

    //         // Limpiar sesión
    //         Session::forget(['asignatura', 'datos']);
    //         $this->reset();

    //         session()->flash('message', 'Clase creada exitosamente');
    //         return redirect()->route('admin.crear-clase.create');
    //     } catch (\Exception $e) {
    //         // Log error
    //         logger()->error('Error al crear clase:', [
    //             'error' => $e->getMessage(),
    //             'datos' => $datosClase ?? null
    //         ]);

    //         return back()->withErrors(['error' => $e->getMessage()]);
    //     }
    
    
    // }
    

    public function render()
    {
        return view('modules.admin.crear_clase.steps.descripcion');
    }
}