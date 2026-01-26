<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'David González',
            'username' => 'dgonzalez',
            'email' => 'david.gonzalez@example.com', // Ajustado para evitar colisión con mgonzalez
            'password' => '930915',
        ]);

        User::create([
            'name' => 'Max Anguiano',
            'username' => 'manguiano',
            'email' => 'maxanguiano@hotmail.com',
            'password' => '671129',
        ]);

        User::create([
            'name' => 'Emilio González',
            'username' => 'egonzalez',
            'email' => 'emilio.gonzalez@example.com', // Ajustado para evitar colisión con mgonzalez
            'password' => '970613',
        ]);
    }
}
