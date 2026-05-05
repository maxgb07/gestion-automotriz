<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta</title>
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
        .grand-total { font-size: 16px; background: #1e293b; color: white; font-weight: bold; }
        .grand-total td { color: white; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Estado de Cuenta</div>
        <div class="subtitle">Fecha de Reporte: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="section-title">Resumen por Proveedor</div>
    
    @php
        $granTotalDeuda = 0;
        $granTotalFavor = 0;
    @endphp

    @if($proveedores->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th class="text-right">Total a Favor (NC)</th>
                    <th class="text-right">Total Deuda</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proveedores as $proveedor)
                    @php
                        $granTotalDeuda += $proveedor->total_deuda;
                        $granTotalFavor += $proveedor->saldo_favor;
                    @endphp
                    <tr>
                        <td><strong>{{ $proveedor->nombre }}</strong></td>
                        <td class="text-right text-emerald-600">${{ number_format($proveedor->saldo_favor, 2) }}</td>
                        <td class="text-right text-red-600"><strong>${{ number_format($proveedor->total_deuda, 2) }}</strong></td>
                    </tr>
                @endforeach
                <tr class="grand-total">
                    <td class="text-right">GRAN TOTAL:</td>
                    <td class="text-right">${{ number_format($granTotalFavor, 2) }}</td>
                    <td class="text-right">${{ number_format($granTotalDeuda, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Detalle por Proveedor -->
        <div style="page-break-before: always;"></div>
        
        @foreach($proveedores as $proveedor)
            <div style="page-break-inside: avoid;">
                <div class="info-box" style="margin-top: 20px;">
                    <table style="margin: 0; border: none;">
                        <tr style="border: none;">
                            <td style="border: none; padding: 0;">
                                <p style="font-size: 16px;"><strong>{{ $proveedor->nombre }}</strong></p>
                                <p><strong>Días de Crédito:</strong> {{ $proveedor->dias_credito }}</p>
                            </td>
                            <td style="border: none; padding: 0; text-align: right;">
                                <p><strong>Deuda Total:</strong> ${{ number_format($proveedor->total_deuda, 2) }}</p>
                                <p><strong>Saldo a Favor:</strong> ${{ number_format($proveedor->saldo_favor, 2) }}</p>
                            </td>
                        </tr>
                    </table>
                </div>

                @if($proveedor->compras->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Fecha Vencimiento</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-right">D. Global</th>
                            <th class="text-right">D. Extra</th>
                            <th class="text-right">D. Interno</th>
                            <th class="text-right">IVA</th>
                            <th class="text-right">Total Factura</th>
                            <th class="text-right">P. Pago</th>
                            <th class="text-right">Saldo Pendiente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proveedor->compras as $factura)
                            @php $vencida = $factura->fecha_vencimiento && $factura->fecha_vencimiento < now()->format('Y-m-d'); @endphp
                            <tr>
                                <td>{{ $factura->factura ?? 'S/F' }}</td>
                                <td class="{{ $vencida ? 'vencida' : '' }}">
                                    {{ \Carbon\Carbon::parse($factura->fecha_vencimiento)->format('d/m/Y') }}
                                    @if($vencida) <br><small>(VENCIDA)</small> @endif
                                </td>
                                <td class="text-right">${{ number_format($factura->subtotal, 2) }}</td>
                                <td class="text-right">{{ number_format($factura->porcentaje_descuento, 2) }}%<br><small>${{ number_format($factura->monto_descuento, 2) }}</small></td>
                                <td class="text-right">{{ number_format($factura->porcentaje_descuento_extra, 2) }}%<br><small>${{ number_format($factura->monto_descuento_extra, 2) }}</small></td>
                                <td class="text-right">
                                    @php
                                        $base_interna = $factura->subtotal - $factura->monto_descuento - $factura->monto_descuento_extra;
                                        $pct_interno_efectivo = $base_interna > 0 ? ($factura->monto_descuento_interno / $base_interna) * 100 : 0;
                                    @endphp
                                    {{ number_format($pct_interno_efectivo, 2) }}%<br><small>${{ number_format($factura->monto_descuento_interno, 2) }}</small>
                                </td>
                                <td class="text-right">${{ number_format($factura->iva, 2) }}</td>
                                <td class="text-right"><strong>${{ number_format($factura->total, 2) }}</strong></td>
                                <td class="text-right">{{ number_format($factura->porcentaje_pronto_pago ?? 0, 2) }}%<br><small>${{ number_format($factura->monto_pronto_pago ?? 0, 2) }}</small></td>
                                <td class="text-right"><strong>${{ number_format($factura->saldo_pendiente, 2) }}</strong></td>
                            </tr>
                        @endforeach
                        <tr class="totals">
                            <td colspan="9" class="text-right">Total Pendiente {{ $proveedor->nombre }}:</td>
                            <td class="text-right">${{ number_format($proveedor->total_deuda, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                @endif
            </div>
        @endforeach
    @else
        <p style="text-align:center; color:#64748b;">No hay proveedores con cuentas por pagar pendientes.</p>
    @endif

</body>
</html>
