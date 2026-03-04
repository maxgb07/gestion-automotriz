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
}
