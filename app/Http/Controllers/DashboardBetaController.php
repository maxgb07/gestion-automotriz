<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\OrdenServicio;
use App\Models\Compra;
use App\Models\Proveedor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardBetaController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------------------------------------------------------
        // 1. Manejo de Filtros principales
        // -------------------------------------------------------------------------
        $fechaDia  = $request->input('fecha_dia',  Carbon::today()->format('Y-m-d'));
        $mesAnio   = $request->input('mes_anio',   Carbon::today()->format('Y-m'));
        $proveedorId = $request->input('proveedor_id');

        $diaSeleccionado = Carbon::parse($fechaDia);
        $mesSeleccionado = Carbon::parse($mesAnio . '-01');

        // Periodos anteriores (para comparativa en cards)
        $diaAnterior = $diaSeleccionado->copy()->subDay();
        $mesAnterior = $mesSeleccionado->copy()->subMonth();

        // -------------------------------------------------------------------------
        // 2. KPIs del Día seleccionado
        // -------------------------------------------------------------------------
        $ventasDia   = Venta::whereDate('fecha', $diaSeleccionado)->where('estado', 'PAGADA')->get();
        $ordenesDia  = OrdenServicio::whereDate('fecha_entrega', $diaSeleccionado)->where('estado', 'ENTREGADO')->get();

        // KPIs del día ANTERIOR
        $ventasDiaAnt  = Venta::whereDate('fecha', $diaAnterior)->where('estado', 'PAGADA')->get();
        $ordenesDiaAnt = OrdenServicio::whereDate('fecha_entrega', $diaAnterior)->where('estado', 'ENTREGADO')->get();

        $kpis = [
            'dia' => [
                'ventas_cantidad'  => $ventasDia->count(),
                'ventas_total'     => $ventasDia->sum('total'),
                'ordenes_cantidad' => $ordenesDia->count(),
                'ordenes_total'    => $ordenesDia->sum('total'),
                // Comparativa día anterior
                'prev_ventas_cantidad'  => $ventasDiaAnt->count(),
                'prev_ventas_total'     => $ventasDiaAnt->sum('total'),
                'prev_ordenes_cantidad' => $ordenesDiaAnt->count(),
                'prev_ordenes_total'    => $ordenesDiaAnt->sum('total'),
                'prev_label'            => 'Ayer (' . $diaAnterior->translatedFormat('d M') . ')',
            ]
        ];

        // -------------------------------------------------------------------------
        // 3. KPIs del Mes seleccionado
        // -------------------------------------------------------------------------
        $ventasMes  = Venta::whereYear('fecha', $mesSeleccionado->year)
            ->whereMonth('fecha', $mesSeleccionado->month)
            ->where('estado', 'PAGADA')->get();
        $ordenesMes = OrdenServicio::whereYear('fecha_entrega', $mesSeleccionado->year)
            ->whereMonth('fecha_entrega', $mesSeleccionado->month)
            ->where('estado', 'ENTREGADO')->get();

        // KPIs del mes ANTERIOR
        $ventasMesAnt  = Venta::whereYear('fecha', $mesAnterior->year)
            ->whereMonth('fecha', $mesAnterior->month)
            ->where('estado', 'PAGADA')->get();
        $ordenesMesAnt = OrdenServicio::whereYear('fecha_entrega', $mesAnterior->year)
            ->whereMonth('fecha_entrega', $mesAnterior->month)
            ->where('estado', 'ENTREGADO')->get();

        $kpis['mes'] = [
            'ventas_cantidad'  => $ventasMes->count(),
            'ventas_total'     => $ventasMes->sum('total'),
            'ordenes_cantidad' => $ordenesMes->count(),
            'ordenes_total'    => $ordenesMes->sum('total'),
            // Comparativa mes anterior
            'prev_ventas_cantidad'  => $ventasMesAnt->count(),
            'prev_ventas_total'     => $ventasMesAnt->sum('total'),
            'prev_ordenes_cantidad' => $ordenesMesAnt->count(),
            'prev_ordenes_total'    => $ordenesMesAnt->sum('total'),
            'prev_label'            => $mesAnterior->translatedFormat('F Y'),
        ];

        // -------------------------------------------------------------------------
        // 4. Gráfica: Ingresos vs Egresos por mes (año completo)
        // -------------------------------------------------------------------------
        $year = $mesSeleccionado->year;
        $ingresosPorMes = array_fill(1, 12, 0);
        $egresosPorMes  = array_fill(1, 12, 0);

        $ventasYear = Venta::whereYear('fecha', $year)->where('estado', 'PAGADA')
            ->selectRaw('MONTH(fecha) as mes, SUM(total) as total')->groupBy('mes')->get();
        $ordenesYear = OrdenServicio::whereYear('fecha_entrega', $year)->where('estado', 'ENTREGADO')
            ->selectRaw('MONTH(fecha_entrega) as mes, SUM(total) as total')->groupBy('mes')->get();
        $comprasYear = Compra::whereYear('fecha_compra', $year)
            ->selectRaw('MONTH(fecha_compra) as mes, SUM(total) as total')->groupBy('mes')->get();

        foreach ($ventasYear  as $v) $ingresosPorMes[$v->mes] += $v->total;
        foreach ($ordenesYear as $o) $ingresosPorMes[$o->mes] += $o->total;
        foreach ($comprasYear as $c) $egresosPorMes[$c->mes]  += $c->total;

        $chartIngresosEgresos = [
            'labels'   => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            'ingresos' => array_values($ingresosPorMes),
            'egresos'  => array_values($egresosPorMes),
        ];

        // -------------------------------------------------------------------------
        // 5. Gráficas por Proveedor (mes seleccionado)
        // -------------------------------------------------------------------------
        $comprasQuery = Compra::with('proveedor')
            ->whereYear('fecha_compra', $mesSeleccionado->year)
            ->whereMonth('fecha_compra', $mesSeleccionado->month);

        if ($proveedorId) {
            $comprasQuery->where('proveedor_id', $proveedorId);
        }

        $comprasMes = $comprasQuery->get();
        $comprasPorProveedor = [];
        $totalComprasMes = 0;

        foreach ($comprasMes as $compra) {
            $nombreProv = $compra->proveedor ? $compra->proveedor->nombre : 'Desconocido';
            $comprasPorProveedor[$nombreProv] = ($comprasPorProveedor[$nombreProv] ?? 0) + $compra->total;
            $totalComprasMes += $compra->total;
        }
        arsort($comprasPorProveedor);

        $labelsProveedores  = array_keys($comprasPorProveedor);
        $dataProveedores    = array_values($comprasPorProveedor);
        $porcentajesProveedores = [];
        if ($totalComprasMes > 0) {
            foreach ($dataProveedores as $monto) {
                $porcentajesProveedores[] = round(($monto / $totalComprasMes) * 100, 2);
            }
        }

        $chartProveedores = [
            'labels'      => $labelsProveedores,
            'montos'      => $dataProveedores,
            'porcentajes' => $porcentajesProveedores,
            'total_mes'   => $totalComprasMes,
        ];

        // -------------------------------------------------------------------------
        // 6. Comparativa completa entre dos meses
        // -------------------------------------------------------------------------
        // Mes A = mes seleccionado, Mes B = mes anterior (se puede hacer configurable)
        $mesA = $mesSeleccionado;
        $mesB = $mesAnterior;

        $helper = function($year, $month) {
            $v = Venta::whereYear('fecha', $year)->whereMonth('fecha', $month)->where('estado', 'PAGADA')->get();
            $o = OrdenServicio::whereYear('fecha_entrega', $year)->whereMonth('fecha_entrega', $month)->where('estado', 'ENTREGADO')->get();
            $c = Compra::whereYear('fecha_compra', $year)->whereMonth('fecha_compra', $month)->get();
            return [
                'ventas_cantidad'  => $v->count(),
                'ventas_total'     => $v->sum('total'),
                'ordenes_cantidad' => $o->count(),
                'ordenes_total'    => $o->sum('total'),
                'compras_total'    => $c->sum('total'),
                'ingresos'         => $v->sum('total') + $o->sum('total'),
            ];
        };

        $comparativa = [
            'mesA' => array_merge($helper($mesA->year, $mesA->month), ['label' => $mesA->translatedFormat('F Y')]),
            'mesB' => array_merge($helper($mesB->year, $mesB->month), ['label' => $mesB->translatedFormat('F Y')]),
        ];

        // -------------------------------------------------------------------------
        // 7. Lista de Proveedores para filtro
        // -------------------------------------------------------------------------
        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('dashboardBeta', compact(
            'kpis', 'chartIngresosEgresos', 'chartProveedores', 'proveedores',
            'fechaDia', 'mesAnio', 'proveedorId', 'diaSeleccionado', 'mesSeleccionado',
            'comparativa', 'mesA', 'mesB'
        ));
    }
}
