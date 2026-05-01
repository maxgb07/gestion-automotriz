<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta - {{ $proveedor->nombre }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px; text-transform: uppercase; }
        .title { font-size: 24px; font-weight: bold; color: #1e293b; }
        .subtitle { font-size: 14px; color: #64748b; margin-top: 5px; }
        .info-box { border: 1px solid #e2e8f0; background: #f8fafc; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .info-box p { margin: 5px 0; font-size: 13px; text-transform: uppercase; }
        .info-box strong { color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 11px; }
        th { background: #1e293b; color: white; padding: 8px; text-align: left; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .section-title { font-size: 16px; font-weight: bold; color: #2563eb; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; }
        .totals { background: #f1f5f9; font-weight: bold; }
        .vencida { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Estado de Cuenta: Proveedor</div>
        <div class="subtitle">Fecha de Reporte: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="info-box">
        <table style="margin: 0; border: none;">
            <tr style="border: none;">
                <td style="border: none; padding: 0;">
                    <p><strong>Proveedor:</strong> {{ $proveedor->nombre }}</p>
                    <p><strong>Días de Crédito:</strong> {{ $proveedor->dias_credito }}</p>
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <p><strong>Deuda Total:</strong> ${{ number_format($facturas->sum('saldo_pendiente'), 2) }}</p>
                    <p><strong>Saldo a Favor (NC):</strong> ${{ number_format($notasCredito->sum('saldo_disponible'), 2) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Facturas Pendientes -->
    <div class="section-title">Facturas Pendientes de Pago</div>
    @if($facturas->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Folio OC</th>
                    <th>Factura</th>
                    <th>Fecha Vencimiento</th>
                    <th class="text-right">Total original</th>
                    <th class="text-right">Saldo Pendiente</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facturas as $factura)
                    @php $vencida = $factura->fecha_vencimiento && $factura->fecha_vencimiento < now()->format('Y-m-d'); @endphp
                    <tr>
                        <td>{{ $factura->folio }}</td>
                        <td>{{ $factura->factura ?? 'S/F' }}</td>
                        <td class="{{ $vencida ? 'vencida' : '' }}">
                            {{ \Carbon\Carbon::parse($factura->fecha_vencimiento)->format('d/m/Y') }}
                            @if($vencida) <br><small>(VENCIDA)</small> @endif
                        </td>
                        <td class="text-right">${{ number_format($factura->total, 2) }}</td>
                        <td class="text-right"><strong>${{ number_format($factura->saldo_pendiente, 2) }}</strong></td>
                    </tr>
                @endforeach
                <tr class="totals">
                    <td colspan="4" class="text-right">Total Pendiente:</td>
                    <td class="text-right">${{ number_format($facturas->sum('saldo_pendiente'), 2) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p style="text-align:center; color:#64748b;">No hay facturas pendientes.</p>
    @endif

    <!-- Notas de Crédito -->
    @if($notasCredito->count() > 0)
    <div style="page-break-inside: avoid;">
        <div class="section-title">Notas de Crédito Activas (Saldo a Favor)</div>
        <table>
            <thead>
                <tr>
                    <th>Folio NC</th>
                    <th>Fecha</th>
                    <th class="text-right">Monto Original</th>
                    <th class="text-right">Saldo Disponible</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notasCredito as $nc)
                    <tr>
                        <td>{{ $nc->folio }}</td>
                        <td>{{ \Carbon\Carbon::parse($nc->fecha)->format('d/m/Y') }}</td>
                        <td class="text-right">${{ number_format($nc->monto_original, 2) }}</td>
                        <td class="text-right"><strong>${{ number_format($nc->saldo_disponible, 2) }}</strong></td>
                    </tr>
                @endforeach
                <tr class="totals">
                    <td colspan="3" class="text-right">Total a Favor:</td>
                    <td class="text-right">${{ number_format($notasCredito->sum('saldo_disponible'), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- Historial de Pagos Recientes -->
    @if($pagos->count() > 0)
    <div style="page-break-inside: avoid;">
        <div class="section-title">Historial de Pagos Recientes</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha Pago</th>
                    <th>Factura Abonada</th>
                    <th>Forma de Pago</th>
                    <th>Referencia / NC</th>
                    <th class="text-right">Monto Pagado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos->take(15) as $pago)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                        <td>{{ $pago->compra->factura ?? $pago->compra->folio }}</td>
                        <td>{{ $pago->forma_pago }}</td>
                        <td>{{ $pago->referencia ?? ($pago->notaCredito ? 'NC: ' . $pago->notaCredito->folio : 'N/A') }}</td>
                        <td class="text-right">${{ number_format($pago->monto, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</body>
</html>
