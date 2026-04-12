<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cliente::updateOrCreate(
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
