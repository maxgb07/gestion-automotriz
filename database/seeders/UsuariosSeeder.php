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
        User::updateOrCreate(
            ['email' => 'david.gonzalez@example.com'],
            [
                'name' => 'David González',
                'username' => 'dgonzalez',
                'password' => '930915',
            ]
        );

        User::updateOrCreate(
            ['email' => 'maxanguiano@hotmail.com'],
            [
                'name' => 'Max Anguiano',
                'username' => 'manguiano',
                'password' => '671129',
            ]
        );

        User::updateOrCreate(
            ['email' => 'emilio.gonzalez@example.com'],
            [
                'name' => 'Emilio González',
                'username' => 'egonzalez',
                'password' => '970613',
            ]
        );

        User::updateOrCreate(
            ['email' => 'lucas.alegre@example.com'],
            [
                'name' => 'Lucas Alegre',
                'username' => 'lalegre',
                'password' => '010716',
            ]
        );

        User::updateOrCreate(
            ['email' => 'enrique.perez@example.com'],
            [
                'name' => 'Enrique Pérez',
                'username' => 'eperez',
                'password' => '731215',
            ]
        );
    }
}
