<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teacher_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('cascade');
                  
            // Un profesor solo puede impartir una vez cada asignatura
            $table->unique(['user_id', 'subject_id']);
        });

        // Insertar relaciones profesor-asignatura
        // Obtener asignaturas en orden por código
        $subjects = DB::table('subjects')->orderBy('code')->get();
        
        // Obtener profesores ordenados por email (para mantener consistencia)
        $teachers = DB::table('users')
            ->where('rol', 'profesor')
            ->orderBy('email')
            ->get();

        // Crear las relaciones específicas
        $teacherSubjectData = [
            // Carlos Rodríguez → Programación Web (PW001)
            [
                'user_id' => $teachers->where('email', 'carlos.rodriguez@aulatec.com')->first()->id,
                'subject_id' => $subjects->where('code', 'PW001')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // María García → Base de Datos (BD001)
            [
                'user_id' => $teachers->where('email', 'maria.garcia@aulatec.com')->first()->id,
                'subject_id' => $subjects->where('code', 'BD001')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Juan López → Sistemas Operativos (SO001)
            [
                'user_id' => $teachers->where('email', 'juan.lopez@aulatec.com')->first()->id,
                'subject_id' => $subjects->where('code', 'SO001')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ana Martínez → Redes de Computadores (RC001)
            [
                'user_id' => $teachers->where('email', 'ana.martinez@aulatec.com')->first()->id,
                'subject_id' => $subjects->where('code', 'RC001')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Pedro Sánchez → Seguridad Informática (SI001)
            [
                'user_id' => $teachers->where('email', 'pedro.sanchez@aulatec.com')->first()->id,
                'subject_id' => $subjects->where('code', 'SI001')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insertar los datos en la tabla
        DB::table('teacher_subject')->insert($teacherSubjectData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_subject');
    }
};