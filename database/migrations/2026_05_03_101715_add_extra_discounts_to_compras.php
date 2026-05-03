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
            $table->decimal('porcentaje_descuento_extra', 5, 2)->default(0)->after('monto_descuento');
            $table->decimal('monto_descuento_extra', 12, 2)->default(0)->after('porcentaje_descuento_extra');
            $table->decimal('monto_descuento_interno', 12, 2)->default(0)->after('monto_descuento_extra');
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['porcentaje_descuento_extra', 'monto_descuento_extra', 'monto_descuento_interno']);
        });
    }
};
