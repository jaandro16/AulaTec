<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ClassSession;

/**
 * Controlador para la creación de clases
 * Gestiona el proceso de crear nuevas sesiones de clase
 */
class CrearClase extends Controller
{
    /**
     * Verifica que el usuario sea un profesor autenticado
     */
    private function checkTeacherRole()
    {
        if (!Auth::check()) {
            abort(403, 'Debes iniciar sesión para acceder.');
        }

        if (Auth::user()->rol !== 'profesor') {
            abort(403, 'Acceso restringido.');
        }
    }

    /**
     * Muestra el formulario para crear una nueva clase
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->checkTeacherRole();
        
        $titulo = 'Crear Nueva Clase';
        $profesor = Auth::user();
        return view('modules.admin.crear_clase.create', compact('titulo', 'profesor'));
    }

    /**
     * Almacena una nueva clase en la base de datos
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->checkTeacherRole();

        // Validar la descripción
        $request->validate([
            'descripcion' => 'nullable|min:10'
        ]);

        try {
            // Obtener datos de la sesión
            $asignaturaData = Session::get('asignatura');
            $datosData = Session::get('datos');

            // Verificar que existan los datos
            if (!$asignaturaData || !$datosData) {
                return back()->withErrors(['error' => 'Datos de sesión perdidos. Por favor, comienza de nuevo.']);
            }

            // Log para debug
            // logger()->info('Creando clase via POST:', [
            //     'asignatura' => $asignaturaData,
            //     'datos' => $datosData,
            //     'descripcion' => $request->descripcion
            // ]);

            // Crear la clase
            $datosClase = [
                'subject_id' => $asignaturaData['asignatura'],
                'user_id' => $asignaturaData['profesor'],
                'classroom_id' => $datosData['aula'],
                'time_slot_id' => $datosData['horario'],
                'date' => $asignaturaData['fecha'],
                'descripcion' => $request->descripcion
            ];

            $clase = ClassSession::create($datosClase);

            if (!$clase) {
                throw new \Exception('Error al crear la clase en la base de datos');
            }

            // Log éxito
            // logger()->info('Clase creada exitosamente via POST:', ['clase_id' => $clase->id]);

            // Limpiar sesión
            Session::forget(['asignatura', 'datos']);

            // Redireccionar con mensaje de éxito
            return redirect()->route('admin.crear-clase.create')
                            ->with('message', 'Clase creada exitosamente');

        } catch (\Exception $e) {
            // logger()->error('Error al crear clase via POST:', [
            //     'error' => $e->getMessage(),
            //     'datos' => $datosClase ?? null
            // ]);
            logger()->error('Error al crear clase via POST');

            return back()->withErrors(['error' => 'Error al crear la clase. Por favor, inténtalo de nuevo.']);        }
    }
}
