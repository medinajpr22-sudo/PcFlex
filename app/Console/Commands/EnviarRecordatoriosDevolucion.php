<?php

namespace App\Console\Commands;

use App\Events\LoanExpiringSoon;
use App\Mail\AlertaBibliotecario;
use App\Mail\AlertaVencimiento;
use App\Mail\RecordatorioDevolucion;
use App\Models\Services;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarRecordatoriosDevolucion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prestamos:enviar-recordatorios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios de devolución y alertas de vencimiento por email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando envío de recordatorios...');
        
        $now = Carbon::now();
        $recordatoriosEnviados = 0;
        $alertasEnviadas = 0;
        
        // 1. Enviar recordatorios para préstamos que están cerca de vencer (1 hora antes)
        $this->info('Buscando préstamos próximos a vencer...');
        $prestamosProximosAVencer = Services::where('status', 'pendiente')
            ->where('reminder_sent', false)
            ->whereNotNull('expected_return_date')
            ->get()
            ->filter(function ($service) use ($now) {
                $expectedReturn = Carbon::parse($service->expected_return_date);
                $horasRestantes = $now->diffInHours($expectedReturn, false);
                // Enviar recordatorio si faltan entre 0.5 y 1.5 horas
                return $horasRestantes > 0 && $horasRestantes <= 1.5;
            });

        foreach ($prestamosProximosAVencer as $service) {
            try {
                $service->load('users.contacts', 'equipment');
                $usuario = $service->users;
                $equipo = $service->equipment;
                
                if ($usuario->contacts && $usuario->contacts->email_con) {
                    $expectedReturn = Carbon::parse($service->expected_return_date);
                    $horasRestantes = round($now->diffInHours($expectedReturn, false), 1);
                    
                    Mail::to($usuario->contacts->email_con)
                        ->send(new RecordatorioDevolucion($service, $usuario, $equipo, $horasRestantes));
                    
                    // Disparar evento de notificación en tiempo real
                    broadcast(new LoanExpiringSoon($service, $horasRestantes))->toOthers();
                    
                    $service->reminder_sent = true;
                    $service->save();
                    
                    $recordatoriosEnviados++;
                    $this->info("✓ Recordatorio enviado a {$usuario->name} ({$usuario->contacts->email_con})");
                }
            } catch (\Exception $e) {
                Log::error("Error enviando recordatorio para service ID {$service->id}: " . $e->getMessage());
                $this->error("✗ Error enviando recordatorio para service ID {$service->id}");
            }
        }

        // 2. Enviar alertas para préstamos vencidos
        $this->info('Buscando préstamos vencidos...');
        $prestamosVencidos = Services::where('status', 'pendiente')
            ->where('overdue_alert_sent', false)
            ->whereNotNull('expected_return_date')
            ->get()
            ->filter(function ($service) use ($now) {
                $expectedReturn = Carbon::parse($service->expected_return_date);
                // Préstamo vencido si la fecha límite ya pasó
                return $now->isAfter($expectedReturn);
            });

        foreach ($prestamosVencidos as $service) {
            try {
                $service->load('users.contacts', 'equipment');
                $usuario = $service->users;
                $equipo = $service->equipment;
                
                if ($usuario->contacts && $usuario->contacts->email_con) {
                    $expectedReturn = Carbon::parse($service->expected_return_date);
                    $horasVencidas = round($expectedReturn->diffInHours($now, false), 1);
                    
                    Mail::to($usuario->contacts->email_con)
                        ->send(new AlertaVencimiento($service, $usuario, $equipo, $horasVencidas));
                    
                    $service->overdue_alert_sent = true;
                    $service->save();
                    
                    $alertasEnviadas++;
                    $this->error("⚠ Alerta de vencimiento enviada a {$usuario->name} ({$usuario->contacts->email_con})");
                }
            } catch (\Exception $e) {
                Log::error("Error enviando alerta de vencimiento para service ID {$service->id}: " . $e->getMessage());
                $this->error("✗ Error enviando alerta para service ID {$service->id}");
            }
        }

        // 3. Enviar resumen a bibliotecarios si hay préstamos vencidos
        if (count($prestamosVencidos) > 0) {
            $this->info('Enviando resumen a bibliotecarios...');
            $this->enviarAlertaBibliotecarios($prestamosVencidos);
        }

        // Resumen
        $this->info('');
        $this->info('═══════════════════════════════════════');
        $this->info("✓ Recordatorios enviados: {$recordatoriosEnviados}");
        $this->info("⚠ Alertas de vencimiento enviadas: {$alertasEnviadas}");
        $this->info("📋 Préstamos vencidos totales: " . count($prestamosVencidos));
        $this->info('═══════════════════════════════════════');
        $this->info('Proceso completado exitosamente');

        return Command::SUCCESS;
    }

    /**
     * Enviar alerta a todos los bibliotecarios sobre préstamos vencidos
     */
    private function enviarAlertaBibliotecarios($prestamosVencidos)
    {
        try {
            // Cargar relaciones necesarias
            $prestamosVencidos->load('users', 'equipment');
            
            // Obtener todos los usuarios con rol de bibliotecario o coordinador
            $bibliotecarios = User::role(['bibliotecario', 'coordinador'])->get();
            
            foreach ($bibliotecarios as $bibliotecario) {
                if ($bibliotecario->email) {
                    try {
                        Mail::to($bibliotecario->email)
                            ->send(new AlertaBibliotecario($prestamosVencidos));
                        
                        $this->info("📧 Alerta enviada a bibliotecario: {$bibliotecario->email}");
                    } catch (\Exception $e) {
                        Log::error("Error enviando alerta a bibliotecario {$bibliotecario->email}: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Error al enviar alertas a bibliotecarios: " . $e->getMessage());
            $this->error("✗ Error al enviar alertas a bibliotecarios");
        }
    }
}
