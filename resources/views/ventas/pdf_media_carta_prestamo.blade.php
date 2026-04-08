<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vale de Préstamo - {{ $venta->folio }}</title>
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
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #000000ff;
            border-top: 0.5px solid #e5e7eb;
            padding-top: 5px;
        }
        .prestamo-legend {
            margin-top: 15px;
            padding: 5px 0;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
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
                    <h1 class="title" style="font-size: 14px;">VALE DE PRÉSTAMO</h1>
                    <p style="margin: 2px 0;">
                        Folio: <strong>{{ $venta->folio }}</strong><br>
                        Fecha: {{ $venta->fecha->format('d/m/Y') }}<br>
                        Método de Pago: <strong>PRÉSTAMO</strong>
                    </p>
                </td>
            </tr>
        </table>

        <table class="sale-info">
            <tr>
                <td style="width: 100%;">
                    <span style="color: #000000ff; text-transform: uppercase; font-size: 8px;">Cliente:</span><br>
                    <strong style="font-size: 11px;">{{ $venta->cliente->nombre }}</strong><br>
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;" class="text-center">Cantidad</th>
                    <th style="width: 90%;">Descripción</th>
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
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="prestamo-legend">
            <span style="font-size: 8px;">ESTE MATERIAL SE ENTREGA EN CALIDAD DE PRÉSTAMO Y DEBERÁ SER DEVUELTO EN SU TOTALIDAD O PAGADO DE ACUERDO AL PRECIO VIGENTE DEL PRODUCTO, SEGÚN LOS TÉRMINOS ACORDADOS.</span>
        </div>

        @if($venta->observaciones)
        <div style="margin-top: 10px; border-top: 1px solid #e5e7eb; padding-top: 6px;">
            <p style="font-size: 8px; color: #6b7280; text-transform: uppercase; font-weight: bold; margin: 0 0 3px 0;">Observaciones:</p>
            <p style="font-size: 9px; color: #1a1a1a; margin: 0;">{{ $venta->observaciones }}</p>
        </div>
        @endif

        <div style="margin-top: 60px; width: 100%; text-align: center;">
            <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto; padding-top: 5px; font-weight: bold; text-transform: uppercase;">
                Nombre y Firma de Recepción
            </div>
        </div>

        <div class="footer">
           {{ config('app.name') }} - Gracias por su preferencia.<br>
           Este documento no representa un comprobante fiscal.
        </div>
    </div>
</body>
</html>
