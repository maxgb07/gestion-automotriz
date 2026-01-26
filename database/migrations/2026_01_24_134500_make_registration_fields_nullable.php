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
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nombre')->nullable()->change();
            $table->string('celular', 20)->nullable()->change();
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->string('marca')->nullable()->change();
            $table->string('modelo')->nullable()->change();
            $table->integer('anio')->nullable()->change();
        });

        Schema::table('proveedores', function (Blueprint $table) {
            $table->string('nombre')->nullable()->change();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->string('nombre')->nullable()->change();
            $table->decimal('precio_compra', 10, 2)->nullable()->default(null)->change();
            $table->decimal('precio_venta', 10, 2)->nullable()->default(null)->change();
            $table->integer('stock')->nullable()->default(null)->change();
            $table->integer('stock_minimo')->nullable()->default(null)->change();
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->date('fecha_compra')->nullable()->change();
            $table->decimal('total', 10, 2)->nullable()->change();
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->string('nombre')->nullable()->change();
            $table->string('sku')->nullable()->change();
            $table->decimal('precio', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting these changes might be tricky if data has been inserted with nulls.
        // We will try to revert to nullable=false, but this will fail if there are nulls.
        // For now, we will just list the operations to reverse the schema definition properly assuming no data violation.

        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nombre')->nullable(false)->change();
            $table->string('celular', 20)->nullable(false)->change();
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->string('marca')->nullable(false)->change();
            $table->string('modelo')->nullable(false)->change();
            $table->integer('anio')->nullable(false)->change();
        });

        Schema::table('proveedores', function (Blueprint $table) {
            $table->string('nombre')->nullable(false)->change();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->string('nombre')->nullable(false)->change();
            $table->decimal('precio_compra', 10, 2)->default(0)->change();
            $table->decimal('precio_venta', 10, 2)->default(0)->change();
            $table->integer('stock')->default(0)->change();
            $table->integer('stock_minimo')->default(0)->change();
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->date('fecha_compra')->nullable(false)->change();
            $table->decimal('total', 10, 2)->nullable(false)->change();
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->string('nombre')->nullable(false)->change();
            $table->string('sku')->nullable(false)->change();
            $table->decimal('precio', 10, 2)->nullable(false)->change();
        });
    }
};
