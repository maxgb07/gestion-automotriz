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
            $table->decimal('porcentaje_pronto_pago', 5, 2)->default(0)->after('monto_descuento_interno');
            $table->decimal('monto_pronto_pago', 12, 2)->default(0)->after('porcentaje_pronto_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['porcentaje_pronto_pago', 'monto_pronto_pago']);
        });
    }
};
