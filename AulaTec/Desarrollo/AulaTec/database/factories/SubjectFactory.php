<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para crear asignaturas
 */
class SubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Matemáticas', 'Física', 'Química', 'Historia', 'Literatura']), // Nombre aleatorio
            'description' => fake()->sentence(), // Descripción aleatoria
        ];
    }
}
