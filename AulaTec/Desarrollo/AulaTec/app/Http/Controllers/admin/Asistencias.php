<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassSession;

/**
 * Controlador para la gestión de asistencias
 * Maneja el registro y visualización de asistencias a clases
 */
class Asistencias extends Controller
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
     * Muestra la vista principal de asistencias
     * Incluye filtros por clases y fechas
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->checkTeacherRole();
        $titulo = 'Registro de Asistencias';

        // Obtener clases únicas del profesor
        $clasesUnicas = Reservation::select('subjects.name as nombre_clase', 'subjects.id')
            ->join('classes', 'reservations.class_id', '=', 'classes.id')
            ->join('subjects', 'classes.subject_id', '=', 'subjects.id')
            ->where('classes.user_id', Auth::id())
            ->where(function($query) {
                $query->where('reservations.estado', 'Completada')
                    ->orWhere('reservations.justificado', 1);
            })
            ->distinct()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nombre' => $item->nombre_clase
                ];
            });

        // Obtener fechas únicas
        $fechasUnicas = Reservation::select('classes.date')
            ->join('classes', 'reservations.class_id', '=', 'classes.id')
            ->where('classes.user_id', Auth::id())
            ->where(function($query) {
                $query->where('reservations.estado', 'Completada')
                    ->orWhere('reservations.justificado', 1);
            })
            ->distinct()
            ->orderBy('classes.date', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'fecha' => $item->date,
                    'formato' => \Carbon\Carbon::parse($item->date)->format('d/m/Y')
                ];
            });

        // Obtener todas las reservas con sus relaciones
        $reservas = Reservation::with(['student', 'class.subject', 'class.timeSlot', 'class.classroom'])
            ->whereHas('class', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where(function($query) {
                $query->where('estado', 'Completada')
                    ->orWhere('justificado', 1);
            })
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'nombre' => trim(($reserva->student->nombre ?? 'N/A') . ' ' . ($reserva->student->apellido ?? '')),
                    'matricula' => $reserva->student->numero_matricula ?? 'N/A',
                    'clase' => $reserva->class->subject->name ?? 'N/A',
                    'clase_id' => $reserva->class->subject->id ?? '',
                    'aula' => $reserva->class->classroom->name ?? 'N/A',
                    'asiento' => $reserva->asiento ?? 'N/A',
                    'estado' => $reserva->justificado ? 'Justificado' : $reserva->estado,
                    'fecha' => $reserva->class->date ?? 'N/A',
                    'hora' => $reserva->class->timeSlot->start_time ?
                        \Carbon\Carbon::parse($reserva->class->timeSlot->start_time)->format('H:i') : 'N/A',
                ];
            });

        return view('modules.admin.asistencia.index', compact('titulo', 'reservas', 'clasesUnicas', 'fechasUnicas'));
    }
}
