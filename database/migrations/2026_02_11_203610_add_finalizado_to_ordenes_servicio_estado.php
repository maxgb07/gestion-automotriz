<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE ordenes_servicio MODIFY COLUMN estado ENUM('RECEPCION', 'REPARACION', 'FINALIZADO', 'ENTREGADO', 'PENDIENTE DE PAGO') DEFAULT 'RECEPCION'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE ordenes_servicio MODIFY COLUMN estado ENUM('RECEPCION', 'REPARACION', 'ENTREGADO', 'PENDIENTE DE PAGO') DEFAULT 'RECEPCION'");
    }
};
