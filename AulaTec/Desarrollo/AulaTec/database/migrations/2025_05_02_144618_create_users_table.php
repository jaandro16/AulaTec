<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('rol', ['alumno', 'profesor'])->default('alumno');
            $table->string('carrera')->nullable();
            $table->string('numero_matricula')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Insertar profesores - uno por cada asignatura
        $profesores = [
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'email' => 'carlos.rodriguez@aulatec.com',
                'password' => Hash::make('contraseña'),
                'rol' => 'profesor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'María',
                'apellido' => 'García',
                'email' => 'maria.garcia@aulatec.com',
                'password' => Hash::make('contraseña'),
                'rol' => 'profesor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Juan',
                'apellido' => 'López',
                'email' => 'juan.lopez@aulatec.com',
                'password' => Hash::make('contraseña'),
                'rol' => 'profesor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'email' => 'ana.martinez@aulatec.com',
                'password' => Hash::make('contraseña'),
                'rol' => 'profesor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Sánchez',
                'email' => 'pedro.sanchez@aulatec.com',
                'password' => Hash::make('contraseña'),
                'rol' => 'profesor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($profesores);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};