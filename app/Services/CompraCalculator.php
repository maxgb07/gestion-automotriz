<?php

namespace App\Services;

class CompraCalculator
{
    /**
     * Calcula los totales de una compra en dos etapas:
     *
     * 1) Cascada COMERCIAL por producto (Global -> Extra -> Interno), sobre el precio
     *    CRUDO de cada producto (sin IVA). El IVA se calcula AL FINAL de la cascada,
     *    sobre la base ya neta de descuentos — igual que en una factura CFDI real
     *    (verificado contra una factura real de DAPESA: el IVA del SAT siempre se
     *    calcula sobre el importe ya con el descuento comercial aplicado, nunca al
     *    revés). Maniobra y Seguro entran a esta misma cascada como una línea más
     *    (participan de Subtotal/IVA, pero nunca reciben Extra ni Interno, y Global
     *    solo si su propio switch "Aplica descuento" está activo). El resultado de
     *    esta etapa es "Total a Pagar".
     *
     * 2) Descuento FINANCIERO por Pronto Pago (o Nota de Crédito del proveedor): un
     *    único porcentaje, capturado a mano para TODA la compra, que se aplica UNA
     *    SOLA VEZ sobre el "Total a Pagar" de la etapa 1. El resultado es el "Saldo
     *    Pendiente" final.
     *
     * @param array $filas Cada elemento: ['cantidad' => float, 'precio_compra' => float, 'pct_extra' => float, 'pct_interno' => float]
     * @param float $pctGlobal Porcentaje fijo del proveedor, igual para toda la compra, cascada por producto
     * @param float $montoManiobra
     * @param bool $aplicaDescuentoManiobra
     * @param float $montoSeguro
     * @param bool $aplicaDescuentoSeguro
     * @param float $pctProntoPago Descuento financiero/Nota de Crédito, aplicado una sola vez al final
     * @return array{
     *     subtotal: float, iva: float, total_factura: float,
     *     descuento_global: float, descuento_extra: float, descuento_interno: float,
     *     total_a_pagar: float,
     *     monto_pronto_pago: float, saldo_pendiente: float,
     *     filas: array
     * }
     */
    public function calcular(
        array $filas,
        float $pctGlobal,
        float $montoManiobra = 0,
        bool $aplicaDescuentoManiobra = false,
        float $montoSeguro = 0,
        bool $aplicaDescuentoSeguro = false,
        float $pctProntoPago = 0
    ): array {
        $todasLasFilas = [];

        foreach ($filas as $fila) {
            $todasLasFilas[] = [
                'cantidad' => (float) ($fila['cantidad'] ?? 0),
                'precio_compra' => (float) ($fila['precio_compra'] ?? 0),
                'pct_extra' => (float) ($fila['pct_extra'] ?? 0),
                'pct_interno' => (float) ($fila['pct_interno'] ?? 0),
                'aplica_global' => true,
                'es_producto' => true,
            ];
        }

        if ($montoManiobra > 0) {
            $todasLasFilas[] = [
                'cantidad' => 1,
                'precio_compra' => $montoManiobra,
                'pct_extra' => 0,
                'pct_interno' => 0,
                'aplica_global' => $aplicaDescuentoManiobra,
                'es_producto' => false,
            ];
        }

        if ($montoSeguro > 0) {
            $todasLasFilas[] = [
                'cantidad' => 1,
                'precio_compra' => $montoSeguro,
                'pct_extra' => 0,
                'pct_interno' => 0,
                'aplica_global' => $aplicaDescuentoSeguro,
                'es_producto' => false,
            ];
        }

        $filasCalculadas = [];
        $subtotal = 0;
        $descuentoGlobal = 0;
        $descuentoExtra = 0;
        $descuentoInterno = 0;
        $iva = 0;
        $totalAPagar = 0;

        foreach ($todasLasFilas as $fila) {
            $subtotalFila = $fila['cantidad'] * $fila['precio_compra'];

            // 1. Descuento Global (fijo del proveedor; en Maniobra/Seguro solo si su switch está activo)
            $montoGlobalFila = $fila['aplica_global'] ? ($subtotalFila * ($pctGlobal / 100)) : 0;
            $resto1 = $subtotalFila - $montoGlobalFila;

            // 2. Descuento Extra (siempre 0 en Maniobra/Seguro)
            $montoExtra = $resto1 * ($fila['pct_extra'] / 100);
            $resto2 = $resto1 - $montoExtra;

            // 3. Descuento Interno (siempre 0 en Maniobra/Seguro)
            $montoInterno = $resto2 * ($fila['pct_interno'] / 100);
            $baseGravableFila = $resto2 - $montoInterno;

            // 4. IVA al final, sobre la base ya neta de descuentos
            $ivaFila = $baseGravableFila * 0.16;
            $totalFilaFinal = $baseGravableFila + $ivaFila;

            if ($fila['es_producto']) {
                $filasCalculadas[] = [
                    'subtotal_fila' => $subtotalFila,
                    'monto_global' => $montoGlobalFila,
                    'monto_extra' => $montoExtra,
                    'monto_interno' => $montoInterno,
                    'base_gravable_fila' => $baseGravableFila,
                    'iva_fila' => $ivaFila,
                    'total_fila_final' => $totalFilaFinal,
                    'pct_extra' => $fila['pct_extra'],
                    'pct_interno' => $fila['pct_interno'],
                ];
            }

            $subtotal += $subtotalFila;
            $descuentoGlobal += $montoGlobalFila;
            $descuentoExtra += $montoExtra;
            $descuentoInterno += $montoInterno;
            $iva += $ivaFila;
            $totalAPagar += $totalFilaFinal;
        }

        $totalFactura = $subtotal + $iva;

        // Etapa 2: Descuento financiero por Pronto Pago / Nota de Crédito, una sola
        // vez sobre el Total a Pagar ya calculado.
        $montoProntoPago = $totalAPagar * ($pctProntoPago / 100);
        $saldoPendiente = $totalAPagar - $montoProntoPago;

        return [
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total_factura' => $totalFactura,
            'descuento_global' => $descuentoGlobal,
            'descuento_extra' => $descuentoExtra,
            'descuento_interno' => $descuentoInterno,
            'total_a_pagar' => $totalAPagar,
            'monto_pronto_pago' => $montoProntoPago,
            'saldo_pendiente' => $saldoPendiente,
            'filas' => $filasCalculadas,
        ];
    }
}
