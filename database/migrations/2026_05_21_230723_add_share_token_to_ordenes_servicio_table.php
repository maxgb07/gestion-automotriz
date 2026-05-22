<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ordenes_servicio', function (Blueprint $table) {
            $table->string('share_token', 64)->nullable()->unique()->after('estado');
        });

        // Generar tokens para registros existentes que ya estén finalizados o entregados
        $ordenes = DB::table('ordenes_servicio')
            ->whereIn('estado', ['FINALIZADO', 'ENTREGADO'])
            ->whereNull('share_token')
            ->get();

        foreach ($ordenes as $orden) {
            DB::table('ordenes_servicio')
                ->where('id', $orden->id)
                ->update(['share_token' => Str::random(32)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes_servicio', function (Blueprint $table) {
            $table->dropColumn('share_token');
        });
    }
};
