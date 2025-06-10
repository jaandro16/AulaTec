<?php
// app/Models/TimeSlot.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start_time',
        'end_time',
    ];

    /**
     * Obtener la representación de texto de la franja horaria
     */
    public function getFormattedTimeAttribute()
    {
        return $this->start_time . ' - ' . $this->end_time;
    }

    /**
     * Relación con las clases que usan esta franja horaria
     */
    public function classes()
    {
        return $this->hasMany(ClassSession::class);
    }
}