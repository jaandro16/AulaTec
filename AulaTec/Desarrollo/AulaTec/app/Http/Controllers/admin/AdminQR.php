<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para la gestión del sistema de asistencia por QR
 * 
 * Este controlador maneja:
 * - La verificación de códigos QR
 * - El registro de asistencias
 * - La validación de permisos de profesor
 */
class AdminQR extends Controller
{
    /**
     * Verifica que el usuario tenga rol de profesor
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException Si el usuario no es profesor
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
     * Muestra la interfaz del escáner QR
     * 
     * @return \Illuminate\View\View Vista del escáner QR
     */
    public function index()
    {
        $this->checkTeacherRole();
        $titulo = 'Escanear Código QR';
        return view('modules.admin.admin_qr.index', compact('titulo'));
    }
    
    /**
     * Procesa y valida un código QR escaneado
     * 
     * Pasos:
     * 1. Desencripta los datos del QR
     * 2. Valida la reserva en base de datos
     * 3. Verifica si ya fue escaneado
     * 
     * @param Request $request Contiene los datos encriptados del QR
     * @return \Illuminate\Http\JsonResponse Resultado de la validación
     */
    public function processQR(Request $request)
    {
        $this->checkTeacherRole();
        try {
            // Desencriptar y validar QR
            $encryptedData = $request->input('qrData');
            $scannedData = Crypt::decrypt($encryptedData);

            if (!isset($scannedData['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR INCORRECTO, RESERVA NO ENCONTRADA'
                ], 400);
            }

            // Obtener reserva con relaciones
            $reservation = Reservation::with([
                'user',
                'classSession.subject',
                'classSession.teacher',
                'classSession.classroom',
                'classSession.timeSlot'
            ])->find($scannedData['id']);

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR INCORRECTO, RESERVA NO ENCONTRADA'
                ], 404);
            }

            // Preparar datos de respuesta
            $dbData = [
                'id' => $reservation->id,
                'estudiante' => $reservation->user->nombre . ' ' . $reservation->user->apellido,
                'clase' => $reservation->classSession->subject->name,
                'profesor' => $reservation->classSession->teacher->nombre . ' ' . $reservation->classSession->teacher->apellido,
                'aula' => $reservation->classSession->classroom->name,
                'fecha' => Carbon::parse($reservation->classSession->date)->format('Y-m-d'),
                'hora' => Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i'),
                'asiento' => $reservation->asiento
            ];

            // Verificar si ya fue escaneado
            if ($reservation->estado === 'Completada') {
                return response()->json([
                    'success' => true,
                    'data' => $dbData,
                    'alreadyScanned' => true,
                    'message' => 'Este QR ya ha sido escaneado previamente'
                ]);
            }

            // Devolver datos para QR válido no escaneado
            return response()->json([
                'success' => true,
                'data' => $dbData,
                'alreadyScanned' => false
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'QR INCORRECTO, RESERVA NO ENCONTRADA'
            ], 500);
        }
    }

    /**
     * Registra la asistencia de un estudiante
     * 
     * @param Request $request Contiene el ID de la reserva
     * @return \Illuminate\Http\JsonResponse Confirmación del registro
     */
    public function registrarAsistencia(Request $request)
    {
        $this->checkTeacherRole();
        try {
            $reservationId = $request->input('reservationId');
            $reservation = Reservation::findOrFail($reservationId);
            
            $reservation->estado = 'Completada';
            $reservation->save();

            return response()->json([
                'success' => true,
                'message' => 'Asistencia registrada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la asistencia'
            ], 500);
        }
    }
}
