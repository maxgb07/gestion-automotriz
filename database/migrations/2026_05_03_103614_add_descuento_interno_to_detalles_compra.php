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
            $table->decimal('descuento_interno_porcentaje', 8, 2)->default(0)->after('descuento_extra_porcentaje');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles_compra', function (Blueprint $table) {
            $table->dropColumn('descuento_interno_porcentaje');
        });
    }
};
