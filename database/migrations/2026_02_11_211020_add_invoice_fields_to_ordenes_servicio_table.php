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
        Schema::table('ordenes_servicio', function (Blueprint $table) {
            $table->string('folio_factura')->nullable()->after('requiere_factura');
            $table->date('fecha_factura')->nullable()->after('folio_factura');
            $table->string('uuid_factura', 50)->nullable()->after('fecha_factura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes_servicio', function (Blueprint $table) {
            $table->dropColumn(['folio_factura', 'fecha_factura', 'uuid_factura']);
        });
    }
};
