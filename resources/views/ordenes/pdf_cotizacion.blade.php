<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización - {{ $orden->folio }}</title>
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
            color: #d97706; /* Ámbar/Amarillo para diferenciar cotización */
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
            border-left: 3px solid #d97706;
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
                    <h1 class="title">COTIZACIÓN</h1>
                    <p>
                        Referencia: <strong>{{ $orden->folio }}</strong><br>
                        Fecha Cotización: {{ now()->format('d/m/Y') }}
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
                </td>
                <td style="width: 50%;">
                    <span class="label">Vehículo:</span>
                    <span class="value">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ({{ $orden->vehiculo->anio }})</span>
                    <div style="font-size: 10px; color: #000000ff;">
                        Placas: <strong>{{ $orden->placas ?: $orden->vehiculo->placas }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Presupuesto de Servicios y Refacciones</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;" class="text-center">Cantidad</th>
                    <th style="width: 60%;">Descripción del Servicio / Producto</th>
                    <th style="width: 15%;" class="text-right">Precio</th>
                    <th style="width: 15%;" class="text-right">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden->detalles as $detalle)
                <tr>
                    <td class="text-center">{{ (float)$detalle->cantidad }}</td>
                    <td class="text-left">
                        <strong style="text-transform: uppercase; font-size: 10px;">
                            {{ $detalle->producto ? $detalle->producto->descripcion : $detalle->servicio->descripcion }}
                        </strong>
                    </td>
                    <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-right font-bold">${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <table style="width: 100%;">
                    <tr class="grand-total">
                        <td class="text-right" style="font-size: 13px;">TOTAL:</td>
                        <td class="text-right" style="font-size: 16px;">${{ number_format($orden->total, 2) }}</td>
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
