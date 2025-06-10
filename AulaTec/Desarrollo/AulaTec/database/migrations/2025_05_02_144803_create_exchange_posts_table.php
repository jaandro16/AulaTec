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
        Schema::create('exchange_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->text('motivo')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('reservation_id')
                  ->references('id')
                  ->on('reservations')
                  ->onDelete('cascade');
                  
            // Una reserva solo puede tener una publicación activa
            $table->unique(['reservation_id', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_posts');
    }
};