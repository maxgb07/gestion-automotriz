<?php

namespace Tests\Feature;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraControllerTest extends TestCase
{
    use RefreshDatabase;

    private function proveedorConDescuento(float $pctGlobal): Proveedor
    {
        return Proveedor::create([
            'nombre' => 'Proveedor Test',
            'dias_credito' => 30,
            'porcentaje_descuento_global' => $pctGlobal,
            'porcentaje_descuento_extra' => 0,
        ]);
    }

    private function productoDePrueba(): Producto
    {
        return Producto::create([
            'nombre' => 'Producto Test',
            'descripcion' => 'Producto de prueba',
            'sku' => 'TEST-001',
            'precio_compra' => 0,
            'precio_venta' => 0,
            'stock' => 5,
            'stock_minimo' => 0,
        ]);
    }

    /** Caso de referencia del spec: 1 producto, cantidad 1, precio 1000, PP 10%, Extra 5%, Interno 3%. */
    public function test_store_calcula_y_guarda_el_caso_de_referencia(): void
    {
        $user = User::factory()->create();
        $proveedor = $this->proveedorConDescuento(10);
        $producto = $this->productoDePrueba();

        $response = $this->actingAs($user)->post(route('compras.store'), [
            'proveedor_id' => $proveedor->id,
            'factura' => 'F-TEST-001',
            'fecha_compra' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id' => $producto->id,
                    'cantidad' => 1,
                    'precio_compra' => 1000,
                    'descuento_extra_porcentaje' => 5,
                    'descuento_interno_porcentaje' => 3,
                    'precio_venta' => 0,
                ],
            ],
        ]);

        $response->assertRedirect(route('compras.index'));

        $compra = Compra::latest('id')->first();

        $this->assertEqualsWithDelta(1000.00, $compra->subtotal, 0.01);
        $this->assertEqualsWithDelta(160.00, $compra->iva, 0.01);
        $this->assertEqualsWithDelta(962.05, $compra->total, 0.01);
        $this->assertEqualsWithDelta(962.05, $compra->saldo_pendiente, 0.01);
        $this->assertEqualsWithDelta(10, $compra->porcentaje_descuento, 0.01);

        $detalle = DetalleCompra::where('compra_id', $compra->id)->first();
        $this->assertEqualsWithDelta(10, $detalle->descuento_porcentaje, 0.01);
        $this->assertEqualsWithDelta(5, $detalle->descuento_extra_porcentaje, 0.01);
        $this->assertEqualsWithDelta(3, $detalle->descuento_interno_porcentaje, 0.01);

        $producto->refresh();
        $this->assertEquals(6, $producto->stock); // 5 + 1
        $this->assertEqualsWithDelta(1000, $producto->precio_compra, 0.01);
    }

    public function test_store_no_sobrescribe_precio_venta_si_viene_en_cero(): void
    {
        $user = User::factory()->create();
        $proveedor = $this->proveedorConDescuento(0);
        $producto = Producto::create([
            'nombre' => 'Producto Con Venta',
            'descripcion' => 'x',
            'sku' => 'TEST-002',
            'precio_compra' => 50,
            'precio_venta' => 150,
            'stock' => 0,
            'stock_minimo' => 0,
        ]);

        $this->actingAs($user)->post(route('compras.store'), [
            'proveedor_id' => $proveedor->id,
            'fecha_compra' => now()->format('Y-m-d'),
            'productos' => [
                [
                    'id' => $producto->id,
                    'cantidad' => 2,
                    'precio_compra' => 60,
                    'precio_venta' => 0,
                ],
            ],
        ]);

        $producto->refresh();
        $this->assertEqualsWithDelta(60, $producto->precio_compra, 0.01);
        $this->assertEqualsWithDelta(150, $producto->precio_venta, 0.01); // sin cambio
        $this->assertEquals(2, $producto->stock);
    }
}
