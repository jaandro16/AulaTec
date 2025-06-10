<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para crear franjas horarias
 */
class TimeSlotFactory extends Factory
{
    public function definition(): array
    {
        $startHour = fake()->numberBetween(8, 16);           // Hora de inicio entre 8 y 16
        $startTime = sprintf('%02d:00:00', $startHour);      // Formato HH:00:00
        $endTime = sprintf('%02d:00:00', $startHour + 1);    // Hora siguiente

        return [
            'start_time' => $startTime,  // Hora de inicio
            'end_time' => $endTime,      // Hora de fin (1 hora después)
        ];
    }
}
