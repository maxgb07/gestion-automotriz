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
            $table->string('rfc')->nullable()->after('nombre');
            $table->string('banco')->nullable()->after('observaciones');
            $table->string('clabe_interbancaria')->nullable()->after('banco');
            $table->string('cuenta_bancaria')->nullable()->after('clabe_interbancaria');
            $table->string('titular_cuenta')->nullable()->after('cuenta_bancaria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropColumn(['rfc', 'banco', 'clabe_interbancaria', 'cuenta_bancaria', 'titular_cuenta']);
        });
    }
};
