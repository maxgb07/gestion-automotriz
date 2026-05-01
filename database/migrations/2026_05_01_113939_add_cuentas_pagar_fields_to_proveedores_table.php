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
        Schema::table('proveedores', function (Blueprint $table) {
            $table->integer('dias_credito')->default(0)->after('observaciones');
            $table->decimal('porcentaje_descuento_global', 5, 2)->default(0.00)->after('dias_credito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropColumn(['dias_credito', 'porcentaje_descuento_global']);
        });
    }
};
