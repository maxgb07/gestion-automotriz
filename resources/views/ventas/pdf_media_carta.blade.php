<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Venta - {{ $venta->folio }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a1a;
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
            width: 100px;
        }
        .company-info {
            text-align: right;
            vertical-align: top;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #1a56db;
            margin: 0;
            text-transform: uppercase;
        }
        .sale-info {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table th {
            background-color: #f3f4f6;
            padding: 5px;
            border-bottom: 1px solid #d1d5db;
            text-align: left;
            text-transform: uppercase;
            font-size: 8px;
            color: #000000ff;
        }
        .table td {
            padding: 5px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        .total-section {
            width: 100%;
            text-align: right;
        }
        .total-box {
            display: inline-block;
            width: 180px;
        }
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
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
                        <h1 class="title" style="color: #1a56db; font-size: 14px;">{{ config('app.name') }}</h1>
                    @endif
                </td>
                <td class="company-info" style="width: 60%">
                    <h1 class="title" style="font-size: 14px;">NOTA DE VENTA</h1>
                    <p style="margin: 2px 0;">
                        Folio: <strong>{{ $venta->folio }}</strong><br>
                        Fecha: {{ $venta->fecha->format('d/m/Y') }}<br>
                        Método de Pago: {{ $venta->metodo_pago }}
                    </p>
                </td>
            </tr>
        </table>

        <table class="sale-info">
            <tr>
                <td style="width: 100%;">
                    <span style="color: #000000ff; text-transform: uppercase; font-size: 8px;">Cliente:</span><br>
                    <strong style="font-size: 11px;">{{ $venta->cliente->nombre }}</strong><br>
                    <!-- {{ $venta->cliente->telefono ?? '' }} -->
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;" class="text-center">Cantidad</th>
                    <th style="width: 65%;">Descripción</th>
                    <th style="width: 25%;" class="text-right">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td class="text-center">{{ (float)$detalle->cantidad }}</td>
                    <td class="text-left">
                        <strong class="uppercase" style="font-size: 9px;">
                            {{ $detalle->producto ? $detalle->producto->nombre : $detalle->servicio->nombre }} - {{ $detalle->producto ? $detalle->producto->descripcion : $detalle->servicio->descripcion }}
                        </strong>
                    </td>
                    <td class="text-right font-bold">${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <table style="width: 100%;">
                    <tr class="grand-total">
                        <td class="text-right" style="font-size: 11px;">TOTAL:</td>
                        <td class="text-right" style="font-size: 14px;">${{ number_format($venta->total, 2) }}</td>
                    </tr>
                    <!-- @if($venta->saldo_pendiente > 0)
                    <tr>
                        <td class="text-right" style="color: #dc2626; padding-top: 3px; font-size: 9px;">Saldo Pendiente:</td>
                        <td class="text-right" style="color: #dc2626; padding-top: 3px; font-size: 9px;">${{ number_format($venta->saldo_pendiente, 2) }}</td>
                    </tr>
                    @endif -->
                </table>
            </div>
        </div>

        <div class="footer">
           {{ config('app.name') }} - Gracias por su preferencia.<br>
           Este documento no representa un comprobante fiscal.
        </div>
    </div>
</body>
</html>
