<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar metodo_pago para incluir PRESTAMO
        DB::statement("ALTER TABLE ventas MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA DE DÉBITO', 'TARJETA DE CRÉDITO', 'TRANSFERENCIA', 'CHEQUE', 'CREDITO', 'PRESTAMO')");
        
        // Actualizar estado para incluir PRESTAMO y DEVUELTO
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado ENUM('PAGADA', 'PENDIENTE', 'CANCELADA', 'PRESTAMO', 'DEVUELTO') DEFAULT 'PAGADA'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a los estados previos (si hay registros con PRESTAMO o DEVUELTO, esto podría fallar si no se limpian primero)
        DB::statement("ALTER TABLE ventas MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA DE DÉBITO', 'TARJETA DE CRÉDITO', 'TRANSFERENCIA', 'CHEQUE', 'CREDITO')");
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado ENUM('PAGADA', 'PENDIENTE', 'CANCELADA') DEFAULT 'PAGADA'");
    }
};
