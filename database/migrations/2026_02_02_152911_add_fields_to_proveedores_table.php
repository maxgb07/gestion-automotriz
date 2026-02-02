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
            $table->string('contacto_secundario')->nullable()->after('contacto');
            $table->text('marcas_productos')->nullable()->after('email');
            $table->text('observaciones')->nullable()->after('direccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropColumn(['contacto_secundario', 'marcas_productos', 'observaciones']);
        });
    }
};
