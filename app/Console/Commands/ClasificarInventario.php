<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClasificarInventario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventario:clasificar';

    protected $description = 'Clasifica los productos (A, B, C, Z) basado en el volumen de salida de los últimos 12 meses.';

    public function handle()
    {
        $this->info('Iniciando clasificación de inventario (Análisis ABC)...');

        $fecha_inicio = \Carbon\Carbon::now()->subMonths(12)->startOfDay();
        $fecha_fin = \Carbon\Carbon::now()->endOfDay();

        $this->info("Periodo analizado: {$fecha_inicio->format('d/m/Y')} al {$fecha_fin->format('d/m/Y')}");

        $productos = \App\Models\Producto::all();

        // Arrays temporales para guardar volúmenes y totales
        $volumen_productos = [];
        $volumen_total = 0;

        $this->info('Calculando volúmenes de venta por producto...');
        $bar = $this->output->createProgressBar($productos->count());

        foreach ($productos as $producto) {
            $ventasQty = \Illuminate\Support\Facades\DB::table('venta_detalles')
                ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->where('venta_detalles.producto_id', $producto->id)
                ->whereBetween('ventas.created_at', [$fecha_inicio, $fecha_fin])
                ->sum('venta_detalles.cantidad');

            $ordenesQty = \Illuminate\Support\Facades\DB::table('orden_servicio_detalles')
                ->join('ordenes_servicio', 'orden_servicio_detalles.orden_servicio_id', '=', 'ordenes_servicio.id')
                ->where('orden_servicio_detalles.producto_id', $producto->id)
                ->whereBetween('ordenes_servicio.created_at', [$fecha_inicio, $fecha_fin])
                ->sum('orden_servicio_detalles.cantidad');

            $total_salidas = $ventasQty + $ordenesQty;

            $volumen_productos[] = [
                'producto_id' => $producto->id,
                'volumen' => $total_salidas
            ];

            $volumen_total += $total_salidas;
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        if ($volumen_total == 0) {
            $this->warn('No se encontraron ventas en el periodo. Todos los productos serán clasificados como Z.');
            \App\Models\Producto::query()->update(['clasificacion' => 'Z']);
            return;
        }

        // Ordenar productos de mayor a menor volumen
        usort($volumen_productos, function ($a, $b) {
            return $b['volumen'] <=> $a['volumen'];
        });

        $this->info("Volumen total de piezas movidas: {$volumen_total}");
        $this->info('Asignando clasificaciones (A=80%, B=15%, C=5%, Z=0%)...');

        $volumen_acumulado = 0;
        $conteo_a = 0; $conteo_b = 0; $conteo_c = 0; $conteo_z = 0;

        // Limite de porcentajes (80% para A, 95% para B (80+15), resto para C)
        $limite_a = $volumen_total * 0.80;
        $limite_b = $volumen_total * 0.95;

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            foreach ($volumen_productos as $item) {
                $producto_id = $item['producto_id'];
                $volumen = $item['volumen'];

                if ($volumen == 0) {
                    $clasificacion = 'Z';
                    $conteo_z++;
                } else {
                    $volumen_acumulado += $volumen;

                    if ($volumen_acumulado <= $limite_a) {
                        $clasificacion = 'A';
                        $conteo_a++;
                    } elseif ($volumen_acumulado <= $limite_b) {
                        $clasificacion = 'B';
                        $conteo_b++;
                    } else {
                        $clasificacion = 'C';
                        $conteo_c++;
                    }
                }

                \Illuminate\Support\Facades\DB::table('productos')
                    ->where('id', $producto_id)
                    ->update(['clasificacion' => $clasificacion]);
            }

            \Illuminate\Support\Facades\DB::commit();

            $this->info('¡Clasificación completada con éxito!');
            $this->table(
                ['Clasificación', 'Cantidad de Productos'],
                [
                    ['A (Alta Rotación)', $conteo_a],
                    ['B (Media Rotación)', $conteo_b],
                    ['C (Baja Rotación)', $conteo_c],
                    ['Z (Sin Movimiento)', $conteo_z],
                ]
            );

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            $this->error('Ocurrió un error durante la clasificación: ' . $e->getMessage());
        }
    }
}
