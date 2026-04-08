<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use App\Models\OrdenServicio;
use App\Models\SeguimientoCredito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditoController extends Controller
{
    // ─── Umbrales de antigüedad de deuda (días) ───────────────────────────────
    private const DIAS_CRITICO = 15;
    private const DIAS_ALERTA  = 10;

    // ─── Helpers privados ─────────────────────────────────────────────────────

    /**
     * Calcula el color de estado según los días transcurridos.
     * Único lugar donde vive esa regla de negocio.
     */
    private function calcularEstadoColor(int $dias): string
    {
        if ($dias > self::DIAS_CRITICO) return 'rojo';
        if ($dias > self::DIAS_ALERTA)  return 'amarillo';
        return 'verde';
    }

    /**
     * Mapea los detalles de una venta/orden al formato requerido por el modal JS.
     * Eliminada la duplicación que existía en show() y generarEstadoCuenta().
     */
    private function mapearDetalles($detalles): \Illuminate\Support\Collection
    {
        return $detalles->map(fn($d) => [
            'cantidad'    => $d->cantidad,
            'nombre'      => $d->producto_id
                ? ($d->producto?->nombre      ?? 'PRODUCTO ELIMINADO')
                : ($d->servicio?->nombre      ?? 'SERVICIO ELIMINADO'),
            'descripcion' => $d->producto_id
                ? ($d->producto?->descripcion ?? 'N/A')
                : ($d->servicio?->descripcion ?? 'N/A'),
            'subtotal'    => $d->subtotal,
        ]);
    }

    /**
     * Obtiene los documentos pendientes de un cliente (ventas + órdenes),
     * con todas las propiedades computadas para la vista de detalle.
     * Usado por show() y puede reutilizarse en el futuro.
     */
    private function getDocumentosCliente(Cliente $cliente): \Illuminate\Support\Collection
    {
        $ventas = Venta::with(['detalles.producto', 'detalles.servicio', 'cliente'])
            ->where('cliente_id', $cliente->id)
            ->where('saldo_pendiente', '>', 0)
            ->get()
            ->map(function ($v) {
                $v->tipo_doc           = 'VENTA';
                $v->fecha_doc          = $v->fecha;
                $v->fecha_vencimiento  = $v->fecha->copy()->addDays(15);
                $v->dias_transcurridos = $v->fecha->diffInDays(now());
                $v->estado_color       = $this->calcularEstadoColor($v->dias_transcurridos);
                $v->items_json         = $this->mapearDetalles($v->detalles);
                $v->vehiculo_info      = null;
                return $v;
            });

        $ordenes = OrdenServicio::with(['detalles.producto', 'detalles.servicio', 'cliente', 'vehiculo', 'pagos'])
            ->where('cliente_id', $cliente->id)
            ->where('estado', 'PENDIENTE DE PAGO')
            ->where('saldo_pendiente', '>', 0)
            ->get()
            ->map(function ($o) {
                $o->tipo_doc           = 'ORDEN';
                $o->fecha_doc          = $o->fecha_entrada;
                $o->fecha_vencimiento  = $o->fecha_entrada->copy()->addDays(15);
                $o->dias_transcurridos = $o->fecha_entrada->diffInDays(now());
                $o->estado_color       = $this->calcularEstadoColor($o->dias_transcurridos);
                $o->items_json         = $this->mapearDetalles($o->detalles);
                $o->vehiculo_info      = $o->vehiculo
                    ? "{$o->vehiculo->marca} {$o->vehiculo->modelo} {$o->vehiculo->año}"
                    : 'N/A';
                return $o;
            });

        return $ventas->concat($ordenes)->sortBy('fecha_doc')->values();
    }

    // ─── Acciones públicas ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $cliente_id = $request->get('cliente_id');

        $baseQuery = Cliente::where('activo', 1)
            ->where(function ($q) {
                $q->whereHas('ventas', fn($q) => $q->where('saldo_pendiente', '>', 0))
                  ->orWhereHas('ordenesServicio', fn($q) => $q
                      ->where('estado', 'PENDIENTE DE PAGO')
                      ->where('saldo_pendiente', '>', 0));
            });

        // Lista para el dropdown — siempre todos, sin filtro de cliente aplicado
        $todosLosClientesConDeuda = (clone $baseQuery)->orderBy('nombre')->get(['id', 'nombre']);

        if ($cliente_id) {
            $baseQuery->where('id', $cliente_id);
        }

        // Eager loading con restricciones: elimina el N+1 (6 queries por cliente → 3 queries totales)
        $clientes = $baseQuery
            ->with([
                'ventas' => fn($q) => $q
                    ->where('saldo_pendiente', '>', 0)
                    ->select(['id', 'cliente_id', 'saldo_pendiente', 'fecha']),
                'ordenesServicio' => fn($q) => $q
                    ->where('estado', 'PENDIENTE DE PAGO')
                    ->where('saldo_pendiente', '>', 0)
                    ->select(['id', 'cliente_id', 'saldo_pendiente', 'fecha_entrada']),
            ])
            ->get()
            ->map(function ($cliente) {
                $ventas  = $cliente->ventas;
                $ordenes = $cliente->ordenesServicio;

                $cliente->saldo_total     = $ventas->sum('saldo_pendiente') + $ordenes->sum('saldo_pendiente');
                $cliente->cant_documentos = $ventas->count() + $ordenes->count();

                // Max de días — usando las relaciones ya cargadas, sin queries adicionales
                $diasVentas  = $ventas->map(fn($v) => $v->fecha->diffInDays(now()));
                $diasOrdenes = $ordenes->map(fn($o) => $o->fecha_entrada->diffInDays(now()));
                $maxDias     = $diasVentas->concat($diasOrdenes)->max() ?? 0;

                $cliente->max_dias     = $maxDias;
                $cliente->estado_color = $this->calcularEstadoColor($maxDias);

                return $cliente;
            })
            ->sort(function ($a, $b) {
                $prioridad = ['rojo' => 3, 'amarillo' => 2, 'verde' => 1];
                if ($prioridad[$a->estado_color] !== $prioridad[$b->estado_color]) {
                    return $prioridad[$b->estado_color] <=> $prioridad[$a->estado_color];
                }
                return $b->saldo_total <=> $a->saldo_total;
            })
            ->values();

        return view('creditos.index', compact('clientes', 'todosLosClientesConDeuda'));
    }

    public function show(Cliente $cliente)
    {
        $documentos = $this->getDocumentosCliente($cliente);

        return view('creditos.partials.detalles', compact('cliente', 'documentos'));
    }

    public function storeComentario(Request $request, Cliente $cliente)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);

        SeguimientoCredito::create([
            'cliente_id' => $cliente->id,
            'user_id'    => Auth::id() ?? 1,
            'comentario' => mb_strtoupper($request->comentario, 'UTF-8'),
        ]);

        return response()->json(['success' => true, 'message' => 'Comentario guardado']);
    }

    public function historialComentarios(Cliente $cliente)
    {
        $comentarios = SeguimientoCredito::with('user')
            ->where('cliente_id', $cliente->id)
            ->latest()
            ->get();

        return view('creditos.partials.historial_comentarios', compact('comentarios'));
    }

    public function generarEstadoCuenta(Cliente $cliente)
    {
        // Para el PDF necesitamos los registros en bruto (sin el mapping del modal),
        // pero evitamos duplicar las condiciones de filtro usando consultas limpias.
        $ventas = Venta::with(['detalles.producto', 'detalles.servicio'])
            ->where('cliente_id', $cliente->id)
            ->where('saldo_pendiente', '>', 0)
            ->get();

        $ordenes = OrdenServicio::with(['detalles.producto', 'detalles.servicio', 'vehiculo', 'pagos'])
            ->where('cliente_id', $cliente->id)
            ->where('saldo_pendiente', '>', 0)
            ->get();

        $pdf = Pdf::loadView('creditos.pdf_estado_cuenta', compact('cliente', 'ventas', 'ordenes'));

        return $pdf->stream('Estado_Cuenta_' . \Illuminate\Support\Str::slug($cliente->nombre) . '.pdf');
    }

    public function reporteGeneral(Request $request)
    {
        $tipo = $request->get('tipo', 'AMBOS'); // AMBOS, VENTAS, ORDENES

        $clientes = Cliente::where('activo', 1)
            ->where(function ($q) {
                $q->whereHas('ventas', fn($q) => $q->where('saldo_pendiente', '>', 0))
                  ->orWhereHas('ordenesServicio', fn($q) => $q
                      ->where('estado', 'PENDIENTE DE PAGO')
                      ->where('saldo_pendiente', '>', 0));
            })
            ->with([
                'ventas' => fn($q) => $q->where('saldo_pendiente', '>', 0)->latest('fecha'),
                'ordenesServicio' => fn($q) => $q->where('estado', 'PENDIENTE DE PAGO')->where('saldo_pendiente', '>', 0)->latest('fecha_entrada')
            ])
            ->orderBy('nombre')
            ->get();

        $datos = $clientes->map(function ($cliente) use ($tipo) {
            $documentos = collect();

            if (in_array($tipo, ['AMBOS', 'ORDENES'])) {
                $ordenes = $cliente->ordenesServicio->map(function ($o) {
                    return [
                        'folio' => $o->folio ?? 'ORD-' . $o->id,
                        'tipo' => 'ORDEN',
                        'fecha_emision' => $o->fecha_entrada,
                        'fecha_vencimiento' => $o->fecha_entrada->copy()->addDays(15),
                        'total' => $o->total,
                        'saldo' => $o->saldo_pendiente,
                        'id_sort' => 1 // Prioridad 1 para órdenes
                    ];
                });
                $documentos = $documentos->concat($ordenes);
            }

            if (in_array($tipo, ['AMBOS', 'VENTAS'])) {
                $ventas = $cliente->ventas->map(function ($v) {
                    return [
                        'folio' => $v->folio ?? 'VTA-' . $v->id,
                        'tipo' => 'VENTA',
                        'fecha_emision' => $v->fecha,
                        'fecha_vencimiento' => $v->fecha->copy()->addDays(15),
                        'total' => $v->total,
                        'saldo' => $v->saldo_pendiente,
                        'id_sort' => 2 // Prioridad 2 para ventas
                    ];
                });
                $documentos = $documentos->concat($ventas);
            }

            // Ordenar por prioridad (id_sort) y luego por fecha reciente (desc)
            $documentos = $documentos->sort(function ($a, $b) {
                if ($a['id_sort'] !== $b['id_sort']) {
                    return $a['id_sort'] <=> $b['id_sort'];
                }
                return $b['fecha_emision'] <=> $a['fecha_emision'];
            });

            return [
                'nombre' => $cliente->nombre,
                'documentos' => $documentos,
                'total_cliente' => $documentos->sum('saldo')
            ];
        })->filter(fn($c) => $c['documentos']->count() > 0);

        $pdf = Pdf::loadView('creditos.pdf_reporte_cobranza', [
            'datos' => $datos,
            'tipo_reporte' => $tipo,
            'fecha_reporte' => now()->format('d/m/Y H:i')
        ]);

        return $pdf->stream('Reporte_General_Cobranza_' . now()->format('dmY_His') . '.pdf');
    }

    public function registrarPagoLote(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('RECIBIDO PAGO LOTE:', $request->all());

        $request->validate([
            'documentos' => 'required|array|min:1',
            'documentos.*.id' => 'required|integer',
            'documentos.*.tipo' => 'required|in:VENTA,ORDEN',
            'monto_total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string',
            'referencia' => 'nullable|string|max:100',
            'requiere_factura' => 'required|in:SI,NO',
            'fecha_pago' => 'nullable|date',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $documentosRequest = collect($request->documentos);
            $montoRestante = floatval($request->monto_total);
            $metodo = $request->metodo_pago;

            if ($metodo === 'CRÉDITO 15 DÍAS') {
                throw new \Exception("No puede abonar cuentas usando crédito a 15 días.");
            }

            if ($montoRestante <= 0) {
                throw new \Exception("El monto total debe ser mayor a 0.");
            }

            $ventasIds = $documentosRequest->where('tipo', 'VENTA')->pluck('id')->toArray();
            $ordenesIds = $documentosRequest->where('tipo', 'ORDEN')->pluck('id')->toArray();

            $ventas = Venta::whereIn('id', $ventasIds)->where('saldo_pendiente', '>', 0)->get()->map(function($v) {
                $v->tipo_doc = 'VENTA';
                $v->fecha_doc = $v->fecha;
                return $v;
            });

            $ordenes = OrdenServicio::whereIn('id', $ordenesIds)->where('saldo_pendiente', '>', 0)->where('estado', 'PENDIENTE DE PAGO')->get()->map(function($o) {
                $o->tipo_doc = 'ORDEN';
                $o->fecha_doc = $o->fecha_entrada;
                return $o;
            });

            $documentos = $ventas->concat($ordenes)->sortBy('fecha_doc')->values();
            
            if ($documentos->isEmpty()) {
                throw new \Exception("Los documentos seleccionados ya no tienen saldo pendiente.");
            }

            foreach ($documentos as $doc) {
                if ($montoRestante <= 0) break;

                $abono = min($doc->saldo_pendiente, $montoRestante);
                \Illuminate\Support\Facades\Log::info("  PROCESANDO DOC {$doc->tipo_doc} ID {$doc->id}: Saldo original {$doc->saldo_pendiente}, Abono {$abono}, Queda por distribuir " . ($montoRestante - $abono));
                
                $montoRestante -= $abono;

                if ($doc->tipo_doc === 'VENTA') {
                    $pago = \App\Models\VentaPago::create([
                        'venta_id' => $doc->id,
                        'monto' => $abono,
                        'fecha_pago' => $request->fecha_pago ?? now()->format('Y-m-d'),
                        'metodo_pago' => $metodo,
                        'referencia' => mb_strtoupper($request->referencia ?? '', 'UTF-8'),
                    ]);

                    $doc->saldo_pendiente -= $pago->monto; // Usar el monto del registro creado
                    $doc->estado = ($doc->saldo_pendiente <= 0) ? 'PAGADA' : 'PENDIENTE';
                    
                    if ($doc->saldo_pendiente <= 0) {
                        $doc->requiere_factura = $request->requiere_factura;
                    }
                    
                    // Recalcular método (leemos de la relación fresca)
                    $metodos = $pago->venta->pagos()->pluck('metodo_pago')->unique();
                    $doc->metodo_pago = $metodos->count() > 1 ? 'MIXTO' : ($metodos->first() ?? $doc->metodo_pago);

                    unset($doc->tipo_doc);
                    unset($doc->fecha_doc);
                    $doc->save();
                } else {
                    $pago = $doc->pagos()->create([
                        'monto' => $abono,
                        'fecha_pago' => $request->fecha_pago ?? now()->format('Y-m-d'),
                        'metodo_pago' => $metodo,
                        'referencia' => mb_strtoupper($request->referencia ?? '', 'UTF-8'),
                    ]);

                    $doc->saldo_pendiente -= $pago->monto;
                    
                    if ($doc->saldo_pendiente <= 0) {
                        $doc->requiere_factura = $request->requiere_factura;
                    }

                    // Aseguramos que el estado sea el correcto para Ordenes
                    $doc->estado = ($doc->saldo_pendiente > 0) ? 'PENDIENTE DE PAGO' : 'ENTREGADO';
                    
                    unset($doc->tipo_doc);
                    unset($doc->fecha_doc);
                    $doc->save();
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pagos procesados correctamente.'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
