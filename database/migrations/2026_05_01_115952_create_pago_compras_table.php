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
        Schema::create('pago_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->string('forma_pago', 50); // EFECTIVO, TRANSFERENCIA, NOTA DE CREDITO
            $table->string('referencia', 100)->nullable();
            $table->string('tipo', 50)->default('PAGO NORMAL'); // PAGO NORMAL, APLICACION NC
            $table->foreignId('nota_credito_id')->nullable()->constrained('nota_credito_proveedors')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_compras');
    }
};
