<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Servicio - {{ $orden->folio }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px 30px;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        .logo {
            width: 140px;
        }
        .company-info {
            text-align: right;
            vertical-align: top;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1a56db;
            margin: 0;
            text-transform: uppercase;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 5px 10px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            color: #000000ff;
            margin-bottom: 10px;
            border-left: 3px solid #1a56db;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-grid td {
            vertical-align: top;
            padding-bottom: 10px;
        }
        .label {
            color: #000000ff;
            text-transform: uppercase;
            font-size: 9px;
            display: block;
            margin-bottom: 2px;
        }
        .value {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f9fafb;
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            text-transform: uppercase;
            font-size: 9px;
            color: #000000ff;
        }
        .table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .total-section {
            width: 100%;
            text-align: right;
        }
        .total-box {
            display: inline-block;
            width: 250px;
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }
        .total-row {
            margin-bottom: 5px;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #000000ff;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 30px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 9px;
            color: #000000ff;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header">
            <tr>
                <td>
                    @php
                        $logoPath = storage_path('app/public/logos/logo-venta.png');
                        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                        $logoSrc = $logoData ? 'data:image/png;base64,' . $logoData : '';
                    @endphp
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" class="logo">
                    @else
                        <h1 class="title">{{ config('app.name') }}</h1>
                    @endif
                </td>
                <td class="company-info">
                    <h1 class="title">ORDEN DE SERVICIO</h1>
                    <p>
                        Folio: <strong>{{ $orden->folio }}</strong><br>
                        @if($orden->fecha_entrega)
                        Fecha Entrega: {{ $orden->fecha_entrega->format('d/m/Y') }}<br>
                        @endif
                        Método de Pago: {{ $orden->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'PENDIENTE' }}
                        <!-- @if($orden->mecanico)
                        <br>Atendió: <strong>{{ $orden->mecanico }}</strong>
                        @endif -->
                    </p>
                </td>
            </tr>
        </table>

        <div class="section-title">Información del Cliente y Vehículo</div>
        <table class="info-grid">
            <tr>
                <td style="width: 50%;">
                    <span class="label">Cliente:</span>
                    <span class="value">{{ $orden->cliente->nombre }}</span>
                    <!-- <div style="font-size: 10px; color: #4b5563;">
                        {{ $orden->cliente->telefono ?? 'S/T' }}<br>
                        {{ $orden->cliente->email ?? '' }}
                    </div> -->
                </td>
                <td style="width: 50%;">
                    <span class="label">Vehículo:</span>
                    <span class="value">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ({{ $orden->vehiculo->anio }})</span>
                    <div style="font-size: 10px; color: #000000ff;">
                        Placas: <strong>{{ $orden->placas ?: $orden->vehiculo->placas }}</strong><br>
                        @if($orden->kilometraje_entrega)
                        <br>Km: {{ number_format($orden->kilometraje_entrega) }} km
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        @if($orden->falla_reportada)
        <div class="section-title">Falla Reportada / Motivo</div>
        <div style="margin-bottom: 20px; padding: 10px; background-color: #fdf2f2; border: 1px solid #fecaca; border-radius: 5px; font-style: italic;">
            {{ $orden->falla_reportada }}
        </div>
        @endif

        <div class="section-title">Servicios y Refacciones</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;" class="text-center">Cantidad</th>
                    <th style="width: 20%;">Clave</th>
                    <th style="width: 40%;">Descripción</th>
                    <th style="width: 15%;" class="text-right">Precio</th>
                    <!-- <th style="width: 15%;" class="text-right">DESC.</th> -->
                    <th style="width: 15%;" class="text-right">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden->detalles as $detalle)
                <tr>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-left">
                        <strong style="text-transform: uppercase;">
                            {{ $detalle->producto ? $detalle->producto->nombre : $detalle->servicio->nombre }}
                        </strong>
                        <div style="font-size: 8px; color: #000000ff; text-transform: uppercase;">
                            {{ $detalle->producto ? 'PRODUCTO' : 'SERVICIO' }}
                        </div>
                    </td>
                    <td class="text-left">
                        <div style="font-size: 9px; color: #000000ff;">
                            {{ $detalle->producto ? $detalle->producto->descripcion : $detalle->servicio->descripcion }}
                        </div>
                    </td>
                    <td class="text-left">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <!-- <td class="text-right">
                        @if($detalle->descuento_porcentaje > 0)
                            <span style="color: #000000ff;">{{ number_format($detalle->descuento_porcentaje, 1) }}%</span>
                        @else
                            <span style="color: #000000ff;">-</span>
                        @endif
                    </td> -->
                    <td class="text-left font-bold">${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <table style="width: 100%;">
                   <!--  <tr>
                        <td class="text-right" style="color: #000000ff;">SUBTOTAL:</td>
                        <td class="text-right" style="width: 100px;">${{ number_format($orden->detalles->sum(function($d){ return $d->precio_unitario * $d->cantidad; }), 2) }}</td>
                    </tr>
                    @php $descTotal = $orden->detalles->sum('descuento_monto'); @endphp
                    @if($descTotal > 0)
                    <tr>
                        <td class="text-right" style="color: #000000ff;">DESCUENTO:</td>
                        <td class="text-right" style="color: #000000ff;">-${{ number_format($descTotal, 2) }}</td>
                    </tr>
                    @endif -->
                    <tr class="grand-total">
                        <td class="text-right" style="font-size: 13px;">TOTAL:</td>
                        <td class="text-right" style="font-size: 16px;">${{ number_format($orden->total, 2) }}</td>
                    </tr>
                    <!-- @php $pagado = $orden->pagos->sum('monto'); @endphp -->
                    <!-- @if($pagado > 0)
                    <tr>
                        <td class="text-right" style="color: #059669; padding-top: 5px;">TOTAL PAGADO:</td>
                        <td class="text-right" style="color: #059669; padding-top: 5px;">${{ number_format($pagado, 2) }}</td>
                    </tr>
                    @endif -->
                    <!-- @if($orden->saldo_pendiente > 0)
                    <tr>
                        <td class="text-right" style="color: #000000ff; padding-top: 5px; font-weight: bold;">SALDO PENDIENTE:</td>
                        <td class="text-right" style="color: #000000ff; padding-top: 5px; font-weight: bold;">${{ number_format($orden->saldo_pendiente, 2) }}</td>
                    </tr>
                    @endif -->
                </table>
            </div>
        </div>

        @if($orden->observaciones_post_reparacion)
        <div style="margin-top: 20px;">
            <div class="section-title">Observaciones</div>
            <div style="font-size: 10px; color: #000000ff;">
                <!-- @if($orden->observaciones)
                    <div><strong>RECEPCIÓN:</strong> {{ $orden->observaciones }}</div>
                @endif -->
                @if($orden->observaciones_post_reparacion)
                    <div style="margin-top: 5px;"><strong>POST-REPARACIÓN:</strong> {{ $orden->observaciones_post_reparacion }}</div>
                @endif
            </div>
        </div>
        @endif

        <div class="footer">
            {{ config('app.name') }} - Gracias por su confianza.<br>
            Este documento es un comprobante de servicio y garantía de los trabajos realizados.
        </div>
    </div>
</body>
</html>
