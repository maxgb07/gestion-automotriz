<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
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
            'productos.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.descuento_extra_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.precio_venta' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $gross_subtotal = 0;
            $monto_descuento_interno = 0;
            
            // Evaluamos proveedor para calcular vencimiento por defecto si no viene
            $proveedor = Proveedor::find($request->proveedor_id);
            $fecha_compra = $request->fecha_compra ?? date('Y-m-d');
            $fecha_vencimiento = $request->fecha_vencimiento ?? date('Y-m-d', strtotime($fecha_compra . ' + ' . $proveedor->dias_credito . ' days'));

            // Generar Folio Automático (OC-00000)
            $ultimoId = Compra::max('id') ?? 0;
            $folio = 'OC-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);

            // Creamos la compra vacía primero para tener el ID
            $compra = Compra::create([
                'proveedor_id' => $request->proveedor_id,
                'folio' => $folio,
                'factura' => mb_strtoupper($request->factura, 'UTF-8'),
                'fecha_compra' => $fecha_compra,
                'fecha_vencimiento' => $fecha_vencimiento,
                'subtotal' => 0,
                'porcentaje_descuento' => $request->porcentaje_descuento ?? 0,
                'monto_descuento' => 0,
                'porcentaje_descuento_extra' => $request->porcentaje_descuento_extra ?? 0,
                'monto_descuento_extra' => 0,
                'monto_descuento_interno' => 0,
                'iva' => 0,
                'total' => 0,
                'saldo_pendiente' => 0,
                'estado_pago' => 'PENDIENTE',
                'estado_complemento' => 'NO_APLICA'
            ]);

            $gross_subtotal = 0;
            $items_for_internal = [];

            foreach ($request->productos as $p) {
                $cantidad = $p['cantidad'] ?? 1;
                $precio = $p['precio_compra'] ?? 0;
                $desc1 = $p['descuento_porcentaje'] ?? 0;
                $desc2 = $p['descuento_extra_porcentaje'] ?? 0;
                $descInt = $p['descuento_interno_porcentaje'] ?? 0;

                $base = $cantidad * $precio;
                $subtotal_fila = $base;
                
                $gross_subtotal += $base;
                $items_for_internal[] = [
                    'row_total' => $base,
                    'pct_int' => $descInt
                ];

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $p['id'],
                    'cantidad' => $cantidad,
                    'precio_compra' => $precio,
                    'descuento_porcentaje' => $desc1,
                    'descuento_extra_porcentaje' => $desc2,
                    'descuento_interno_porcentaje' => $descInt,
                    'subtotal' => $subtotal_fila,
                    'precio_venta_sugerido' => $p['precio_venta'] ?? 0,
                ]);

                // Guardamos los porcentajes globales del primer producto para el encabezado
                if(!isset($pct_global)) $pct_global = $desc1;
                if(!isset($pct_extra)) $pct_extra = $desc2;

                // Actualizar Producto: Stock y Precios
                $producto = Producto::find($p['id']);
                if ($producto) {
                    $producto->stock += $cantidad;
                    $producto->precio_compra = $precio;
                    if(isset($p['precio_venta']) && $p['precio_venta'] > 0){
                        $producto->precio_venta = $p['precio_venta'];
                    }
                    $producto->save();
                }
            }            $monto_maniobra = $request->monto_maniobra ?? 0;
            $monto_seguro = $request->monto_seguro ?? 0;
            $aplica_m = $request->has('aplica_descuento_maniobra');
            $aplica_s = $request->has('aplica_descuento_seguro');

            // Descuentos en Cascada: 1. Global -> 2. Extra Global -> 3. Interno
            $pct_global = $pct_global ?? 0;
            $pct_extra = $pct_extra ?? 0;

            // Base descontable (SIN IVA)
            $discountable_total = $gross_subtotal + ($aplica_m ? $monto_maniobra : 0) + ($aplica_s ? $monto_seguro : 0);

            $remaining = $discountable_total;
            
            // 1. Global
            $monto_global = $remaining * ($pct_global / 100);
            $remaining -= $monto_global;

            // 2. Extra Global
            $monto_extra = $remaining * ($pct_extra / 100);
            $remaining -= $monto_extra;

            // 3. Interno
            $monto_descuento_interno = 0;
            $factor_cascada = (1 - ($pct_global / 100)) * (1 - ($pct_extra / 100));
            foreach ($items_for_internal as $item) {
                $monto_descuento_interno += ($item['row_total'] * $factor_cascada * ($item['pct_int'] / 100));
            }

            $total_descuentos = $monto_global + $monto_extra + $monto_descuento_interno;
            $gross_subtotal_con_gastos = $gross_subtotal + $monto_maniobra + $monto_seguro;
            
            // Base Imponible
            $base_imponible = $gross_subtotal_con_gastos - $total_descuentos;

            // IVA y Total Factura
            $iva_compra = $base_imponible * 0.16;
            $total_factura = $base_imponible + $iva_compra;

            // Descuento Financiero (Pronto Pago)
            $pct_pronto_pago = $request->porcentaje_pronto_pago ?? 0;
            $monto_pronto_pago = $total_factura * ($pct_pronto_pago / 100);

            $saldo_pendiente = round($total_factura - $monto_pronto_pago, 2);

            $compra->update([
                'subtotal' => $gross_subtotal_con_gastos,
                'porcentaje_descuento' => $pct_global,
                'monto_descuento' => $monto_global,
                'porcentaje_descuento_extra' => $pct_extra,
                'monto_descuento_extra' => $monto_extra,
                'monto_descuento_interno' => $monto_descuento_interno,
                'porcentaje_pronto_pago' => $pct_pronto_pago,
                'monto_pronto_pago' => $monto_pronto_pago,
                'monto_maniobra' => $monto_maniobra,
                'aplica_descuento_maniobra' => $aplica_m,
                'monto_seguro' => $monto_seguro,
                'aplica_descuento_seguro' => $aplica_s,
                'iva' => $iva_compra,
                'total' => $total_factura,
                'saldo_pendiente' => $saldo_pendiente
            ]);

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
            'productos.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.descuento_extra_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.precio_venta' => 'nullable|numeric|min:0',
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

            // 3. Procesar nuevos detalles y sumar stock
            $gross_subtotal = 0;
            $items_for_internal = [];
            
            foreach ($request->productos as $p) {
                $cantidad = $p['cantidad'] ?? 1;
                $precio = $p['precio_compra'] ?? 0;
                $desc1 = $p['descuento_porcentaje'] ?? 0;
                $desc2 = $p['descuento_extra_porcentaje'] ?? 0;
                $descInt = $p['descuento_interno_porcentaje'] ?? 0;

                $base = $cantidad * $precio;
                $subtotal_fila = $base;
                
                $gross_subtotal += $base;
                $items_for_internal[] = [
                    'row_total' => $base,
                    'pct_int' => $descInt
                ];

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $p['id'],
                    'cantidad' => $cantidad,
                    'precio_compra' => $precio,
                    'descuento_porcentaje' => $desc1,
                    'descuento_extra_porcentaje' => $desc2,
                    'descuento_interno_porcentaje' => $descInt,
                    'subtotal' => $subtotal_fila,
                    'precio_venta_sugerido' => $p['precio_venta'] ?? 0,
                ]);

                if(!isset($pct_global)) $pct_global = $desc1;
                if(!isset($pct_extra)) $pct_extra = $desc2;

                // Sumar nuevo stock y actualizar precios
                $producto = Producto::find($p['id']);
                $producto->stock += $cantidad;
                $producto->precio_compra = $precio;
                if(isset($p['precio_venta']) && $p['precio_venta'] > 0){
                    $producto->precio_venta = $p['precio_venta'];
                }
                $producto->save();
            }

            // 4. Actualizar totales de la compra
            $monto_maniobra = $request->monto_maniobra ?? 0;
            $monto_seguro = $request->monto_seguro ?? 0;
            $aplica_m = $request->has('aplica_descuento_maniobra');
            $aplica_s = $request->has('aplica_descuento_seguro');

            // Descuentos en Cascada: 1. Global -> 2. Extra Global -> 3. Interno
            $pct_global = $pct_global ?? 0;
            $pct_extra = $pct_extra ?? 0;

            // Base descontable (SIN IVA)
            $discountable_total = $gross_subtotal + ($aplica_m ? $monto_maniobra : 0) + ($aplica_s ? $monto_seguro : 0);

            $remaining = $discountable_total;
            
            // 1. Global
            $monto_global = $remaining * ($pct_global / 100);
            $remaining -= $monto_global;

            // 2. Extra Global
            $monto_extra = $remaining * ($pct_extra / 100);
            $remaining -= $monto_extra;

            // 3. Interno
            $monto_descuento_interno = 0;
            $factor_cascada = (1 - ($pct_global / 100)) * (1 - ($pct_extra / 100));
            foreach ($items_for_internal as $item) {
                $monto_descuento_interno += ($item['row_total'] * $factor_cascada * ($item['pct_int'] / 100));
            }

            $total_descuentos = $monto_global + $monto_extra + $monto_descuento_interno;
            $gross_subtotal_con_gastos = $gross_subtotal + $monto_maniobra + $monto_seguro;
            
            // Base Imponible
            $base_imponible = $gross_subtotal_con_gastos - $total_descuentos;

            // IVA y Total Factura
            $iva_compra = $base_imponible * 0.16;
            $total_factura = $base_imponible + $iva_compra;

            // Descuento Financiero (Pronto Pago)
            $pct_pronto_pago = $request->porcentaje_pronto_pago ?? 0;
            $monto_pronto_pago = $total_factura * ($pct_pronto_pago / 100);

            $saldo_pendiente = round($total_factura - $monto_pronto_pago, 2);
            
            // Evaluamos proveedor para calcular vencimiento si no viene
            $proveedor = Proveedor::find($request->proveedor_id);
            $fecha_compra = $request->fecha_compra ?? $compra->fecha_compra;
            $fecha_vencimiento = $request->fecha_vencimiento ?? date('Y-m-d', strtotime($fecha_compra . ' + ' . $proveedor->dias_credito . ' days'));

            $compra->update([
                'proveedor_id' => $request->proveedor_id,
                'factura' => mb_strtoupper($request->factura, 'UTF-8'),
                'fecha_compra' => $fecha_compra,
                'fecha_vencimiento' => $fecha_vencimiento,
                'subtotal' => $gross_subtotal_con_gastos,
                'porcentaje_descuento' => $pct_global,
                'monto_descuento' => $monto_global,
                'porcentaje_descuento_extra' => $pct_extra,
                'monto_descuento_extra' => $monto_extra,
                'monto_descuento_interno' => $monto_descuento_interno,
                'porcentaje_pronto_pago' => $pct_pronto_pago,
                'monto_pronto_pago' => $monto_pronto_pago,
                'monto_maniobra' => $monto_maniobra,
                'aplica_descuento_maniobra' => $aplica_m,
                'monto_seguro' => $monto_seguro,
                'aplica_descuento_seguro' => $aplica_s,
                'iva' => $iva_compra,
                'total' => $total_factura,
                'saldo_pendiente' => $saldo_pendiente,
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
