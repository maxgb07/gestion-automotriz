<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Compra;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CuentasPorPagarController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::with(['compras' => function($q) {
            $q->where('saldo_pendiente', '>', 0)->where('estado_pago', '!=', 'PAGADA');
        }]);

        if ($request->has('buscar') && $request->buscar != '') {
            $buscar = $request->buscar;
            $query->where('nombre', 'like', "%{$buscar}%");
        }

        $proveedores = $query->get()->map(function ($proveedor) {
            $proveedor->total_deuda = $proveedor->compras->sum('saldo_pendiente');
            // Obtener saldo a favor de notas de crédito
            $proveedor->saldo_favor = \App\Models\NotaCreditoProveedor::where('proveedor_id', $proveedor->id)
                ->where('estado', 'ACTIVA')
                ->sum('saldo_disponible');
            
            $proveedor->facturas_vencidas = $proveedor->compras->filter(function($c) {
                return $c->fecha_vencimiento && $c->fecha_vencimiento < now()->format('Y-m-d');
            })->count();

            return $proveedor;
        })->filter(function ($proveedor) {
            return $proveedor->total_deuda >= 0 || $proveedor->saldo_favor >= 0;
        })->sortByDesc('total_deuda');

        return view('cuentas_por_pagar.index', compact('proveedores'));
    }

    public function eventosCalendario(Request $request)
    {
        $compras = Compra::with('proveedor')
            ->where('saldo_pendiente', '>', 0)
            ->whereNotNull('fecha_vencimiento')
            ->get();

        $eventos = $compras->map(function ($compra) {
            $proveedorNombre = $compra->proveedor ? $compra->proveedor->nombre : 'Desconocido';
            $factura = $compra->factura ?? $compra->folio;
            
            return [
                'id' => $compra->id,
                'title' => mb_strtoupper($proveedorNombre, 'UTF-8'),
                'start' => $compra->fecha_vencimiento,
                'allDay' => true,
                'backgroundColor' => '#059669', // emerald-600 para combinar con el boton
                'borderColor' => '#047857',
                'extendedProps' => [
                    'proveedor' => $proveedorNombre,
                    'factura' => $factura,
                    'monto_total' => number_format($compra->monto_total, 2),
                    'saldo_pendiente' => number_format($compra->saldo_pendiente, 2)
                ]
            ];
        });

        return response()->json($eventos);
    }

    public function show(Proveedor $proveedor)
    {
        // Facturas pendientes (paginadas)
        $facturas = Compra::where('proveedor_id', $proveedor->id)
            ->where('saldo_pendiente', '>', 0)
            ->orderBy('factura', 'asc')
            ->paginate(20, ['*'], 'facturas_page');

        // Facturas pendientes completas para el modal de pagos
        $facturasModal = Compra::where('proveedor_id', $proveedor->id)
            ->where('saldo_pendiente', '>', 0)
            ->orderBy('factura', 'asc')
            ->get();

        // Notas de crédito disponibles (paginadas)
        $notasCredito = \App\Models\NotaCreditoProveedor::where('proveedor_id', $proveedor->id)
            ->where('estado', 'ACTIVA')
            ->orderBy('fecha', 'desc')
            ->paginate(20, ['*'], 'notas_page');

        // Notas de crédito completas para el modal
        $notasCreditoModal = \App\Models\NotaCreditoProveedor::where('proveedor_id', $proveedor->id)
            ->where('estado', 'ACTIVA')
            ->orderBy('fecha', 'desc')
            ->get();

        // Historial de pagos (agrupados)
        $pagosRaw = \App\Models\PagoCompra::whereHas('compra', function($q) use ($proveedor) {
                $q->where('proveedor_id', $proveedor->id);
            })
            ->with('compra', 'notaCredito')
            ->orderBy('fecha_pago', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $pagosAgrupados = $pagosRaw->groupBy(function($item) {
            return $item->grupo_pago_id ?: 'indiv_' . $item->id;
        });

        return view('cuentas_por_pagar.show', compact('proveedor', 'facturas', 'facturasModal', 'notasCredito', 'notasCreditoModal', 'pagosAgrupados'));
    }

    public function registrarNotaCredito(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'folio' => 'required|string|max:100',
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string'
        ]);

        \App\Models\NotaCreditoProveedor::create([
            'proveedor_id' => $request->proveedor_id,
            'folio' => mb_strtoupper($request->folio, 'UTF-8'),
            'monto_original' => $request->monto,
            'saldo_disponible' => $request->monto,
            'fecha' => $request->fecha,
            'estado' => 'ACTIVA',
            'observaciones' => $request->observaciones
        ]);

        return back()->with('success', 'Nota de Crédito registrada exitosamente.');
    }

    public function actualizarNotaCredito(Request $request, \App\Models\NotaCreditoProveedor $notaCredito)
    {
        $request->validate([
            'folio'         => 'required|string|max:100',
            'fecha'         => 'required|date',
            'monto'         => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string'
        ]);

        // Calcular diferencia usada y ajustar saldo disponible
        $montoAnterior = $notaCredito->monto_original;
        $diferencia = $request->monto - $montoAnterior;
        $nuevoSaldo = max(0, $notaCredito->saldo_disponible + $diferencia);

        $notaCredito->update([
            'folio'           => mb_strtoupper($request->folio, 'UTF-8'),
            'monto_original'  => $request->monto,
            'saldo_disponible'=> $nuevoSaldo,
            'fecha'           => $request->fecha,
            'observaciones'   => $request->observaciones
        ]);

        return back()->with('success', 'Nota de Crédito actualizada exitosamente.');
    }

    public function eliminarNotaCredito(\App\Models\NotaCreditoProveedor $notaCredito)
    {
        $notaCredito->delete();
        return back()->with('success', 'Nota de Crédito eliminada exitosamente.');
    }

    public function registrarPago(Request $request)
    {
        $request->validate([
            'proveedor_id'       => 'required|exists:proveedores,id',
            'monto_pago'         => 'nullable|numeric|min:0',
            'forma_pago'         => 'required|string',
            'fecha_pago'         => 'required|date',
            'referencia'         => 'nullable|string',
            'facturas'           => 'required|array|min:1',
            'notas_credito_ids'  => 'nullable|array',
            'notas_credito_ids.*'=> 'exists:nota_credito_proveedors,id',
        ]);

        try {
            DB::beginTransaction();

            $montoDinero  = (float) ($request->monto_pago ?? 0);

            // Cargar todas las NCs seleccionadas (puede ser 0, 1 o N)
            $notasCredito = collect();
            if (!empty($request->notas_credito_ids)) {
                $notasCredito = \App\Models\NotaCreditoProveedor::whereIn('id', $request->notas_credito_ids)->get();
            }

            $totalNC = $notasCredito->sum('saldo_disponible');

            if ($montoDinero <= 0 && $totalNC <= 0) {
                throw new \Exception('Debe ingresar un monto a pagar o seleccionar al menos una Nota de Crédito con saldo.');
            }

            $facturas = Compra::whereIn('id', $request->facturas)
                              ->where('proveedor_id', $request->proveedor_id)
                              ->where('saldo_pendiente', '>', 0)
                              ->orderBy('factura', 'asc')
                              ->get();

            $grupoPagoId = (string) \Illuminate\Support\Str::uuid();

            // 1. Distribuir cada Nota de Crédito, una por una (en el orden recibido)
            foreach ($notasCredito as $notaCredito) {
                $montoNC = (float) $notaCredito->saldo_disponible;
                if ($montoNC <= 0) continue;

                foreach ($facturas as $factura) {
                    if ($montoNC <= 0) break;
                    if ($factura->saldo_pendiente <= 0) continue;

                    $abonoNC = min($factura->saldo_pendiente, $montoNC);

                    \App\Models\PagoCompra::create([
                        'compra_id'         => $factura->id,
                        'monto'             => $abonoNC,
                        'fecha_pago'        => $request->fecha_pago,
                        'forma_pago'        => 'NOTA DE CREDITO',
                        'tipo'              => 'APLICACION NC',
                        'nota_credito_id'   => $notaCredito->id,
                        'grupo_pago_id'     => $grupoPagoId,
                        'estado_documentos' => 'PENDIENTE',
                    ]);

                    $factura->saldo_pendiente -= $abonoNC;
                    $factura->estado_pago      = $factura->saldo_pendiente <= 0 ? 'PAGADA' : 'PARCIAL';
                    $factura->save();

                    $montoNC                        -= $abonoNC;
                    $notaCredito->saldo_disponible  -= $abonoNC;
                }

                if ($notaCredito->saldo_disponible <= 0) {
                    $notaCredito->estado = 'AGOTADA';
                }
                $notaCredito->save();
            }

            // 2. Distribuir el Dinero
            if ($montoDinero > 0) {
                foreach ($facturas as $factura) {
                    if ($montoDinero <= 0) break;
                    if ($factura->saldo_pendiente <= 0) continue;

                    $abonoDinero = min($factura->saldo_pendiente, $montoDinero);

                    \App\Models\PagoCompra::create([
                        'compra_id'         => $factura->id,
                        'monto'             => $abonoDinero,
                        'fecha_pago'        => $request->fecha_pago,
                        'forma_pago'        => $request->forma_pago,
                        'referencia'        => $request->referencia,
                        'tipo'              => 'PAGO NORMAL',
                        'nota_credito_id'   => null,
                        'grupo_pago_id'     => $grupoPagoId,
                        'estado_documentos' => 'PENDIENTE',
                    ]);

                    $factura->saldo_pendiente -= $abonoDinero;
                    $factura->estado_pago      = $factura->saldo_pendiente <= 0 ? 'PAGADA' : 'PARCIAL';
                    $factura->save();

                    $montoDinero -= $abonoDinero;
                }
            }

            DB::commit();
            return back()->with('success', 'Pago y/o Nota(s) de Crédito aplicados correctamente a las facturas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function registrarComplemento(Request $request)
    {
        $request->validate([
            'grupo_pago_id' => 'required|string',
            'complemento_folio' => 'nullable|string',
            'complemento_fecha' => 'nullable|date',
            'complemento_monto' => 'nullable|numeric|min:0',
            'ncs_informativas' => 'nullable|array' // Para las NCs informativas agregadas
        ]);

        try {
            DB::beginTransaction();

            $pagos = \App\Models\PagoCompra::where('grupo_pago_id', $request->grupo_pago_id)->get();

            if ($pagos->isEmpty()) {
                throw new \Exception('No se encontraron pagos vinculados a este grupo.');
            }

            foreach ($pagos as $pago) {
                $pago->complemento_folio = $request->complemento_folio;
                $pago->complemento_fecha = $request->complemento_fecha;
                $pago->complemento_monto = $request->complemento_monto;
                $pago->ncs_informativas = $request->ncs_informativas;
                $pago->estado_documentos = 'COMPLETO';
                $pago->save();
            }

            DB::commit();
            return back()->with('success', 'El Complemento de Pago (REP) se ha guardado y el expediente fue cerrado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar el complemento: ' . $e->getMessage());
        }
    }

    public function getFacturasPagadas(Request $request, $grupo_pago_id)
    {
        $query = \App\Models\PagoCompra::select('pago_compras.*')
            ->join('compras', 'pago_compras.compra_id', '=', 'compras.id');

        // Si el grupo es null o es un id individual viejo
        if (str_starts_with($grupo_pago_id, 'indiv_')) {
            $id = str_replace('indiv_', '', $grupo_pago_id);
            $query->where('pago_compras.id', $id);
        } else {
            $query->where('pago_compras.grupo_pago_id', $grupo_pago_id);
        }

        $query->orderBy('compras.factura', 'asc');

        $pagos = $query->with('compra')->paginate(10);

        $html = '';
        foreach ($pagos as $item) {
            $esDinero = $item->tipo === 'PAGO NORMAL';
            $factura = $item->compra->factura ?? $item->compra->folio;
            $montoStr = '$' . number_format($item->monto, 2);
            $color = $esDinero ? 'text-blue-300' : 'text-emerald-400';
            $tipoStr = $esDinero ? ($item->forma_pago ?? 'TRANSFERENCIA') : 'NOTA DE CREDITO';

            $html .= '
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-center text-white font-medium uppercase">' . ($item->compra->folio ?? '') . '</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-white font-medium uppercase">' . $factura . '</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-white font-medium uppercase">' . $tipoStr . '</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center font-black text-lg ' . $color . '">' . $montoStr . '</td>
                </tr>
            ';
        }

        $links = (string) $pagos->links('vendor.pagination.custom');

        return response()->json([
            'html' => $html,
            'pagination' => $links
        ]);
    }

    public function descargarPDF(Proveedor $proveedor)
    {
        // Facturas pendientes
        $facturas = Compra::where('proveedor_id', $proveedor->id)
            ->where('saldo_pendiente', '>', 0)
            ->orderBy('factura', 'asc')
            ->get();

        // Notas de crédito disponibles
        $notasCredito = \App\Models\NotaCreditoProveedor::where('proveedor_id', $proveedor->id)
            ->where('estado', 'ACTIVA')
            ->orderBy('fecha', 'desc')
            ->get();

        // Historial de pagos
        $pagos = \App\Models\PagoCompra::whereHas('compra', function($q) use ($proveedor) {
                $q->where('proveedor_id', $proveedor->id);
            })
            ->with('compra', 'notaCredito')
            ->orderBy('fecha_pago', 'desc')
            ->limit(50)
            ->get();

        $pdf = Pdf::loadView('cuentas_por_pagar.pdf', compact('proveedor', 'facturas', 'notasCredito', 'pagos'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('estado_de_cuenta_' . str_replace(' ', '_', $proveedor->nombre) . '_' . date('Ymd') . '.pdf');
    }

    public function descargarPDFGlobal()
    {
        $proveedores = Proveedor::with(['compras' => function($q) {
            $q->where('saldo_pendiente', '>', 0)->where('estado_pago', '!=', 'PAGADA');
        }])->orderBy('nombre', 'asc')->get()->map(function ($proveedor) {
            $proveedor->total_deuda = $proveedor->compras->sum('saldo_pendiente');
            // Obtener saldo a favor de notas de crédito
            $proveedor->saldo_favor = \App\Models\NotaCreditoProveedor::where('proveedor_id', $proveedor->id)
                ->where('estado', 'ACTIVA')
                ->sum('saldo_disponible');
            
            return $proveedor;
        })->filter(function ($proveedor) {
            return $proveedor->total_deuda > 0 || $proveedor->saldo_favor > 0;
        });

        $pdf = Pdf::loadView('cuentas_por_pagar.pdf_global', compact('proveedores'))
                    ->setPaper('a4', 'landscape');
        
        return $pdf->stream('estado_de_cuenta_global_' . date('Ymd') . '.pdf');
    }
}
