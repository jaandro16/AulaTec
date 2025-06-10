<?php

namespace App\Http\Controllers;

use App\Mail\ExchangeConfirmation;
use App\Models\ExchangePost;
use App\Models\ExchangeRequest;
use App\Models\Reservation;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador de Intercambio de Reservas
 * 
 * Maneja toda la funcionalidad del sistema de intercambio de asientos:
 * - Publicar reservas para intercambio
 * - Solicitar intercambios de otros usuarios
 * - Aceptar/rechazar solicitudes de intercambio
 * - Gestionar el historial de intercambios
 */
class ExchangeController extends Controller
{
    /**
     * Verifica que el usuario actual tenga rol de alumno
     * 
     * Control de acceso centralizado para asegurar que solo los estudiantes
     * puedan participar en el sistema de intercambio de reservas
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
     * Obtiene todas las asignaturas disponibles
     * 
     * Utilizado para poblar dropdowns y filtros en el frontend
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubjects()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Obtener todas las asignaturas ordenadas alfabéticamente
            $subjects = Subject::orderBy('name')->get();
            return response()->json($subjects);
        } catch (\Exception $e) {
            // En caso de error, loggear y devolver array vacío
            Log::error('Error cargando asignaturas: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
    
    /**
     * Obtiene todas las publicaciones de intercambio activas
     * 
     * Devuelve las reservas disponibles para intercambio, excluyendo:
     * - Las del usuario actual
     * - Las de clases ya pasadas
     * - Las inactivas
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveExchanges()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Obtener fecha y hora actual para filtrar clases futuras
            $currentDateTime = Carbon::now();

            // Buscar publicaciones activas con todas las relaciones necesarias
            $exchanges = ExchangePost::with([
                'reservation.classSession.subject',    // Materia de la clase
                'reservation.classSession.classroom',  // Aula de la clase
                'reservation.classSession.timeSlot',   // Horario de la clase
                'reservation.user'                     // Usuario que publicó
            ])
            ->where('active', true)                    // Solo publicaciones activas
            // Filtrar solo clases futuras (fecha futura o misma fecha pero hora futura)
            ->whereHas('reservation.classSession', function($query) use ($currentDateTime) {
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
            // Excluir las reservas del usuario actual
            ->whereHas('reservation', function($query) {
                $query->where('user_id', '!=', Auth::id());
            })
            ->get()
            // Transformar los datos para el frontend
            ->map(function($exchange) {
                return [
                    'id' => $exchange->id,
                    'motivo' => $exchange->motivo,
                    'reservation' => [
                        'id' => $exchange->reservation->id,
                        'asiento' => $exchange->reservation->asiento,
                        'user' => [
                            'fullName' => $exchange->reservation->user->nombre . ' ' . $exchange->reservation->user->apellido
                        ],
                        'class_session' => [
                            'subject' => [
                                'id' => $exchange->reservation->classSession->subject->id,
                                'name' => $exchange->reservation->classSession->subject->name
                            ],
                            'classroom' => [
                                'name' => $exchange->reservation->classSession->classroom->name
                            ],
                            'date' => $exchange->reservation->classSession->date,
                            'time_slot' => [
                                'start_time' => $exchange->reservation->classSession->timeSlot->start_time,
                                // Calcular duración de la clase en minutos
                                'duration' => Carbon::parse($exchange->reservation->classSession->timeSlot->start_time)
                                    ->diffInMinutes($exchange->reservation->classSession->timeSlot->end_time)
                            ]
                        ]
                    ]
                ];
            });

            return response()->json($exchanges);
        } catch (\Exception $e) {
            // Logging detallado para debugging
            Log::error('Error en getActiveExchanges: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error al obtener las reservas disponibles para intercambio'], 500);
        }
    }

    /**
     * Crea una nueva publicación de intercambio
     * 
     * Permite a un usuario publicar una de sus reservas para intercambio
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Validar los datos recibidos
            $validated = $request->validate([
                'reservation_id' => 'required|exists:reservations,id',  // ID de reserva válido
                'motivo' => 'required|string|max:500'                   // Motivo del intercambio
            ]);

            // Verificar que la reserva pertenece al usuario autenticado
            $reservation = Reservation::findOrFail($validated['reservation_id']);
            if ($reservation->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No tienes permiso para publicar esta reserva'
                ], 403);
            }

            // Verificar que la reserva no tiene ya un intercambio activo
            if ($reservation->exchangePost()->where('active', true)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Esta reserva ya está publicada para intercambio'
                ], 422);
            }

            // Crear la publicación de intercambio
            $exchangePost = ExchangePost::create([
                'reservation_id' => $validated['reservation_id'],
                'motivo' => $validated['motivo'],
                'active' => true                    // Activar inmediatamente
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Reserva publicada correctamente para intercambio',
                'data' => $exchangePost
            ]);

        } catch (\Exception $e) {
            Log::error('Error al publicar intercambio: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo procesar la solicitud. Por favor, inténtalo de nuevo.'
            ], 500);
        }
    }

    /**
     * Crea una solicitud de intercambio
     * 
     * Permite a un usuario solicitar intercambiar su reserva por otra publicación
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeRequest(Request $request)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Validar los datos de la solicitud
            $validated = $request->validate([
                'exchange_post_id' => 'required|exists:exchange_posts,id',  // Publicación objetivo
                'reservation_id' => 'required|exists:reservations,id'      // Reserva que ofrece
            ]);

            // Verificar que la reserva pertenece al usuario actual
            $reservation = Reservation::findOrFail($validated['reservation_id']);
            if ($reservation->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No tienes permiso para usar esta reserva'
                ], 403);
            }

            // Verificar que no existe una solicitud pendiente para este intercambio
            $existingRequest = ExchangeRequest::where([
                'exchange_post_id' => $validated['exchange_post_id'],
                'reservation_id' => $validated['reservation_id'],
                'estado' => 'Pendiente'
            ])->exists();

            if ($existingRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ya has solicitado este intercambio anteriormente'
                ], 422);
            }

            // Crear la solicitud de intercambio
            $exchangeRequest = ExchangeRequest::create([
                'exchange_post_id' => $validated['exchange_post_id'],
                'reservation_id' => $validated['reservation_id'],
                'estado' => 'Pendiente'           // Estado inicial
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Solicitud de intercambio enviada correctamente',
                'data' => $exchangeRequest
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear solicitud de intercambio: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la solicitud de intercambio'
            ], 500);
        }
    }

    /**
     * Obtiene las publicaciones de intercambio del usuario actual
     * 
     * Devuelve todas las publicaciones creadas por el usuario junto con
     * las solicitudes recibidas para cada una
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserExchangePosts()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Obtener fecha y hora actual para filtrar clases futuras
            $now = Carbon::now();

            // Buscar las publicaciones del usuario con todas las relaciones
            $exchangePosts = ExchangePost::with([
                'reservation.classSession.subject',
                'reservation.classSession.classroom',
                'reservation.classSession.timeSlot',
                'exchangeRequests.reservation.classSession.subject',      // Solicitudes recibidas
                'exchangeRequests.reservation.classSession.classroom',
                'exchangeRequests.reservation.classSession.timeSlot',
                'exchangeRequests.reservation.user'                       // Usuario que solicita
            ])
            // Solo clases futuras
            ->whereHas('reservation.classSession', function($query) use ($now) {
                $query->where(function($q) use ($now) {
                    $q->whereDate('date', '>', $now->toDateString())
                        ->orWhere(function($q) use ($now) {
                            $q->whereDate('date', '=', $now->toDateString())
                                ->whereHas('timeSlot', function($q) use ($now) {
                                    $q->where('start_time', '>', $now->format('H:i:s'));
                                });
                        });
                });
            })
            // Solo las publicaciones del usuario actual
            ->whereHas('reservation', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();

            // Formatear los datos para el frontend
            $formattedPosts = $exchangePosts->map(function($exchangePost) {
                return [
                    'id' => $exchangePost->id,
                    'motivo' => $exchangePost->motivo,
                    'reservation' => [
                        'id' => $exchangePost->reservation->id,
                        'user_id' => $exchangePost->reservation->user_id,
                        'asiento' => $exchangePost->reservation->asiento,
                        'class_session' => [
                            'subject' => [
                                'name' => $exchangePost->reservation->classSession->subject->name
                            ],
                            'date' => $exchangePost->reservation->classSession->date,
                            'classroom' => [
                                'name' => $exchangePost->reservation->classSession->classroom->name
                            ],
                            'time_slot' => [
                                'start_time' => $exchangePost->reservation->classSession->timeSlot->start_time,
                                // Calcular duración en minutos
                                'duration' => Carbon::parse($exchangePost->reservation->classSession->timeSlot->start_time)
                                    ->diffInMinutes(Carbon::parse($exchangePost->reservation->classSession->timeSlot->end_time))
                            ]
                        ]
                    ],
                    // Mapear todas las solicitudes recibidas para esta publicación
                    'requests' => $exchangePost->exchangeRequests->map(function($request) {
                        return [
                            'id' => $request->id,
                            'estado' => $request->estado,
                            'user' => [
                                'nombre' => $request->reservation->user->nombre,
                                'apellido' => $request->reservation->user->apellido
                            ],
                            // Detalles de la reserva ofrecida en el intercambio
                            'offered_reservation' => [
                                'subject' => $request->reservation->classSession->subject->name,
                                'date' => $request->reservation->classSession->date,
                                'time' => $request->reservation->classSession->timeSlot->start_time,
                                'duration' => Carbon::parse($request->reservation->classSession->timeSlot->start_time)
                                    ->diffInMinutes(Carbon::parse($request->reservation->classSession->timeSlot->end_time)),
                                'classroom' => $request->reservation->classSession->classroom->name,
                                'asiento' => $request->reservation->asiento
                            ]
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedPosts
            ]);

        } catch (\Exception $e) {
            Log::error('Error en getUserExchangePosts: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cargar las publicaciones'
            ], 500);
        }
    }

    /**
     * Elimina una publicación de intercambio
     * 
     * Cancela una publicación propia y elimina todas las solicitudes asociadas
     * 
     * @param int $id ID de la publicación
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Buscar la publicación asegurando que pertenece al usuario actual
            $exchangePost = ExchangePost::with('reservation')
                ->whereHas('reservation', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->findOrFail($id);

            // Eliminar las solicitudes asociadas primero (integridad referencial)
            $exchangePost->exchangeRequests()->delete();
            
            // Eliminar la publicación
            $exchangePost->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Publicación cancelada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cancelar publicación: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cancelar la publicación'
            ], 500);
        }
    }

    /**
     * Acepta una solicitud de intercambio
     * 
     * Proceso complejo que:
     * 1. Intercambia las reservas entre usuarios
     * 2. Genera nuevos códigos QR
     * 3. Envía emails de confirmación
     * 4. Elimina la publicación
     * 
     * @param int $id ID de la solicitud de intercambio
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptRequest($id)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Iniciar transacción para asegurar consistencia
            DB::beginTransaction();

            // Cargar el exchange request con todas las relaciones necesarias
            $exchangeRequest = ExchangeRequest::with([
                'exchangePost.reservation.user',                           // Usuario que publicó
                'exchangePost.reservation.classSession.subject',
                'exchangePost.reservation.classSession.teacher',
                'exchangePost.reservation.classSession.classroom',
                'exchangePost.reservation.classSession.timeSlot',
                'reservation.user',                                        // Usuario que solicita
                'reservation.classSession.subject',
                'reservation.classSession.teacher',
                'reservation.classSession.classroom',
                'reservation.classSession.timeSlot'
            ])->findOrFail($id);

            // Verificar que el usuario actual es el dueño de la publicación
            if ($exchangeRequest->exchangePost->reservation->user_id !== Auth::id()) {
                throw new \Exception('No autorizado para aceptar esta solicitud');
            }

            // Guardar referencias a los usuarios y sus emails para notificaciones
            $publisherEmail = $exchangeRequest->exchangePost->reservation->user->email;
            $requesterEmail = $exchangeRequest->reservation->user->email;

            // Obtener las reservas que se van a intercambiar
            $publisherReservation = $exchangeRequest->exchangePost->reservation;
            $requesterReservation = $exchangeRequest->reservation;

            // INTERCAMBIO: Intercambiar los user_id de las reservas
            $tempUserId = $publisherReservation->user_id;
            $publisherReservation->user_id = $requesterReservation->user_id;
            $requesterReservation->user_id = $tempUserId;

            // Guardar los cambios en la base de datos
            $publisherReservation->save();
            $requesterReservation->save();

            // Actualizar estado de la solicitud
            $exchangeRequest->estado = 'Aceptada';
            $exchangeRequest->save();

            // Confirmar todos los cambios en la base de datos
            DB::commit();

            // Recargar las reservas con todas sus relaciones para tener los datos actualizados
            $publisherNewReservation = Reservation::with([
                'classSession.subject',
                'classSession.teacher',
                'classSession.classroom',
                'classSession.timeSlot',
                'user'
            ])->find($requesterReservation->id);

            $requesterNewReservation = Reservation::with([
                'classSession.subject',
                'classSession.teacher',
                'classSession.classroom',
                'classSession.timeSlot',
                'user'
            ])->find($publisherReservation->id);

            // GENERAR CÓDIGOS QR ACTUALIZADOS PARA AMBOS USUARIOS

            // Datos QR para el usuario que publicó (ahora tiene la nueva reserva)
            $publisherQrData = [
                'id' => $publisherNewReservation->id,
                'clase' => $publisherNewReservation->classSession->subject->name,
                'profesor' => $publisherNewReservation->classSession->teacher->nombre . ' ' . $publisherNewReservation->classSession->teacher->apellido,
                'aula' => $publisherNewReservation->classSession->classroom->name,
                'fecha' => Carbon::parse($publisherNewReservation->classSession->date)->format('Y-m-d'),
                'hora' => Carbon::parse($publisherNewReservation->classSession->timeSlot->start_time)->format('H:i'),
                'asiento' => $publisherNewReservation->asiento,
                'estudiante' => $publisherNewReservation->user->nombre . ' ' . $publisherNewReservation->user->apellido,
                'token' => md5($publisherNewReservation->id . $publisherNewReservation->user_id . $publisherNewReservation->created_at)
            ];

            // Datos QR para el usuario que solicitó (ahora tiene la nueva reserva)
            $requesterQrData = [
                'id' => $requesterNewReservation->id,
                'clase' => $requesterNewReservation->classSession->subject->name,
                'profesor' => $requesterNewReservation->classSession->teacher->nombre . ' ' . $requesterNewReservation->classSession->teacher->apellido,
                'aula' => $requesterNewReservation->classSession->classroom->name,
                'fecha' => Carbon::parse($requesterNewReservation->classSession->date)->format('Y-m-d'),
                'hora' => Carbon::parse($requesterNewReservation->classSession->timeSlot->start_time)->format('H:i'),
                'asiento' => $requesterNewReservation->asiento,
                'estudiante' => $requesterNewReservation->user->nombre . ' ' . $requesterNewReservation->user->apellido,
                'token' => md5($requesterNewReservation->id . $requesterNewReservation->user_id . $requesterNewReservation->created_at)
            ];

            // Encriptar los datos QR para seguridad
            $encryptedData1 = Crypt::encrypt($publisherQrData);
            $encryptedData2 = Crypt::encrypt($requesterQrData);

            // Configurar el generador de códigos QR
            $renderer = new ImageRenderer(
                new RendererStyle(400, 10),    // Tamaño y margen
                new SvgImageBackEnd()          // Formato SVG
            );
            $writer = new Writer($renderer);

            // Generar los códigos QR
            $qrCode1 = $writer->writeString($encryptedData1);
            $qrCode2 = $writer->writeString($encryptedData2);

            // Crear directorio temporal si no existe
            if (!Storage::disk('public')->exists('temp')) {
                Storage::disk('public')->makeDirectory('temp');
            }
            
            // Guardar códigos QR como archivos SVG temporales
            $qrFileName1 = 'qr-' . uniqid() . '-' . $publisherNewReservation->id . '.svg';
            Storage::disk('public')->put('temp/' . $qrFileName1, $qrCode1);

            $qrFileName2 = 'qr-' . uniqid() . '-' . $requesterNewReservation->id . '.svg';
            Storage::disk('public')->put('temp/' . $qrFileName2, $qrCode2);
            
            // Generar URLs públicas de los códigos QR
            $qrUrl1 = url('storage/temp/' . $qrFileName1);
            $qrUrl2 = url('storage/temp/' . $qrFileName2);

            // ENVIAR EMAILS DE CONFIRMACIÓN A AMBOS USUARIOS
            try {
                // Email al usuario que publicó la reserva
                Mail::to($publisherEmail)->send(new ExchangeConfirmation(
                    $publisherNewReservation
                ));

                // Email al usuario que solicitó el intercambio
                Mail::to($requesterEmail)->send(new ExchangeConfirmation(
                    $requesterNewReservation
                ));
            } catch (\Exception $emailError) {
                // Si falla el email, loggear pero no fallar el intercambio
                Log::error('Error enviando emails de confirmación de intercambio: ' . $emailError->getMessage());
            }

            // Eliminar la publicación después de completar el intercambio exitosamente
            $exchangeRequest->exchangePost->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Intercambio realizado correctamente'
            ]);

        } catch (\Exception $e) {
            // Si algo falla, revertir todos los cambios
            DB::rollBack();
            Log::error('Error en intercambio: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar el intercambio'
            ], 500);
        }
    }
    
    /**
     * Verifica si una reserva ya está publicada para intercambio
     * 
     * Utilizado por el frontend para evitar publicaciones duplicadas
     * 
     * @param int $reservationId ID de la reserva
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkReservation($reservationId)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Verificar si existe una publicación activa para esta reserva
            $exists = ExchangePost::where('reservation_id', $reservationId)
                ->exists();

            return response()->json([
                'status' => 'success',
                'exists' => $exists
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking reservation: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al verificar la reserva'
            ], 500);
        }
    }

    /**
     * Obtiene las solicitudes de intercambio realizadas por el usuario actual
     * 
     * Devuelve el historial de todas las solicitudes que el usuario ha enviado
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyRequests()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Buscar todas las solicitudes del usuario con relaciones completas
            $requests = ExchangeRequest::with([
                'reservation.classSession.subject',                    // Reserva que ofrece el usuario
                'reservation.classSession.classroom',
                'reservation.classSession.timeSlot',
                'exchangePost.reservation.classSession.subject',      // Reserva que solicita
                'exchangePost.reservation.classSession.classroom',
                'exchangePost.reservation.classSession.timeSlot',
                'exchangePost.reservation.user'                       // Dueño de la reserva solicitada
            ])
            // Solo solicitudes donde el usuario es quien ofrece la reserva
            ->whereHas('reservation', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderByDesc('created_at')      // Más recientes primero
            ->get()
            // Formatear datos para el frontend
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'estado' => $request->estado,
                    // Reserva que el usuario ofrece en el intercambio
                    'offered_reservation' => [
                        'subject' => $request->reservation->classSession->subject->name,
                        'date' => $request->reservation->classSession->date,
                        'time' => $request->reservation->classSession->timeSlot->start_time,
                        'classroom' => $request->reservation->classSession->classroom->name,
                        'asiento' => $request->reservation->asiento
                    ],
                    // Reserva que el usuario quiere obtener
                    'requested_reservation' => [
                        'subject' => $request->exchangePost->reservation->classSession->subject->name,
                        'date' => $request->exchangePost->reservation->classSession->date,
                        'time' => $request->exchangePost->reservation->classSession->timeSlot->start_time,
                        'classroom' => $request->exchangePost->reservation->classSession->classroom->name,
                        'asiento' => $request->exchangePost->reservation->asiento,
                        // Nombre del dueño de la reserva solicitada
                        'owner' => $request->exchangePost->reservation->user->nombre . ' ' . 
                                $request->exchangePost->reservation->user->apellido
                    ]
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $requests
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching exchange requests: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cargar las solicitudes'
            ], 500);
        }
    }

    /**
     * Rechaza una solicitud de intercambio
     * 
     * Permite al dueño de una publicación rechazar una solicitud específica
     * 
     * @param int $id ID de la solicitud
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectRequest($id)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Buscar la solicitud
            $request = ExchangeRequest::findOrFail($id);
            
            // Verificar que el usuario es el dueño de la publicación
            if ($request->exchangePost->reservation->user_id !== Auth::id()) {
                throw new \Exception('No autorizado para rechazar esta solicitud');
            }

            // Actualizar el estado de la solicitud
            $request->update(['estado' => 'Rechazada']);

            return response()->json([
                'status' => 'success',
                'message' => 'Solicitud rechazada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al rechazar solicitud: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al rechazar la solicitud'
            ], 500);
        }
    }

    /**
     * Cancela una solicitud de intercambio propia
     * 
     * Permite al usuario cancelar una solicitud que él mismo envió
     * 
     * @param int $id ID de la solicitud
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyRequest($id)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();

        try {
            // Buscar la solicitud
            $request = ExchangeRequest::findOrFail($id);
            
            // Verificar que el usuario es el dueño de la solicitud
            if ($request->reservation->user_id !== Auth::id()) {
                throw new \Exception('No autorizado para cancelar esta solicitud');
            }

            // Eliminar la solicitud completamente
            $request->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Solicitud cancelada correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cancelar solicitud: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cancelar la solicitud'
            ], 500);
        }
    }
}