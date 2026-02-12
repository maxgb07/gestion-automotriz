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
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('requiere_factura', ['SI', 'NO'])->default('NO')->after('observaciones');
            $table->string('folio_factura')->nullable()->after('requiere_factura');
            $table->datetime('fecha_factura')->nullable()->after('folio_factura');
            $table->string('uuid_factura')->nullable()->after('fecha_factura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['requiere_factura', 'folio_factura', 'fecha_factura', 'uuid_factura']);
        });
    }
};
