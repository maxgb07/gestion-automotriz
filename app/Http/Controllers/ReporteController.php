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
        
        $ventas = Venta::with(['cliente', 'detalles.producto', 'detalles.servicio'])
            ->whereDate('created_at', $hoy)
            ->where('estado', 'PAGADA')
            ->get();

        $ordenes = OrdenServicio::with(['cliente', 'vehiculo', 'detalles.producto', 'detalles.servicio', 'pagos'])
            ->whereDate('created_at', $hoy)
            ->where('estado', 'ENTREGADO')
            ->get();

        $pagoVentas = VentaPago::with(['venta.cliente', 'venta.pagos'])
            ->whereDate('fecha_pago', $hoy)
            ->get();

        $pagoOrdenes = OrdenServicioPago::with(['ordenServicio.cliente', 'ordenServicio.pagos'])
            ->whereDate('fecha_pago', $hoy)
            ->get();

        return view('reportes.corte', compact('ventas', 'ordenes', 'pagoVentas', 'pagoOrdenes'));
    }

    public function ventas(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $fecha_fin = $request->get('fecha_fin', Carbon::today()->format('Y-m-d'));

        // Ventas finalizadas en el periodo
        $ventas = Venta::with(['cliente', 'detalles.producto', 'detalles.servicio'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$fecha_inicio, $fecha_fin])
            ->where('estado', 'PAGADA')
            ->get();

        // Pagos recibidos en el periodo
        $pagos = VentaPago::with(['venta.cliente', 'venta.pagos', 'venta.detalles.producto', 'venta.detalles.servicio'])
            ->whereBetween(DB::raw('DATE(fecha_pago)'), [$fecha_inicio, $fecha_fin])
            ->get();

        return view('reportes.ventas', compact('ventas', 'pagos', 'fecha_inicio', 'fecha_fin'));
    }

    public function ordenes(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $fecha_fin = $request->get('fecha_fin', Carbon::today()->format('Y-m-d'));

        // Órdenes entregadas en el periodo
        $ordenes = OrdenServicio::with(['cliente', 'vehiculo', 'detalles.producto', 'detalles.servicio', 'pagos'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$fecha_inicio, $fecha_fin])
            ->where('estado', 'ENTREGADO')
            ->get();

        // Pagos recibidos en el periodo
        $pagos = OrdenServicioPago::with(['ordenServicio.cliente', 'ordenServicio.pagos', 'ordenServicio.vehiculo', 'ordenServicio.detalles.producto', 'ordenServicio.detalles.servicio'])
            ->whereBetween(DB::raw('DATE(fecha_pago)'), [$fecha_inicio, $fecha_fin])
            ->get();

        return view('reportes.ordenes', compact('ordenes', 'pagos', 'fecha_inicio', 'fecha_fin'));
    }

    public function cortePDF()
    {
        $hoy = Carbon::today();
        
        $ventas = Venta::with(['cliente', 'detalles.producto', 'detalles.servicio'])
            ->whereDate('created_at', $hoy)
            ->where('estado', 'PAGADA')
            ->get();

        $ordenes = OrdenServicio::with(['cliente', 'vehiculo', 'detalles.producto', 'detalles.servicio', 'pagos'])
            ->whereDate('created_at', $hoy)
            ->where('estado', 'ENTREGADO')
            ->get();

        $pagoVentas = VentaPago::with(['venta.cliente', 'venta.pagos'])
            ->whereDate('fecha_pago', $hoy)
            ->get();

        $pagoOrdenes = OrdenServicioPago::with(['ordenServicio.cliente', 'ordenServicio.pagos'])
            ->whereDate('fecha_pago', $hoy)
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.corte', compact('ventas', 'ordenes', 'pagoVentas', 'pagoOrdenes', 'hoy'));
        return $pdf->stream("Corte_{$hoy->format('d-m-Y')}.pdf");
    }

    public function ventasPDF(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        // Ventas finalizadas en el periodo
        $ventas = Venta::with(['cliente', 'detalles.producto', 'detalles.servicio'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$fecha_inicio, $fecha_fin])
            ->where('estado', 'PAGADA')
            ->get();

        // Pagos recibidos en el periodo
        $pagos = VentaPago::with(['venta.cliente', 'venta.pagos', 'venta.detalles.producto', 'venta.detalles.servicio'])
            ->whereBetween(DB::raw('DATE(fecha_pago)'), [$fecha_inicio, $fecha_fin])
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.ventas', compact('ventas', 'pagos', 'fecha_inicio', 'fecha_fin'));
        return $pdf->stream("Reporte_Ventas_{$fecha_inicio}_al_{$fecha_fin}.pdf");
    }

    public function ordenesPDF(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        // Órdenes entregadas en el periodo
        $ordenes = OrdenServicio::with(['cliente', 'vehiculo', 'detalles.producto', 'detalles.servicio', 'pagos'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$fecha_inicio, $fecha_fin])
            ->where('estado', 'ENTREGADO')
            ->get();

        // Pagos recibidos en el periodo
        $pagos = OrdenServicioPago::with(['ordenServicio.cliente', 'ordenServicio.pagos', 'ordenServicio.vehiculo', 'ordenServicio.detalles.producto', 'ordenServicio.detalles.servicio'])
            ->whereBetween(DB::raw('DATE(fecha_pago)'), [$fecha_inicio, $fecha_fin])
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.ordenes', compact('ordenes', 'pagos', 'fecha_inicio', 'fecha_fin'));
        return $pdf->stream("Reporte_Ordenes_{$fecha_inicio}_al_{$fecha_fin}.pdf");
    }

}
