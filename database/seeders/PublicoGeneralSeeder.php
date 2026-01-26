<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublicoGeneralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Cliente::updateOrCreate(
            ['nombre' => 'PÚBLICO GENERAL'],
            [
                'telefono' => '0000000000',
                'celular' => '0000000000',
                'email' => 'publico@general.com',
                'direccion' => 'CIUDAD',
                'rfc' => 'XAXX010101000'
            ]
        );
    }
}
