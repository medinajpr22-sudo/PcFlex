<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_borrower_id');
            $table->string('equipment_type'); // Tipo de equipo deseado (portátil, audífonos, etc.)
            $table->timestamp('reservation_date'); // Fecha en que se hizo la reserva
            $table->timestamp('desired_date')->nullable(); // Fecha deseada para recoger el equipo
            $table->enum('status', ['pendiente', 'aprobada', 'rechazada', 'completada', 'cancelada'])->default('pendiente');
            $table->text('notes')->nullable(); // Notas adicionales
            $table->unsignedBigInteger('equipment_id')->nullable(); // Equipo asignado (cuando se aprueba)
            $table->unsignedBigInteger('approved_by')->nullable(); // Bibliotecario que aprobó
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_borrower_id')->references('id')->on('borrower_users')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
