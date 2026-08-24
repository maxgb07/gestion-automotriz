@extends('layouts.app')

@section('title', 'Inventario de Productos')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white uppercase">Inventario de Productos</h1>
            <p class="text-blue-200">Gestión de existencias, precios y aplicaciones</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button onclick="abrirModalInventario()" class="w-fit inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-lg shadow-lg shadow-indigo-900/40 transition-all text-sm uppercase tracking-widest cursor-pointer" style="background-color: #4f46e5;">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Inventariar
            </button>
            <button onclick="abrirModalPedimento()" class="w-fit inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-black rounded-lg shadow-lg shadow-amber-900/40 transition-all text-sm uppercase tracking-widest" style="background-color: #d97706;">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Generar Pedimento
            </button>

            <a href="{{ route('productos.crear_lote') }}" class="w-fit inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-lg shadow-lg shadow-indigo-900/40 transition-all text-sm uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Registro Masivo
            </a>
            <a href="{{ route('productos.create') }}" class="w-fit inline-flex items-center px-4 py-2 bg-brand hover:bg-brand-strong text-white font-black rounded-lg shadow-lg shadow-brand-medium/40 transition-all text-sm uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Search Box -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
        <form action="{{ route('productos.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-stretch">
            {{-- flex-1: ocupa todo el espacio sobrante --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="BUSCAR POR SKU, MARCA, CLAVE, CÓDIGO O APLICACIÓN..." class="block w-full h-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm uppercase">
            </div>
            {{-- ancho fijo para clasificación --}}
            <div class="shrink-0 md:w-56">
                <select name="clasificacion" onchange="this.form.submit()" class="block w-full h-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm uppercase">
                    <option value="" class="bg-slate-800">TODA CLASIFICACIÓN</option>
                    <option value="A" class="bg-slate-800" {{ request('clasificacion') == 'A' ? 'selected' : '' }}>A - ALTA ROTACIÓN</option>
                    <option value="B" class="bg-slate-800" {{ request('clasificacion') == 'B' ? 'selected' : '' }}>B - MEDIA ROTACIÓN</option>
                    <option value="C" class="bg-slate-800" {{ request('clasificacion') == 'C' ? 'selected' : '' }}>C - BAJA ROTACIÓN</option>
                    <option value="Z" class="bg-slate-800" {{ request('clasificacion') == 'Z' ? 'selected' : '' }}>Z - SIN MOVIMIENTO</option>
                </select>
            </div>
            {{-- ancho fijo para botón buscar --}}
            <button type="submit" class="shrink-0 w-28 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase tracking-widest">
                BUSCAR
            </button>
            {{-- ancho fijo para botón limpiar (solo si hay filtros activos) --}}
            @if(request('buscar') || request('clasificacion'))
                <a href="{{ route('productos.index') }}" class="shrink-0 w-28 px-4 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center uppercase tracking-widest flex items-center justify-center">
                    LIMPIAR
                </a>
            @endif
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white/10 backdrop-blur-xl rounded-3xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Clave</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Marca</th>
                        <!-- <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Código Barras</th> -->
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Aplicación</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Clasificación</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Stock</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Precios</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($productos as $producto)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-4">
                                    <!-- <div class="relative w-12 h-12 rounded-xl overflow-hidden bg-slate-800 border border-white/10 flex-shrink-0 group-hover:scale-105 transition-transform">
                                        @if($producto->imagen)
                                            <img src="{{ Storage::url($producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-blue-400 font-bold">
                                                {{ substr($producto->nombre, 0, 1) }}
                                            </div>
                                        @endif
                                    </div> -->
                                    <div class="flex flex-col items-center">
                                        <span class="text-white font-bold uppercase group-hover:text-blue-300 transition-colors">{{ $producto->nombre }}</span>
                                        <span class="text-xs text-blue-200/60 uppercase line-clamp-1 text-center">{{ $producto->descripcion }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-bold uppercase">{{ $producto->marca ?? 'N/A' }}</span>
                            </td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($producto->codigo_barras)
                                    <span class="px-3 py-1 bg-purple-500/10 rounded-lg text-xs font-mono text-purple-300 border border-purple-500/20 w-fit mx-auto">
                                        {{ $producto->codigo_barras }}
                                    </span>
                                @else
                                    <span class="text-xs text-blue-200/30 italic">SIN CÓDIGO</span>
                                @endif
                            </td> -->
                            <td class="px-6 py-4 text-center">
                                <span class="text-blue-100 text-sm font-bold uppercase line-clamp-2 text-center">{{ $producto->aplicacion ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-white font-bold uppercase">{{ $producto->clasificacion ?? 'N/D' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-bold {{ $producto->stock <= $producto->stock_minimo ? 'text-red-400' : 'text-green-400' }}">
                                        {{ $producto->stock }}
                                    </span>
                                    <!-- <span class="text-[10px] text-blue-200/50 uppercase">MIN: {{ $producto->stock_minimo }}</span> -->
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-col">
                                    <span class="text-xs text-blue-200/60 uppercase">COMPRA: ${{ number_format($producto->precio_compra, 2) }}</span>
                                    <span class="text-white font-bold uppercase">VENTA: ${{ number_format($producto->precio_venta, 2) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick='verProducto(@json($producto))' class="p-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-xl transition-all" title="VER DETALLES">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('productos.edit', $producto) }}" class="p-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-xl transition-all" title="EDITAR">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="eliminarProducto({{ $producto->id }}, '{{ $producto->nombre }}')" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-xl transition-all" title="ELIMINAR">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $producto->id }}" action="{{ route('productos.destroy', $producto) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-blue-300/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xl font-medium text-blue-200">NO HAY PRODUCTOS REGISTRADOS</p>
                                    <p class="text-sm text-blue-200/50 mt-2 uppercase">COMIENZA AGREGANDO UN NUEVO PRODUCTO AL INVENTARIO.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($productos->hasPages())
            <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                {{ $productos->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

<script>
    const ultimasVentasBaseUrl = '{{ url("productos") }}';
    const UC_PER_PAGE = 10;

    function renderUltimasCompras(compras, page) {
        const container = document.getElementById('uc-table-container');
        if (!container) return;

        if (!compras || compras.length === 0) {
            container.innerHTML = `<p class="px-4 py-6 text-center text-blue-200/40 font-bold text-xs uppercase tracking-widest">SIN HISTORIAL DE COMPRAS</p>`;
            return;
        }

        const total    = compras.length;
        const lastPage = Math.ceil(total / UC_PER_PAGE);
        page = Math.max(1, Math.min(page, lastPage));
        const slice    = compras.slice((page - 1) * UC_PER_PAGE, page * UC_PER_PAGE);

        const fmt     = (n) => new Intl.NumberFormat('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n);
        const fmtQty  = (n) => new Intl.NumberFormat('es-MX').format(n);
        const fmtDate = (d) => d ? d.split('-').reverse().join('/') : '-';

        let rows = slice.map(comp => `
            <tr class="hover:bg-white/5 transition-colors">
                <td class="px-4 py-3 text-md font-bold text-white">${fmtDate(comp.fecha_compra)}</td>
                <td class="px-4 py-3 text-md font-bold uppercase text-white truncate" title="${comp.proveedor_nombre || 'N/A'}">${comp.proveedor_nombre || 'N/A'}</td>
                <td class="px-4 py-3 text-md font-bold text-center text-white">${comp.cantidad ? fmtQty(comp.cantidad) : '0'}</td>
                <td class="px-4 py-3 text-md font-bold uppercase text-center text-white font-mono">${comp.folio || '-'}</td>
                <td class="px-4 py-3 text-md font-bold uppercase text-center text-white">${comp.factura || '-'}</td>
                <td class="px-4 py-3 text-md font-black text-white text-right pr-6">$${fmt(comp.precio_compra)}</td>
            </tr>`).join('');

        let paginacion = '';
        if (lastPage > 1) {
            const prevDis = page <= 1 ? 'opacity-40 pointer-events-none' : 'cursor-pointer hover:bg-white/10';
            const nextDis = page >= lastPage ? 'opacity-40 pointer-events-none' : 'cursor-pointer hover:bg-white/10';
            paginacion = `
                <div class="flex items-center justify-between px-4 py-3 border-t border-white/10">
                    <span class="text-[11px] text-blue-200/50 uppercase font-bold">
                        Página ${page} de ${lastPage} &bull; ${total} registros
                    </span>
                    <div class="flex items-center gap-2">
                        <button onclick="renderUltimasCompras(window._ucCompras, ${page - 1})"
                            class="px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-white text-xs font-bold transition-all ${prevDis}">
                            &#8592; Anterior
                        </button>
                        <button onclick="renderUltimasCompras(window._ucCompras, ${page + 1})"
                            class="px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-white text-xs font-bold transition-all ${nextDis}">
                            Siguiente &#8594;
                        </button>
                    </div>
                </div>`;
        }

        container.innerHTML = `
            <table class="w-full text-left border-collapse text-md">
                <thead class="bg-white/10 sticky top-0 backdrop-blur-md">
                    <tr>
                        <th class="px-4 py-2 text-md text-white font-bold uppercase">FECHA</th>
                        <th class="px-4 py-2 text-md text-white font-bold uppercase">PROVEEDOR</th>
                        <th class="px-4 py-2 text-md text-white font-bold uppercase text-center">CANTIDAD</th>
                        <th class="px-4 py-2 text-md text-white font-bold uppercase text-center">FOLIO COMPRA</th>
                        <th class="px-4 py-2 text-md text-white font-bold uppercase text-center">FACTURA</th>
                        <th class="px-4 py-2 text-md text-white font-bold uppercase text-right pr-6">PRECIO</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">${rows}</tbody>
            </table>
            ${paginacion}`;
    }

    function renderUltimasVentas(productoId, page) {
        const container = document.getElementById('uv-table-container');
        if (!container) return;

        container.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <svg class="animate-spin w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="ml-3 text-blue-300/60 text-xs uppercase font-bold tracking-widest">Cargando...</span>
            </div>`;

        fetch(`${ultimasVentasBaseUrl}/${productoId}/ultimas-ventas?page=${page}`)
            .then(r => r.json())
            .then(res => {
                if (!res.data || res.data.length === 0) {
                    container.innerHTML = `<p class="px-4 py-6 text-center text-blue-200/40 font-bold text-xs uppercase tracking-widest">SIN HISTORIAL DE VENTAS</p>`;
                    return;
                }

                const fmt    = (n) => new Intl.NumberFormat('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n);
                const fmtQty = (n) => new Intl.NumberFormat('es-MX').format(n);
                const fmtDate = (d) => d ? d.split('-').reverse().join('/') : '-';

                let rows = res.data.map(r => {
                    return `
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-md font-bold text-white">${fmtDate(r.fecha)}</td>
                            <td class="px-4 py-3 text-md font-bold text-white text-center font-mono">${r.folio || '-'}</td>
                            <td class="px-4 py-3 text-md font-bold text-white text-center">${fmtQty(r.cantidad)}</td>
                            <td class="px-4 py-3 text-md font-black text-emerald-400 text-right pr-4">$${fmt(r.precio_venta)}</td>
                        </tr>`;
                }).join('');

                // Paginación
                let paginacion = '';
                if (res.last_page > 1) {
                    const prevDis = res.current_page <= 1 ? 'opacity-40 pointer-events-none' : 'cursor-pointer hover:bg-white/10';
                    const nextDis = res.current_page >= res.last_page ? 'opacity-40 pointer-events-none' : 'cursor-pointer hover:bg-white/10';
                    paginacion = `
                        <div class="flex items-center justify-between px-4 py-3 border-t border-white/10 bg-white/3">
                            <span class="text-[11px] text-blue-200/50 uppercase font-bold">
                                Página ${res.current_page} de ${res.last_page} &bull; ${res.total} registros
                            </span>
                            <div class="flex items-center gap-2">
                                <button onclick="renderUltimasVentas(${productoId}, ${res.current_page - 1})"
                                    class="px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-white text-xs font-bold transition-all ${prevDis}">
                                    &#8592; Anterior
                                </button>
                                <button onclick="renderUltimasVentas(${productoId}, ${res.current_page + 1})"
                                    class="px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-white text-xs font-bold transition-all ${nextDis}">
                                    Siguiente &#8594;
                                </button>
                            </div>
                        </div>`;
                }

                container.innerHTML = `
                    <table class="w-full text-left border-collapse text-md">
                        <thead class="bg-white/10 sticky top-0 backdrop-blur-md">
                            <tr>
                                <th class="px-4 py-2 text-md text-white font-bold uppercase">FECHA</th>
                                <th class="px-4 py-2 text-md text-white font-bold uppercase text-center">FOLIO</th>
                                <th class="px-4 py-2 text-md text-white font-bold uppercase text-center">CANTIDAD</th>
                                <th class="px-4 py-2 text-md text-white font-bold uppercase text-right pr-4">PRECIO VENTA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">${rows}</tbody>
                    </table>
                    ${paginacion}`;
            })
            .catch(() => {
                container.innerHTML = `<p class="px-4 py-6 text-center text-red-400/60 font-bold text-xs uppercase tracking-widest">Error al cargar las ventas</p>`;
            });
    }

    function verProducto(producto) {
        const imagenHtml = producto.imagen 
            ? `<div class="flex justify-center mb-6">
                <img src="/storage/${producto.imagen}" class="w-48 h-48 object-cover rounded-3xl border-2 border-white/20 shadow-2xl shadow-blue-500/20">
               </div>`
            : `<div class="flex justify-center mb-6">
                <div class="w-48 h-48 bg-white/5 rounded-3xl border-2 border-dashed border-white/10 flex items-center justify-center text-blue-200/20">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
               </div>`;

        Swal.fire({
            html: `
                <div class="text-left">
                    ${imagenHtml}
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-black text-white uppercase mb-2 tracking-tighter">${producto.nombre}</h3>
                        <!--<p class="text-blue-300/60 text-md font-mono uppercase mb-4 tracking-widest">${producto.sku || 'SIN SKU'}</p>-->
                        <div class="h-px w-20 bg-gradient-to-r from-transparent via-blue-500/50 to-transparent mx-auto mb-4"></div>
                        <p class="text-blue-100/80 text-md uppercase px-6 leading-relaxed">${producto.descripcion || 'SIN DESCRIPCIÓN'}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 bg-white/5 p-6 rounded-[2rem] border border-white/10 shadow-inner">
                        <div class="space-y-1 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors">
                            <p class="text-md font-black text-blue-300/40 uppercase tracking-widest">Marca Original</p>
                            <p class="text-white font-bold uppercase text-md">${producto.marca || 'N/A'}</p>
                        </div>
                        <div class="space-y-1 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors">
                            <p class="text-md font-black text-blue-300/40 uppercase tracking-widest">Código de Barras</p>
                            <p class="text-white font-mono font-bold text-md">${producto.codigo_barras || 'N/A'}</p>
                        </div>
                        <div class="col-span-2 space-y-1 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors">
                            <p class="text-md font-black text-blue-300/40 uppercase tracking-widest">Aplicación / Compatibilidad</p>
                            <p class="text-white font-bold uppercase text-md leading-relaxed">${producto.aplicacion || 'N/A'}</p>
                        </div>
                        <div class="space-y-1 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 group hover:bg-green-500/15 transition-all">
                            <p class="text-md font-black text-green-400/40 uppercase tracking-widest mb-1">Precio Venta</p>
                            <p class="text-green-400 font-black text-2xl group-hover:scale-105 transition-transform origin-left">$${new Intl.NumberFormat().format(producto.precio_venta)}</p>
                        </div>
                        <div class="space-y-1 p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 group hover:bg-blue-500/15 transition-all">
                            <p class="text-md font-black text-blue-400/40 uppercase tracking-widest mb-1">Precio Compra</p>
                            <p class="text-blue-400 font-bold text-xl group-hover:scale-105 transition-transform origin-left">$${new Intl.NumberFormat().format(producto.precio_compra)}</p>
                        </div>
                        <div class="space-y-1 p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col justify-center items-center text-center">
                            <p class="text-md font-black text-blue-300/40 uppercase tracking-widest mb-2">Stock Existente</p>
                            <p class="text-white font-black text-3xl tracking-tighter">${producto.stock}</p>
                        </div>
                        <div class="space-y-1 p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col justify-center items-center text-center">
                            <p class="text-md font-black text-blue-300/40 uppercase tracking-widest mb-2">Punto de Reorden</p>
                            <p class="text-white font-bold text-2xl tracking-tighter">${producto.stock_minimo}</p>
                        </div>
                        <div class="col-span-2 space-y-1 p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-md font-black text-blue-300/40 uppercase tracking-widest mb-2">Observaciones Internas</p>
                            <p class="text-white/70 text-md uppercase italic leading-relaxed">${producto.observaciones || 'SIN COMENTARIOS ADICIONALES'}</p>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-white/10 pt-6">
                        <h4 class="text-md font-black text-blue-200 uppercase tracking-widest mb-4">Últimas Compras</h4>
                        <div id="uc-table-container" class="rounded-xl bg-white/5 border border-white/10 overflow-hidden">
                            <p class="px-4 py-6 text-center text-blue-200/40 font-bold text-xs uppercase tracking-widest">Cargando...</p>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-white/10 pt-6">
                        <h4 class="text-md font-black text-blue-200 uppercase tracking-widest mb-4">Últimas Ventas</h4>
                        <div id="uv-table-container" class="rounded-xl bg-white/5 border border-white/10 overflow-hidden">
                            <div class="flex items-center justify-center py-8">
                                <svg class="animate-spin w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span class="ml-3 text-blue-300/60 text-xs uppercase font-bold tracking-widest">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'CERRAR VENTANA',
            confirmButtonColor: '#3b82f6',
            background: 'rgba(15, 23, 42, 0.95)',
            color: '#fff',
            width: '1000px',
            customClass: {
                popup: 'backdrop-blur-xl border border-white/20 rounded-[3rem] p-8',
                confirmButton: 'px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-xs'
            },
            didOpen: () => {
                window._ucCompras = producto.historial_compras || [];
                renderUltimasCompras(window._ucCompras, 1);
                renderUltimasVentas(producto.id, 1);
            }
        });
    }

    function eliminarProducto(id, nombre) {
        Swal.fire({
            title: '¿ELIMINAR PRODUCTO?',
            text: `ESTÁS A PUNTO DE ELIMINAR "${nombre}". ESTA ACCIÓN NO SE PUEDE DESHACER.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'SÍ, ELIMINAR',
            cancelButtonText: 'CANCELAR',
            background: 'rgba(15, 23, 42, 0.95)',
            color: '#fff',
            customClass: {
                popup: 'backdrop-blur-xl border border-white/20 rounded-3xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    function abrirModalPedimento() {
        const marcas = @json($marcas);
        let options = '<option value="">TODAS</option>';
        marcas.forEach(marca => {
            options += `<option value="${marca}">${marca}</option>`;
        });

        Swal.fire({
            title: 'GENERAR PEDIMENTO',
            html: `
                <div class="text-left space-y-4">
                    <div>
                        <p class="text-blue-200 text-sm mb-2 uppercase font-bold">1. Selecciona el periodo</p>
                        <select id="swal-periodo" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all uppercase">
                            <option value="personalizado" class="bg-slate-800" selected>PERSONALIZADO (FECHAS)</option>
                            <option value="completo" class="bg-slate-800">COMPLETO (TODO BAJO STOCK)</option>
                            <option value="hoy" class="bg-slate-800">HOY</option>
                            <option value="semanal" class="bg-slate-800">SEMANAL (LUNES A HOY)</option>
                            <option value="quincenal" class="bg-slate-800">QUINCENAL (2 SEMANAS)</option>
                            <option value="mensual" class="bg-slate-800">MENSUAL (MES ACTUAL)</option>
                        </select>
                    </div>

                    <div id="div-fechas" class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-blue-200 text-xs mb-1 uppercase font-bold">Inicio</p>
                            <input type="date" id="swal-fecha-inicio" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white text-sm">
                        </div>
                        <div>
                            <p class="text-blue-200 text-xs mb-1 uppercase font-bold">Fin</p>
                            <input type="date" id="swal-fecha-fin" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white text-sm">
                        </div>
                    </div>

                    <div>
                        <p class="text-blue-200 text-sm mb-2 uppercase font-bold">2. Selecciona la marca (Opcional)</p>
                        <select id="swal-marca" class="w-full">
                            ${options}
                        </select>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'GENERAR PDF',
            cancelButtonText: 'CANCELAR',
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#475569',
            background: '#1e293b',
            color: '#fff',
            customClass: {
                popup: 'rounded-3xl border border-white/20 shadow-2xl overflow-visible',
                title: 'text-xl font-black uppercase tracking-tighter'
            },
            didOpen: () => {
                $('#swal-marca').select2({
                    width: '100%',
                    dropdownParent: Swal.getPopup()
                });

                $('#swal-periodo').on('change', function() {
                    if ($(this).val() === 'personalizado') {
                        $('#div-fechas').removeClass('hidden');
                    } else {
                        $('#div-fechas').addClass('hidden');
                    }
                });
            },
            preConfirm: () => {
                const periodo = $('#swal-periodo').val();
                const marca = $('#swal-marca').val();
                const fecha_inicio = $('#swal-fecha-inicio').val();
                const fecha_fin = $('#swal-fecha-fin').val();

                if (periodo === 'personalizado' && (!fecha_inicio || !fecha_fin)) {
                    Swal.showValidationMessage('DEBES SELECCIONAR AMBAS FECHAS');
                }

                return { periodo, marca, fecha_inicio, fecha_fin };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const { periodo, marca, fecha_inicio, fecha_fin } = result.value;
                let url = '{{ route("productos.pedimento") }}';
                url += `?periodo=${periodo}`;
                if (marca) url += `&marca=${encodeURIComponent(marca)}`;
                if (fecha_inicio) url += `&fecha_inicio=${fecha_inicio}`;
                if (fecha_fin) url += `&fecha_fin=${fecha_fin}`;
                
                window.open(url, '_blank');
            }
        });
    }

    function abrirModalInventario() {
        const marcas = @json($marcas);
        let options = '<option value="">TODAS</option>';
        marcas.forEach(marca => {
            options += `<option value="${marca}">${marca}</option>`;
        });

        Swal.fire({
            title: 'INVENTARIO FÍSICO',
            html: `
                <div class="text-left space-y-4">
                    <div>
                        <p class="text-blue-200 text-sm mb-2 uppercase font-bold">1. Selecciona la marca</p>
                        <select id="swal-marca-inv" class="w-full">
                            ${options}
                        </select>
                    </div>
                    <div>
                        <p class="text-blue-200 text-sm mb-2 uppercase font-bold">2. Forma de inventariar</p>
                        <select id="swal-modo-inv" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all uppercase">
                            <option value="digital" class="bg-slate-800">CAPTURA DIGITAL (SISTEMA)</option>
                            <option value="rapida" class="bg-slate-800" selected>CAPTURA RÁPIDA (POR CLAVE)</option>
                            <option value="imprimir" class="bg-slate-800">IMPRIMIR LISTA (PDF)</option>
                        </select>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'CONTINUAR',
            cancelButtonText: 'CANCELAR',
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#475569',
            background: '#1e293b',
            color: '#fff',
            customClass: {
                popup: 'rounded-3xl border border-white/20 shadow-2xl overflow-visible',
                title: 'text-xl font-black uppercase tracking-tighter'
            },
            didOpen: () => {
                $('#swal-marca-inv').select2({
                    width: '100%',
                    dropdownParent: Swal.getPopup()
                });
            },
            preConfirm: () => {
                const marca = $('#swal-marca-inv').val();
                const modo = $('#swal-modo-inv').val();
                return { marca, modo };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const { marca, modo } = result.value;
                if (modo === 'digital') {
                    let url = '{{ route("productos.inventario") }}';
                    if (marca) url += '?marca=' + encodeURIComponent(marca);
                    window.location.href = url;
                } else if (modo === 'rapida') {
                    window.location.href = '{{ route("productos.inventario.captura_rapida") }}';
                } else {
                    let url = '{{ route("productos.inventario.pdf") }}';
                    if (marca) url += '?marca=' + encodeURIComponent(marca);
                    window.open(url, '_blank');
                }
            }
        });
    }


</script>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Estilos Select2 idénticos a los del sistema */
        .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.75rem !important;
            height: 46px !important;
            padding: 8px 12px !important;
            color: white !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white !important;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 0.875rem;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            top: 1px !important;
            right: 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: rgba(191, 219, 254, 0.5) !important;
        }
        .select2-dropdown {
            background-color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 1rem !important;
            overflow: hidden !important;
            z-index: 9999 !important;
        }
        .select2-search__field {
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.5rem !important;
            color: #0f172a !important;
            text-transform: uppercase;
            font-weight: bold;
        }
        .select2-results__option {
            padding: 8px 12px !important;
            font-size: 0.875rem !important;
            text-transform: uppercase !important;
            color: #0f172a !important;
            font-weight: 600 !important;
        }
        .select2-results__option--highlighted {
            background-color: #3b82f6 !important;
            color: white !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: rgba(255, 255, 255, 0.5) transparent transparent transparent !important;
        }
        /* Ajuste para que el select2 se vea bien sobre el sweetalert */
        .swal2-container {
            z-index: 10000;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
@endsection
