<?php
// app/Models/Subject.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Relación con los profesores que imparten esta asignatura
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_subject')
                    ->where('rol', 'profesor');
    }

    /**
     * Relación con las clases de esta asignatura
     */
    public function classes()
    {
        return $this->hasMany(ClassSession::class);
    }
}