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
        // 1. Expand enum to include new values in ventas
        DB::statement("ALTER TABLE ventas MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA', 'TARJETA DE DÉBITO', 'TARJETA DE CRÉDITO', 'TRANSFERENCIA', 'CHEQUE', 'CREDITO')");
        
        // 2. Update existing 'TARJETA' values
        DB::statement("UPDATE ventas SET metodo_pago = 'TARJETA DE DÉBITO' WHERE metodo_pago = 'TARJETA'");
        
        // 3. Final enum without the old 'TARJETA' value
        DB::statement("ALTER TABLE ventas MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA DE DÉBITO', 'TARJETA DE CRÉDITO', 'TRANSFERENCIA', 'CHEQUE', 'CREDITO')");

        // Repeat for venta_pagos
        DB::statement("ALTER TABLE venta_pagos MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA', 'TARJETA DE DÉBITO', 'TARJETA DE CRÉDITO', 'TRANSFERENCIA', 'CHEQUE')");
        DB::statement("UPDATE venta_pagos SET metodo_pago = 'TARJETA DE DÉBITO' WHERE metodo_pago = 'TARJETA'");
        DB::statement("ALTER TABLE venta_pagos MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA DE DÉBITO', 'TARJETA DE CRÉDITO', 'TRANSFERENCIA', 'CHEQUE')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert defined enums
        DB::statement("ALTER TABLE ventas MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'CREDITO')");
        DB::statement("UPDATE ventas SET metodo_pago = 'TARJETA' WHERE metodo_pago = 'TARJETA DE DÉBITO'");

        DB::statement("ALTER TABLE venta_pagos MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA', 'TRANSFERENCIA')");
        DB::statement("UPDATE venta_pagos SET metodo_pago = 'TARJETA' WHERE metodo_pago = 'TARJETA DE DÉBITO'");
    }
};
