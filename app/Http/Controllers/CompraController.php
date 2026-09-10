<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\CompraCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'detalles.producto']);

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('folio', 'like', "%{$buscar}%")
                  ->orWhere('factura', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        $compras = $query->latest()->paginate(15)->withQueryString();
        
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('compras.crear', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'factura' => 'nullable|string|max:100',
            'fecha_compra' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'nullable|numeric|min:0.1',
            'productos.*.precio_compra' => 'nullable|numeric|min:0',
            'productos.*.descuento_extra_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.descuento_interno_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.precio_venta' => 'nullable|numeric|min:0',
            'monto_maniobra' => 'nullable|numeric|min:0',
            'monto_seguro' => 'nullable|numeric|min:0',
            'porcentaje_pronto_pago' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Evaluamos proveedor para calcular vencimiento por defecto si no viene
            $proveedor = Proveedor::find($request->proveedor_id);
            $fecha_compra = $request->fecha_compra ?? date('Y-m-d');
            $fecha_vencimiento = $request->fecha_vencimiento ?? date('Y-m-d', strtotime($fecha_compra . ' + ' . $proveedor->dias_credito . ' days'));

            // Generar Folio Automático (OC-00000)
            $ultimoId = Compra::max('id') ?? 0;
            $folio = 'OC-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);

            // Descuento Global: fijo del proveedor, cascada por producto
            $pctGlobal = (float) $proveedor->porcentaje_descuento_global;
            $montoManiobra = (float) ($request->monto_maniobra ?? 0);
            $montoSeguro = (float) ($request->monto_seguro ?? 0);
            $aplicaManiobra = $request->has('aplica_descuento_maniobra');
            $aplicaSeguro = $request->has('aplica_descuento_seguro');
            // Descuento por Pronto Pago / Nota de Crédito: capturado a mano, una sola vez sobre el Total a Pagar
            $pctProntoPago = (float) ($request->porcentaje_pronto_pago ?? 0);

            $filas = collect($request->productos)->map(fn ($p) => [
                'cantidad' => $p['cantidad'] ?? 1,
                'precio_compra' => $p['precio_compra'] ?? 0,
                'pct_extra' => $p['descuento_extra_porcentaje'] ?? 0,
                'pct_interno' => $p['descuento_interno_porcentaje'] ?? 0,
            ])->all();

            $calculo = (new CompraCalculator())->calcular(
                filas: $filas,
                pctGlobal: $pctGlobal,
                montoManiobra: $montoManiobra,
                aplicaDescuentoManiobra: $aplicaManiobra,
                montoSeguro: $montoSeguro,
                aplicaDescuentoSeguro: $aplicaSeguro,
                pctProntoPago: $pctProntoPago,
            );

            $compra = Compra::create([
                'proveedor_id' => $request->proveedor_id,
                'folio' => $folio,
                'factura' => mb_strtoupper($request->factura, 'UTF-8'),
                'fecha_compra' => $fecha_compra,
                'fecha_vencimiento' => $fecha_vencimiento,
                'subtotal' => $calculo['subtotal'],
                'porcentaje_descuento' => $pctGlobal,
                'monto_descuento' => $calculo['descuento_global'],
                'porcentaje_descuento_extra' => 0,
                'monto_descuento_extra' => $calculo['descuento_extra'],
                'monto_descuento_interno' => $calculo['descuento_interno'],
                'monto_maniobra' => $montoManiobra,
                'aplica_descuento_maniobra' => $aplicaManiobra,
                'monto_seguro' => $montoSeguro,
                'aplica_descuento_seguro' => $aplicaSeguro,
                'iva' => $calculo['iva'],
                'porcentaje_pronto_pago' => $pctProntoPago,
                'monto_pronto_pago' => $calculo['monto_pronto_pago'],
                'total' => round($calculo['total_a_pagar'], 2),
                'saldo_pendiente' => round($calculo['saldo_pendiente'], 2),
                'estado_pago' => 'PENDIENTE',
                'estado_complemento' => 'NO_APLICA',
            ]);

            foreach ($request->productos as $i => $p) {
                $filaCalculada = $calculo['filas'][$i];
                $cantidad = $p['cantidad'] ?? 1;
                $precio = $p['precio_compra'] ?? 0;

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $p['id'],
                    'cantidad' => $cantidad,
                    'precio_compra' => $precio,
                    'descuento_porcentaje' => $pctGlobal,
                    'descuento_extra_porcentaje' => $filaCalculada['pct_extra'],
                    'descuento_interno_porcentaje' => $filaCalculada['pct_interno'],
                    'subtotal' => $filaCalculada['subtotal_fila'],
                    'precio_venta_sugerido' => $p['precio_venta'] ?? 0,
                ]);

                // Actualizar Producto: Stock y Precios
                $producto = Producto::find($p['id']);
                if ($producto) {
                    $producto->stock += $cantidad;
                    $producto->precio_compra = $precio;
                    if (isset($p['precio_venta']) && $p['precio_venta'] > 0) {
                        $producto->precio_venta = $p['precio_venta'];
                    }
                    $producto->save();
                }
            }

            DB::commit();

            return redirect()->route('compras.index')->with('success', 'Compra registrada y stock actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al registrar la compra: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Compra $compra)
    {
        $compra->load('proveedor', 'detalles.producto');
        return view('compras.ver', compact('compra'));
    }

    public function edit(Compra $compra)
    {
        $compra->load('detalles.producto', 'proveedor');
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('compras.editar', compact('compra', 'proveedores'));
    }

    public function update(Request $request, Compra $compra)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'factura' => 'nullable|string|max:100',
            'fecha_compra' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'nullable|numeric|min:0.1',
            'productos.*.precio_compra' => 'nullable|numeric|min:0',
            'productos.*.descuento_extra_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.descuento_interno_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.precio_venta' => 'nullable|numeric|min:0',
            'monto_maniobra' => 'nullable|numeric|min:0',
            'monto_seguro' => 'nullable|numeric|min:0',
            'porcentaje_pronto_pago' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // 1. Revertir el stock de los productos que ya estaban en la compra
            foreach ($compra->detalles as $detalleAntiguo) {
                $producto = Producto::find($detalleAntiguo->producto_id);
                if ($producto) {
                    $producto->stock -= $detalleAntiguo->cantidad;
                    $producto->save();
                }
            }

            // 2. Eliminar detalles antiguos
            $compra->detalles()->delete();

            // 3. Evaluamos proveedor (puede haber cambiado) para vencimiento y Descuento Global
            $proveedor = Proveedor::find($request->proveedor_id);
            $fecha_compra = $request->fecha_compra ?? $compra->fecha_compra;
            $fecha_vencimiento = $request->fecha_vencimiento ?? date('Y-m-d', strtotime($fecha_compra . ' + ' . $proveedor->dias_credito . ' days'));

            $pctGlobal = (float) $proveedor->porcentaje_descuento_global;
            $montoManiobra = (float) ($request->monto_maniobra ?? 0);
            $montoSeguro = (float) ($request->monto_seguro ?? 0);
            $aplicaManiobra = $request->has('aplica_descuento_maniobra');
            $aplicaSeguro = $request->has('aplica_descuento_seguro');
            $pctProntoPago = (float) ($request->porcentaje_pronto_pago ?? 0);

            $filas = collect($request->productos)->map(fn ($p) => [
                'cantidad' => $p['cantidad'] ?? 1,
                'precio_compra' => $p['precio_compra'] ?? 0,
                'pct_extra' => $p['descuento_extra_porcentaje'] ?? 0,
                'pct_interno' => $p['descuento_interno_porcentaje'] ?? 0,
            ])->all();

            $calculo = (new CompraCalculator())->calcular(
                filas: $filas,
                pctGlobal: $pctGlobal,
                montoManiobra: $montoManiobra,
                aplicaDescuentoManiobra: $aplicaManiobra,
                montoSeguro: $montoSeguro,
                aplicaDescuentoSeguro: $aplicaSeguro,
                pctProntoPago: $pctProntoPago,
            );

            // 4. Procesar nuevos detalles y sumar stock
            foreach ($request->productos as $i => $p) {
                $filaCalculada = $calculo['filas'][$i];
                $cantidad = $p['cantidad'] ?? 1;
                $precio = $p['precio_compra'] ?? 0;

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $p['id'],
                    'cantidad' => $cantidad,
                    'precio_compra' => $precio,
                    'descuento_porcentaje' => $pctGlobal,
                    'descuento_extra_porcentaje' => $filaCalculada['pct_extra'],
                    'descuento_interno_porcentaje' => $filaCalculada['pct_interno'],
                    'subtotal' => $filaCalculada['subtotal_fila'],
                    'precio_venta_sugerido' => $p['precio_venta'] ?? 0,
                ]);

                // Sumar nuevo stock y actualizar precios
                $producto = Producto::find($p['id']);
                $producto->stock += $cantidad;
                $producto->precio_compra = $precio;
                if (isset($p['precio_venta']) && $p['precio_venta'] > 0) {
                    $producto->precio_venta = $p['precio_venta'];
                }
                $producto->save();
            }

            // 5. Actualizar totales de la compra
            $compra->update([
                'proveedor_id' => $request->proveedor_id,
                'factura' => mb_strtoupper($request->factura, 'UTF-8'),
                'fecha_compra' => $fecha_compra,
                'fecha_vencimiento' => $fecha_vencimiento,
                'subtotal' => $calculo['subtotal'],
                'porcentaje_descuento' => $pctGlobal,
                'monto_descuento' => $calculo['descuento_global'],
                'porcentaje_descuento_extra' => 0,
                'monto_descuento_extra' => $calculo['descuento_extra'],
                'monto_descuento_interno' => $calculo['descuento_interno'],
                'monto_maniobra' => $montoManiobra,
                'aplica_descuento_maniobra' => $aplicaManiobra,
                'monto_seguro' => $montoSeguro,
                'aplica_descuento_seguro' => $aplicaSeguro,
                'iva' => $calculo['iva'],
                'porcentaje_pronto_pago' => $pctProntoPago,
                'monto_pronto_pago' => $calculo['monto_pronto_pago'],
                'total' => round($calculo['total_a_pagar'], 2),
                'saldo_pendiente' => round($calculo['saldo_pendiente'], 2),
            ]);

            DB::commit();

            return redirect()->route('compras.index')->with('success', 'Compra actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Compra $compra)
    {
        // Nota: Eliminar una compra requiere decidir si se revierte el stock.
        // Por ahora, solo eliminaremos el registro para mantener simplicidad, 
        // pero en un sistema real se debería advertir o revertir.
        $compra->delete();
        return redirect()->route('compras.index')->with('success', 'Registro de compra eliminado.');
    }
}
