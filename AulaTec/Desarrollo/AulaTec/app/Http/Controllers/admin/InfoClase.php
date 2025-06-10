<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para la información de clases
 * Maneja la visualización de detalles de las clases
 */
class InfoClase extends Controller
{
    /**
     * Constructor que establece el locale a español
     */
    public function __construct()
    {
        Carbon::setLocale('es');
    }

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
     * Muestra la lista de clases del profesor
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->checkTeacherRole();

        $titulo = 'Info Clase';

        $clases = ClassSession::with(['teacher', 'subject', 'classroom', 'timeSlot'])
            ->where('user_id', Auth::id())
            ->whereDate('date', '>=', Carbon::today())
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($clase) {
                $horaInicio = Carbon::parse($clase->timeSlot->start_time);
                $horaFin = Carbon::parse($clase->timeSlot->end_time);
                $duracion = $horaInicio->diffInMinutes($horaFin);
                
                return [
                    'id' => $clase->id,
                    'nombre' => $clase->subject->name ?? 'Sin nombre',
                    'fecha' => Carbon::parse($clase->date),
                    'hora_inicio' => $horaInicio->format('H:i'),
                    'hora_fin' => $horaFin->format('H:i'),
                    'duracion' => $duracion,
                    'profesor' => $clase->teacher->nombre . ' ' . $clase->teacher->apellido,
                    'aula' => $clase->classroom->name ?? 'Sin aula'
                ];
            });

        return view('modules.admin.infoclase.index', compact('titulo', 'clases'));
    }

    /**
     * Obtiene los detalles específicos de una clase
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetalles($id)
    {
        $this->checkTeacherRole();

        $clase = ClassSession::with(['teacher', 'subject', 'classroom', 'timeSlot', 'reservations.user'])
            ->findOrFail($id);

        $horaInicio = Carbon::parse($clase->timeSlot->start_time);
        $horaFin = Carbon::parse($clase->timeSlot->end_time);
        $duracion = $horaInicio->diffInMinutes($horaFin);

        // Obtener información de los asientos ocupados con datos de los estudiantes
        $asientosOcupados = $clase->reservations->map(function($reserva) {
            return [
                'asiento' => $reserva->asiento,
                'estudiante' => [
                    'nombre' => $reserva->user->nombre . ' ' . $reserva->user->apellido,
                    'matricula' => $reserva->user->numero_matricula,
                    'email' => $reserva->user->email
                ]
            ];
        });

        return response()->json([
            'fecha' => Carbon::parse($clase->date)->isoFormat('dddd, D [de] MMMM [de] YYYY'),
            'hora' => $horaInicio->format('H:i') . '-' . $horaFin->format('H:i') . ' (' . $duracion . ' minutos)',
            'aula' => $clase->classroom->name,
            'profesor' => 'Profesor: ' . $clase->teacher->nombre . ' ' . $clase->teacher->apellido,
            'estadisticas' => [
                'ocupados' => $clase->reservations->count(),
                'total' => $clase->classroom->capacity,
                'porcentaje' => round(($clase->reservations->count() / $clase->classroom->capacity) * 100)
            ],
            'asientosOcupados' => $asientosOcupados
        ]);
    }

    /**
     * Elimina una clase específica
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->checkTeacherRole();

        try {
            $clase = ClassSession::findOrFail($id);
            $clase->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar la clase'], 500);
        }
    }
}
