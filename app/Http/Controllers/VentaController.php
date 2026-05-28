<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\StockAlerta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'folio');
        $direction = $request->get('direction', 'desc');

        // Mapeo seguro de columnas
        $sortMapping = [
            'folio'   => 'ventas.id',
            'fecha'   => 'ventas.fecha',
            'cliente' => 'clientes.nombre',
            'total'   => 'ventas.total',
            'metodo'  => 'ventas.metodo_pago',
            'estado'  => 'ventas.estado',
            'factura' => 'ventas.folio_factura',
        ];

        $column = $sortMapping[$sort] ?? 'ventas.id';
        $dir = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';

        $query = Venta::query()
            ->select('ventas.*')
            ->leftJoin('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->with(['cliente', 'detalles.producto', 'detalles.servicio'])
            ->withCount('detalles');

        if ($request->has('buscar') && $request->buscar != '') {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('ventas.folio', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', function($q2) use ($buscar) {
                      $q2->where('nombre', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->has('cliente_id') && $request->cliente_id != '') {
            $query->where('ventas.cliente_id', $request->cliente_id);
        }

        if ($request->has('metodo_pago') && $request->metodo_pago != '') {
            $query->where('ventas.metodo_pago', $request->metodo_pago);
        }

        // Filtro por Periodo
        $periodo = $request->get('periodo');
        
        // Si no hay ningún parámetro de búsqueda ni periodo, por defecto es HOY
        if (!$request->has('periodo') && !$request->filled('buscar') && !$request->filled('cliente_id') && !$request->filled('metodo_pago')) {
            $periodo = 'hoy';
        }

        if ($periodo && $periodo !== 'todos') {
            $now = now();
            
            if ($periodo == 'hoy') {
                $query->whereDate('ventas.fecha', $now->toDateString());
            } elseif ($periodo == 'semana') {
                $query->whereBetween('ventas.fecha', [
                    $now->startOfWeek()->toDateString(), 
                    $now->endOfWeek()->toDateString()
                ]);
            } elseif ($periodo == 'mes') {
                $query->whereYear('ventas.fecha', $now->year)
                      ->whereMonth('ventas.fecha', $now->month);
            }
        }

        $ventas = $query->orderBy($column, $dir)->paginate(15)->withQueryString();
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::/* where('stock', '>', 0)-> */orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();
        $publicoGeneral = Cliente::where('nombre', 'PÚBLICO GENERAL')->first();

        return view('ventas.crear', compact('clientes', 'productos', 'servicios', 'publicoGeneral'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'metodo_pago' => 'required|string',
            'requiere_factura' => 'required|in:SI,NO',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Calculamos el folio basado en el siguiente ID
            $nextId = DB::table('ventas')->max('id') + 1;
            $folio = 'V-' . sprintf('%05d', $nextId);
            
            $venta = Venta::create([
                'folio' => $folio,
                'cliente_id' => $request->cliente_id,
                'fecha' => $request->fecha,
                'metodo_pago' => $request->metodo_pago,
                'requiere_factura' => $request->requiere_factura,
                'estado' => $request->metodo_pago === 'PRESTAMO' ? 'PRESTAMO' : ($request->metodo_pago === 'CREDITO' ? 'PENDIENTE' : 'PAGADA'),
                'total' => 0,
                'saldo_pendiente' => 0,
                'observaciones' => $request->observaciones,
                'user_id' => Auth::id(),
            ]);

            $total = 0;
            $productosSinStock = [];

            foreach ($request->items as $item) {
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                $total += $subtotal;

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['tipo'] === 'producto' ? $item['id'] : null,
                    'servicio_id' => $item['tipo'] === 'servicio' ? $item['id'] : null,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subtotal,
                ]);

                // Descontar stock si es producto
                if ($item['tipo'] === 'producto') {
                    $producto = Producto::find($item['id']);
                    $producto->stock -= $item['cantidad'];
                    $producto->save();

                    if ($producto->stock <= 0) {
                        $productosSinStock[] = $producto->nombre;
                    }
                }
            }

            // Actualizamos el total real y saldo
            $venta->total = $total;
            $venta->saldo_pendiente = ($venta->estado === 'PAGADA' ? 0 : $total);
            $venta->save();

            // Si fue pagada en efectivo o tarjeta, crear el registro de pago
            if ($venta->estado === 'PAGADA') {
                $venta->pagos()->create([
                    'monto' => $total,
                    'fecha_pago' => $request->fecha,
                    'metodo_pago' => $request->metodo_pago,
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();

            $mensaje = "Venta {$folio} registrada correctamente.";
            if (!empty($productosSinStock)) {
                $mensaje .= " (ADVERTENCIA: Algunos productos quedaron con stock negativo: " . implode(', ', $productosSinStock) . ")";
            }

            if ($request->ajax() || $request->wantsJson()) {
                $metodo = $request->metodo_pago;
                // PRESTAMO usa PDF (pdf_media_carta_prestamo)
                $isTicket = !in_array($metodo, ['CREDITO', 'PRESTAMO']);
                
                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'folio' => $folio,
                    'pdf_url' => route('ventas.pdf', $venta),
                    'ticket_url' => $isTicket ? route('ventas.ticket', $venta) : null,
                    'print_direct' => $isTicket
                ]);
            }

            return redirect()->route('ventas.show', $venta)->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar la venta: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'detalles.producto', 'detalles.servicio', 'pagos']);
        $productos = Producto::orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();
        return view('ventas.ver', compact('venta', 'productos', 'servicios'));
    }

    public function downloadPDF(Venta $venta)
    {
        $venta->load(['cliente', 'detalles.producto', 'detalles.servicio']);
        
        // Seleccionar vista según si es préstamo o no
        $view = ($venta->metodo_pago === 'PRESTAMO' || $venta->estado === 'PRESTAMO') 
                ? 'ventas.pdf_media_carta_prestamo' 
                : 'ventas.pdf_media_carta';

        $pdf = Pdf::loadView($view, compact('venta'));
        $pdf->setPaper([0, 0, 396, 612], 'portrait'); // Media carta aproximado
        
        return $pdf->stream("venta-{$venta->folio}.pdf");
    }

    public function showTicket(Venta $venta)
    {
        $venta->load(['cliente', 'detalles.producto', 'detalles.servicio']);
        return view('ventas.ticket_80mm', compact('venta'));
    }

    public function cancelar(Request $request, Venta $venta)
    {
        if ($venta->estado === 'CANCELADA') {
            return response()->json(['success' => false, 'message' => 'La venta ya está cancelada'], 400);
        }

        try {
            DB::beginTransaction();

            $venta->estado = 'CANCELADA';
            $venta->saldo_pendiente = 0;
            $venta->motivo_cancelacion = $request->motivo_cancelacion;
            $venta->cancelado_at = now();
            $venta->save();

            // Restaurar stock
            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto_id) {
                    $producto = $detalle->producto;
                    $producto->stock += $detalle->cantidad;
                    $producto->save();
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Venta cancelada correctamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function resolverPrestamo(Request $request, Venta $venta)
    {
        try {
            DB::beginTransaction();

            if ($request->resolucion === 'devolucion') {
                $venta->estado = 'DEVUELTO';
                // Restaurar stock
                foreach ($venta->detalles as $detalle) {
                    if ($detalle->producto_id) {
                        $producto = $detalle->producto;
                        $producto->stock += $detalle->cantidad;
                        $producto->save();
                    }
                }
            } else {
                // Se convierte en venta real
                $venta->estado = $request->metodo_pago === 'CREDITO' ? 'PENDIENTE' : 'PAGADA';
                $venta->metodo_pago = $request->metodo_pago;
                $venta->requiere_factura = $request->requiere_factura;
                $venta->saldo_pendiente = ($venta->estado === 'PAGADA' ? 0 : $venta->total);
                
                // Actualizar precios si se modificaron
                if ($request->has('items')) {
                    $nuevoTotal = 0;
                    foreach ($request->items as $detalleId => $data) {
                        $detalle = VentaDetalle::find($detalleId);
                        if ($detalle) {
                            $detalle->precio_unitario = $data['precio'];
                            $detalle->subtotal = $detalle->cantidad * $data['precio'];
                            $detalle->save();
                            $nuevoTotal += $detalle->subtotal;
                        }
                    }
                    $venta->total = $nuevoTotal;
                    $venta->saldo_pendiente = ($venta->estado === 'PAGADA' ? 0 : $nuevoTotal);
                }
            }

            $venta->save();

            DB::commit();
            
            $metodo = $venta->metodo_pago;
            $isTicket = $venta->estado === 'PAGADA' && !in_array($metodo, ['CREDITO', 'PRESTAMO']);

            return response()->json([
                'success' => true, 
                'message' => 'Préstamo resuelto correctamente',
                'print_direct' => $isTicket,
                'ticket_url' => $isTicket ? route('ventas.ticket', $venta) : null,
                'pdf_url' => route('ventas.pdf', $venta)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function registrarFactura(Request $request, Venta $venta)
    {
        $request->validate([
            'folio_factura' => 'required|string|max:50',
        ]);

        try {
            $venta->update([
                'folio_factura' => mb_strtoupper($request->folio_factura, 'UTF-8'),
                'requiere_factura' => 'SI'
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Factura registrada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
