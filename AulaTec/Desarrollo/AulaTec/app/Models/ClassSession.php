<?php
// app/Models/ClassSession.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_id',
        'user_id',
        'classroom_id',
        'time_slot_id',
        'date',
        'descripcion',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relación con la asignatura
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relación con el profesor
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el aula
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relación con la franja horaria
     */
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Relación con las reservas de esta clase
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'class_id');
    }
}