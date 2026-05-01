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
        Schema::table('pago_compras', function (Blueprint $table) {
            $table->json('ncs_informativas')->nullable()->after('complemento_monto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pago_compras', function (Blueprint $table) {
            $table->dropColumn('ncs_informativas');
        });
    }
};
