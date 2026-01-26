<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Corte del Día</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #3b82f6; text-transform: uppercase; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f8fafc; color: #475569; font-weight: bold; text-transform: uppercase; padding: 8px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 8px; border-bottom: 1px solid #f1f5f9; text-align: center; }
        
        .type-label { border-radius: 4px; padding: 2px 5px; font-weight: bold; font-size: 8px; }
        .venta { background-color: #ede9fe; color: #6d28d9; }
        .orden { background-color: #dbeafe; color: #1d4ed8; }
        
        .total-row { background-color: #1e293b; color: white; font-weight: bold; }
        .text-right { text-align: right; }
        .monospace { font-family: 'Courier New', Courier, monospace; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #94a3b8; font-size: 8px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Corte del Día</h1>
        <p>{{ $hoy->isoFormat('LL') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Tipo</th>
                <th>Folio</th>
                <th>Estado</th>
                <th>Método de Pago</th>
                <th>Total</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $combined = $ventas->map(fn($v) => ['item' => $v, 'tipo' => 'VENTA', 'hora' => $v->created_at])
                    ->concat($ordenes->map(fn($o) => ['item' => $o, 'tipo' => 'ORDEN', 'hora' => $o->created_at]))
                    ->sortBy('hora');
                $totalG = 0;
                $saldoG = 0;
            @endphp
            @foreach($combined as $mov)
                @php 
                    $totalG += $mov['item']->total;
                    $saldoG += $mov['item']->saldo_pendiente;
                @endphp
                <tr>
                    <td>{{ $mov['hora']->format('H:i') }}</td>
                    <td>
                        <span class="type-label {{ $mov['tipo'] == 'VENTA' ? 'venta' : 'orden' }}">
                            {{ $mov['tipo'] }}
                        </span>
                    </td>
                    <td><strong>{{ $mov['item']->folio }}</strong></td>
                    <td style="text-transform: uppercase;">{{ $mov['item']->estado }}</td>
                    <td style="text-transform: uppercase;">
                        @if($mov['tipo'] == 'VENTA')
                            {{ $mov['item']->metodo_pago }}
                        @else
                            {{ $mov['item']->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'PENDIENTE' }}
                        @endif
                    </td>
                    <td class="monospace">${{ number_format($mov['item']->total, 2) }}</td>
                    <td class="monospace" style="color: #94a3b8;">${{ number_format($mov['item']->saldo_pendiente, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL DEL DÍA:</td>
                <td class="monospace">${{ number_format($totalG, 2) }}</td>
                <td class="monospace">${{ number_format($saldoG, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i:s') }} - Sistema de Gestión Automotriz
    </div>
</body>
</html>
