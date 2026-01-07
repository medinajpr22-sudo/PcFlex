<?php

namespace App\Console\Commands;

use App\Models\Borrower_users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestBorrower extends Command
{
    protected $signature = 'borrower:create-test';
    protected $description = 'Crea un usuario de prueba para el portal de usuarios';

    public function handle()
    {
        // Verificar si ya existe
        $existing = Borrower_users::where('number_identification', '1234567890')->first();
        
        if ($existing) {
            $this->warn('⚠️  Ya existe un usuario con identificación 1234567890');
            
            if ($this->confirm('¿Deseas actualizar su contraseña?', true)) {
                $existing->password = Hash::make('password123');
                $existing->save();
                
                $this->info('✅ Contraseña actualizada exitosamente');
                $this->newLine();
                $this->line('📝 Credenciales de acceso:');
                $this->line('   URL: http://localhost/borrower/login');
                $this->line('   Identificación: 1234567890');
                $this->line('   Contraseña: password123');
            }
            
            return Command::SUCCESS;
        }
        
        // Crear nuevo usuario
        $user = new Borrower_users();
        $user->name_user = 'Juan';
        $user->lastname_user = 'Pérez';
        $user->number_identification = '1234567890';
        $user->user_type = 'estudiante';
        $user->status = 'activo';
        $user->password = Hash::make('password123');
        $user->save();
        
        $this->info('✅ Usuario de prueba creado exitosamente');
        $this->newLine();
        $this->line('📝 Credenciales de acceso:');
        $this->line('   URL: http://localhost/borrower/login');
        $this->line('   Identificación: 1234567890');
        $this->line('   Contraseña: password123');
        $this->newLine();
        $this->comment('Nota: Puedes cambiar la contraseña después de iniciar sesión');
        
        return Command::SUCCESS;
    }
}
