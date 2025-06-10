<?php
// app/Models/Reservation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'class_id',
        'asiento',
        'estado',
        'justificado',
        'justificante_path',
        'qr_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'justificado' => 'boolean',
    ];

    /**
     * Relación con el alumno
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la clase
     */
    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'class_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassSession::class, 'class_id');
    }

    /**
     * Relación con la publicación de intercambio
     */
    public function exchangePost()
    {
        return $this->hasOne(ExchangePost::class, 'reservation_id');
    }

    /**
     * Relación con las solicitudes de intercambio realizadas
     */
    public function exchangeRequests()
    {
        return $this->hasMany(ExchangeRequest::class, 'reservation_id');
    }

    /**
     * Verificar si la reserva tiene una publicación de intercambio activa
     */
    public function hasActiveExchangePost()
    {
        return $this->exchangePost && $this->exchangePost->active;
    }

    /**
     * Marcar la reserva como completada (asistencia confirmada)
     */
    public function markAsCompleted()
    {
        $this->estado = 'Completada';
        $this->save();
    }

    /**
     * Subir justificante
     */
    public function uploadJustification($path)
    {
        $this->justificado = true;
        $this->justificante_path = $path;
        $this->save();
    }
}