<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Duración del préstamo en horas (ej: 6, 12, 24)
            $table->integer('loan_duration_hours')->default(6)->after('date_ser');
            
            // Fecha y hora límite calculada para devolver
            $table->timestamp('expected_return_date')->nullable()->after('loan_duration_hours');
            
            // Controlar si ya se envió recordatorio
            $table->boolean('reminder_sent')->default(false)->after('expected_return_date');
            
            // Controlar si ya se envió alerta de vencimiento
            $table->boolean('overdue_alert_sent')->default(false)->after('reminder_sent');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['loan_duration_hours', 'expected_return_date', 'reminder_sent', 'overdue_alert_sent']);
        });
    }
};
