<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de Reservas
 * 
 * Maneja todas las operaciones relacionadas con las reservas de estudiantes:
 * - Obtener reservas activas y historial
 * - Gestionar clases perdidas y justificantes
 * - Cancelar reservas
 * - Preparar datos para intercambios
 */
class ReservationController extends Controller
{
    /**
     * Verifica que el usuario actual tenga rol de alumno
     * 
     * Control de acceso centralizado para asegurar que solo los estudiantes
     * puedan gestionar sus reservas
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function checkTeacherRole()
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            abort(403, 'Debes iniciar sesión para acceder.');
        }

        // Verificar que el usuario sea un alumno (no profesor)
        if (Auth::user()->rol !== 'alumno') {
            abort(403, 'Acceso restringido.');
        }
    }
    
    /**
     * Obtiene las reservas futuras del usuario actual
     * 
     * Devuelve solo las reservas con estado "No asistido" para clases
     * que aún no han ocurrido (fechas futuras)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserReservations()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        // Obtener fecha y hora actual para filtrar clases futuras
        $now = Carbon::now();

        // Buscar reservas del usuario con todas las relaciones necesarias
        $reservations = Reservation::with(['classSession.subject', 'classSession.classroom', 'classSession.timeSlot', 'classSession.teacher'])
            ->where('user_id', Auth::id())                    // Solo reservas del usuario actual
            ->where('estado', 'No asistido')                  // Solo reservas pendientes
            // Filtrar solo clases futuras (fecha futura o misma fecha pero hora futura)
            ->whereHas('classSession', function($query) use ($now) {
                $query->where(function($q) use ($now) {
                    $q->where('date', '>', $now->toDateString())
                        ->orWhere(function($q) use ($now) {
                            $q->where('date', '=', $now->toDateString())
                                ->whereHas('timeSlot', function($q) use ($now) {
                                    $q->where('start_time', '>', $now->format('H:i:s'));
                                });
                        });
                });
            })
            ->orderBy('created_at', 'desc')                   // Más recientes primero
            ->get()
            // Transformar los datos para el frontend
            ->map(function($reservation) {
                return [
                    'id' => $reservation->id,
                    'clase' => $reservation->classSession->subject->name,
                    'profesor' => $reservation->classSession->teacher->nombre . ' ' . $reservation->classSession->teacher->apellido,
                    'aula' => $reservation->classSession->classroom->name,
                    'fecha' => $reservation->classSession->date,
                    'hora' => Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i'),
                    'hora_fin' => Carbon::parse($reservation->classSession->timeSlot->end_time)->format('H:i'),
                    // Calcular duración de la clase en minutos
                    'duracion' => Carbon::parse($reservation->classSession->timeSlot->start_time)
                        ->diffInMinutes($reservation->classSession->timeSlot->end_time),
                    'asiento' => $reservation->asiento,
                    'estado' => $reservation->estado
                ];
            });

        return response()->json($reservations);
    }

    /**
     * Obtiene reservas activas disponibles para intercambio
     * 
     * Devuelve reservas futuras del usuario que no están actualmente
     * publicadas en el sistema de intercambio
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveReservations()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Buscar reservas activas del usuario
            $activeReservations = Reservation::with([
                'classSession.subject', 
                'classSession.timeSlot',
                'classSession.classroom'
            ])
                ->where('user_id', Auth::id())                // Solo del usuario actual
                // Solo clases de hoy en adelante
                ->whereHas('classSession', function($query) {
                    $query->where('date', '>=', now()->toDateString());
                })
                // Excluir reservas que ya están publicadas para intercambio
                ->whereDoesntHave('exchangePost', function($query) {
                    $query->where('active', true);
                })
                ->get()
                // Simplificar estructura para el frontend
                ->map(function($reservation) {
                    return [
                        'id' => $reservation->id,
                        'subject' => $reservation->classSession->subject->name,
                        'date' => $reservation->classSession->date,
                        'time' => $reservation->classSession->timeSlot->start_time,
                        'classroom' => $reservation->classSession->classroom->name
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $activeReservations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cargar las reservas'
            ], 500);
        }
    }

    /**
     * Cancela una reserva específica
     * 
     * Permite al usuario eliminar una de sus reservas
     * 
     * @param \App\Models\Reservation $reservation Modelo de reserva (inyección de dependencia)
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Reservation $reservation)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        // Verificar que la reserva pertenece al usuario actual
        if ($reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Eliminar la reserva de la base de datos
        $reservation->delete();
        
        return response()->json(['message' => 'Reserva cancelada correctamente']);
    }

    /**
     * Obtiene el historial completo de reservas del usuario
     * 
     * Combina reservas activas (futuras) con reservas completadas y justificadas
     * para mostrar un historial completo
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistorialReservations()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        $now = Carbon::now();

        // RESERVAS ACTIVAS (futuras con estado "No asistido")
        $activeReservations = Reservation::with(['classSession.subject', 'classSession.classroom', 'classSession.timeSlot', 'classSession.teacher'])
            ->where('user_id', Auth::id())
            ->where('estado', 'No asistido')
            // Solo clases futuras
            ->whereHas('classSession', function($query) use ($now) {
                $query->where(function($q) use ($now) {
                    $q->where('date', '>', $now->toDateString())
                        ->orWhere(function($q) use ($now) {
                            $q->where('date', '=', $now->toDateString())
                                ->whereHas('timeSlot', function($q) use ($now) {
                                    $q->where('start_time', '>', $now->format('H:i:s'));
                                });
                        });
                });
            });

        // RESERVAS COMPLETADAS Y JUSTIFICADAS
        $completedReservations = Reservation::with(['classSession.subject', 'classSession.classroom', 'classSession.timeSlot', 'classSession.teacher'])
            ->where('user_id', Auth::id())
            ->where(function($query) {
                $query->where('estado', 'Completada')                    // Clases completadas
                    ->orWhere(function($q) {
                        $q->where('estado', 'No asistido')
                            ->where('justificado', true);                // Faltas justificadas
                    });
            });

        // COMBINAR AMBOS TIPOS DE RESERVAS
        $reservations = $activeReservations->union($completedReservations)
            ->orderBy('created_at', 'desc')                               // Más recientes primero
            ->get()
            // Formatear datos para el frontend
            ->map(function($reservation) {
                return [
                    'id' => $reservation->id,
                    'clase' => $reservation->classSession->subject->name,
                    'profesor' => $reservation->classSession->teacher->nombre . ' ' . $reservation->classSession->teacher->apellido,
                    'aula' => $reservation->classSession->classroom->name,
                    'fecha' => $reservation->classSession->date,
                    'hora' => Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i'),
                    'hora_fin' => Carbon::parse($reservation->classSession->timeSlot->end_time)->format('H:i'),
                    'duracion' => Carbon::parse($reservation->classSession->timeSlot->start_time)
                        ->diffInMinutes($reservation->classSession->timeSlot->end_time),
                    'asiento' => $reservation->asiento,
                    'estado' => $reservation->estado,
                    'justificado' => $reservation->justificado           // Campo para mostrar si está justificado
                ];
            });

        return response()->json($reservations);
    }

    /**
     * Obtiene las clases perdidas (faltas sin justificar)
     * 
     * Devuelve reservas pasadas con estado "No asistido" que no tienen
     * justificante subido
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMissedClasses()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        $now = Carbon::now();

        // Buscar reservas de clases ya pasadas sin justificar
        $reservations = Reservation::with(['classSession.subject', 'classSession.classroom', 'classSession.timeSlot', 'classSession.teacher'])
            ->where('user_id', Auth::id())
            ->where('estado', 'No asistido')                    // No asistió a la clase
            ->where('justificado', false)                       // Sin justificante válido
            // Solo clases que ya ocurrieron (fecha pasada o misma fecha pero hora pasada)
            ->whereHas('classSession', function($query) use ($now) {
                $query->where(function($q) use ($now) {
                    $q->where('date', '<', $now->toDateString())
                        ->orWhere(function($q) use ($now) {
                            $q->where('date', '=', $now->toDateString())
                                ->whereHas('timeSlot', function($q) use ($now) {
                                    $q->where('start_time', '<', $now->format('H:i:s'));
                                });
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            // Incluir información del justificante si existe
            ->map(function($reservation) {
                return [
                    'id' => $reservation->id,
                    'clase' => $reservation->classSession->subject->name,
                    'profesor' => $reservation->classSession->teacher->nombre . ' ' . $reservation->classSession->teacher->apellido,
                    'aula' => $reservation->classSession->classroom->name,
                    'fecha' => $reservation->classSession->date,
                    'hora' => Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i'),
                    'hora_fin' => Carbon::parse($reservation->classSession->timeSlot->end_time)->format('H:i'),
                    'asiento' => $reservation->asiento,
                    'justificante_path' => $reservation->justificante_path,
                    // Extraer solo el nombre del archivo para mostrar al usuario
                    'justificante_nombre' => $reservation->justificante_path ? basename($reservation->justificante_path) : null
                ];
            });

        return response()->json($reservations);
    }

    /**
     * Sube un justificante para una falta
     * 
     * Permite al usuario subir un archivo (imagen o documento) como
     * justificante para una clase a la que no asistió
     * 
     * @param \Illuminate\Http\Request $request Archivo del justificante
     * @param \App\Models\Reservation $reservation Reserva a justificar
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadJustificante(Request $request, Reservation $reservation)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        // Validar el archivo subido
        $request->validate([
            'justificante' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240'  // Máximo 10MB
        ]);

        // Verificar que la reserva pertenece al usuario actual
        if ($reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        try {
            // LIMPIAR JUSTIFICANTE ANTERIOR
            
            // Si ya existe un justificante, eliminarlo del storage
            if ($reservation->justificante_path) {
                Storage::disk('public')->delete($reservation->justificante_path);
            }

            // PROCESAR EL NUEVO ARCHIVO
            
            // Generar nombre único usando timestamp + nombre original
            $fileName = time() . '_' . $request->file('justificante')->getClientOriginalName();
            
            // Crear carpeta específica para el usuario si no existe
            $folder = 'justificantes/' . Auth::id();
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }

            // Guardar el archivo en el storage público
            $path = $request->file('justificante')->storeAs(
                $folder,        // Carpeta de destino
                $fileName,      // Nombre del archivo
                'public'        // Disco de storage
            );
            
            // ACTUALIZAR LA RESERVA
            
            // Guardar solo la ruta del justificante (no cambiar estado aún)
            $reservation->update([
                'justificante_path' => $path
            ]);

            return response()->json([
                'message' => 'Justificante subido correctamente',
                'path' => Storage::url($path)                    // URL pública del archivo
            ]);

        } catch (\Exception $e) {
            // Logging del error para debugging
            Log::error('Error al subir justificante: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Error al subir el justificante',
            ], 500);
        }
    }

    /**
     * Obtiene reservas disponibles para el sistema de intercambio
     * 
     * Devuelve todas las reservas futuras del usuario, incluyendo las que
     * ya están publicadas para intercambio (comentado el filtro)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableForExchange()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            $currentDateTime = Carbon::now();

            // Buscar reservas futuras del usuario
            $reservations = Reservation::with(['classSession.subject', 'classSession.classroom', 'classSession.timeSlot'])
                ->where('user_id', Auth::id())
                // Solo clases futuras
                ->whereHas('classSession', function($query) use ($currentDateTime) {
                    $query->where(function($q) use ($currentDateTime) {
                        $q->whereDate('date', '>', $currentDateTime->toDateString())
                            ->orWhere(function($q) use ($currentDateTime) {
                                $q->whereDate('date', '=', $currentDateTime->toDateString())
                                    ->whereHas('timeSlot', function($q) use ($currentDateTime) {
                                        $q->where('start_time', '>', $currentDateTime->format('H:i:s'));
                                    });
                            });
                    });
                })
                // FILTRO COMENTADO: No excluir reservas ya publicadas
                // ->whereDoesntHave('exchangePost', function($query) {
                //     $query->where('active', true);
                // })
                ->get()
                // Formatear para el sistema de intercambio
                ->map(function($reservation) {
                    return [
                        'id' => $reservation->id,
                        'subject' => $reservation->classSession->subject->name,
                        'date' => $reservation->classSession->date,
                        'time' => $reservation->classSession->timeSlot->start_time,
                        // Calcular duración en minutos
                        'duration' => Carbon::parse($reservation->classSession->timeSlot->start_time)
                            ->diffInMinutes(Carbon::parse($reservation->classSession->timeSlot->end_time)),
                        'classroom' => $reservation->classSession->classroom->name,
                        'asiento' => $reservation->asiento
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $reservations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cargar las reservas disponibles'
            ], 500);
        }
    }
}