{{--
    Pagaré parcial — se incluye en los PDFs de Orden de Servicio y Venta
    cuando el método de pago es CRÉDITO.

    Variables esperadas:
      $cliente        — instancia del modelo Cliente
      $total          — monto numérico del total
      $folio          — folio del documento
      $fechaPago      — string de la fecha de vencimiento (d/m/Y) o null para dejar en blanco
--}}
@php
    /* ---------------------------------------------------------------
     * Convierte un número entero a palabras en español (hasta 999 999)
     * ---------------------------------------------------------------*/
    $numeroALetras = function(int $n) use (&$numeroALetras): string {
        $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
                     'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS',
                     'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
        $decenas  = ['', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS',
                     'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        if ($n === 0)   return 'CERO';
        if ($n === 100) return 'CIEN';

        $texto = '';

        if ($n >= 1000) {
            $miles = (int)($n / 1000);
            $resto = $n % 1000;
            $texto .= ($miles === 1 ? 'MIL' : $numeroALetras($miles) . ' MIL');
            if ($resto > 0) $texto .= ' ';
            $n = $resto;
        }

        if ($n >= 100) {
            $texto .= $centenas[(int)($n / 100)];
            $n %= 100;
            if ($n > 0) $texto .= ' ';
        }

        if ($n >= 20) {
            $texto .= $decenas[(int)($n / 10)];
            $n %= 10;
            if ($n > 0) $texto .= ' Y ' . $unidades[$n];
        } elseif ($n > 0) {
            $texto .= $unidades[$n];
        }

        return trim($texto);
    };

    $entero    = (int) floor($total);
    $centavos  = (int) round(($total - $entero) * 100);
    $enLetras  = $numeroALetras($entero) . ' PESOS ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100 M.N.';

    $appName    = config('app.razon_social', config('app.name'));
    $ciudad     = 'MORELIA, MICHOACÁN';
    $fechaDoc   = $fechaEmision ?? now()->format('d/m/Y');
@endphp

<div style="
    margin-top: 28px;
    border: 1.5px solid #333;
    padding: 14px 16px 10px 16px;
    font-family: 'Helvetica', 'Arial', sans-serif;
    font-size: 9px;
    color: #111;
    line-height: 1.5;
    page-break-inside: avoid;
">

    {{-- ── Encabezado ──────────────────────────────────────── --}}
    <table style="width: 100%; margin-bottom: 6px; border-collapse: collapse;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; letter-spacing: 2px; vertical-align: bottom;">
                PAGARÉ
            </td>
            <td style="text-align: right; vertical-align: bottom; font-size: 8.5px; line-height: 1.6;">
                No. 1 de 1
                &nbsp;&nbsp;&nbsp;
                <strong>Bueno por ${{ number_format($total, 2) }}</strong><br>
                {{ $ciudad }} a {{ $fechaDoc }}
            </td>
        </tr>
    </table>

    <hr style="border: none; border-top: 1px solid #555; margin: 4px 0 8px 0;">

    {{-- ── Cuerpo principal ────────────────────────────────── --}}
    <p style="margin: 0 0 8px 0; text-align: justify;">
        Debo(emos) y pagaré(mos) incondicionalmente por este pagaré a la orden de
        <strong>{{ mb_strtoupper($appName, 'UTF-8') }}</strong> en sus oficinas de {{ $ciudad }},
        o donde elija el beneficiario, el día <strong>{{ $fechaPago ?? '___________________' }}</strong>.
    </p>

    <p style="margin: 0 0 8px 0;">
        La cantidad de:
        (<strong>{{ $enLetras }}</strong>)
    </p>

    <p style="margin: 0 0 10px 0; text-align: justify;">
        Valor recibido a mi (nuestra) entera satisfacción. Este pagaré forma parte de una serie
        enumerada del 1 al 1 y todos están sujetos a la condición de que, al no pagarse cualquiera
        de ellos a su vencimiento, serán exigibles todos los que le sigan en número, además de los
        ya vencidos, desde la fecha de vencimiento de este documento hasta el día de su total
        liquidación, causará intereses moratorios al tipo de <strong>7% mensual</strong>, pagadero
        en esta ciudad juntamente con el principal.
    </p>

    {{-- ── Datos del Deudor + Firma ────────────────────────── --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 65%; vertical-align: top;">
                <p style="margin: 0 0 4px 0; font-weight: bold; font-size: 9px;">Nombre y datos del Deudor</p>
                <p style="margin: 0 0 3px 0;">
                    <strong>NOMBRE:</strong> {{ mb_strtoupper($cliente->nombre, 'UTF-8') }}
                </p>
                @if($cliente->direccion)
                <p style="margin: 0 0 3px 0;">
                    <strong>Dirección:</strong> {{ mb_strtoupper($cliente->direccion, 'UTF-8') }}
                </p>
                @endif
                <p style="margin: 0;">
                    <strong>Población:</strong> {{ $ciudad }}
                </p>
            </td>
            <td style="width: 35%; vertical-align: bottom; text-align: center;">
                <p style="margin: 0 0 2px 0;">Acepto(mos):</p>
                <div style="
                    border-bottom: 1px solid #333;
                    min-height: 40px;
                    margin: 4px 10px 0 10px;
                "></div>
                <p style="margin: 4px 0 0 0; font-size: 8px;">Acepto(mos)</p>
            </td>
        </tr>
    </table>

</div>
