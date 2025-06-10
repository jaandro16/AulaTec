<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ReservationConfirmation;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador de Detalles de Clase
 * 
 * Maneja toda la funcionalidad relacionada con la selección de asientos y reservas:
 * - Mostrar detalles de la clase y asientos disponibles
 * - Procesar reservas de asientos
 * - Generar códigos QR para las reservas
 * - Enviar confirmaciones por email
 * - Mostrar página de confirmación
 */
class ClassDetailsController extends Controller
{
    /**
     * Verifica que el usuario actual tenga rol de alumno
     * 
     * Esta función centraliza la verificación de permisos para todos los métodos
     * del controlador, asegurando que solo los alumnos puedan reservar asientos
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
     * Muestra los detalles de una clase específica y los asientos disponibles
     * 
     * @param string $token Token único que identifica la sesión de clase
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($token)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();
        
        try {
            // Recuperar el ID de la clase desde la sesión usando el token
            $classId = session("class_token_{$token}");
            
            // Si no existe el token en sesión, redirigir con error
            if (!$classId) {
                return redirect()->route('dashboard')
                    ->with('error', 'Sesión expirada o inválida');
            }

            // Obtener los detalles completos de la clase con todas las relaciones
            $classDetails = ClassSession::with([
                'subject',      // Materia de la clase
                'teacher',      // Profesor que imparte la clase
                'classroom',    // Aula donde se imparte
                'timeSlot',     // Horario de la clase
                'reservations'  // Reservas existentes
            ])->findOrFail($classId);

            // Extraer los números de asientos ya ocupados
            $asientosOcupados = $classDetails->reservations->pluck('asiento')->toArray();

            // Nota: El token se mantiene en sesión para permitir la reserva
            // session()->forget("class_token_{$token}"); - Comentado intencionalmente
                
            return view('modules.seleccion-asientos.index', compact('classDetails', 'asientosOcupados', 'token'));
                
        } catch (\Exception $e) {
            // En caso de error, redirigir al dashboard con mensaje de error
            return redirect()->route('dashboard')
                ->with('error', 'La clase solicitada no está disponible');
        }
    }

    /**
     * Procesa la reserva de un asiento específico
     * 
     * @param \Illuminate\Http\Request $request Datos del formulario (asiento seleccionado)
     * @param string $token Token que identifica la sesión de clase
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $token)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();
        
        try {
            // Validar que el token de sesión siga siendo válido
            $classId = session("class_token_{$token}");
            if (!$classId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sesión expirada o inválida'
                ], 400);
            }

            // Obtener la sesión de clase correspondiente
            $classSession = ClassSession::findOrFail($classId);

            // VALIDACIÓN 1: Verificar si el usuario ya tiene una reserva para esta clase
            $existingReservation = Reservation::where('user_id', Auth::user()->id)
                ->where('class_id', $classId)
                ->exists();

            if ($existingReservation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ya tienes un asiento reservado para esta clase'
                ], 400);
            }
            
            // VALIDACIÓN 2: Verificar que el asiento seleccionado esté disponible
            $asientoOcupado = $classSession->reservations()
                ->where('asiento', $request->asiento)
                ->exists();

            if ($asientoOcupado) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El asiento seleccionado ya no está disponible'
                ], 400);
            }

            // CREAR LA RESERVA
            $reservation = new Reservation([
                'user_id' => Auth::user()->id,     // ID del usuario que reserva
                'class_id' => $classId,           // ID de la clase
                'asiento' => $request->asiento,   // Número del asiento
                'estado' => 'No asistido'         // Estado inicial de la reserva
            ]);

            // Guardar la reserva en la base de datos
            $classSession->reservations()->save($reservation);

            // GENERAR CÓDIGO QR CON DATOS DE LA RESERVA
            
            // Preparar datos que irán en el código QR
            $qrData = [
                'id' => $reservation->id,
                'clase' => $reservation->classSession->subject->name,
                'profesor' => $reservation->classSession->teacher->nombre . ' ' . $reservation->classSession->teacher->apellido,
                'aula' => $reservation->classSession->classroom->name,
                'fecha' => Carbon::parse($reservation->classSession->date)->format('Y-m-d'),
                'hora' => Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i'),
                'asiento' => $reservation->asiento,
                'estudiante' => Auth::user()->name . ' ' . Auth::user()->apellido,
                'token' => md5($reservation->id . Auth::id() . $reservation->created_at) // Token de seguridad
            ];

            // Encriptar los datos para mayor seguridad
            $encryptedData = Crypt::encrypt($qrData);
            
            // Configurar el generador de códigos QR
            $renderer = new ImageRenderer(
                new RendererStyle(400, 10), // Tamaño 400x400px con margen de 10
                new SvgImageBackEnd()        // Formato SVG para mejor calidad
            );

            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($encryptedData);

            // GUARDAR QR TEMPORALMENTE PARA EL EMAIL
            
            // Crear directorio temporal si no existe
            if (!Storage::disk('public')->exists('temp')) {
                Storage::disk('public')->makeDirectory('temp');
            }
            
            // Generar nombre único para el archivo QR
            $qrFileName = 'qr-' . uniqid() . '-' . $reservation->id . '.svg';
            Storage::disk('public')->put('temp/' . $qrFileName, $qrCode);
            
            // Generar URL pública del archivo QR
            $qrUrl = url('storage/temp/' . $qrFileName);

            // ENVIAR EMAIL DE CONFIRMACIÓN
            try {
                $userEmail = Auth::user()->email;
                if ($userEmail) {
                    // Enviar email con los detalles de la reserva
                    Mail::to($userEmail)->send(new ReservationConfirmation($reservation));
                    Log::info('Email de confirmación enviado correctamente.');
                }
            } catch (\Exception $e) {
                // Si falla el email, loggear el error pero no fallar toda la reserva
                Log::error('Error enviando correo: ' . $e->getMessage());
            }

            // Responder con éxito y URL de redirección
            return response()->json([
                'status' => 'success',
                'message' => 'Reserva confirmada correctamente',
                'redirect' => route('seleccion-asientos.confirmation', $reservation->id)
            ]);

        } catch (\Exception $e) {
            // Log detallado para debugging (solo servidor)
            Log::error('Error procesando reserva: ' . $e->getMessage());
            
            // Mensaje genérico para usuario
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la reserva. Por favor, inténtalo de nuevo.'
            ], 500);
        }
    }

    /**
     * Muestra la página de confirmación de la reserva con código QR
     * 
     * @param \App\Models\Reservation $reservation Modelo de reserva (inyección de dependencia)
     * @return \Illuminate\View\View
     */
    public function confirmation(Reservation $reservation)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();
        
