<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Órdenes</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #3b82f6; text-transform: uppercase; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f8fafc; color: #475569; font-weight: bold; padding: 8px; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #f1f5f9; text-align: center; }
        
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .monospace { font-family: 'Courier New', Courier, monospace; }
        .total-row { background-color: #1e293b; color: white; font-weight: bold; }
        .vehiculo-info { font-size: 8px; color: #64748b; display: block; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #94a3b8; font-size: 8px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Órdenes de Servicio</h1>
        <p>Periodo: {{ $fecha_inicio }} al {{ $fecha_fin }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th class="text-left">Folio</th>
                <th class="text-left">Cliente / Vehículo</th>
                <th>Método de Pago</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordenes as $orden)
                <tr>
                    <td>{{ $orden->created_at->format('d/m/Y') }}</td>
                    <td class="text-left"><strong>{{ $orden->folio }}</strong></td>
                    <td class="text-left">
                        {{ $orden->cliente->nombre }}
                        <span class="vehiculo-info">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ({{ $orden->vehiculo->placas }})</span>
                    </td>
                    <td style="text-transform: uppercase;">
                        {{ $orden->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'PENDIENTE' }}
                    </td>
                    <td style="text-transform: uppercase;">{{ $orden->estado }}</td>
                    <td class="monospace">${{ number_format($orden->total, 2) }}</td>
                    <td class="monospace" style="color: #94a3b8;">${{ number_format($orden->saldo_pendiente, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL ACUMULADO:</td>
                <td class="monospace">${{ number_format($ordenes->sum('total'), 2) }}</td>
                <td class="monospace">${{ number_format($ordenes->sum('saldo_pendiente'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i:s') }} - Sistema de Gestión Automotriz
    </div>
</body>
</html>
