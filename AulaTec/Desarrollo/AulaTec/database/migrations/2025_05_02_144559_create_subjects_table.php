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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insertar asignaturas por defecto
        DB::table('subjects')->insert([
            [
                'name' => 'Programación Web',
                'code' => 'PW001',
                'description' => 'Desarrollo de aplicaciones web con tecnologías modernas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Base de Datos',
                'code' => 'BD001',
                'description' => 'Diseño y gestión de bases de datos relacionales',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sistemas Operativos',
                'code' => 'SO001',
                'description' => 'Administración y configuración de sistemas operativos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Redes de Computadores',
                'code' => 'RC001',
                'description' => 'Configuración y mantenimiento de redes informáticas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Seguridad Informática',
                'code' => 'SI001',
                'description' => 'Principios y técnicas de seguridad en sistemas informáticos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};