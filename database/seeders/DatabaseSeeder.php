<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario administrador principal
        User::create([
            'name' => 'Maximiliano González',
            'username' => 'mgonzalez',
            'email' => 'maxgb07@gmail.com',
            'password' => '910219',
        ]);

        $this->call([
            UsuariosSeeder::class,
            ClienteSeeder::class,
            PublicoGeneralSeeder::class,
        ]);
    }
}
