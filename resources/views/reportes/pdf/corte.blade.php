<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Corte del Día</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; color: #3b82f6; text-transform: uppercase; font-size: 18px; }
        .header p { margin: 3px 0; color: #666; font-weight: bold; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f8fafc; color: #1e293b; font-weight: bold; text-transform: uppercase; padding: 8px 4px; border-bottom: 2px solid #3b82f6; font-size: 9px; }
        td { padding: 8px 4px; border-bottom: 1px solid #e2e8f0; text-align: center; font-size: 10px; }
        
        .type-label { border-radius: 4px; padding: 2px 4px; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        .venta { background-color: #f3e8ff; color: #7e22ce; }
        .orden { background-color: #dbeafe; color: #1d4ed8; }
        .pago { background-color: #ffedd5; color: #9a3412; }
        
        .status-label { font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .status-entregado { color: #059669; }
        .status-pendiente { color: #d97706; }
        
        .total-row { background-color: #f8fafc; font-weight: bold; }
        .text-right { text-align: right; }
        .monospace { font-family: 'Courier New', Courier, monospace; font-weight: bold; }
        
        .fila-principal { margin-bottom: 10px; width: 100%; border-spacing: 10px; }
        .card-p { 
            display: inline-block; 
            width: 31%; 
            padding: 12px 4px; 
            background: #f1f5f9;
            border: 1px solid #cbd5e1; 
            border-radius: 8px;
            text-align: center;
        }

        .metodos-container { margin-bottom: 20px; width: 100%; }
        .card-m { 
            display: inline-block; 
            width: 18%; 
            margin-right: 1%; 
            padding: 8px 4px; 
            background: #f8fafc;
            border: 1px solid #e2e8f0; 
            border-radius: 6px;
            text-align: center;
        }
        .card-m p { margin: 0; padding: 0; }
        .label-m { font-size: 6px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 2px !important; }
        .total-m { font-size: 10px; font-weight: bold; font-family: 'Courier New', monospace; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #94a3b8; font-size: 8px; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    @php 
        $hoy = \Carbon\Carbon::today();
        $movs = collect();
        $metodosBase = ['EFECTIVO' => 0, 'CHEQUE' => 0, 'TRANSFERENCIA' => 0, 'TARJETA CRÉDITO' => 0, 'TARJETA DÉBITO' => 0];
        $totalesPorMetodo = $metodosBase;
        
        $mapearMetodo = function($metodoOriginal) {
            $m = strtoupper($metodoOriginal);
            if(str_contains($m, 'EFECTIVO')) return 'EFECTIVO';
            if(str_contains($m, 'CHEQUE')) return 'CHEQUE';
            if(str_contains($m, 'TRANSFERENCIA')) return 'TRANSFERENCIA';
            if(str_contains($m, 'TARJETA')) {
                if(str_contains($m, 'CREDITO') || str_contains($m, 'CRÉDITO')) return 'TARJETA CRÉDITO';
                return 'TARJETA DÉBITO';
            }
            return $m ?: 'OTRO';
        };

        foreach($ordenes as $o) {
            $metodoOriginal = $o->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'PENDIENTE';
            $metodoMapeado = $mapearMetodo($metodoOriginal);
            $movs->push(['tipo' => 'ORDEN', 'label' => 'ORDEN SERVICIO', 'folio' => $o->folio, 'estado_label' => $o->estado, 'metodo_mapeado' => $metodoMapeado, 'total' => $o->total, 'es_pago' => false]);
            if(isset($totalesPorMetodo[$metodoMapeado])) $totalesPorMetodo[$metodoMapeado] += $o->total;
        }

        foreach($ventas as $v) {
            $metodoMapeado = $mapearMetodo($v->metodo_pago);
            $movs->push(['tipo' => 'VENTA', 'label' => 'VENTA', 'folio' => $v->folio, 'estado_label' => $v->estado, 'metodo_mapeado' => $metodoMapeado, 'total' => $v->total, 'es_pago' => false]);
            if(isset($totalesPorMetodo[$metodoMapeado])) $totalesPorMetodo[$metodoMapeado] += $v->total;
        }

        $pagoOrdenesHoy = $pagoOrdenes->groupBy('orden_servicio_id');
        $pagoVentasHoy = $pagoVentas->groupBy('venta_id');
        $documentosHoyIds = ['ORDEN' => $ordenes->pluck('id')->toArray(), 'VENTA' => $ventas->pluck('id')->toArray()];

        foreach($pagoOrdenesHoy as $osId => $pagos) {
            if(!in_array($osId, $documentosHoyIds['ORDEN'])) {
                $os = $pagos->first()->ordenServicio;
                $montoHoy = $pagos->sum('monto');
                $metodoMapeado = $mapearMetodo($pagos->pluck('metodo_pago')->unique()->implode(', '));
                $movs->push(['label' => $os->totalPaidEver >= $os->total ? 'ORDEN SERVICIO' : 'ABONO ORDEN', 'folio' => $os->folio, 'tipo' => 'PAGO_ORDEN', 'estado_label' => $os->totalPaidEver >= $os->total ? 'ENTREGADO' : $os->estado, 'metodo_mapeado' => $metodoMapeado, 'total' => $montoHoy, 'es_pago' => true]);
                if(isset($totalesPorMetodo[$metodoMapeado])) $totalesPorMetodo[$metodoMapeado] += $montoHoy;
            }
        }

        foreach($pagoVentasHoy as $vId => $pagos) {
            if(!in_array($vId, $documentosHoyIds['VENTA'])) {
                $v = $pagos->first()->venta;
                $montoHoy = $pagos->sum('monto');
                $metodoMapeado = $mapearMetodo($pagos->pluck('metodo_pago')->unique()->implode(', '));
                $movs->push(['label' => $v->totalPaidEver >= $v->total ? 'VENTA' : 'ABONO VENTA', 'folio' => $v->folio, 'tipo' => 'PAGO_VENTA', 'estado_label' => $v->totalPaidEver >= $v->total ? 'PAGADA' : $v->estado, 'metodo_mapeado' => $metodoMapeado, 'total' => $montoHoy, 'es_pago' => true]);
                if(isset($totalesPorMetodo[$metodoMapeado])) $totalesPorMetodo[$metodoMapeado] += $montoHoy;
            }
        }

        // TOTALES REALES
        $totalVentasPDF = $movs->whereIn('tipo', ['VENTA', 'PAGO_VENTA'])->sum('total');
        $totalOrdenesPDF = $movs->whereIn('tipo', ['ORDEN', 'PAGO_ORDEN'])->sum('total');
        $totalGeneralPDF = $totalVentasPDF + $totalOrdenesPDF;

        // ORDENADO PARA TABLA
        $combined = $movs->sort(function($a, $b) {
            $prioA = (str_contains($a['tipo'], 'ORDEN')) ? 0 : 1;
            $prioB = (str_contains($b['tipo'], 'ORDEN')) ? 0 : 1;
            if ($prioA !== $prioB) return $prioA <=> $prioB;
            return strcmp($a['metodo_mapeado'], $b['metodo_mapeado']);
        });

        $totalG = 0;
    @endphp

    <div class="header">
        <h1>Corte del Día</h1>
        <p>{{ $hoy->isoFormat('LL') }}</p>
    </div>

    <!-- Fila 1 -->
    <div class="fila-principal">
        <div class="card-p">
            <p style="font-size: 7px; color: #64748b; font-weight: bold; text-transform: uppercase; margin: 0 0 4px 0;">Total Ventas Mostrador</p>
            <p style="font-size: 14px; font-weight: bold; font-family: 'Courier New', monospace; margin: 0;">${{ number_format($totalVentasPDF, 2) }}</p>
        </div>
        <div class="card-p">
            <p style="font-size: 7px; color: #64748b; font-weight: bold; text-transform: uppercase; margin: 0 0 4px 0;">Total Órdenes de Servicio</p>
            <p style="font-size: 14px; font-weight: bold; font-family: 'Courier New', monospace; margin: 0;">${{ number_format($totalOrdenesPDF, 2) }}</p>
        </div>
        <div class="card-p">
            <p style="font-size: 7px; color: #64748b; font-weight: bold; text-transform: uppercase; margin: 0 0 4px 0;">Total</p>
            <p style="font-size: 14px; font-weight: bold; font-family: 'Courier New', monospace; margin: 0;">${{ number_format($totalGeneralPDF, 2) }}</p>
        </div>
    </div>

    <!-- Fila 2 -->
    <div class="metodos-container">
        @foreach(['EFECTIVO', 'CHEQUE', 'TRANSFERENCIA', 'TARJETA CRÉDITO', 'TARJETA DÉBITO'] as $met)
            <div class="card-m">
                <p class="label-m">{{ $met }}</p>
                <p class="total-m">${{ number_format($totalesPorMetodo[$met] ?? 0, 2) }}</p>
            </div>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Folio</th>
                <th>Estado</th>
                <th>Método</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($combined as $mov)
                @php 
                    $totalG += $mov['total'];
                    $metodo = $mov['metodo_mapeado'];
                    $hex = '#333';
                    if($metodo == 'EFECTIVO') $hex = '#059669';
                    elseif($metodo == 'TRANSFERENCIA') $hex = '#2563eb';
                    elseif($metodo == 'TARJETA CRÉDITO') $hex = '#7c3aed';
                    elseif($metodo == 'TARJETA DÉBITO') $hex = '#d97706';
                @endphp
                <tr>
                    <td><span class="type-label {{ str_contains($mov['label'], 'ABONO') ? 'pago' : (str_contains($mov['tipo'], 'VENTA') ? 'venta' : 'orden') }}">{{ $mov['label'] }}</span></td>
                    <td><strong>{{ $mov['folio'] }}</strong></td>
                    <td class="status-label {{ str_contains($mov['estado_label'], 'ENTREGADO') || str_contains($mov['estado_label'], 'PAGADA') ? 'status-entregado' : 'status-pendiente' }}">{{ $mov['estado_label'] }}</td>
                    <td style="color: {{ $hex }}; font-weight: bold; text-transform: uppercase;">{{ $mov['metodo_mapeado'] }}</td>
                    <td class="monospace">${{ number_format($mov['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL:</td>
                <td class="monospace">${{ number_format($totalG, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i:s') }} - Sistema de Gestión Automotriz
    </div>
</body>
</html>
