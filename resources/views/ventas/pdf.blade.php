<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Venta - {{ $venta->folio }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 30px;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
        }
        .logo {
            width: 150px;
        }
        .company-info {
            text-align: right;
            vertical-align: top;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #1a56db;
            margin: 0;
            text-transform: uppercase;
        }
        .sale-info {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #f9fafb;
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            color: #000000ff;
        }
        .table td {
            padding: 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .total-section {
            width: 100%;
            text-align: right;
        }
        .total-box {
            display: inline-block;
            width: 250px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            border-top: 2px solid #111827;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 30px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 10px;
            color: #000000ff;
            border-top: 1px solid #000000ff;
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
                        $logoData = base64_encode(file_get_contents($logoPath));
                        $logoSrc = 'data:image/png;base64,' . $logoData;
                    @endphp
                    <img src="{{ $logoSrc }}" class="logo">
                </td>
                <td class="company-info">
                    <h1 class="title">NOTA DE VENTA</h1>
                    <p>
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
                    <span style="color: #000000ff; text-transform: uppercase; font-size: 10px;">Cliente:</span><br>
                    <strong style="font-size: 14px;">{{ $venta->cliente->nombre }}</strong><br>
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 8%;" class="text-center">Cantidad</th>
                    <th style="width: 20%;">Clave</th>
                    <th style="width: 40%;">Descripción</th>
                    <th style="width: 15%;" class="text-center">Precio</th>
                    <!-- <th style="width: 15%;" class="text-right">Desc.</th> -->
                    <th style="width: 15%;" class="text-center">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-left">
                        <strong style="text-transform: uppercase; font-size: 9px;">
                            {{ $detalle->producto ? $detalle->producto->nombre : $detalle->servicio->nombre }}
                        </strong>
                        <div style="font-size: 7px; color: #000000ff; text-transform: uppercase;">
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
                            <span style="color: #000000ff;">{{ number_format($detalle->descuento_porcentaje, 0) }}%</span>
                        @else
                            <span style="color: #000000ff;">-</span>
                        @endif
                    </td> -->
                    <td class="text-center font-bold">${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <table style="width: 100%;">
                    <!-- <tr>
                        <td class="text-right" style="font-size: 14px;">Subtotal:</td>
                        <td class="text-right" style="font-size: 14px;">${{ number_format($venta->detalles->sum('subtotal') + $venta->descuento, 2) }}</td>
                    </tr> -->
                    <!-- @if($venta->descuento > 0)
                    <tr>
                        <td class="text-right" style="color: #000000ff;">Descuento:</td>
                        <td class="text-right" style="color: #000000ff;">-${{ number_format($venta->descuento, 2) }}</td>
                    </tr>
                    @endif -->
                    <tr class="grand-total">
                        <td class="text-right" style="font-size: 14px;">TOTAL:</td>
                        <td class="text-right" style="font-size: 18px;">${{ number_format($venta->total, 2) }}</td>
                    </tr>
                    <!-- @if($venta->saldo_pendiente > 0)
                    <tr>
                        <td class="text-right" style="color: #000000ff; padding-top: 10px;">Saldo Pendiente:</td>
                        <td class="text-right" style="color: #000000ff; padding-top: 10px;">${{ number_format($venta->saldo_pendiente, 2) }}</td>
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
