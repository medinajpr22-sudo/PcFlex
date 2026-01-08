<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario admin solo si no existe
        if (!User::where('email', 'admin@pcflex.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'last_name' => 'PcFlex',
                'type_identification' => 'cc',
                'number_identification' => '1000000001',
                'sexo' => 'masculino',
                'telefono' => '3000000000',
                'direccion' => 'SENA',
                'email' => 'admin@pcflex.com', 
                'password' => Hash::make('admin123'), 
            ]);
        }

        // Crear usuarios específicos solo si no existen
        if (!User::where('email', 'juan@example.com')->exists()) {
            User::create([
                'name' => 'juan P',
                'last_name' => 'ramos',
                'type_identification' => 'cc',
                'number_identification' => '1004250142',
                'sexo' => 'masculino',
                'telefono' => '3224110856',
                'direccion' => 'la plata',
                'email' => 'juan@example.com', 
                'password' => Hash::make('password123'), 
            ]);
        }

        if (!User::where('email', 'wimer@example.com')->exists()) {
            User::create([
                'name' => 'wimer',
                'last_name' => 'vargas',
                'type_identification' => 'cc',
                'number_identification' => '1004250143', 
                'sexo' => 'masculino',
                'telefono' => '3224110857',
                'direccion' => 'la plata',
                'email' => 'wimer@example.com', 
                'password' => Hash::make('password123'), 
            ]);
        }

        $this->call(RolesAndPermissionsSeeder::class);
    }
}
