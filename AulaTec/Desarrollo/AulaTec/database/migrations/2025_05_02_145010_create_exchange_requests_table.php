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
        Schema::create('exchange_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exchange_post_id');
            $table->unsignedBigInteger('reservation_id'); // Reserva del solicitante
            $table->enum('estado', ['Pendiente', 'Aceptada', 'Rechazada'])->default('Pendiente');
            $table->timestamps();

            $table->foreign('exchange_post_id')
                  ->references('id')
                  ->on('exchange_posts')
                  ->onDelete('cascade');
                  
            $table->foreign('reservation_id')
                  ->references('id')
                  ->on('reservations')
                  ->onDelete('cascade');
                  
            // Un alumno no puede solicitar dos veces el mismo intercambio
            $table->unique(['exchange_post_id', 'reservation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_requests');
    }
};