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
        Schema::create('nota_credito_proveedors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->string('folio', 100);
            $table->decimal('monto_original', 10, 2);
            $table->decimal('saldo_disponible', 10, 2);
            $table->date('fecha');
            $table->string('estado', 20)->default('ACTIVA'); // ACTIVA, AGOTADA, CANCELADA
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_credito_proveedors');
    }
};
