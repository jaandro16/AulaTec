<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;

/**
 * Clase de Email de Confirmación de Reserva
 * 
 * Genera y envía un email de confirmación cuando un estudiante
 * realiza una nueva reserva de asiento, incluyendo:
 * - Datos completos de la reserva
 * - PDF con código QR para verificación
 * - Información de la clase reservada
 */
class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * La reserva recién creada para confirmar
     * 
     * @var \App\Models\Reservation
     */
    public $reservation;

    /**
     * Crea una nueva instancia del mensaje de email
     *
     * @param Reservation $reservation La reserva recién creada
     */
    public function __construct(Reservation $reservation)
    {
        // Almacenar la reserva para usar en la construcción del email
        $this->reservation = $reservation;
    }

    /**
     * Construye el mensaje de email con PDF adjunto
     * 
     * Genera un email completo que incluye la vista HTML y un PDF
     * con código QR para la nueva reserva
     * 
     * @return $this
     */
    public function build()
    {
        // Logging para seguimiento del proceso de envío
        Log::info('Construyendo email para reserva ID: ' . $this->reservation->id);

        // GENERAR DATOS PARA EL CÓDIGO QR
        
        // Crear array con toda la información de la reserva
        $qrData = [
            'id' => $this->reservation->id,
            'clase' => $this->reservation->classSession->subject->name,
            'profesor' => $this->reservation->classSession->teacher->nombre . ' ' . $this->reservation->classSession->teacher->apellido,
            'aula' => $this->reservation->classSession->classroom->name,
            'fecha' => $this->reservation->classSession->date,
            'hora' => $this->reservation->classSession->timeSlot->start_time,
            'asiento' => $this->reservation->asiento,
            'estudiante' => $this->reservation->student->nombre . ' ' . $this->reservation->student->apellido,
            // Token de seguridad único para verificación posterior
            'token' => md5($this->reservation->id . $this->reservation->user_id . $this->reservation->created_at)
        ];

        // Encriptar los datos para seguridad del código QR
        $encryptedData = Crypt::encrypt($qrData);
        
        // CONFIGURAR EL GENERADOR DE CÓDIGOS QR
        
        // Configurar renderer con tamaño optimizado para PDF
        $renderer = new ImageRenderer(
            new RendererStyle(400, 10),    // Tamaño 400x400px con margen de 10
            new SvgImageBackEnd()          // Formato SVG para mejor calidad
        );

        $writer = new Writer($renderer);
        // Generar el código QR con los datos encriptados
        $qrCode = $writer->writeString($encryptedData);

        // GENERAR PDF CON CÓDIGO QR
        
        // Crear PDF usando la vista específica para reservas
        $pdf = Pdf::loadView('modules.pdfs.reservation', [
            'reservation' => $this->reservation,
            // Convertir SVG a Data URI para embedder en el PDF
            'qrCode' => 'data:image/svg+xml;base64,' . base64_encode($qrCode)
        ]);

        // CONSTRUIR Y RETORNAR EL EMAIL COMPLETO
        
        return $this->from(config('mail.from.address'), config('mail.from.name'))  // Remitente desde configuración
                    ->subject('Confirmación de tu reserva - ' . now()->format('d/m/Y'))  // Asunto con fecha actual
                    ->view('modules.emails.reservation-confirmation')              // Vista HTML del email
                    // Adjuntar el PDF generado
                    ->attachData(
                        $pdf->output(),                    // Contenido binario del PDF
                        'comprobante-reserva.pdf',         // Nombre del archivo adjunto
                        [
                            'mime' => 'application/pdf'    // Tipo MIME del archivo
                        ]
                    )
                    // Pasar datos a la vista del email
                    ->with([
                        'reservation' => $this->reservation
                    ]);
    }
}