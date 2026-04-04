<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Cobranza</title>
    <style>
        @page { 
            margin: 1cm;
            margin-bottom: 2.5cm; /* Reservar espacio para el footer fixed */
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; /* Incrementado para legibilidad similar a text-md */
            color: #1e293b; 
            line-height: 1.5; 
        }
        .header { 
            width: 100%;
            margin-bottom: 20px; 
            border-bottom: 2px solid #1e293b; 
            padding-bottom: 10px; 
        }
        .header table { width: 100%; }
        .logo { width: 120px; }
        .title { 
            font-size: 18px; 
            font-weight: bold; 
            color: #1e293b; 
            text-transform: uppercase; 
            margin: 0;
        }
        .subtitle { 
            font-size: 10px; 
            color: #64748b; 
            text-transform: uppercase; 
            font-weight: bold;
        }
        
        /* Contenedor por cliente para evitar saltos de página a mitad de tabla */
        .client-group {
            page-break-inside: avoid;
            margin-bottom: 30px;
        }

        .client-header {
            background: #f8fafc;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-left: 5px solid #3b82f6;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px; /* Más grande para destacar */
            color: #1e293b;
        }

        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
        }
        table.data-table th { 
            background: #f1f5f9; 
            padding: 8px 10px; 
            text-align: center; 
            text-transform: uppercase; 
            font-size: 10px; 
            border-bottom: 2px solid #cbd5e1;
        }
        table.data-table td { 
            padding: 8px 10px; 
            border-bottom: 1px solid #f1f5f9; 
            vertical-align: middle;
            font-size: 11px;
            text-align: center;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .vencido { color: #ef4444; }
        
        .client-total {
            text-align: right;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 12px;
            border-top: 1px dashed #1e293b;
            background: #f8fafc;
        }

        .grand-total-box {
            margin-top: 40px;
            border: 2px solid #1e293b;
            padding: 20px;
            text-align: right;
            background: #f8fafc;
            page-break-inside: avoid;
        }
        
        .footer { 
            position: fixed; 
            bottom: -1cm; /* Ajustado para estar dentro del margen reservado */
            left: 0;
            right: 0;
            height: 2cm;
            width: 100%; 
            text-align: center; 
            font-size: 9px; 
            color: #64748b; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 15px; 
        }
    </style>
</head>
<body>
    <div class="header">
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%">
                    <h1 class="title">REPORTE DE COBRANZA</h1>
                    <div class="subtitle">TIPO: {{ $tipo_reporte == 'AMBOS' ? 'ORDENES Y VENTAS' : $tipo_reporte }}</div>
                </td>
                <td width="30%" align="right">
                    <div style="font-weight: bold; font-size: 12px;">{{ config('app.name') }}</div>
                    <div>Fecha: {{ $fecha_reporte }}</div>
                </td>
            </tr>
        </table>
    </div>

    @foreach($datos as $cliente)
        <div class="client-group">
            <div class="client-header">
                {{ $cliente['nombre'] }}
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="12%">TIPO</th>
                        <th width="15%">FOLIO</th>
                        <th width="18%">EMISIÓN</th>
                        <th width="18%">VENCIMIENTO</th>
                        <th width="18%">TOTAL</th>
                        <th width="19%">SALDO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cliente['documentos'] as $doc)
                        <tr>
                            <td class="text-bold">{{ $doc['tipo'] }}</td>
                            <td class="text-bold">{{ $doc['folio'] }}</td>
                            <td>{{ $doc['fecha_emision']->format('d/m/Y') }}</td>
                            <td class="{{ $doc['fecha_vencimiento']->lt(now()) ? 'vencido text-bold' : '' }}">
                                {{ $doc['fecha_vencimiento']->format('d/m/Y') }}
                            </td>
                            <td class="text-bold">${{ number_format($doc['total'], 2) }}</td>
                            <td class="text-bold">${{ number_format($doc['saldo'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="client-total">
                SUBTOTAL {{ $cliente['nombre'] }}: ${{ number_format($cliente['total_cliente'], 2) }}
            </div>
        </div>
    @endforeach

    <div class="grand-total-box">
        <div style="font-size: 12px; text-transform: uppercase; font-weight: bold; color: #64748b;">Total General por Cobrar</div>
        <div style="font-size: 24px; font-weight: bold; color: #1e293b;">${{ number_format($datos->sum('total_cliente'), 2) }}</div>
    </div>

    <div class="footer">
        Este documento es un reporte interno de cobranza y no tiene validez oficial como factura.<br>
        Generado por <strong>{{ config('app.name') }}</strong> - {{ date('Y') }}
    </div>
</body>
</html>
