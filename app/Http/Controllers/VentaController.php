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
        $query = Venta::with('cliente');

        if ($request->has('buscar') && $request->buscar != '') {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('folio', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', function($q2) use ($buscar) {
                      $q2->where('nombre', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->has('cliente_id') && $request->cliente_id != '') {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->has('metodo_pago') && $request->metodo_pago != '') {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        // Filtro por Periodo
        $periodo = $request->get('periodo');
        
        // Si no hay ningún parámetro de búsqueda ni periodo, por defecto es HOY
        // Si el periodo es 'todos', no aplicamos filtro de fecha
        if (!$request->has('periodo') && !$request->filled('buscar') && !$request->filled('cliente_id') && !$request->filled('metodo_pago')) {
            $periodo = 'hoy';
        }

        if ($periodo && $periodo !== 'todos') {
            $now = now();
            
            if ($periodo == 'hoy') {
                $query->whereDate('fecha', $now->toDateString());
            } elseif ($periodo == 'semana') {
                $query->whereBetween('fecha', [
                    $now->startOfWeek()->toDateString(), 
                    $now->endOfWeek()->toDateString()
                ]);
            } elseif ($periodo == 'mes') {
                $query->whereYear('fecha', $now->year)
                      ->whereMonth('fecha', $now->month);
            }
        }

        $ventas = $query->latest()->paginate(15)->withQueryString();
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
            'metodo_pago' => 'required|in:EFECTIVO,TARJETA,TRANSFERENCIA,CREDITO',
            'items' => 'required|array|min:1',
            'items.*.tipo' => 'required|in:producto,servicio',
            'items.*.id' => 'required',
            'items.*.cantidad' => 'required|numeric|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
        ], [
            'items.*.id.required' => 'Debe seleccionar un producto o servicio para cada fila.',
            'items.required' => 'Debe agregar al menos un ítem a la venta.'
        ]);

        try {
            DB::beginTransaction();

            // Generar Folio Automático
            $ultimoId = Venta::max('id') ?? 0;
            $folio = 'V-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);

            $totalVenta = 0;
            $totalDescuentoVenta = 0;
            $productosSinStock = [];

            // Primero calculamos totales para la cabecera de la venta usando los subtotales enviados (permitiendo modificaciones manuales)
            foreach ($request->items as $item) {
                $baseCalculada = $item['cantidad'] * $item['precio_unitario'];
                $subtotalEnviado = $item['subtotal'] ?? $baseCalculada;
                
                $montoDesc = max(0, $baseCalculada - $subtotalEnviado);
                
                $totalDescuentoVenta += $montoDesc;
                $totalVenta += $subtotalEnviado;
            }

            $esCredito = $request->metodo_pago === 'CREDITO';
            
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'folio' => $folio,
                'fecha' => $request->fecha,
                'total' => $totalVenta,
                'descuento' => $totalDescuentoVenta,
                'saldo_pendiente' => $esCredito ? $totalVenta : 0,
                'metodo_pago' => $request->metodo_pago,
                'estado' => $esCredito ? 'PENDIENTE' : 'PAGADA',
                'fecha_vencimiento' => $esCredito ? \Carbon\Carbon::parse($request->fecha)->addDays(15) : null,
                'observaciones' => $request->observaciones,
            ]);

            foreach ($request->items as $item) {
                $baseCalculada = $item['cantidad'] * $item['precio_unitario'];
                $subtotalItem = $item['subtotal'] ?? $baseCalculada;
                $porcentajeDesc = $item['descuento_porcentaje'] ?? 0;
                $montoDesc = max(0, $baseCalculada - $subtotalItem);

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['tipo'] === 'producto' ? $item['id'] : null,
                    'servicio_id' => $item['tipo'] === 'servicio' ? $item['id'] : null,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'descuento_porcentaje' => $porcentajeDesc,
                    'descuento_monto' => $montoDesc,
                    'subtotal' => $subtotalItem,
                ]);

                // Descontar Stock si es producto
                if ($item['tipo'] === 'producto') {
                    $producto = Producto::find($item['id']);
                    
                    if ($producto->stock < $item['cantidad']) {
                        // throw new \Exception("Stock insuficiente para el producto: {$producto->nombre}");
                        
                        // Registrar Incidencia de Stock
                        StockAlerta::create([
                            'producto_id' => $producto->id,
                            'user_id' => Auth::id() ?? 1, // Fallback a ID 1 si no hay auth (ej. seeders o consola)
                            'stock_previo' => $producto->stock,
                            'cantidad_solicitada' => $item['cantidad'],
                            'referencia_tipo' => 'VENTA',
                            'referencia_id' => $venta->id,
                            'fecha' => now(),
                        ]);

                        $productosSinStock[] = $producto->nombre;
                    }

                    $producto->stock = max(0, $producto->stock - $item['cantidad']);
                    $producto->save();
                }
            }

            DB::commit();

            $mensaje = "Venta {$folio} registrada correctamente.";
            if (!empty($productosSinStock)) {
                $mensaje .= " (ADVERTENCIA: Algunos productos quedaron con stock negativo: " . implode(', ', $productosSinStock) . ")";
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'folio' => $folio,
                    'pdf_url' => route('ventas.pdf', $venta)
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

            return back()->with('error', 'Error al registrar la venta: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'detalles.producto', 'detalles.servicio', 'pagos']);
        return view('ventas.ver', compact('venta'));
    }

    public function downloadPDF(Venta $venta)
    {
        if (!extension_loaded('gd')) {
            return response()->json([
                'success' => false,
                'message' => 'La extensión PHP GD no está instalada en el servidor. Es necesaria para generar el PDF con imágenes (logo). Por favor, contacte al administrador o instálela con: sudo apt-get install php-gd'
            ], 500);
        }

        $venta->load(['cliente', 'detalles.producto', 'detalles.servicio']);
        
        // =========================================================================
        // CONFIGURACIÓN DE FORMATO DE IMPRESIÓN
        // =========================================================================
        // TAMAÑO CARTA (Por defecto):
        /* $vista = 'ventas.pdf';
        $papel = 'letter'; */
        
        // TAMAÑO MEDIA CARTA:
        // Descomente las siguientes dos líneas para usar Media Carta y comente las de arriba
        $vista = 'ventas.pdf_media_carta';
        $papel = array(0, 0, 396, 612); // 5.5 x 8.5 pulgadas
        // =========================================================================

        $pdf = Pdf::loadView($vista, compact('venta'));
        $pdf->setPaper($papel);
        
        return $pdf->stream("Comprobante_{$venta->folio}.pdf");
    }
}
