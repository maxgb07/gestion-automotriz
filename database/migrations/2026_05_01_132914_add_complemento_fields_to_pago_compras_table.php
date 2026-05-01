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
            $table->string('grupo_pago_id', 36)->nullable()->after('id');
            $table->string('estado_documentos', 20)->default('COMPLETO')->after('observaciones'); // COMPLETO, PENDIENTE
            $table->string('complemento_folio', 100)->nullable()->after('estado_documentos');
            $table->date('complemento_fecha')->nullable()->after('complemento_folio');
            $table->decimal('complemento_monto', 10, 2)->nullable()->after('complemento_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pago_compras', function (Blueprint $table) {
            $table->dropColumn([
                'grupo_pago_id',
                'estado_documentos',
                'complemento_folio',
                'complemento_fecha',
                'complemento_monto'
            ]);
        });
    }
};
