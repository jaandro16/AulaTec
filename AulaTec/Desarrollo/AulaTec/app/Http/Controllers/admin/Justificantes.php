<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la gestión de justificantes de asistencia
 * Maneja la validación y registro de justificantes para faltas
 */
class Justificantes extends Controller
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
     * Muestra la lista de asistencias que pueden ser justificadas
     * Filtra por profesor y fechas/horas pasadas
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->checkTeacherRole();

        $titulo = 'Gestión de Justificantes';
        $now = now();

        // Obtener reservas no asistidas que pueden justificarse
        $reservas = Reservation::with(['student', 'class.subject', 'class.classroom', 'class.timeSlot', 'classSession'])
            ->whereHas('class', function($query) use ($now) {
                $query->where(function($q) use ($now) {
                    // Fechas anteriores a hoy
                    $q->where('date', '<', $now->toDateString())
                    // O fecha de hoy pero hora ya pasada
                    ->orWhere(function($sq) use ($now) {
                        $sq->where('date', $now->toDateString())
                        ->whereHas('timeSlot', function($tsq) use ($now) {
                            $tsq->where('end_time', '<', $now->format('H:i:s'));
                        });
                    });
                })
                ->where('user_id', Auth::id());
            })
            ->where('estado', 'No asistido')
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'nombre' => ($reserva->student->nombre ?? 'N/A') . ' ' . ($reserva->student->apellido ?? ''),
                    'matricula' => $reserva->student->numero_matricula ?? 'N/A',
                    'clase' => $reserva->class->subject->name ?? 'N/A',
                    'clase_id' => $reserva->class->subject->id ?? '',
                    'aula' => $reserva->class->classroom->name ?? 'N/A',
                    'asiento' => $reserva->asiento ?? 'N/A',
                    'estado' => $reserva->estado ?? 'N/A',
                    'fecha' => $reserva->class->date ?? 'N/A',
                    'hora' => $reserva->class->timeSlot->start_time ?
                        \Carbon\Carbon::parse($reserva->class->timeSlot->start_time)->format('H:i') : 'N/A',
                    'justificado' => $reserva->justificado ?? 0,
                    'justificante_path' => $reserva->justificante_path ?? null,
                ];
            });

        // Obtener clases únicas para filtrado
        $clasesUnicas = Reservation::select('subjects.name as nombre_clase', 'subjects.id')
            ->join('classes', 'reservations.class_id', '=', 'classes.id')
            ->join('subjects', 'classes.subject_id', '=', 'subjects.id')
            ->join('time_slots', 'classes.time_slot_id', '=', 'time_slots.id')
            ->where(function($query) use ($now) {
                $query->where('classes.date', '<', $now->toDateString())
                    ->orWhere(function($q) use ($now) {
                        $q->where('classes.date', $now->toDateString())
                        ->where('time_slots.end_time', '<', $now->format('H:i:s'));
                    });
            })
            ->where('classes.user_id', Auth::id())
            ->where('reservations.estado', 'No asistido')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nombre' => $item->nombre_clase
                ];
            });

        // Obtener fechas únicas para filtrado
        $fechasUnicas = Reservation::select('classes.date')
            ->join('classes', 'reservations.class_id', '=', 'classes.id')
            ->join('time_slots', 'classes.time_slot_id', '=', 'time_slots.id')
            ->where(function($query) use ($now) {
                $query->where('classes.date', '<', $now->toDateString())
                    ->orWhere(function($q) use ($now) {
                        $q->where('classes.date', $now->toDateString())
                        ->where('time_slots.end_time', '<', $now->format('H:i:s'));
                    });
            })
            ->where('classes.user_id', Auth::id())
            ->where('reservations.estado', 'No asistido')
            ->distinct()
            ->orderBy('classes.date', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'fecha' => $item->date,
                    'formato' => \Carbon\Carbon::parse($item->date)->format('d/m/Y')
                ];
            });

        return view('modules.admin.justificantes.index', compact('titulo', 'reservas', 'clasesUnicas', 'fechasUnicas'));
    }

    /**
     * Procesa la justificación de una falta de asistencia
     * @param Reservation $reservation Reserva a justificar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function justificar(Reservation $reservation)
    {
        $this->checkTeacherRole();

        try {
            // Control de tiempo entre acciones
            if (session()->has('justificacion_timestamp')) {
                $lastTime = session('justificacion_timestamp');
                $elapsedTime = time() - $lastTime;

                if ($elapsedTime < 2) {
                    session()->flash('error', 'Por favor espere antes de realizar otra acción');
                    return back();
                }
            }

            session()->put('justificacion_timestamp', time());

            // Alternar estado de justificación
            $nuevoEstado = $reservation->justificado == 1 ? 0 : 1;
            
            // Transacción para actualizar estado
            DB::beginTransaction();
            
            try {
                $reservation->update(['justificado' => $nuevoEstado]);
                DB::commit();

                // Mensaje de éxito personalizado
                $nombreEstudiante = trim(($reservation->student->nombre ?? '') . ' ' . ($reservation->student->apellido ?? '')) ?: 'Estudiante';                
                session()->flash('success', $nuevoEstado == 1 ?
                    "Asistencia de {$nombreEstudiante} justificada correctamente" :
                    "Justificación eliminada correctamente");

                return back();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error al justificar reserva: ' . $e->getMessage());
            session()->flash('error', 'Error al actualizar el estado. Por favor, inténtalo de nuevo.');
            return back();
        }
    }

    /**
     * Muestra los detalles de una justificación específica
     * @param string $id ID de la justificación
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $this->checkTeacherRole();
        return view('modules.admin.justificantes.index', compact('titulo', 'reservas'));
    }
}
