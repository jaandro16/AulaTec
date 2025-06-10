<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * Factory para crear sesiones de clase
 */
class ClassSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),      // Crea o asocia una asignatura
            'teacher_id' => User::factory()->state(['rol' => 'profesor']), // Crea o asocia un profesor
            'classroom_id' => Classroom::factory(),  // Crea o asocia un aula
            'timeslot_id' => TimeSlot::factory(),   // Crea o asocia una franja horaria
            'date' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'), // Fecha aleatoria
        ];
    }
}
