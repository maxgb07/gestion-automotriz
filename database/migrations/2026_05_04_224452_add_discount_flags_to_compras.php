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
            $table->boolean('aplica_descuento_maniobra')->default(false)->after('monto_maniobra');
            $table->boolean('aplica_descuento_seguro')->default(false)->after('monto_seguro');
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['aplica_descuento_maniobra', 'aplica_descuento_seguro']);
        });
    }
};
