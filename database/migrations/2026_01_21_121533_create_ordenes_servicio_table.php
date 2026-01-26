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
        Schema::create('ordenes_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->datetime('fecha_entrada');
            $table->integer('kilometraje_entrada');
            $table->text('falla_reportada');
            $table->string('placas')->nullable();
            $table->string('numero_serie')->nullable();
            $table->integer('kilometraje_entrega')->nullable();
            $table->datetime('fecha_entrega')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);
            $table->enum('estado', ['RECEPCION', 'REPARACION', 'ENTREGADO'])->default('RECEPCION');
            $table->text('observaciones')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_servicio');
    }
};
