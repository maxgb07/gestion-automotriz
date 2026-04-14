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

    public function masVendidos(Request $request)
    {
        $periodo    = $request->get('periodo', 'completo');
        $marca      = $request->get('marca');
        $fecha_inicio = null;
        $fecha_fin    = Carbon::now();
        $search       = $request->get('search');

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
                $fecha_fin    = $request->filled('fecha_fin')    ? Carbon::parse($request->fecha_fin)->endOfDay()       : Carbon::now();
                break;
        }

        // Subquery: ventas directas
        $ventasQuery = DB::table('venta_detalles')
            ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
            ->whereNull('ventas.cancelado_at')
            ->whereNotNull('venta_detalles.producto_id')
            ->select(
                'venta_detalles.producto_id',
                DB::raw('SUM(venta_detalles.cantidad) as total_qty'),
                DB::raw('MAX(ventas.created_at) as ultima_venta')
            )
            ->groupBy('venta_detalles.producto_id');

        // Subquery: órdenes de servicio
        $ordenesQuery = DB::table('orden_servicio_detalles')
            ->join('ordenes_servicio', 'orden_servicio_detalles.orden_servicio_id', '=', 'ordenes_servicio.id')
            ->whereNotNull('orden_servicio_detalles.producto_id')
            ->select(
                'orden_servicio_detalles.producto_id',
                DB::raw('SUM(orden_servicio_detalles.cantidad) as total_qty'),
                DB::raw('MAX(ordenes_servicio.created_at) as ultima_venta')
            )
            ->groupBy('orden_servicio_detalles.producto_id');

        // Aplicar filtro de fechas a ambos subqueries
        if ($periodo !== 'completo' && $fecha_inicio) {
            $ventasQuery->whereBetween('ventas.created_at', [$fecha_inicio, $fecha_fin]);
            $ordenesQuery->whereBetween('ordenes_servicio.created_at', [$fecha_inicio, $fecha_fin]);
        }

        // UNION ALL: consolida ventas + órdenes de servicio
        $unionBindings = array_merge($ventasQuery->getBindings(), $ordenesQuery->getBindings());

        $rawResults = DB::select("
            SELECT
                producto_id,
                SUM(total_qty)    AS cantidad_vendida,
                MAX(ultima_venta) AS ultima_venta
            FROM (
                {$ventasQuery->toSql()}
                UNION ALL
                {$ordenesQuery->toSql()}
            ) AS movimientos
            GROUP BY producto_id
            ORDER BY cantidad_vendida DESC
        ", $unionBindings);

        // Obtener IDs en orden
        $productosIds = array_column($rawResults, 'producto_id');

        if (empty($productosIds)) {
            $productos = collect()->paginate(15);
            // Crear paginador vacío manualmente
            $productos = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Traer productos preservando el orden, excluyendo los de la lista negra.
            // Nota: whereNotIn con NULL en MySQL retorna NULL (falso), por eso
            // se trata nombre y SKU por separado para no excluir productos con SKU vacío.
            $excluidos = self::PRODUCTOS_EXCLUIDOS;
            $productosList = Producto::whereIn('id', $productosIds)
                ->when($marca, fn($q) => $q->where('marca', $marca))
                ->whereNotIn('nombre', $excluidos)
                ->where(function($q) use ($excluidos) {
                    // SKU nulo/vacío pasa el filtro; SKU con valor debe no estar en la lista
                    $q->whereNull('sku')
                      ->orWhere('sku', '')
                      ->orWhereNotIn('sku', $excluidos);
                })
                ->get()
                ->keyBy('id');

            // Última compra por producto
            $ultimasCompras = DB::table('detalles_compra')
                ->join('compras', 'detalles_compra.compra_id', '=', 'compras.id')
                ->whereIn('detalles_compra.producto_id', $productosIds)
                ->select(
                    'detalles_compra.producto_id',
                    DB::raw('MAX(compras.fecha_compra) as ultima_compra')
                )
                ->groupBy('detalles_compra.producto_id')
                ->get()
                ->keyBy('producto_id');

            // Ensamblar en orden de cantidad descendente
            $ordenados = collect($rawResults)
                ->filter(fn($r) => isset($productosList[$r->producto_id]))
                ->values()
                ->map(function ($r, $index) use ($productosList, $ultimasCompras) {
                    $p = $productosList[$r->producto_id];
                    $p->cantidad_vendida = (int) $r->cantidad_vendida;
                    $p->ultima_venta     = $r->ultima_venta;
                    $p->ultima_compra    = $ultimasCompras[$r->producto_id]->ultima_compra ?? null;
                    $p->ranking_real     = $index + 1;
                    return $p;
                });

            // Filtrar por texto multi-término en resultados precalculados
            if ($search) {
                $terminos = array_filter(explode(' ', strtolower($search)));
                
                $ordenados = $ordenados->filter(function($p) use ($terminos) {
                    foreach ($terminos as $termino) {
                        $matchEnTermino = 
                            str_contains(strtolower($p->nombre), $termino) ||
                            str_contains(strtolower($p->descripcion ?? ''), $termino) ||
                            str_contains(strtolower($p->aplicacion ?? ''), $termino) ||
                            str_contains(strtolower($p->marca ?? ''), $termino) ||
                            str_contains(strtolower($p->sku ?? ''), $termino) ||
                            str_contains(strtolower($p->codigo_barras ?? ''), $termino);
                        
                        if (!$matchEnTermino) {
                            return false;
                        }
                    }
                    return true;
                })->values();
            }

            // Paginación manual
            $perPage     = 15;
            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            $slice       = $ordenados->slice(($currentPage - 1) * $perPage, $perPage)->values();

            // Cargar historial de ventas/órdenes para los productos en la página actual ($slice)
            $sliceIds = $slice->pluck('id')->toArray();
            if (!empty($sliceIds)) {
                $historialVentasQ = DB::table('venta_detalles')
                    ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                    ->whereIn('venta_detalles.producto_id', $sliceIds)
                    ->whereNull('ventas.cancelado_at')
                    ->select(
                        'venta_detalles.producto_id',
                        'venta_detalles.cantidad',
                        'ventas.created_at as fecha',
                        'ventas.folio',
                        DB::raw("'Venta' as tipo")
                    );

                $historialOrdenesQ = DB::table('orden_servicio_detalles')
                    ->join('ordenes_servicio', 'orden_servicio_detalles.orden_servicio_id', '=', 'ordenes_servicio.id')
                    ->whereIn('orden_servicio_detalles.producto_id', $sliceIds)
                    ->whereNotNull('orden_servicio_detalles.producto_id')
                    ->select(
                        'orden_servicio_detalles.producto_id',
                        'orden_servicio_detalles.cantidad',
                        'ordenes_servicio.created_at as fecha',
                        'ordenes_servicio.folio',
                        DB::raw("'Orden de Servicio' as tipo")
                    );

                // Aplicar filtros de fecha si aplican
                if ($periodo !== 'completo' && $fecha_inicio) {
                    $historialVentasQ->whereBetween('ventas.created_at', [$fecha_inicio, $fecha_fin]);
                    $historialOrdenesQ->whereBetween('ordenes_servicio.created_at', [$fecha_inicio, $fecha_fin]);
                }

                $hBindings = array_merge($historialVentasQ->getBindings(), $historialOrdenesQ->getBindings());
                $historialUnido = DB::select("
                    SELECT producto_id, cantidad, fecha, folio, tipo 
                    FROM ({$historialVentasQ->toSql()} UNION ALL {$historialOrdenesQ->toSql()}) AS h 
                    ORDER BY fecha DESC
                ", $hBindings);

                $historialAgrupado = collect($historialUnido)->groupBy('producto_id');

                $slice = $slice->map(function ($p) use ($historialAgrupado) {
                    $p->historial_transacciones = $historialAgrupado->get($p->id, collect())->take(50); // Muestra top 50
                    return $p;
                });
            }

            $productos = new \Illuminate\Pagination\LengthAwarePaginator(
                $slice,
                $ordenados->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        $marcas = Producto::whereNotNull('marca')->where('marca', '!=', '')->distinct()->orderBy('marca')->pluck('marca');

        return view('productos.mas_vendidos', compact('productos', 'marcas', 'periodo', 'marca', 'fecha_inicio', 'fecha_fin'));
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
