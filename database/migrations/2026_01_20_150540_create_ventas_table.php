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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('folio')->unique();
            $table->datetime('fecha');
            $table->decimal('total', 12, 2);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);
            $table->enum('metodo_pago', ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'CREDITO']);
            $table->enum('estado', ['PAGADA', 'PENDIENTE', 'CANCELADA'])->default('PAGADA');
            $table->date('fecha_vencimiento')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
