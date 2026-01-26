<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query();

        if ($request->has('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('sku', 'like', "%{$buscar}%")
                  ->orWhere('marca', 'like', "%{$buscar}%")
                  ->orWhere('codigo_barras', 'like', "%{$buscar}%")
                  ->orWhere('aplicacion', 'like', "%{$buscar}%");
            });
        }

        $productos = $query->latest()->paginate(15)->withQueryString();
        $marcas = Producto::whereNotNull('marca')->where('marca', '!=', '')->distinct()->orderBy('marca')->pluck('marca');

        return view('productos.index', compact('productos', 'marcas'));
    }

    public function create()
    {
        return view('productos.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:100',
            'codigo_barras' => 'nullable|string|max:100',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $data['imagen'] = $path;
        }

        $producto = Producto::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto registrado exitosamente',
                'data' => $producto
            ]);
        }

        return redirect()->route('productos.index')->with('success', 'Producto registrado exitosamente');
    }

    public function edit(Producto $producto)
    {
        return view('productos.editar', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:100',
            'codigo_barras' => 'nullable|string|max:100',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $path = $request->file('imagen')->store('productos', 'public');
            $data['imagen'] = $path;
        }

        $producto->update($data);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente');
    }

    public function pedimento(Request $request)
    {
        $query = Producto::whereColumn('stock', '<=', 'stock_minimo');

        if ($request->filled('marca')) {
            $query->where('marca', $request->marca);
        }

        $productos = $query->get();
        
        $pdf = Pdf::loadView('productos.pdf_pedimento', compact('productos'));
        
        return $pdf->stream('pedimento_inventario_' . date('Y-m-d') . '.pdf');
    }

    public function inventario(Request $request)
    {
        $marca = $request->input('marca');

        if (!$marca) {
            return redirect()->route('productos.index')->with('error', 'Debes seleccionar una marca para realizar el inventario.');
        }

        $productos = Producto::where('marca', $marca)
                            ->orderBy('nombre')
                            ->paginate(25)
                            ->appends(['marca' => $marca]);

        return view('productos.inventario', compact('productos', 'marca'));
    }

    public function updateInventario(Request $request)
    {
        $stocks = $request->input('stocks', []);
        $updatedCount = 0;

        foreach ($stocks as $id => $cantidad) {
            // Lógica estricta:
            // Si es NULL o cadena vacía ("") -> IGNORAR
            // Si es "0" o cualquier número -> ACTUALIZAR
            
            if ($cantidad !== null && $cantidad !== '') {
                $producto = Producto::find($id);
                if ($producto) {
                    $producto->update(['stock' => $cantidad]);
                    $updatedCount++;
                }
            }
        }

        return redirect()->route('productos.index')->with('success', "Inventario actualizado correctamente. Se modificaron {$updatedCount} productos.");
    }
}
