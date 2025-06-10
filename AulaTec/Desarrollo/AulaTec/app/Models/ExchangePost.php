<?php
// app/Models/ExchangePost.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangePost extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reservation_id',
        'motivo',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relación con la reserva
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function exchangeRequests()
    {
        return $this->hasMany(ExchangeRequest::class);
    }

    /**
     * Relación con las solicitudes recibidas
     */
    public function requests()
    {
        return $this->hasMany(ExchangeRequest::class);
    }

    /**
     * Desactivar la publicación de intercambio
     */
    public function deactivate()
    {
        $this->active = false;
        $this->save();
    }
}