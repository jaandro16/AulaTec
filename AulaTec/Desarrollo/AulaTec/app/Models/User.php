<?php
// Contraseña hash para todos los usuarios por defecto "contraseña"

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'rol',
        'carrera',
        'numero_matricula',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Comprobar si el usuario es profesor
     */
    public function isTeacher()
    {
        return $this->rol === 'profesor';
    }

    /**
     * Comprobar si el usuario es alumno
     */
    public function isStudent()
    {
        return $this->rol === 'alumno';
    }

    /**
     * Relación con las asignaturas que imparte (si es profesor)
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }

    /**
     * Relación con las clases que imparte (si es profesor)
     */
    public function teachingClasses()
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * Relación con las reservas (si es alumno)
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}