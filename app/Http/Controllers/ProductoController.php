<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('historialCompras');

        if ($request->has('buscar') && $request->get('buscar') != '') {
            $buscar = $request->get('buscar');
            $terminos = array_filter(explode(' ', $buscar));

            $query->where(function($q) use ($terminos) {
                foreach ($terminos as $termino) {
                    $q->where(function($subQ) use ($termino) {
                        $subQ->where('nombre', 'like', "%{$termino}%")
                          ->orWhere('descripcion', 'like', "%{$termino}%")
                          ->orWhere('sku', 'like', "%{$termino}%")
                          ->orWhere('marca', 'like', "%{$termino}%")
                          ->orWhere('codigo_barras', 'like', "%{$termino}%")
                          ->orWhere('aplicacion', 'like', "%{$termino}%");
                    });
                }
            });
        }

        $productos = $query->orderBy('descripcion', 'asc')
                           ->orderBy('nombre', 'asc')
                           ->paginate(15)
                           ->withQueryString();
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

        return redirect()->route('productos.create')->with('success', 'Producto registrado exitosamente');
    }

    public function edit(Producto $producto)
    {
        $producto->load('historialCompras');
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
        $periodo = $request->get('periodo', 'completo');
        $fecha_inicio = null;
        $fecha_fin = Carbon::now();

        switch ($periodo) {
            case 'hoy':
                $fecha_inicio = Carbon::today();
                break;
            case 'semanal':
                $fecha_inicio = Carbon::now()->startOfWeek();
                break;
            case 'quincenal':
                $fecha_inicio = Carbon::now()->subWeek()->startOfWeek();
                break;
            case 'mensual':
                $fecha_inicio = Carbon::now()->startOfMonth();
                break;
            case 'personalizado':
                $fecha_inicio = $request->filled('fecha_inicio') ? Carbon::parse($request->fecha_inicio)->startOfDay() : null;
                $fecha_fin = $request->filled('fecha_fin') ? Carbon::parse($request->fecha_fin)->endOfDay() : Carbon::now();
                break;
        }

        $query = Producto::whereColumn('stock', '<=', 'stock_minimo');

        if ($request->filled('marca')) {
            $query->where('marca', $request->marca);
        }

        if ($periodo !== 'completo' && $fecha_inicio) {
            // Filtrar productos que tengan ventas u órdenes en el periodo
            $query->where(function($q) use ($fecha_inicio, $fecha_fin) {
                $q->whereHas('ventaDetalles.venta', function($qv) use ($fecha_inicio, $fecha_fin) {
                    $qv->whereBetween('created_at', [$fecha_inicio, $fecha_fin]);
                })->orWhereHas('ordenServicioDetalles.ordenServicio', function($qo) use ($fecha_inicio, $fecha_fin) {
                    $qo->whereBetween('created_at', [$fecha_inicio, $fecha_fin]);
                });
            });

            // Obtener el conteo de movimientos para cada producto y filtrar estrictamente
            $productos = $query->get()->map(function($producto) use ($fecha_inicio, $fecha_fin) {
                $ventasQty = DB::table('venta_detalles')
                    ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                    ->where('venta_detalles.producto_id', $producto->id)
                    ->whereBetween('ventas.created_at', [$fecha_inicio, $fecha_fin])
                    ->sum('venta_detalles.cantidad');

                $ordenesQty = DB::table('orden_servicio_detalles')
                    ->join('ordenes_servicio', 'orden_servicio_detalles.orden_servicio_id', '=', 'ordenes_servicio.id')
                    ->where('orden_servicio_detalles.producto_id', $producto->id)
                    ->whereBetween('ordenes_servicio.created_at', [$fecha_inicio, $fecha_fin])
                    ->sum('orden_servicio_detalles.cantidad');

                $producto->ventas_periodo = $ventasQty + $ordenesQty;
                return $producto;
            })->filter(function($producto) {
                return $producto->ventas_periodo > 0;
            })->values()
            ->sortBy([
                ['marca', 'asc'],
                ['nombre', 'asc'],
            ]);
        } else {
            $productos = $query->orderBy('marca', 'asc')
                                ->orderBy('nombre', 'asc')
                                ->get()
                                ->map(function($p) {
                                    $p->ventas_periodo = 0;
                                    return $p;
                                });
        }

        $pdf = Pdf::loadView('productos.pdf_pedimento', compact('productos', 'periodo', 'fecha_inicio', 'fecha_fin'));
        
        return $pdf->stream('pedimento_inventario_' . date('Y-m-d') . '.pdf');
    }

    public function inventario(Request $request)
    {
        $marca = $request->input('marca');

        $query = Producto::query();

        if ($marca) {
            $query->where('marca', $marca);
        }

        $productos = $query->orderBy('descripcion', 'asc')
                            ->orderBy('nombre', 'asc')
                            ->get();

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

    public function buscar(Request $request)
    {
        $term = $request->get('q');
        $query = Producto::query();

        if (!empty(trim($term))) {
            $terminos = array_filter(explode(' ', $term));
            $query->where(function($q) use ($terminos) {
                foreach ($terminos as $termino) {
                    $q->where(function($subQ) use ($termino) {
                        $subQ->where('nombre', 'like', "%{$termino}%")
                             ->orWhere('descripcion', 'like', "%{$termino}%")
                             ->orWhere('sku', 'like', "%{$termino}%")
                             ->orWhere('marca', 'like', "%{$termino}%")
                             ->orWhere('codigo_barras', 'like', "%{$termino}%")
                             ->orWhere('aplicacion', 'like', "%{$termino}%");
                    });
                }
            });
        }

        $productos = $query->limit(10)
                           ->get(['id', 'nombre', 'sku', 'marca', 'descripcion', 'aplicacion', 'codigo_barras', 'precio_compra', 'precio_venta']);

        $results = [];
        foreach ($productos as $producto) {
            $results[] = [
                'id' => $producto->id,
                'text' => "{$producto->nombre} - " . ($producto->descripcion ?? 'SIN DESCRIPCIÓN'),
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion ?? 'SIN DESCRIPCIÓN',
                'sku' => $producto->sku,
                'marca' => $producto->marca,
                'precio_compra' => $producto->precio_compra,
                'precio_venta' => $producto->precio_venta
            ];
        }

        return response()->json(['results' => $results]);
    }

    public function exportarInventarioPDF(Request $request)
    {
        $marca = $request->input('marca');

        $query = Producto::query();

        if ($marca) {
            $query->where('marca', $marca);
        }

        $productos = $query->orderBy('descripcion', 'asc')
                            ->orderBy('nombre', 'asc')
                            ->get();

        $pdf = Pdf::loadView('productos.pdf_lista_inventario', compact('productos', 'marca'));
        
        $filename = 'inventario_fisico_' . ($marca ? strtolower(str_replace(' ', '_', $marca)) : 'global') . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->stream($filename);
    }

    public function capturaRapida()
    {
        return view('productos.captura_rapida');
    }

    public function guardarLoteInventario(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $updatedCount = 0;
            foreach ($request->items as $item) {
                $producto = Producto::find($item['id']);
                if ($producto) {
                    $producto->update(['stock' => $item['cantidad']]);
                    $updatedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Se actualizaron {$updatedCount} productos correctamente."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el inventario: ' . $e->getMessage()
            ], 500);
        }
    }
}
