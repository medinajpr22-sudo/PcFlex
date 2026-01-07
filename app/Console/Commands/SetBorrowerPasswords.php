<?php

namespace App\Console\Commands;

use App\Models\Borrower_users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetBorrowerPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrowers:set-passwords 
                            {--password=sena2024 : La contraseña por defecto a asignar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna contraseñas por defecto a usuarios prestamistas que no tienen contraseña';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultPassword = $this->option('password');
        
        // Contar usuarios sin contraseña
        $usersWithoutPassword = Borrower_users::whereNull('password')->count();
        
        if ($usersWithoutPassword === 0) {
            $this->info('✅ Todos los usuarios ya tienen contraseña asignada.');
            return Command::SUCCESS;
        }
        
        $this->warn("⚠️  Se encontraron {$usersWithoutPassword} usuarios sin contraseña.");
        
        if (!$this->confirm('¿Deseas asignarles la contraseña por defecto?', true)) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }
        
        $bar = $this->output->createProgressBar($usersWithoutPassword);
        $bar->start();
        
        // Actualizar usuarios
        $updated = 0;
        Borrower_users::whereNull('password')->chunk(100, function ($users) use ($defaultPassword, &$updated, $bar) {
            foreach ($users as $user) {
                $user->password = Hash::make($defaultPassword);
                $user->save();
                $updated++;
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Se actualizaron {$updated} usuarios exitosamente.");
        $this->line("📝 Contraseña asignada: <fg=yellow>{$defaultPassword}</>");
        $this->newLine();
        $this->warn('⚠️  IMPORTANTE: Los usuarios deben cambiar su contraseña en el primer inicio de sesión.');
        
        return Command::SUCCESS;
    }
}
