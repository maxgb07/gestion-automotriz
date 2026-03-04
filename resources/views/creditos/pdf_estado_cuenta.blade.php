<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta - {{ $cliente->nombre }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            color: #000; 
            line-height: 1.4; 
            margin: 0;
            padding: 0;
        }
        .header { 
            width: 100%;
            margin-bottom: 25px; 
            border-bottom: 3px solid #1a56db; 
            padding-bottom: 15px; 
        }
        .header table { width: 100%; border: none; }
        .logo { width: 130px; }
        .company-info { text-align: right; vertical-align: top; }
        .title { 
            font-size: 20px; 
            font-weight: bold; 
            color: #1a56db; 
            margin: 0; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        
        .info-tablas { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-tablas td { vertical-align: top; width: 50%; border: none; padding: 0; }
        .client-card { 
            padding: 10px 0; 
            border-bottom: 1px solid #000; 
            margin-bottom: 20px;
        }
        .card-label { font-size: 8px; text-transform: uppercase; color: #000; font-weight: bold; margin-bottom: 2px; }
        .card-value { font-size: 11px; font-weight: bold; color: #000; }

        .section-title { 
            background: #f1f5f9; 
            padding: 6px 10px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-bottom: 10px; 
            border-left: 4px solid #1a56db;
            color: #1a56db;
            font-size: 10px;
        }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        table.data-table th { 
            background: #f8fafc; 
            padding: 8px 10px; 
            text-align: left; 
            text-transform: uppercase; 
            font-size: 8px; 
            color: #000;
            border-bottom: 1px solid #000;
        }
        table.data-table td { 
            padding: 8px 10px; 
            border-bottom: 1px solid #eee; 
            vertical-align: middle;
            color: #000;
        }
        
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .vencido { color: #dc2626; font-weight: bold; }
        
        .summary-section { 
            float: right; 
            width: 250px; 
            margin-top: 20px;
            text-align: right;
            padding-top: 10px;
            border-top: 2px solid #000;
        }
        .summary-label { font-size: 10px; text-transform: uppercase; color: #000; font-weight: bold; }
        .summary-value { font-size: 18px; font-weight: bold; color: #000; margin-top: 5px; }

        .footer { 
            position: fixed; 
            bottom: 0; 
            width: 100%; 
            text-align: center; 
            font-size: 8px; 
            color: #000; 
            border-top: 1px solid #eee; 
            padding-top: 10px; 
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
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
                        <h1 class="title">{{ config('app.name') }}</h1>
                    @endif
                </td>
                <td class="company-info">
                    <h1 class="title">Estado de Cuenta</h1>
                    <p style="margin: 5px 0 0 0; color: #000;">
                        Fecha de Corte: <strong>{{ now()->format('d/m/Y') }}</strong><br>
                        Hora: {{ now()->format('H:i A') }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <div class="client-card">
        <div class="card-label">Cliente</div>
        <div class="card-value" style="font-size: 14px;">{{ $cliente->nombre }}</div>
        <div style="margin-top: 8px;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 33%; border: none; padding: 0;">
                        <span class="card-label">RFC:</span><br>
                        <span class="card-value">{{ $cliente->rfc ?? 'XAXX010101000' }}</span>
                    </td>
                    <td style="width: 33%; border: none; padding: 0;">
                        <span class="card-label">Teléfono:</span><br>
                        <span class="card-value">{{ $cliente->telefono ?? $cliente->celular ?? 'S/N' }}</span>
                    </td>
                    <td style="width: 33%; border: none; padding: 0;">
                        <span class="card-label">Email:</span><br>
                        <span class="card-value" style="text-transform: lowercase;">{{ $cliente->email ?? 'N/A' }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @if($ventas->count() > 0)
        <div class="section-title">Detalle de Ventas Pendientes</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Folio</th>
                    <th style="width: 20%;">Fecha</th>
                    <th style="width: 20%;">Vencimiento</th>
                    <th style="width: 20%; text-align: right;">Total Doc.</th>
                    <th style="width: 25%; text-align: right;">Saldo Pendiente</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $venta)
                    <tr>
                        <td class="text-bold">{{ $venta->folio }}</td>
                        <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                        <td class="{{ $venta->fecha->lt(now()->subDays(15)) ? 'vencido' : '' }}">
                            {{ $venta->fecha->addDays(15)->format('d/m/Y') }}
                        </td>
                        <td class="text-right">${{ number_format($venta->total, 2) }}</td>
                        <td class="text-right text-bold" style="color: #1e293b;">${{ number_format($venta->saldo_pendiente, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($ordenes->count() > 0)
        <div class="section-title">Detalle de Órdenes de Servicio Pendientes</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Folio</th>
                    <th style="width: 20%;">Fecha</th>
                    <th style="width: 20%;">Vencimiento</th>
                    <th style="width: 20%; text-align: right;">Total Doc.</th>
                    <th style="width: 25%; text-align: right;">Saldo Pendiente</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordenes as $orden)
                    <tr>
                        <td class="text-bold">
                            {{ $orden->folio }}<br>
                            <span style="font-size: 8px; font-weight: bold; color: #000;">
                                {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}
                            </span>
                        </td>
                        <td>{{ $orden->fecha_entrada->format('d/m/Y') }}</td>
                        <td class="{{ $orden->fecha_entrada->lt(now()->subDays(15)) ? 'vencido' : '' }}">
                            {{ $orden->fecha_entrada->addDays(15)->format('d/m/Y') }}
                        </td>
                        <td class="text-right">${{ number_format($orden->total, 2) }}</td>
                        <td class="text-right text-bold" style="color: #1e293b;">${{ number_format($orden->saldo_pendiente, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="summary-section">
        <div class="summary-label">Saldo Total Adeudado</div>
        <div class="summary-value">
            ${{ number_format($ventas->sum('saldo_pendiente') + $ordenes->sum('saldo_pendiente'), 2) }}
        </div>
    </div>

    <div class="footer">
        Este documento es un comprobante informativo de saldos a la fecha de emisión.<br>
        <strong>{{ config('app.name') }}</strong>
    </div>
</body>
</html>
