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
        Schema::table('detalles_compra', function (Blueprint $table) {
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->after('precio_compra');
            $table->decimal('descuento_extra_porcentaje', 5, 2)->default(0)->after('descuento_porcentaje');
            $table->decimal('subtotal', 10, 2)->default(0)->after('descuento_extra_porcentaje');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles_compra', function (Blueprint $table) {
            $table->dropColumn([
                'descuento_porcentaje',
                'descuento_extra_porcentaje',
                'subtotal'
            ]);
        });
    }
};
