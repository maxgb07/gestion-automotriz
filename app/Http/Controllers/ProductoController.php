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
    /**
     * Productos excluidos del ranking de más vendidos (por nombre o SKU).
     * Agregar aquí los identificadores a omitir.
     */
    private const PRODUCTOS_EXCLUIDOS = [
        'WFC134280',
        'WFC134281',
        'SEGB',
        'TORG',
        'BIRT',
        'BIR',
        'DOT4',
        'RES',
        'TUE',
        'MGAS',
        'BAL',
        'ACE',
        'BAT3',
        'BCAMFE',
        'MAN"'
    ];

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

        if ($request->has('clasificacion') && $request->get('clasificacion') != '') {
            $query->where('clasificacion', $request->get('clasificacion'));
        }

        $hayFiltros = ($request->filled('buscar') || $request->filled('clasificacion'));

        if ($hayFiltros) {
            $productos = $query->orderByRaw("FIELD(clasificacion, 'A', 'B', 'C', 'Z')")
                               ->orderBy('descripcion', 'asc')
                               ->paginate(15)
                               ->withQueryString();
        } else {
            $productos = $query->orderBy('descripcion', 'asc')
                               ->paginate(15)
                               ->withQueryString();
        }
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
            'clasificacion' => 'nullable|string|max:100',
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
            'clasificacion' => 'nullable|string|max:100',
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
                ['clasificacion', 'asc'],
                ['marca', 'asc'],
                ['nombre', 'asc'],
            ]);
        } else {
            $productos = $query->orderBy('clasificacion', 'asc')
                                ->orderBy('marca', 'asc')
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
                           ->get(['id', 'nombre', 'sku', 'marca', 'descripcion', 'aplicacion', 'codigo_barras', 'precio_compra', 'precio_venta', 'stock', 'stock_minimo']);

        $results = [];
        foreach ($productos as $producto) {
            $results[] = [
                'id' => $producto->id,
                'text' => "{$producto->nombre} - " . ($producto->descripcion ?? 'SIN DESCRIPCIÓN'),
                'nombre' => $producto->nombre,
                'sku' => $producto->sku,
                'marca' => $producto->marca,
                'descripcion' => $producto->descripcion ?? 'SIN DESCRIPCIÓN',
                'aplicacion' => $producto->aplicacion ?? '',
                'precio_compra' => $producto->precio_compra,
                'precio_venta' => $producto->precio_venta,
                'stock' => $producto->stock,
                'stock_minimo' => $producto->stock_minimo
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
            'items.*.marca' => 'nullable|string|max:100',
            'items.*.descripcion' => 'nullable|string',
            'items.*.aplicacion' => 'nullable|string',
            'items.*.precio_compra' => 'required|numeric|min:0',
            'items.*.precio_venta' => 'required|numeric|min:0',
            'items.*.stock' => 'required|numeric|min:0',
            'items.*.stock_minimo' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $updatedCount = 0;
            foreach ($request->items as $item) {
                $producto = Producto::find($item['id']);
                if ($producto) {
                    $producto->update([
                        'marca' => $item['marca'],
                        'descripcion' => $item['descripcion'],
                        'aplicacion' => $item['aplicacion'],
                        'precio_compra' => $item['precio_compra'],
                        'precio_venta' => $item['precio_venta'],
                        'stock' => $item['stock'],
                        'stock_minimo' => $item['stock_minimo'],
                    ]);
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


    public function ultimasVentas(Request $request, Producto $producto)
    {
        $perPage = 10;
        $page    = max(1, (int) $request->get('page', 1));

        // Ventas
        $ventasQ = DB::table('venta_detalles')
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->where('venta_detalles.producto_id', $producto->id)
            ->whereNull('ventas.cancelado_at')
            ->select(
                DB::raw("DATE(ventas.created_at) as fecha"),
                'ventas.folio as folio',
                DB::raw("'Venta' as tipo"),
                'venta_detalles.cantidad',
                'venta_detalles.precio_unitario as precio_venta'
            );

        // Órdenes de servicio
        $ordenesQ = DB::table('orden_servicio_detalles')
            ->join('ordenes_servicio', 'orden_servicio_detalles.orden_servicio_id', '=', 'ordenes_servicio.id')
            ->where('orden_servicio_detalles.producto_id', $producto->id)
            ->whereNull('ordenes_servicio.deleted_at')
            ->select(
                DB::raw("DATE(ordenes_servicio.created_at) as fecha"),
                'ordenes_servicio.folio as folio',
                DB::raw("'Orden de Servicio' as tipo"),
                'orden_servicio_detalles.cantidad',
                'orden_servicio_detalles.precio_unitario as precio_venta'
            );

        // UNION + total
        $unionSql      = "({$ventasQ->toSql()}) UNION ALL ({$ordenesQ->toSql()})";
        $unionBindings = array_merge($ventasQ->getBindings(), $ordenesQ->getBindings());

        $total = DB::select(
            "SELECT COUNT(*) as total FROM ({$unionSql}) AS u",
            $unionBindings
        );
        $total = $total[0]->total ?? 0;

        $offset = ($page - 1) * $perPage;
        $rows   = DB::select(
            "SELECT * FROM ({$unionSql}) AS u ORDER BY fecha DESC LIMIT {$perPage} OFFSET {$offset}",
            $unionBindings
        );

        return response()->json([
            'data'         => $rows,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ]);
    }

    public function crearLote()
    {
        return view('productos.crear_lote');
    }

    public function guardarLoteNuevos(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.nombre' => 'required|string|max:255',
            'items.*.marca' => 'nullable|string|max:100',
            'items.*.descripcion' => 'nullable|string',
            'items.*.aplicacion' => 'nullable|string',
            'items.*.precio_compra' => 'required|numeric|min:0',
            'items.*.precio_venta' => 'required|numeric|min:0',
            'items.*.stock' => 'required|numeric|min:0',
            'items.*.stock_minimo' => 'required|numeric|min:0'
        ]);

        $successCount = 0;
        $duplicates = [];

        try {
            DB::beginTransaction();

            foreach ($request->items as $item) {
                $nombreUpper = mb_strtoupper($item['nombre']);
                
                // Verificar si ya existe
                $existe = Producto::where('nombre', $nombreUpper)->exists();

                if ($existe) {
                    $duplicates[] = $nombreUpper;
                    continue;
                }

                Producto::create([
                    'nombre' => $nombreUpper,
                    'marca' => $item['marca'],
                    'descripcion' => $item['descripcion'],
                    'aplicacion' => $item['aplicacion'],
                    'precio_compra' => $item['precio_compra'],
                    'precio_venta' => $item['precio_venta'],
                    'stock' => $item['stock'],
                    'stock_minimo' => $item['stock_minimo'],
                ]);

                $successCount++;
            }

            DB::commit();

            $message = "Se registraron {$successCount} productos correctamente.";
            if (count($duplicates) > 0) {
                $message .= " Se omitieron " . count($duplicates) . " duplicados.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'duplicates' => $duplicates,
                'registered' => $successCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el lote: ' . $e->getMessage()
            ], 500);
        }
    }
}
