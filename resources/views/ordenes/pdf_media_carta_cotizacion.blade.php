<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización - {{ $orden->folio }}</title>
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
            color: #d97706; /* Ámbar/Amarillo */
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
            border-left: 2px solid #d97706;
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
                    <h1 class="title">COTIZACIÓN</h1>
                    <p style="margin: 2px 0;">
                        Referencia: <strong>{{ $orden->folio }}</strong><br>
                        Cotizado: {{ now()->format('d/m/Y') }}
                    </p>
                </td>
            </tr>
        </table>

        <div class="section-title">Cliente y Vehículo</div>
        <table class="info-grid">
            <tr>
                <td style="width: 50%;">
                    <span class="label">Cliente:</span>
                    <span class="value">{{ $orden->cliente->nombre }}</span>
                </td>
                <td style="width: 50%;">
                    <span class="label">Vehículo:</span>
                    <span class="value">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</span>
                </td>
            </tr>
        </table>

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
                            {{ $detalle->producto ? $detalle->producto->descripcion : $detalle->servicio->descripcion }}
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
                </table>
            </div>
        </div>

        <div class="footer">
            {{ config('app.name') }} - Esta cotización tiene una vigencia de 15 días.<br>
            Los precios están sujetos a cambios sin previo aviso según disponibilidad de refacciones.
        </div>
    </div>
</body>
</html>
