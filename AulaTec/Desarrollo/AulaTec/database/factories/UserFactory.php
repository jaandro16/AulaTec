<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory para la creación de usuarios de prueba
 */
class UserFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo
     * Crea usuarios con rol de alumno por defecto
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),          // Genera un nombre aleatorio
            'apellido' => fake()->lastName(),         // Genera un apellido aleatorio
            'email' => fake()->unique()->safeEmail(), // Genera un email único
            'numero_matricula' => fake()->unique()->numerify('#####'), // Número de matrícula único
            'password' => Hash::make('password'),     // Contraseña encriptada
            'rol' => 'alumno',                        // Rol por defecto
            'remember_token' => Str::random(10),      // Token para "recordar usuario"
        ];
    }

    /**
     * Método para crear específicamente usuarios profesores
     */
    public function profesor()
    {
        return $this->state(function (array $attributes) {
            return [
                'rol' => 'profesor',
            ];
        });
    }
}
