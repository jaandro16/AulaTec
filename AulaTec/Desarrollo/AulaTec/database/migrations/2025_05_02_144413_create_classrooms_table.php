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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('capacity')->unsigned();
            $table->timestamps();
        });

        // Insertar aulas por defecto
        DB::table('classrooms')->insert([
            [
                'name' => 'Aula 101',
                'capacity' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aula 102',
                'capacity' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aula 103',
                'capacity' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aula 201',
                'capacity' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aula 202',
                'capacity' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lab Informática 1',
                'capacity' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lab Informática 2',
                'capacity' => 27,
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
        Schema::dropIfExists('classrooms');
    }
};