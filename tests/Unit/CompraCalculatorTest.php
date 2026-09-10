<?php

namespace Tests\Unit;

use App\Services\CompraCalculator;
use PHPUnit\Framework\TestCase;

class CompraCalculatorTest extends TestCase
{
    private CompraCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new CompraCalculator();
    }

    /**
     * Caso de referencia del spec: 1 producto, cascada completa (sin Pronto Pago
     * financiero). El descuento se aplica sobre el precio CRUDO; el IVA se calcula
     * al final, sobre la base ya neta de descuentos (orden verificado contra una
     * factura CFDI real).
     */
    public function test_caso_de_referencia_un_producto(): void
    {
        $resultado = $this->calculator->calcular(
            filas: [
                ['cantidad' => 1, 'precio_compra' => 1000, 'pct_extra' => 5, 'pct_interno' => 3],
            ],
            pctGlobal: 10,
        );

        $fila = $resultado['filas'][0];

        $this->assertEqualsWithDelta(1000.00, $fila['subtotal_fila'], 0.001);
        $this->assertEqualsWithDelta(100.00, $fila['monto_global'], 0.001);     // 1000 * 10%
        $this->assertEqualsWithDelta(45.00, $fila['monto_extra'], 0.001);      // (1000-100) * 5%
        $this->assertEqualsWithDelta(25.65, $fila['monto_interno'], 0.001);    // (900-45) * 3%
        $this->assertEqualsWithDelta(829.35, $fila['base_gravable_fila'], 0.001);
        $this->assertEqualsWithDelta(132.696, $fila['iva_fila'], 0.001);
        $this->assertEqualsWithDelta(962.046, $fila['total_fila_final'], 0.001);

        $this->assertEqualsWithDelta(1000.00, $resultado['subtotal'], 0.001);
        $this->assertEqualsWithDelta(132.696, $resultado['iva'], 0.001);
        $this->assertEqualsWithDelta(1132.696, $resultado['total_factura'], 0.001);
        $this->assertEqualsWithDelta(962.046, $resultado['total_a_pagar'], 0.001);
        // Sin Pronto Pago financiero, Saldo Pendiente = Total a Pagar
        $this->assertEqualsWithDelta(962.046, $resultado['saldo_pendiente'], 0.001);
        $this->assertEqualsWithDelta(0.0, $resultado['monto_pronto_pago'], 0.001);
    }

    /** Dos productos con Descuento Extra distinto entre sí. */
    public function test_dos_productos_con_extra_distinto(): void
    {
        $resultado = $this->calculator->calcular(
            filas: [
                ['cantidad' => 1, 'precio_compra' => 1000, 'pct_extra' => 5, 'pct_interno' => 0],
                ['cantidad' => 1, 'precio_compra' => 1000, 'pct_extra' => 0, 'pct_interno' => 0],
            ],
            pctGlobal: 10,
        );

        // Fila A: 1000 -> Global 10% -> 900 -> Extra 5% -> base 855 -> +IVA 16% -> 991.80
        $this->assertEqualsWithDelta(991.80, $resultado['filas'][0]['total_fila_final'], 0.001);
        // Fila B: 1000 -> Global 10% -> 900 -> Extra 0% -> base 900 -> +IVA 16% -> 1044.00
        $this->assertEqualsWithDelta(1044.00, $resultado['filas'][1]['total_fila_final'], 0.001);

        $this->assertEqualsWithDelta(2000.00, $resultado['subtotal'], 0.001);
        $this->assertEqualsWithDelta(200.00, $resultado['descuento_global'], 0.001); // 100 + 100, sobre precio crudo
        $this->assertEqualsWithDelta(45.00, $resultado['descuento_extra'], 0.001);   // solo fila A: 900*0.05
        $this->assertEqualsWithDelta(0.00, $resultado['descuento_interno'], 0.001);
        $this->assertEqualsWithDelta(991.80 + 1044.00, $resultado['total_a_pagar'], 0.001);
    }

    public function test_maniobra_con_switch_activo_entra_al_subtotal_e_iva(): void
    {
        $resultado = $this->calculator->calcular(
            filas: [],
            pctGlobal: 10,
            montoManiobra: 100,
            aplicaDescuentoManiobra: true,
        );

        // 100 -> Global 10% -> base 90 -> +IVA 16% -> 104.4
        $this->assertEqualsWithDelta(100.0, $resultado['subtotal'], 0.001);
        $this->assertEqualsWithDelta(14.4, $resultado['iva'], 0.001);
        $this->assertEqualsWithDelta(114.4, $resultado['total_factura'], 0.001);
        $this->assertEqualsWithDelta(10.0, $resultado['descuento_global'], 0.001);
        $this->assertEqualsWithDelta(0.0, $resultado['descuento_extra'], 0.001);
        $this->assertEqualsWithDelta(104.4, $resultado['total_a_pagar'], 0.001);
    }

    public function test_maniobra_con_switch_inactivo_no_recibe_descuento(): void
    {
        $resultado = $this->calculator->calcular(
            filas: [],
            pctGlobal: 10,
            montoManiobra: 100,
            aplicaDescuentoManiobra: false,
        );

        $this->assertEqualsWithDelta(100.0, $resultado['subtotal'], 0.001);
        $this->assertEqualsWithDelta(116.0, $resultado['total_factura'], 0.001);
        $this->assertEqualsWithDelta(0.0, $resultado['descuento_global'], 0.001);
        $this->assertEqualsWithDelta(116.0, $resultado['total_a_pagar'], 0.001);
    }

    public function test_seguro_con_switch_activo(): void
    {
        $resultado = $this->calculator->calcular(
            filas: [],
            pctGlobal: 10,
            montoSeguro: 200,
            aplicaDescuentoSeguro: true,
        );

        // 200 -> Global 10% -> base 180 -> +IVA 16% -> 208.8
        $this->assertEqualsWithDelta(200.0, $resultado['subtotal'], 0.001);
        $this->assertEqualsWithDelta(228.8, $resultado['total_factura'], 0.001);
        $this->assertEqualsWithDelta(20.0, $resultado['descuento_global'], 0.001);
        $this->assertEqualsWithDelta(208.8, $resultado['total_a_pagar'], 0.001);
    }

    public function test_seguro_con_switch_inactivo(): void
    {
        $resultado = $this->calculator->calcular(
            filas: [],
            pctGlobal: 10,
            montoSeguro: 200,
            aplicaDescuentoSeguro: false,
        );

        $this->assertEqualsWithDelta(232.0, $resultado['total_factura'], 0.001);
        $this->assertEqualsWithDelta(0.0, $resultado['descuento_global'], 0.001);
        $this->assertEqualsWithDelta(232.0, $resultado['total_a_pagar'], 0.001);
    }

    public function test_maniobra_y_seguro_activos_junto_con_productos(): void
    {
        $resultado = $this->calculator->calcular(
            filas: [
                ['cantidad' => 1, 'precio_compra' => 1000, 'pct_extra' => 5, 'pct_interno' => 3],
            ],
            pctGlobal: 10,
            montoManiobra: 100,
            aplicaDescuentoManiobra: true,
            montoSeguro: 200,
            aplicaDescuentoSeguro: true,
        );

        $this->assertEqualsWithDelta(1300.0, $resultado['subtotal'], 0.001);
        // El Total a Pagar por línea es invariante al orden IVA/descuento (commutativo),
        // pero el IVA agregado ahora se calcula sobre la base neta de cada línea.
        $this->assertEqualsWithDelta(132.696 + 14.4 + 28.8, $resultado['iva'], 0.001);
        $this->assertEqualsWithDelta(962.046 + 104.4 + 208.8, $resultado['total_a_pagar'], 0.001);
        $this->assertCount(1, $resultado['filas']);
    }

    /** Sin Descuento Extra ni Interno: Total a Pagar = Total de Factura - Descuento Global. */
    public function test_sin_extra_ni_interno(): void
    {
        $resultado = $this->calculator->calcular(
            filas: [
                ['cantidad' => 2, 'precio_compra' => 500, 'pct_extra' => 0, 'pct_interno' => 0],
            ],
            pctGlobal: 10,
        );

        // 1000 -> Global 10% -> base 900 -> +IVA 16% -> 1044
        $this->assertEqualsWithDelta(1000.00, $resultado['subtotal'], 0.001);
        $this->assertEqualsWithDelta(144.00, $resultado['iva'], 0.001);
        $this->assertEqualsWithDelta(1144.00, $resultado['total_factura'], 0.001);
        $this->assertEqualsWithDelta(100.00, $resultado['descuento_global'], 0.001);
        $this->assertEqualsWithDelta(0.00, $resultado['descuento_extra'], 0.001);
        $this->assertEqualsWithDelta(0.00, $resultado['descuento_interno'], 0.001);
        $this->assertEqualsWithDelta(1044.00, $resultado['total_a_pagar'], 0.001);
        $this->assertEqualsWithDelta(
            $resultado['total_factura'] - $resultado['descuento_global'],
            $resultado['total_a_pagar'],
            0.001
        );
    }

    /** Descuento financiero por Pronto Pago: una sola vez, al final, sobre el Total a Pagar. */
    public function test_descuento_pronto_pago_financiero_se_aplica_una_sola_vez_al_final(): void
    {
        $resultado = $this->calculator->calcular(
            filas: [
                ['cantidad' => 1, 'precio_compra' => 1000, 'pct_extra' => 5, 'pct_interno' => 3],
            ],
            pctGlobal: 10,
            pctProntoPago: 10,
        );

        $this->assertEqualsWithDelta(962.046, $resultado['total_a_pagar'], 0.001);
        $this->assertEqualsWithDelta(96.2046, $resultado['monto_pronto_pago'], 0.001);
        $this->assertEqualsWithDelta(962.046 * 0.9, $resultado['saldo_pendiente'], 0.001);
    }

    /**
     * Caso de regresión con datos reales: factura CFDI de DAPESA (folio 1FC-2633354,
     * UUID 1818531d-b3b6-4631-8424-f037c5940bc3), 11 productos + un cargo de "Seguro
     * por Envío" de $110.70 sin descuento, más una Nota de Crédito del 10% (descuento
     * financiero, aplicado una sola vez sobre el Total a Pagar). Descuento Global de
     * DAPESA registrado: 3%. Del XML real: SubTotal=$6,377.22, Descuento=$731.52,
     * IVA=$903.31, Total=$6,549.01. Saldo verificado por el usuario contra factura +
     * Nota de Crédito: $5,894.09.
     */
    public function test_regresion_factura_real_dapesa_con_nota_de_credito(): void
    {
        $filas = [
            ['cantidad' => 1, 'precio_compra' => 433.76, 'pct_extra' => 8.0411, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 464.61, 'pct_extra' => 8.0411, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 349.88, 'pct_extra' => 0.0010, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 75.56,  'pct_extra' => 0.0010, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 459.40, 'pct_extra' => 0.0010, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 803.35, 'pct_extra' => 13.0008, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 1181.50, 'pct_extra' => 8.0422, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 608.48, 'pct_extra' => 13.0010, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 865.68, 'pct_extra' => 13.0010, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 978.80, 'pct_extra' => 10.0003, 'pct_interno' => 0],
            ['cantidad' => 1, 'precio_compra' => 45.50,  'pct_extra' => 0.0114, 'pct_interno' => 0],
        ];

        $resultado = $this->calculator->calcular(
            filas: $filas,
            pctGlobal: 3,
            montoSeguro: 110.70,
            aplicaDescuentoSeguro: false,
            pctProntoPago: 10,
        );

        // Subtotal reproduce el SubTotal real exacto.
        $this->assertEqualsWithDelta(6377.22, $resultado['subtotal'], 0.01);

        // La suma de descuentos y el IVA reproducen los valores reales del CFDI
        // (dentro de margen de redondeo de los % de Extra derivados a mano del XML).
        $sumaDescuentos = $resultado['descuento_global'] + $resultado['descuento_extra'] + $resultado['descuento_interno'];
        $this->assertEqualsWithDelta(731.52, $sumaDescuentos, 1.0);
        $this->assertEqualsWithDelta(903.31, $resultado['iva'], 1.0);

        // Total a Pagar (sin la Nota de Crédito) reproduce el Total real de la factura.
        $this->assertEqualsWithDelta(6549.01, $resultado['total_a_pagar'], 1.5);

        // Con el 10% de Nota de Crédito aplicado una sola vez al final, reproduce el
        // saldo que el usuario verificó a mano: $5,894.09.
        $this->assertEqualsWithDelta(5894.09, $resultado['saldo_pendiente'], 1.5);
    }
}
