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
        $tables = [
            'clientes',
            'vehiculos',
            'proveedores',
            'productos',
            'servicios',
            'ventas',
            'venta_detalles',
            'venta_pagos',
            'ordenes_servicio',
            'orden_servicio_detalles',
            'orden_servicio_pagos',
            'compras'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                }
                if (!Schema::hasColumn($table->getTable(), 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable();
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                }
                if (!Schema::hasColumn($table->getTable(), 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable();
                    $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'clientes',
            'vehiculos',
            'proveedores',
            'productos',
            'servicios',
            'ventas',
            'venta_detalles',
            'venta_pagos',
            'ordenes_servicio',
            'orden_servicio_detalles',
            'orden_servicio_pagos',
            'compras'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Posible que las llaves no existan si algo falló, pero usamos el array para que sea más seguro
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropForeign(['deleted_by']);
                
                $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
            });
        }
    }
};
