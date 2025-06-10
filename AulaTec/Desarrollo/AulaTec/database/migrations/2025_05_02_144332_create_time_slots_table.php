<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            
            // Agregar un índice único para evitar duplicados
            $table->unique(['start_time', 'end_time']);
        });
        
        // Insertar las franjas horarias fijas
        DB::table('time_slots')->insert([
            ['start_time' => '10:00', 'end_time' => '11:00', 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '11:00', 'end_time' => '12:00', 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '12:00', 'end_time' => '13:00', 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '15:30', 'end_time' => '16:30', 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '16:30', 'end_time' => '17:30', 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '17:30', 'end_time' => '18:30', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};