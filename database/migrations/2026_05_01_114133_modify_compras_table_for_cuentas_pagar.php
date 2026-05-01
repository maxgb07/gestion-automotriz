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
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->after('total'); // keeping it after total for ease, though ideally before
            $table->decimal('porcentaje_descuento', 5, 2)->default(0)->after('subtotal');
            $table->decimal('monto_descuento', 10, 2)->default(0)->after('porcentaje_descuento');
            $table->decimal('iva', 10, 2)->default(0)->after('monto_descuento');
            $table->date('fecha_vencimiento')->nullable()->after('fecha_compra');
            $table->decimal('saldo_pendiente', 10, 2)->default(0)->after('iva');
            $table->string('estado_pago', 20)->default('PENDIENTE')->after('saldo_pendiente');
            $table->string('estado_complemento', 20)->default('NO_APLICA')->after('estado_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal', 
                'porcentaje_descuento', 
                'monto_descuento', 
                'iva', 
                'fecha_vencimiento', 
                'saldo_pendiente', 
                'estado_pago', 
                'estado_complemento'
            ]);
        });
    }
};
