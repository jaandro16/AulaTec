<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para crear reservas de asientos
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),    // Crea o asocia un usuario
            'class_id' => 1,                 // ID de la clase (debe existir)
            'asiento' => fake()->randomElement(['A1', 'A2', 'B1', 'B2', 'C1', 'C2']), // Asiento aleatorio
            'estado' => fake()->randomElement(['Pendiente', 'Completada', 'Cancelada']), // Estado aleatorio
            'justificado' => fake()->boolean(20), // 20% de probabilidad de estar justificado
        ];
    }
}
