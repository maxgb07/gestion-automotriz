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
        $query = Compra::with('proveedor');

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where('factura', 'like', "%{$buscar}%");
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
        $productos = Producto::orderBy('nombre')->get();
        return view('compras.crear', compact('proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'factura' => 'nullable|string|max:100',
            'fecha_compra' => 'nullable|date',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'nullable|integer|min:1',
            'productos.*.precio_compra' => 'nullable|numeric|min:0',
            'productos.*.precio_venta' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            foreach ($request->productos as $p) {
                $total += $p['cantidad'] * $p['precio_compra'];
            }

            $compra = Compra::create([
                'proveedor_id' => $request->proveedor_id,
                'factura' => $request->factura,
                'fecha_compra' => $request->fecha_compra,
                'total' => $total,
            ]);

            foreach ($request->productos as $p) {
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $p['id'],
                    'cantidad' => $p['cantidad'],
                    'precio_compra' => $p['precio_compra'],
                    'precio_venta_sugerido' => $p['precio_venta'],
                ]);

                // Actualizar Producto: Stock y Precios
                $producto = Producto::find($p['id']);
                $producto->stock += $p['cantidad'];
                $producto->precio_compra = $p['precio_compra'];
                $producto->precio_venta = $p['precio_venta'];
                $producto->save();
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

    public function destroy(Compra $compra)
    {
        // Nota: Eliminar una compra requiere decidir si se revierte el stock.
        // Por ahora, solo eliminaremos el registro para mantener simplicidad, 
        // pero en un sistema real se debería advertir o revertir.
        $compra->delete();
        return redirect()->route('compras.index')->with('success', 'Registro de compra eliminado.');
    }
}
