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

            $subtotal_compra = 0;
            $monto_descuento_compra = 0;
            $total_compra = 0;
            
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
                'porcentaje_descuento' => 0,
                'monto_descuento' => 0,
                'iva' => 0,
                'total' => 0,
                'saldo_pendiente' => 0,
                'estado_pago' => 'PENDIENTE',
                'estado_complemento' => 'NO_APLICA'
            ]);

            foreach ($request->productos as $p) {
                $cantidad = $p['cantidad'] ?? 1;
                $precio = $p['precio_compra'] ?? 0;
                $desc1 = $p['descuento_porcentaje'] ?? 0;
                $desc2 = $p['descuento_extra_porcentaje'] ?? 0;

                $base = $cantidad * $precio;
                $subtotal_fila = $base * (1 - ($desc1 / 100)) * (1 - ($desc2 / 100));
                
                $subtotal_compra += $subtotal_fila;
                $monto_descuento_compra += ($base - $subtotal_fila);

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $p['id'],
                    'cantidad' => $cantidad,
                    'precio_compra' => $precio,
                    'descuento_porcentaje' => $desc1,
                    'descuento_extra_porcentaje' => $desc2,
                    'subtotal' => $subtotal_fila,
                    'precio_venta_sugerido' => $p['precio_venta'] ?? 0,
                ]);

                // Actualizar Producto: Stock y Precios
                $producto = Producto::find($p['id']);
                $producto->stock += $cantidad;
                $producto->precio_compra = $precio;
                if(isset($p['precio_venta']) && $p['precio_venta'] > 0){
                    $producto->precio_venta = $p['precio_venta'];
                }
                $producto->save();
            }

            $iva_compra = $subtotal_compra * 0.16;
            $total_compra = $subtotal_compra + $iva_compra;

            $compra->update([
                'subtotal' => $subtotal_compra,
                'monto_descuento' => $monto_descuento_compra,
                'iva' => $iva_compra,
                'total' => $total_compra,
                'saldo_pendiente' => $total_compra,
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
            $subtotal_compra = 0;
            $monto_descuento_compra = 0;
            
            foreach ($request->productos as $p) {
                $cantidad = $p['cantidad'] ?? 1;
                $precio = $p['precio_compra'] ?? 0;
                $desc1 = $p['descuento_porcentaje'] ?? 0;
                $desc2 = $p['descuento_extra_porcentaje'] ?? 0;

                $base = $cantidad * $precio;
                $subtotal_fila = $base * (1 - ($desc1 / 100)) * (1 - ($desc2 / 100));
                
                $subtotal_compra += $subtotal_fila;
                $monto_descuento_compra += ($base - $subtotal_fila);

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $p['id'],
                    'cantidad' => $cantidad,
                    'precio_compra' => $precio,
                    'descuento_porcentaje' => $desc1,
                    'descuento_extra_porcentaje' => $desc2,
                    'subtotal' => $subtotal_fila,
                    'precio_venta_sugerido' => $p['precio_venta'] ?? 0,
                ]);

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
            $iva_compra = $subtotal_compra * 0.16;
            $total_compra = $subtotal_compra + $iva_compra;
            
            // Evaluamos proveedor para calcular vencimiento si no viene
            $proveedor = Proveedor::find($request->proveedor_id);
            $fecha_compra = $request->fecha_compra ?? $compra->fecha_compra;
            $fecha_vencimiento = $request->fecha_vencimiento ?? date('Y-m-d', strtotime($fecha_compra . ' + ' . $proveedor->dias_credito . ' days'));

            // Calcular saldo pendiente actual (Total nuevo - Total pagado hasta ahora)
            // Si la compra tenía pagos, restarlos. Por ahora asumimos saldo = total
            // TODO en Fase 3: Considerar pagos previos al calcular saldo_pendiente
            
            $compra->update([
                'proveedor_id' => $request->proveedor_id,
                'factura' => mb_strtoupper($request->factura, 'UTF-8'),
                'fecha_compra' => $fecha_compra,
                'fecha_vencimiento' => $fecha_vencimiento,
                'subtotal' => $subtotal_compra,
                'monto_descuento' => $monto_descuento_compra,
                'iva' => $iva_compra,
                'total' => $total_compra,
                'saldo_pendiente' => $total_compra, // Temporal hasta Fase 3
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
