<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('classroom_id');
            $table->unsignedBigInteger('time_slot_id');
            $table->date('date');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('cascade');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('classroom_id')
                  ->references('id')
                  ->on('classrooms')
                  ->onDelete('cascade');
                  
            $table->foreign('time_slot_id')
                  ->references('id')
                  ->on('time_slots')
                  ->onDelete('cascade');
                  
            // Evitar que un aula tenga dos clases en la misma fecha y franja horaria
            $table->unique(['classroom_id', 'time_slot_id', 'date']);
            
            // Evitar que un profesor tenga dos clases en la misma fecha y franja horaria
            $table->unique(['user_id', 'time_slot_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};