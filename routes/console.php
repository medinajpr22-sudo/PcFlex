<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Programar el envío de recordatorios de devolución cada hora
Schedule::command('prestamos:enviar-recordatorios')
    ->hourly()
    ->runInBackground()
    ->withoutOverlapping()
    ->onFailure(function () {
        \Log::error('Falló el envío de recordatorios de préstamos');
    })
    ->onSuccess(function () {
        \Log::info('Recordatorios de préstamos enviados exitosamente');
    });
