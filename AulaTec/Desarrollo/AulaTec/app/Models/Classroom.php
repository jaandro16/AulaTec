<?php
// app/Models/Classroom.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'capacity',
    ];

    /**
     * Relación con las clases que se imparten en esta aula
     */
    public function classes()
    {
        return $this->hasMany(ClassSession::class);
    }
}