<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventario Físico - {{ $marca }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 18px;
        }
        .info {
            margin-bottom: 10px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th {
            background-color: #f2f2f2;
            padding: 8px;
            text-transform: uppercase;
        }
        td {
            padding: 6px;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .espacio-conteo {
            width: 80px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventario Físico de Productos</h1>
    </div>

    <div class="info">
        MARCA: {{ $marca }}<br>
        FECHA: {{ date('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="40%">CLAVE / PRODUCTO</th>
                <th width="30%">APLICACIÓN</th>
                <th width="10%">STOCK SISTEMA</th>
                <th width="15%">CONTEO FÍSICO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $index => $producto)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $producto->nombre }}</strong><br>
                        <small>{{ $producto->descripcion }}</small>
                    </td>
                    <td>{{ $producto->aplicacion }}</td>
                    <td class="text-center">{{ $producto->stock }}</td>
                    <td class="espacio-conteo"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p>FECHA DE CIERRE: ___________________________</p>
        <p>REALIZADO POR: ___________________________</p>
    </div>
</body>
</html>
