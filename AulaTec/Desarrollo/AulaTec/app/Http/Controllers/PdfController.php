<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de Generación de PDFs
 * 
 * Maneja la generación y descarga de PDFs de confirmación de reservas:
 * - Generar códigos QR con datos encriptados
 * - Crear PDF con los detalles de la reserva
 * - Proporcionar descarga directa del archivo
 */
class PdfController extends Controller
{
    /**
     * Genera y descarga un PDF de confirmación de reserva
     * 
     * Crea un PDF que incluye todos los detalles de la reserva junto
     * con un código QR encriptado para verificación
     * 
     * @param \Illuminate\Http\Request $request Contiene el ID de la reserva
     * @return \Illuminate\Http\Response Descarga directa del PDF
     */
    public function downloadPdf(Request $request)
    {
        // OBTENER Y VALIDAR LA RESERVA
        
        // Buscar la reserva asegurando que pertenece al usuario autenticado
        $reservation = Reservation::where('id', $request->reservation_id)
            ->where('user_id', Auth::user()->id)  // Verificación de seguridad
            ->firstOrFail();                      // Lanza 404 si no existe

        // PREPARAR DATOS PARA EL CÓDIGO QR
        
        // Crear array con todos los datos importantes de la reserva
        $qrData = [
            'id' => $reservation->id,
            'clase' => $reservation->classSession->subject->name,
            'profesor' => $reservation->classSession->teacher->nombre . ' ' . $reservation->classSession->teacher->apellido,
            'aula' => $reservation->classSession->classroom->name,
            'fecha' => Carbon::parse($reservation->classSession->date)->format('Y-m-d'),
            'hora' => Carbon::parse($reservation->classSession->timeSlot->start_time)->format('H:i'),
            'asiento' => $reservation->asiento,
            'estudiante' => $reservation->student->nombre . ' ' . $reservation->student->apellido,
            'token' => md5($reservation->id . Auth::id() . $reservation->created_at)  // Token de seguridad único
        ];

        // Encriptar los datos para mayor seguridad del QR
        $encryptedData = Crypt::encrypt($qrData);

        // GENERAR CÓDIGO QR
        
        // Configurar el renderizador QR con tamaño optimizado para PDF
        $renderer = new ImageRenderer(
            new RendererStyle(200, 4),    // Tamaño 200x200px con margen de 4
            new SvgImageBackEnd()         // Formato SVG para mejor calidad en PDF
        );

        $writer = new Writer($renderer);
        
        // Generar el código QR con los datos encriptados
        $qrCode = $writer->writeString($encryptedData);

        // PROCESAR EL SVG PARA USO EN PDF
        
        // Limpiar el SVG generado
        $qrCodeSvg = trim($qrCode);
        
        // Remover la declaración XML si existe (para evitar conflictos en el PDF)
        if (strpos($qrCodeSvg, '<?xml') === 0) {
            $qrCodeSvg = substr($qrCodeSvg, strpos($qrCodeSvg, '?>') + 2);
        }
        
        // Convertir a Data URI para embedder en el PDF
        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        // GENERAR EL PDF
        
        // Cargar la vista del PDF con los datos de la reserva y el QR
        $pdf = Pdf::loadView('modules.pdfs.reservation', [
            'reservation' => $reservation,    // Datos completos de la reserva
            'qrCode' => $qrDataUri           // Código QR como Data URI
        ]);
        
        // DESCARGAR EL ARCHIVO
        
        // Crear nombre de archivo con la fecha de la clase
        $fecha = $reservation->classSession->date->format('Y-m-d');
        
        // Retornar el PDF como descarga directa
        return $pdf->download("confirmacion-reserva-{$fecha}.pdf");
    }
}