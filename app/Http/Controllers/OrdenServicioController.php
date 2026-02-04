<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Servicio;
use App\Models\Producto;
use App\Models\OrdenServicioPago;
use App\Models\OrdenServicioDetalle;
use App\Models\OrdenServicioImagen; // Added this line as it's used in eliminarImagen
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdenServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = OrdenServicio::with(['cliente', 'vehiculo', 'pagos']);

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('folio', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', function($q2) use ($buscar) {
                      $q2->where('nombre', 'like', "%{$buscar}%");
                  })
                  ->orWhereHas('vehiculo', function($q2) use ($buscar) {
                      $q2->where('placas', 'like', "%{$buscar}%")
                        ->orWhere('marca', 'like', "%{$buscar}%")
                        ->orWhere('modelo', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }

        // Filtro por Periodo
        $periodo = $request->get('periodo');
        
        // Si no hay ningún parámetro de búsqueda ni periodo, por defecto es HOY
        // Si el periodo es 'todos', no aplicamos filtro de fecha
        if (!$request->has('periodo') && !$request->filled('buscar') && !$request->filled('estado') && !$request->filled('cliente_id') && !$request->filled('vehiculo_id')) {
            $periodo = 'hoy';
        }

        if ($periodo && $periodo !== 'todos') {
            $now = now();
            
            if ($periodo == 'hoy') {
                $query->whereDate('fecha_entrada', $now->toDateString());
            } elseif ($periodo == 'semana') {
                $query->whereBetween('fecha_entrada', [
                    $now->startOfWeek()->toDateString(), 
                    $now->endOfWeek()->toDateString()
                ]);
            } elseif ($periodo == 'mes') {
                $query->whereYear('fecha_entrada', $now->year)
                      ->whereMonth('fecha_entrada', $now->month);
            }
        }

        $ordenes = $query->latest()->paginate(10)->withQueryString();

        return view('ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ordenes.crear');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha_entrada' => 'required|date',
            'kilometraje_entrada' => 'nullable|integer',
            'falla_reportada' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        $orden = new OrdenServicio();
        $orden->folio = $this->generarFolio();
        $orden->cliente_id = $request->cliente_id;
        $orden->vehiculo_id = $request->vehiculo_id;
        $orden->fecha_entrada = $request->fecha_entrada;
        $orden->kilometraje_entrada = $request->kilometraje_entrada ?? 0;
        $orden->falla_reportada = mb_strtoupper($request->falla_reportada, 'UTF-8');
        $orden->observaciones = $request->observaciones ? mb_strtoupper($request->observaciones, 'UTF-8') : null;
        $orden->estado = 'RECEPCION';
        $orden->saldo_pendiente = 0;
        $orden->total = 0;
        $orden->save();

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Orden de servicio creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrdenServicio $ordene)
    {
        $orden = $ordene->load(['cliente', 'vehiculo', 'detalles.producto', 'detalles.servicio', 'pagos', 'imagenes']);
        $productos = Producto::where('stock', '>', 0)->orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();
        
        return view('ordenes.ver', compact('orden', 'productos', 'servicios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrdenServicio $ordene)
    {
        if ($request->has('entrega')) {
            $request->validate([
                'kilometraje_entrega' => 'required|integer|min:' . $ordene->kilometraje_entrada,
                'fecha_entrega' => 'required|date',
                'placas' => 'nullable|string|max:20',
                'numero_serie' => 'nullable|string|max:50',
                'monto_pago' => 'nullable|numeric|min:0',
                'metodo_pago' => 'nullable|string',
                'referencia_pago' => 'nullable|string',
                'observaciones_post_reparacion' => 'nullable|string',
                'mecanico' => 'required|string',
            ]);

            try {
                DB::beginTransaction();

                // Determinar el nuevo estado
                $nuevoEstado = ($request->metodo_pago === 'CRÉDITO 15 DÍAS') ? 'PENDIENTE DE PAGO' : 'ENTREGADO';

                $ordene->update([
                    'kilometraje_entrega' => $request->kilometraje_entrega,
                    'fecha_entrega' => $request->fecha_entrega,
                    'placas' => mb_strtoupper($request->placas, 'UTF-8'),
                    'numero_serie' => mb_strtoupper($request->numero_serie, 'UTF-8'),
                    'observaciones_post_reparacion' => mb_strtoupper($request->observaciones_post_reparacion, 'UTF-8'),
                    'mecanico' => $request->mecanico,
                    'estado' => $nuevoEstado
                ]);

                // Actualizar también los datos actuales del vehículo
                $ordene->vehiculo->update([
                    'placas' => mb_strtoupper($request->placas, 'UTF-8'),
                    'numero_serie' => mb_strtoupper($request->numero_serie, 'UTF-8'),
                ]);

                // Registrar pago si se proporcionó uno
                if ($request->monto_pago > 0 && $request->metodo_pago !== 'CRÉDITO 15 DÍAS') {
                    $monto = floatval($request->monto_pago);
                    
                    OrdenServicioPago::create([
                        'orden_servicio_id' => $ordene->id,
                        'monto' => $monto,
                        'fecha_pago' => now(),
                        'metodo_pago' => $request->metodo_pago ?? 'EFECTIVO',
                        'referencia' => $request->referencia_pago,
                    ]);

                    $ordene->decrement('saldo_pendiente', $monto);
                }

                DB::commit();

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Orden finalizada y vehículo entregado',
                        'pdf_url' => route('ordenes.pdf', $ordene)
                    ]);
                }

                return redirect()->back()->with('success', 'Vehículo entregado exitosamente');

            } catch (\Exception $e) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        if ($request->has('cambiar_estado')) {
            $ordene->update(['estado' => $request->estado]);
            return redirect()->back()->with('success', 'Estado de la orden actualizado');
        }

        return redirect()->back();
    }

    public function agregarDetalle(Request $request, OrdenServicio $orden)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.tipo' => 'required|in:producto,servicio',
            'items.*.item_id' => 'required',
            'items.*.cantidad' => 'required|numeric|min:0.1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
        ], [
            'items.*.item_id.required' => 'Debe seleccionar un producto o servicio para cada fila.'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->items as $item) {
                $precio = $item['precio_unitario'];
                $cantidad = $item['cantidad'];
                $porcentajeDesc = $item['descuento_porcentaje'] ?? 0;
                
                $baseCalculada = $precio * $cantidad;
                $montoDesc = $baseCalculada * ($porcentajeDesc / 100);
                $subtotal = $baseCalculada - $montoDesc;

                $orden->detalles()->create([
                    'producto_id' => $item['tipo'] === 'producto' ? $item['item_id'] : null,
                    'servicio_id' => $item['tipo'] === 'servicio' ? $item['item_id'] : null,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'descuento_porcentaje' => $porcentajeDesc,
                    'descuento_monto' => $montoDesc,
                    'subtotal' => $subtotal,
                ]);

                $orden->increment('total', $subtotal);
                $orden->increment('saldo_pendiente', $subtotal);

                if ($item['tipo'] === 'producto') {
                    $producto = Producto::find($item['item_id']);
                    if ($producto->stock < $cantidad) {
                        throw new \Exception("Stock insuficiente para: " . $producto->nombre);
                    }
                    $producto->decrement('stock', $cantidad);
                }
            }

            if ($orden->estado === 'RECEPCION') {
                $orden->update(['estado' => 'REPARACION']);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ítems agregados correctamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function eliminarDetalle(OrdenServicio $orden, OrdenServicioDetalle $detalle)
    {
        DB::transaction(function() use ($orden, $detalle) {
            $orden->decrement('total', $detalle->subtotal);
            $orden->decrement('saldo_pendiente', $detalle->subtotal);

            if ($detalle->producto_id) {
                $producto = Producto::find($detalle->producto_id);
                $producto->increment('stock', $detalle->cantidad);
            }

            $detalle->delete();
        });

        return redirect()->back()->with('success', 'Item eliminado de la orden');
    }

    public function subirImagen(Request $request, OrdenServicio $orden)
    {
        // Si el POST llega vacío pero es una petición POST, usualmente es porque se superó post_max_size
        if ($request->isMethod('post') && empty($request->all()) && empty($request->file())) {
            return response()->json([
                'success' => false, 
                'message' => 'El tamaño total de los archivos supera el límite permitido por el servidor (8MB). Por favor, sube menos imágenes o archivos más pequeños.'
            ], 422);
        }

        $request->validate([
            'imagenes' => 'required|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|uploaded',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'imagenes.*.image' => 'El archivo debe ser una imagen.',
            'imagenes.*.mimes' => 'La imagen debe ser jpeg, png, jpg o gif.',
            'imagenes.*.uploaded' => 'La imagen es demasiado grande (Límite servidor: 2MB). Intenta comprimirla o subirla desde una PC.',
        ]);

        try {
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $file) {
                    $path = $file->store('ordenes/' . $orden->id, 'public');
                    $orden->imagenes()->create([
                        'ruta' => $path,
                        'descripcion' => $request->descripcion ? mb_strtoupper($request->descripcion, 'UTF-8') : null,
                    ]);
                }
            }

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Imágenes subidas correctamente']);
            }

            return redirect()->back()->with('success', 'Imágenes subidas correctamente');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al subir imágenes: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al subir imágenes: ' . $e->getMessage());
        }
    }

    public function eliminarImagen(OrdenServicio $orden, OrdenServicioImagen $imagen)
    {
        \Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();
        return redirect()->back()->with('success', 'Imagen eliminada');
    }

    public function registrarPago(Request $request, OrdenServicio $orden)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $orden->saldo_pendiente,
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
            'referencia' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request, $orden) {
            $orden->pagos()->create([
                'monto' => $request->monto,
                'fecha_pago' => $request->fecha_pago,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => mb_strtoupper($request->referencia, 'UTF-8'),
            ]);

            $orden->decrement('saldo_pendiente', $request->monto);
            
            // Si el estado era PENDIENTE DE PAGO y el saldo llega a 0, cambiar a ENTREGADO
            if ($orden->estado === 'PENDIENTE DE PAGO' && $orden->fresh()->saldo_pendiente <= 0) {
                $orden->update(['estado' => 'ENTREGADO']);
            }
        });

        return redirect()->back()->with('success', 'Pago registrado exitosamente');
    }

    public function descargarPDF(OrdenServicio $orden)
    {
        if (!extension_loaded('gd')) {
            return back()->with('error', 'La extensión PHP GD no está instalada.');
        }

        $orden->load(['cliente', 'vehiculo', 'detalles.producto', 'detalles.servicio', 'pagos']);
        
        // =========================================================================
        // CONFIGURACIÓN DE FORMATO DE IMPRESIÓN
        // =========================================================================
        // TAMAÑO CARTA (Por defecto):
        /* $vista = 'ordenes.pdf';
        $papel = 'letter'; */
        
        // TAMAÑO MEDIA CARTA:
        // Descomente las siguientes dos líneas para usar Media Carta y comente las de arriba
        $vista = 'ordenes.pdf_media_carta';
        $papel = array(0, 0, 396, 612); // 5.5 x 8.5 pulgadas
        // =========================================================================

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($vista, compact('orden'));
        $pdf->setPaper($papel);
        
        return $pdf->stream("Orden_{$orden->folio}.pdf");
    }

    public function descargarCotizacionPDF(OrdenServicio $orden)
    {
        $orden->load(['cliente', 'vehiculo', 'detalles.producto', 'detalles.servicio']);
        
        // =========================================================================
        // CONFIGURACIÓN DE FORMATO DE IMPRESIÓN (COTIZACIÓN)
        // =========================================================================
        // TAMAÑO CARTA (Por defecto):
        // $vista = 'ordenes.pdf_cotizacion';
        // $papel = 'letter';
        
        // TAMAÑO MEDIA CARTA:
        $vista = 'ordenes.pdf_media_carta_cotizacion';
        $papel = array(0, 0, 396, 612); // 5.5 x 8.5 pulgadas
        // =========================================================================

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($vista, compact('orden'));
        $pdf->setPaper($papel);
        
        return $pdf->stream("Cotizacion_{$orden->folio}.pdf");
    }

    private function generarFolio()
    {
        $ultimo = OrdenServicio::withTrashed()->latest()->first();
        $numero = $ultimo ? ((int) str_replace('OR-', '', $ultimo->folio)) + 1 : 1;
        return 'OR-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
