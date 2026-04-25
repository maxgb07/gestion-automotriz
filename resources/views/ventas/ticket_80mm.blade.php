<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $venta->folio }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            width: 80mm;
            margin: 0;
            padding: 2mm;
            font-size: 12px;
            color: #000;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header {
            margin-bottom: 4mm;
        }
        .logo {
            max-width: 60mm;
            margin-bottom: 3mm;
        }
        .datos-emisor {
            font-size: 12px;
            line-height: 1.2;
            margin-bottom: 4mm;
        }
        .divider {
            border-top: 2px dashed #000;
            margin: 3mm 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            border-bottom: 1px dashed #000;
            text-align: left;
            padding: 1mm 0;
            font-size: 12px;
        }
        .table td {
            padding: 2mm 0;
            vertical-align: top;
            font-size: 12px;
        }
        .totals {
            margin-top: 3mm;
        }
        .footer {
            margin-top: 6mm;
            font-size: 12px;
        }
        
        /* Forzar 12px en todos los elementos secundarios */
        h2, span, p, div, td, th, small, i, em {
            font-size: 12px !important;
            font-style: normal !important;
            font-weight: bold !important;
        }

        /* Clase especial para el Total */
        .total-row td {
            font-size: 14px !important;
        }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body {
                width: 80mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header text-center">
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
            <h2 class="uppercase" style="margin: 0;">{{ config('app.name') }}</h2>
        @endif
        
        <div class="datos-emisor uppercase">
            MAXIMILIANO GONZÁLEZ MALDONADO<br>
            RFC: GOMM671129AQ9<br>
            SOMBREREROS DE JARACUARO #299. COL. VASCO DE QUIROGA, CP 58230 MORELIA, MICH.<br>
            TEL: 4433143310<br>
            EMAIL: MAXANGUIANO@HOTMAIL.COM
        </div>

        <div class="divider"></div>

        <p class="uppercase" style="margin: 0;">
            FOLIO: {{ $venta->folio }}<br>
            FECHA: {{ $venta->fecha->format('d/m/Y') }}<br>
            FORMA DE PAGO: {{ $venta->metodo_pago }}
        </p>
    </div>

    <div class="divider"></div>

    <div class="cliente">
        <span class="uppercase">CLIENTE: {{ $venta->cliente->nombre }}</span>
    </div>

    <div class="divider"></div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 15%">CANT</th>
                <th style="width: 55%">DESCRIPCIÓN</th>
                <th style="width: 30%" class="text-right">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td>{{ (float)$detalle->cantidad }}</td>
                <td class="uppercase">
                    @if($detalle->producto)
                        {{ $detalle->producto->nombre }}
                        @if($detalle->producto->sku)
                            ({{ $detalle->producto->sku }})
                        @endif
                        <br>
                        <span>{{ $detalle->producto->descripcion }}</span>
                    @else
                        {{ $detalle->servicio->nombre }}
                        <br>
                        <span>{{ $detalle->servicio->descripcion }}</span>
                    @endif
                </td>
                <td class="text-right">${{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="totals">
        <table style="width: 100%">
            <tr class="total-row">
                <td class="text-right">TOTAL MXN:</td>
                <td class="text-right">${{ number_format($venta->total, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($venta->observaciones)
    <div class="divider"></div>
    <div class="observaciones uppercase">
        <span>NOTAS:</span><br>
        {{ $venta->observaciones }}
    </div>
    @endif

    <div class="footer text-center uppercase">
        <div class="divider"></div>
        {{ config('app.name') }} - GRACIAS POR SU PREFERENCIA.<br>
        ESTE DOCUMENTO NO REPRESENTA UN COMPROBANTE FISCAL.
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 1000);
        };
    </script>
</body>
</html>
