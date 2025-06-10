<?php
// app/Models/ExchangeRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exchange_post_id',
        'reservation_id',
        'estado'
    ];

    /**
     * Relación con la publicación de intercambio
     */
    public function exchangePost()
    {
        return $this->belongsTo(ExchangePost::class);
    }

    protected $casts = [
        'estado' => 'string'
    ];

    /**
     * Relación con la reserva del solicitante
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Aceptar la solicitud de intercambio
     */
    public function accept()
    {
        // Comprobar que la solicitud está pendiente
        if ($this->estado !== 'Pendiente') {
            return false;
        }

        // Obtener las dos reservas a intercambiar
        $postReservation = $this->exchangePost->reservation;
        $requestReservation = $this->reservation;

        // Realizar el intercambio de alumnos
        $tempUserId = $postReservation->user_id;
        $postReservation->user_id = $requestReservation->user_id;
        $requestReservation->user_id = $tempUserId;

        // Generar nuevos QR
        $postReservation->qr_data = $this->generateQrData($postReservation);
        $requestReservation->qr_data = $this->generateQrData($requestReservation);

        // Guardar los cambios
        $postReservation->save();
        $requestReservation->save();

        // Actualizar el estado de la solicitud
        $this->estado = 'Aceptada';
        $this->save();

        // Desactivar la publicación
        $this->exchangePost->deactivate();

        return true;
    }

    /**
     * Rechazar la solicitud de intercambio
     */
    public function reject()
    {
        $this->estado = 'Rechazada';
        $this->save();
        return true;
    }

    /**
     * Generar datos para el QR
     */
    private function generateQrData($reservation)
    {
        // Este método generaría los datos necesarios para el QR
        return json_encode([
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'class_id' => $reservation->class_id,
            'asiento' => $reservation->asiento,
            'timestamp' => now()->timestamp
        ]);
    }
}