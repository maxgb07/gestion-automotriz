<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Compra;
use App\Models\Proveedor;
use Carbon\Carbon;

$compras = Compra::all();
$count = 0;

foreach ($compras as $compra) {
    $proveedor = Proveedor::find($compra->proveedor_id);
    $dias = $proveedor ? (int) $proveedor->dias_credito : 0;
    
    $fecha_compra = $compra->fecha_compra ? Carbon::parse($compra->fecha_compra) : Carbon::now();
    $compra->fecha_vencimiento = $fecha_compra->addDays($dias)->format('Y-m-d');
    
    // Move total to subtotal and recalculate if it hasn't been migrated yet
    // Assuming un-migrated rows have subtotal = 0 and iva = 0
    if ($compra->subtotal == 0 && $compra->total > 0) {
        $subtotal = $compra->total;
        $compra->subtotal = $subtotal;
        $compra->iva = $subtotal * 0.16;
        $compra->total = $compra->subtotal + $compra->iva;
    }
    
    // Si no tiene saldo pendiente o estado_pago, lo inicializamos
    if ($compra->saldo_pendiente == 0 && $compra->estado_pago == 'PENDIENTE') {
        $compra->saldo_pendiente = $compra->total;
    }
    
    if (!$compra->estado_pago) {
        $compra->estado_pago = 'PENDIENTE';
        $compra->saldo_pendiente = $compra->total;
    }

    $compra->save();
    $count++;
}

echo "Procesadas {$count} compras.\n";
