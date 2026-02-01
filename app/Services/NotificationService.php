<?php

namespace App\Services;

use App\Models\OrdenServicio;
use App\Models\Venta;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationService
{
    public function getAlerts(): Collection
    {
        $alerts = collect();

        // 1. Servicios (Taller)
        $this->checkLongRepairs($alerts);

        // 2. Cobranza (Órdenes y Ventas)
        $this->checkPendingOrderPayments($alerts);
        $this->checkPendingSalePayments($alerts);

        // 3. Inventario
        $this->checkLowStock($alerts);

        // Retornamos la colección sin agrupar aquí, lo haremos en la vista para ser flexibles
        // Pero ordenamos por severidad y fecha dentro de todo
        return $alerts->sort(function ($a, $b) {
            // Orden personalizado de categorías para que aparezcan en el orden solicitado si no se agrupan, 
            // pero el usuario pidió separación visual, así que el orden de inserción/agrupamiento importa más.
            return $a['date'] <=> $b['date'];
        })->values();
    }

    private function checkLongRepairs(Collection $alerts)
    {
        $orders = OrdenServicio::where('estado', 'REPARACION')
            ->where('fecha_entrada', '<', Carbon::now()->subDays(3))
            ->get();

        foreach ($orders as $order) {
            // Usar floatDiffInDays para precisión y round para redondeo estándar (3.5 -> 4, 3.4 -> 3)
            $days = round($order->fecha_entrada->floatDiffInDays(Carbon::now()));
            
            $alerts->push([
                'category' => 'servicios',
                'type' => 'warning',
                'icon' => 'fa-wrench',
                'message' => "Orden #{$order->folio}: {$days} días en taller",
                'url' => route('ordenes.show', $order->id),
                'date' => $order->fecha_entrada
            ]);
        }
    }

    private function checkPendingOrderPayments(Collection $alerts)
    {
        $orders = OrdenServicio::where('saldo_pendiente', '>', 0)
            ->whereIn('estado', ['PENDIENTE DE PAGO', 'ENTREGADO']) 
            ->get();

        foreach ($orders as $order) {
            $fechaBase = $order->fecha_entrega ?? $order->fecha_entrada;
            if (!$fechaBase) continue;

            $diasTranscurridos = round($fechaBase->floatDiffInDays(Carbon::now()));
            
            if ($diasTranscurridos >= 15) {
                $alerts->push([
                    'category' => 'cobranza',
                    'type' => 'critical',
                    'icon' => 'fa-file-invoice-dollar',
                    'message' => "Orden #{$order->folio} vencida ({$diasTranscurridos} días)",
                    'url' => route('ordenes.show', $order->id),
                    'date' => $fechaBase
                ]);
            } elseif ($diasTranscurridos >= 10) {
                $alerts->push([
                    'category' => 'cobranza',
                    'type' => 'warning',
                    'icon' => 'fa-clock',
                    'message' => "Orden #{$order->folio} por vencer ({$diasTranscurridos} días)",
                    'url' => route('ordenes.show', $order->id),
                    'date' => $fechaBase
                ]);
            }
        }
    }

    private function checkPendingSalePayments(Collection $alerts)
    {
        $ventas = Venta::where('saldo_pendiente', '>', 0)
            ->where('estado', 'PENDIENTE')
            ->get();

        foreach ($ventas as $venta) {
            $vencimiento = $venta->fecha_vencimiento ?? $venta->fecha->addDays(15);
            $hoy = Carbon::now();

            if ($hoy->gt($vencimiento)) {
                $diasVencido = round($hoy->floatDiffInDays($vencimiento));
                $alerts->push([
                    'category' => 'cobranza',
                    'type' => 'critical',
                    'icon' => 'fa-cash-register',
                    'message' => "Venta #{$venta->folio} vencida hace {$diasVencido} días",
                    'url' => route('ventas.show', $venta->id), 
                    'date' => $vencimiento
                ]);
            } else {
                $diasRestantes = round($hoy->floatDiffInDays($vencimiento));
                if ($diasRestantes <= 5) {
                    $alerts->push([
                        'category' => 'cobranza',
                        'type' => 'warning',
                        'icon' => 'fa-exclamation-circle',
                        'message' => "Venta #{$venta->folio} vence en {$diasRestantes} días",
                        'url' => route('ventas.show', $venta->id),
                        'date' => $vencimiento
                    ]);
                }
            }
        }
    }

    private function checkLowStock(Collection $alerts)
    {
        $products = Producto::whereColumn('stock', '<=', 'stock_minimo')->get();

        foreach ($products as $producto) {
            $alerts->push([
                'category' => 'stock',
                'type' => 'critical',
                'icon' => 'fa-box-open',
                'message' => "Stock Bajo: {$producto->nombre} ({$producto->stock})",
                'url' => route('productos.index', ['search' => $producto->sku]),
                'date' => Carbon::now()
            ]);
        }
    }

    /**
     * Obtiene órdenes en reparación de >= 3 días si es el penúltimo o último día del mes.
     */
    public function getEndOfMonthRepairs(): ?array
    {
        $hoy = Carbon::now();
        $ultimoDiaMes = (clone $hoy)->endOfMonth();
        $penultimoDiaMes = (clone $ultimoDiaMes)->subDay();

        // Verificar si es penúltimo o último día
        if (!$hoy->isSameDay($ultimoDiaMes) && !$hoy->isSameDay($penultimoDiaMes)) {
            return null;
        }

        $orders = OrdenServicio::where('estado', 'REPARACION')
            ->get();

        if ($orders->isEmpty()) {
            return null;
        }

        return [
            'count' => $orders->count(),
            'folios' => $orders->pluck('folio')->implode(', ')
        ];
    }
    /**
     * Obtiene órdenes en reparación que son de meses anteriores al actual.
     */
    public function getPreviousMonthRepairs(): ?array
    {
        $inicioMesActual = Carbon::now()->startOfMonth();

        $orders = OrdenServicio::where('estado', 'REPARACION')
            ->where('fecha_entrada', '<', $inicioMesActual)
            ->get();

        if ($orders->isEmpty()) {
            return null;
        }

        return [
            'count' => $orders->count(),
            'folios' => $orders->pluck('folio')->implode(', ')
        ];
    }
}
