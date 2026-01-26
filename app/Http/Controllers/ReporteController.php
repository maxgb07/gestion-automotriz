<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\OrdenServicio;
use App\Models\VentaPago;
use App\Models\OrdenServicioPago;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function corteDia()
    {
        $hoy = Carbon::today();
        
        // Ventas de hoy: No canceladas 
        // Siguiendo regla: Si es crédito, solo si hubo pago hoy. 
        // Simplificado: Ventas creadas hoy que no estén en recepción/reparación (aunque ventas no tienen esos estados)
        // Aplicamos la lógica solicitada:
        $ventas = Venta::with(['cliente', 'pagos'])
            ->whereDate('created_at', $hoy)
            ->where('estado', '!=', 'CANCELADO')
            ->get()
            ->filter(function($venta) use ($hoy) {
                // Si es crédito/pendiente, verificar si hubo pago hoy
                if ($venta->estado === 'PENDIENTE') {
                    return $venta->pagos->whereBetween('fecha_pago', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->count() > 0;
                }
                return true;
            });

        // Órdenes de hoy
        $ordenes = OrdenServicio::with(['cliente', 'pagos'])
            ->whereDate('created_at', $hoy)
            ->whereNotIn('estado', ['RECEPCION', 'REPARACION'])
            ->get()
            ->filter(function($orden) use ($hoy) {
                if ($orden->estado === 'PENDIENTE DE PAGO') {
                    return $orden->pagos->whereBetween('fecha_pago', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->count() > 0;
                }
                return true;
            });

        return view('reportes.corte', compact('ventas', 'ordenes'));
    }

    public function ventas(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $fecha_fin = $request->get('fecha_fin', Carbon::today()->format('Y-m-d'));

        $ventas = Venta::with('cliente')
            ->whereBetween(DB::raw('DATE(created_at)'), [$fecha_inicio, $fecha_fin])
            ->latest()
            ->get();

        return view('reportes.ventas', compact('ventas', 'fecha_inicio', 'fecha_fin'));
    }

    public function ordenes(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $fecha_fin = $request->get('fecha_fin', Carbon::today()->format('Y-m-d'));

        $ordenes = OrdenServicio::with(['cliente', 'vehiculo', 'pagos'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$fecha_inicio, $fecha_fin])
            ->latest()
            ->get();

        return view('reportes.ordenes', compact('ordenes', 'fecha_inicio', 'fecha_fin'));
    }

    public function cortePDF()
    {
        $hoy = Carbon::today();
        // Misma lógica que corteDia para el PDF
        $ventas = Venta::with(['cliente', 'pagos'])
            ->whereDate('created_at', $hoy)
            ->where('estado', '!=', 'CANCELADO')
            ->get()
            ->filter(function($venta) use ($hoy) {
                if ($venta->estado === 'PENDIENTE') {
                    return $venta->pagos->whereBetween('fecha_pago', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->count() > 0;
                }
                return true;
            });

        $ordenes = OrdenServicio::with(['cliente', 'pagos'])
            ->whereDate('created_at', $hoy)
            ->whereNotIn('estado', ['RECEPCION', 'REPARACION'])
            ->get()
            ->filter(function($orden) use ($hoy) {
                if ($orden->estado === 'PENDIENTE DE PAGO') {
                    return $orden->pagos->whereBetween('fecha_pago', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->count() > 0;
                }
                return true;
            });

        $pdf = Pdf::loadView('reportes.pdf.corte', compact('ventas', 'ordenes', 'hoy'));
        return $pdf->stream("Corte_{$hoy->format('d-m-Y')}.pdf");
    }

    public function ventasPDF(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        $ventas = Venta::with('cliente')
            ->whereBetween(DB::raw('DATE(created_at)'), [$fecha_inicio, $fecha_fin])
            ->latest()
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.ventas', compact('ventas', 'fecha_inicio', 'fecha_fin'));
        return $pdf->stream("Reporte_Ventas_{$fecha_inicio}_al_{$fecha_fin}.pdf");
    }

    public function ordenesPDF(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        $ordenes = OrdenServicio::with(['cliente', 'vehiculo', 'pagos'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$fecha_inicio, $fecha_fin])
            ->latest()
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.ordenes', compact('ordenes', 'fecha_inicio', 'fecha_fin'));
        return $pdf->stream("Reporte_Ordenes_{$fecha_inicio}_al_{$fecha_fin}.pdf");
    }
}
