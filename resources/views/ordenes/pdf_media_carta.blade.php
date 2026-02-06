<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Servicio - {{ $orden->folio }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000ff;
            font-size: 9px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 5px;
        }
        .header {
            width: 100%;
            margin-bottom: 10px;
        }
        .logo {
            width: 90px;
        }
        .company-info {
            text-align: right;
            vertical-align: top;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            color: #1a56db;
            margin: 0;
            text-transform: uppercase;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 3px 8px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            color: #000000ff;
            margin: 8px 0 5px 0;
            border-left: 2px solid #1a56db;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .info-grid td {
            vertical-align: top;
            padding-bottom: 5px;
        }
        .label {
            color: #000000ff;
            text-transform: uppercase;
            font-size: 7px;
            display: block;
        }
        .value {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .table th {
            background-color: #f3f4f6;
            padding: 4px 6px;
            border-bottom: 1px solid #d1d5db;
            text-align: left;
            text-transform: uppercase;
            font-size: 7px;
            color: #000000ff;
        }
        .table td {
            padding: 4px 6px;
            border-bottom: 1px solid #f3f4f6;
        }
        .total-section {
            width: 100%;
            text-align: right;
        }
        .total-box {
            display: inline-block;
            width: 180px;
            background-color: #f9fafb;
            padding: 10px;
            border-radius: 5px;
        }
        .grand-total {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            border-top: 1px solid #d1d5db;
            padding-top: 5px;
            margin-top: 5px;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #000000ff;
            border-top: 0.5px solid #e5e7eb;
            padding-top: 5px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="container">
        <table class="header">
            <tr>
                <td style="width: 40%">
                    @php
                        $logoPath = storage_path('app/public/logos/logo-venta.png');
                        $logoSrc = '';
                        if (file_exists($logoPath)) {
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoSrc = 'data:image/png;base64,' . $logoData;
                        }
                    @endphp
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" class="logo">
                    @else
                        <h1 class="title" style="font-size: 12px;">{{ config('app.name') }}</h1>
                    @endif
                </td>
                <td class="company-info" style="width: 60%">
                    <h1 class="title">ORDEN DE SERVICIO</h1>
                    <p style="margin: 2px 0;">
                        Folio: <strong>{{ $orden->folio }}</strong><br>
                        Fecha: {{ $orden->fecha_entrega ? $orden->fecha_entrega->format('d/m/Y') : 'PENDIENTE' }}<br>
                        Método de Pago: {{ $orden->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'PENDIENTE' }}
                        <!-- @if($orden->mecanico)
                        <br>Atendió: <strong>{{ $orden->mecanico }}</strong>
                        @endif -->
                    </p>
                </td>
            </tr>
        </table>

        <div class="section-title">Cliente y Vehículo</div>
        <table class="info-grid">
            <tr>
                <td style="width: 40%;">
                    <span class="value">Cliente: {{ $orden->cliente->nombre }}</span>
                </td>
                <td style="width: 60%;">
                    <span class="value">Vehículo: {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} | Placas: {{ $orden->placas ?: $orden->vehiculo->placas }} | Km: {{ number_format($orden->kilometraje_entrega ?? $orden->kilometraje_entrada) }}</span>
                </td>
            </tr>
        </table>

        <!-- @if($orden->falla_reportada)
        <div class="section-title">Motivo / Falla</div>
        <div style="padding: 5px; border: 0.5px solid #e5e7eb; border-radius: 3px; font-size: 8px;">
            {{ $orden->falla_reportada }}
        </div>
        @endif -->

        <div class="section-title">Servicios y Refacciones</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;" class="text-center">Cantidad</th>
                    <th style="width: 65%;">Descripción</th>
                    <th style="width: 25%;">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden->detalles as $detalle)
                <tr>
                    <td class="text-center">{{ (float)$detalle->cantidad }}</td>
                    <td class="text-left">
                        <strong class="uppercase" style="font-size: 8px;">
                            {{ $detalle->producto ? $detalle->producto->nombre : $detalle->servicio->nombre }} - {{ $detalle->producto ? $detalle->producto->descripcion : $detalle->servicio->descripcion }}
                        </strong>
                    </td>
                    <td class="font-bold">${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <table style="width: 100%;">
                    <tr class="grand-total">
                        <td style="font-size: 9px; border:none; padding-top:0;">TOTAL:</td>
                        <td style="font-size: 12px; border:none; padding-top:0;">${{ number_format($orden->total, 2) }}</td>
                    </tr>
                    <!-- @if($orden->saldo_pendiente > 0)
                    <tr>
                        <td class="text-right" style="color: #6b7280; font-size: 8px;">Saldo:</td>
                        <td class="text-right" style="color: #6b7280; font-size: 8px;">${{ number_format($orden->saldo_pendiente, 2) }}</td>
                    </tr>
                    @endif -->
                </table>
            </div>
        </div>

        @if($orden->observaciones_post_reparacion)
        <div style="margin-top: 10px;">
            <div class="section-title">Observaciones</div>
            <div style="font-size: 8px; color: #000000ff;">
                <strong>POST-REPARACIÓN:</strong> {{ $orden->observaciones_post_reparacion }}
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