        // SEGURIDAD: Verificar que la reserva pertenece al usuario actual
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'No tienes permisos para ver esta reserva');
        }

        // GENERAR CÓDIGO QR PARA LA VISTA DE CONFIRMACIÓN
        
        // Preparar los mismos datos que se usaron al crear la reserva
        $qrData = [
            'id' => $reservation->id,
            'clase' => $reservation->classSession->subject->name,
            'profesor' => $reservation->classSession->teacher->nombre . ' ' . $reservation->classSession->teacher->apellido,
            'aula' => $reservation->classSession->classroom->name,
            'fecha' => Carbon::parse($reservation->classSession->date)->format('Y-m-d'),
            'hora' => Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i'),
            'asiento' => $reservation->asiento,
            'estudiante' => Auth::user()->name . ' ' . Auth::user()->apellido,
            'token' => md5($reservation->id . Auth::id() . $reservation->created_at)
        ];

        // Encriptar los datos del QR
        $encryptedData = Crypt::encrypt($qrData);

        // Configurar el renderizador QR (menor margen para la vista)
        $renderer = new ImageRenderer(
            new RendererStyle(400, 4), // Margen más pequeño para vista web
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        
        // Generar el código QR
        $qrCode = $writer->writeString($encryptedData);
        
        // Limpiar el SVG: remover declaración XML si existe
        $qrCodeSvg = trim($qrCode);
        if (strpos($qrCodeSvg, '<?xml') === 0) {
            $qrCodeSvg = substr($qrCodeSvg, strpos($qrCodeSvg, '?>') + 2);
        }

        // Retornar vista con la reserva y el código QR codificado en base64
        return view('modules.seleccion-asientos.confirmacion', [
            'reservation' => $reservation,
            'qrCode' => 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg)
        ]);
    }
}