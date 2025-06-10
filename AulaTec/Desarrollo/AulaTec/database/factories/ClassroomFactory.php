<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para crear aulas
 */
class ClassroomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Aula 101', 'Aula 102', 'Laboratorio A', 'Auditorio']), // Nombre aleatorio
            'capacity' => fake()->numberBetween(20, 50), // Capacidad entre 20 y 50 estudiantes
        ];
    }
}
