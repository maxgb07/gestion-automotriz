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
        $clientes = [
            [
                'nombre' => 'PÚBLICO GENERAL',
                'telefono' => '0000000000',
                'celular' => '0000000000',
                'email' => 'publico@general.com',
                'direccion' => 'CIUDAD',
                'rfc' => 'XAXX010101000'
            ],
            [
                'nombre' => 'Juan Pérez',
                'direccion' => 'Av. Reforma 123, CDMX',
                'codigo_postal' => '01000',
                'rfc' => 'PERJ800101XYZ',
                'telefono' => '5512345678',
                'celular' => '5598765432',
                'email' => 'juan.perez@example.com',
            ],
            [
                'nombre' => 'María García',
                'direccion' => 'Calle 5 de Mayo 456, Guadalajara',
                'codigo_postal' => '44100',
                'rfc' => 'GARM850505ABC',
                'telefono' => '3312345678',
                'celular' => '3398765432',
                'email' => 'maria.garcia@example.com',
            ],
            [
                'nombre' => 'Talleres Mecánicos S.A. de C.V.',
                'direccion' => 'Zona Industrial, Monterrey',
                'codigo_postal' => '64000',
                'rfc' => 'TME900101123',
                'telefono' => '8112345678',
                'celular' => '8198765432',
                'email' => 'contacto@talleres.com',
            ],
        ];

        foreach ($clientes as $datosCliente) {
            $cliente = Cliente::create($datosCliente);

            // Agregar un vehículo a cada cliente
            Vehiculo::create([
                'cliente_id' => $cliente->id,
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'anio' => 2020,
                'placas' => 'ABC-1234',
                'kilometraje' => 45000,
                'observaciones' => 'Servicio de rutina',
            ]);

            if ($cliente->nombre == 'María García') {
                 Vehiculo::create([
                    'cliente_id' => $cliente->id,
                    'marca' => 'Honda',
                    'modelo' => 'Civic',
                    'anio' => 2018,
                    'placas' => 'XYZ-5678',
                    'kilometraje' => 70000,
                    'observaciones' => 'Cambio de frenos pendiente',
                ]);
            }
        }
    }
}
