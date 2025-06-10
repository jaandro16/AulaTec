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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // alumno
            $table->unsignedBigInteger('class_id');
            $table->string('asiento');
            $table->enum('estado', ['No asistido', 'Completada'])->default('No asistido');
            $table->boolean('justificado')->default(false);
            $table->string('justificante_path')->nullable();
            $table->string('qr_data')->nullable(); // Datos para generar el QR
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('class_id')
                  ->references('id')
                  ->on('classes')
                  ->onDelete('cascade');
                  
            // Un asiento solo puede ser reservado una vez por clase
            $table->unique(['class_id', 'asiento']);
            
            // Un alumno solo puede tener una reserva por clase
            // $table->unique(['user_id', 'class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::table('reservations', function (Blueprint $table) {
            $table->integer('asiento')->change();
        });
    }
};