<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; }
        .date { font-size: 10px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 10px; text-align: center; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        td { border: 1px solid #cbd5e1; padding: 8px; font-size: 11px; }
        .low-stock { color: #dc2626; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding: 10px 0; border-top: 1px solid #eee; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .uppercase { text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">REPORTE DE PEDIMENTO - EXISTENCIAS MÍNIMAS</div>
        <div class="date">GENERADO EL: {{ date('d/m/Y H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">#</th>
                <th width="20%" class="text-center">SKU / CLAVE</th>
                <th width="40%" class="text-center">CÓDIGO BARRAS / DESCRIPCIÓN</th>
                <th width="10%" class="text-center">STOCK ACT.</th>
                <th width="10%" class="text-center">S. MÍN.</th>
                <th width="10%" class="text-center">SUGERIDO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $index => $producto)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <strong>{{ $producto->nombre }}</strong><br>
                        <span style="font-size: 10px; color: #1e40af;">MARCA: {{ $producto->marca ?? 'N/A' }}</span>
                    </td>
                    <td class="uppercase text-center">
                        <small style="color:#666">{{ $producto->codigo_barras ?? 'S/B' }}</small><br>
                        {{ $producto->descripcion }}<br>
                        <span style="font-size: 9px;">APLICA: {{ $producto->aplicacion ?? 'N/A' }}</span>
                    </td>
                    <td class="text-center {{ $producto->stock <= $producto->stock_minimo ? 'low-stock' : '' }}">
                        {{ $producto->stock }}
                    </td>
                    <td class="text-center">{{ $producto->stock_minimo }}</td>
                    <td class="text-center" style="background-color: #eff6ff; font-weight: bold;">
                        {{ ($producto->stock_minimo * 2) - $producto->stock > 0 ? ($producto->stock_minimo * 2) - $producto->stock : ($producto->stock_minimo - $producto->stock + 5) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($productos->isEmpty())
        <div style="text-align: center; margin-top: 50px; color: #666;">
            NO SE ENCONTRARON PRODUCTOS POR DEBAJO DEL STOCK MÍNIMO PARA ESTE PEDIMENTO.
        </div>
    @endif

    <div class="footer">
        SISTEMA DE GESTIÓN AUTOMOTRIZ - REPORTE DE INVENTARIO
    </div>
</body>
</html>
