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
        Schema::table('compras', function (Blueprint $row) {
            $row->decimal('monto_maniobra', 12, 2)->default(0)->after('monto_descuento_interno');
            $row->decimal('monto_seguro', 12, 2)->default(0)->after('monto_maniobra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $row) {
            $row->dropColumn(['monto_maniobra', 'monto_seguro']);
        });
    }
};
