@extends('layouts.app')

@section('title', 'Productos Más Vendidos')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white uppercase">Productos Más Vendidos</h1>
            <p class="text-blue-200">Ranking por ventas directas y órdenes de servicio</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button onclick="abrirFiltros()" class="w-fit inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-lg shadow-lg shadow-indigo-900/40 transition-all text-md uppercase tracking-widest cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"></path>
                </svg>
                Cambiar Filtros
            </button>
            <a href="{{ route('productos.index') }}" class="w-fit inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-black rounded-lg border border-white/20 transition-all text-md uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Regresar
            </a>
        </div>
    </div>

    {{-- Filtros activos --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/30 rounded-xl">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-emerald-300 font-bold text-md uppercase tracking-widest">
                Periodo:
                @switch($periodo)
                    @case('hoy') HOY @break
                    @case('semanal') SEMANAL @break
                    @case('quincenal') QUINCENAL @break
                    @case('mensual') MENSUAL @break
                    @case('personalizado')
                        {{ $fecha_inicio ? \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') : '—' }}
                        al
                        {{ $fecha_fin ? \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') : '—' }}
                        @break
                    @default HISTORIAL COMPLETO
                @endswitch
            </span>
        </div>
        @if($marca)
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 border border-blue-500/30 rounded-xl">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="text-blue-300 font-bold text-md uppercase tracking-widest">Marca: {{ $marca }}</span>
            </div>
        @endif
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-xl">
            <span class="text-blue-200 font-bold text-md uppercase tracking-widest">{{ $productos->total() }} productos encontrados</span>
        </div>
    </div>

    {{-- Buscador Style Inventario --}}
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
        <form action="{{ route('productos.mas_vendidos') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="hidden" name="periodo" value="{{ $periodo }}">
            @if($marca)<input type="hidden" name="marca" value="{{ $marca }}">@endif
            @if($fecha_inicio)<input type="hidden" name="fecha_inicio" value="{{ $fecha_inicio }}">@endif
            @if($fecha_fin)<input type="hidden" name="fecha_fin" value="{{ $fecha_fin }}">@endif

            <div class="flex-grow relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="BUSCAR POR SKU, MARCA, CLAVE, CÓDIGO O APLICACIÓN EN ESTOS RESULTADOS..." class="block w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm uppercase">
            </div>
            <button type="submit" class="w-fit px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase tracking-widest">
                BUSCAR
            </button>
            @if(request('search'))
                <a href="{{ route('productos.mas_vendidos', request()->except('search')) }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center uppercase tracking-widest flex items-center justify-center">
                    LIMPIAR
                </a>
            @endif
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white/10 backdrop-blur-xl rounded-3xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center w-12">#</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider">Producto</th>
                        <!-- <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Marca</th> -->
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Cantidad Vendida</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Última Venta</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Última Compra</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @php $offset = ($productos->currentPage() - 1) * $productos->perPage(); @endphp
                    @forelse($productos as $index => $producto)
                        @php
                            $dataDetalle = [
                                'nombre'           => $producto->nombre,
                                'descripcion'      => $producto->descripcion,
                                'marca'            => $producto->marca,
                                'aplicacion'       => $producto->aplicacion,
                                'sku'              => $producto->sku,
                                'cantidad_vendida' => $producto->cantidad_vendida,
                                'ultima_venta'     => $producto->ultima_venta,
                                'ultima_compra'    => $producto->ultima_compra,
                                'stock'            => $producto->stock,
                                'precio_venta'     => $producto->precio_venta,
                                'precio_compra'    => $producto->precio_compra,
                                'historial'        => $producto->historial_transacciones ?? [],
                            ];
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors group">
                            {{-- Ranking --}}
                            <td class="px-4 py-4 text-center">
                                @php $rank = $producto->ranking_real ?? ($offset + $loop->iteration); @endphp
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500/20 border border-yellow-400/40 text-yellow-300 font-black text-md">🥇</span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-400/20 border border-slate-300/40 text-slate-300 font-black text-md">🥈</span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-800/20 border border-orange-600/40 text-orange-400 font-black text-md">🥉</span>
                                @else
                                    <span class="text-blue-200/50 font-bold text-md">{{ $rank }}</span>
                                @endif
                            </td>
                            {{-- Nombre / Descripción --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-white font-bold uppercase group-hover:text-blue-300 transition-colors">{{ $producto->nombre }}</span>
                                    <span class="text-md text-blue-200/60 uppercase">{{ $producto->descripcion ?? 'SIN DESCRIPCIÓN' }}</span>
                                </div>
                            </td>
                            {{-- Marca --}}
                            <!-- <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="text-white font-bold uppercase">{{ $producto->marca ?? 'N/A' }}</span>
                            </td> -->
                            {{-- Cantidad --}}
                            <td class="px-6 py-4 text-center">
                                <span class="text-white font-bold text-md">{{ number_format($producto->cantidad_vendida) }}</span>
                            </td>
                            {{-- Última Venta --}}
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($producto->ultima_venta)
                                    <span class="text-blue-100 text-md font-semibold">
                                        {{ \Carbon\Carbon::parse($producto->ultima_venta)->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-blue-200/30 text-md">N/A</span>
                                @endif
                            </td>
                            {{-- Última Compra --}}
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($producto->ultima_compra)
                                    <span class="text-blue-100 text-md font-semibold">
                                        {{ \Carbon\Carbon::parse($producto->ultima_compra)->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-blue-200/30 text-md">Sin compras</span>
                                @endif
                            </td>
                            {{-- Detalle --}}
                            <td class="px-6 py-4 text-center">
                                <button onclick="verDetalle({{ json_encode($dataDetalle) }})"
                                    class="p-2 rounded-xl transition-all"
                                    style="background-color:rgba(16,185,129,0.2); color:#34d399;"
                                    onmouseover="this.style.backgroundColor='rgba(16,185,129,0.35)'"
                                    onmouseout="this.style.backgroundColor='rgba(16,185,129,0.2)'"
                                    title="VER DETALLE">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-blue-300/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xl font-medium text-blue-200">SIN DATOS PARA EL PERIODO SELECCIONADO</p>
                                    <p class="text-md text-blue-200/50 mt-2 uppercase">Prueba con un periodo diferente o sin filtro de marca.</p>
                                    <button onclick="abrirFiltros()" class="mt-4 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm uppercase transition-all">
                                        Cambiar Filtros
                                    </button>
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
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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
            background-color: #10b981 !important;
            color: white !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: rgba(255, 255, 255, 0.5) transparent transparent transparent !important;
        }
        .swal2-container { z-index: 10000; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function fmtFecha(str) {
            if (!str) return 'N/A';
            // Soporta tanto "2026-03-24T03:56:40" (ISO) como "2026-03-24 03:56:40" (MySQL)
            const partesFecha = str.replace('T', ' ').split(' ');
            return partesFecha[0].split('-').reverse().join('/');
        }

        function verDetalle(p) {
            let filasHistorial = (!p.historial || p.historial.length === 0)
                ? '<tr><td colspan="4" class="text-center text-blue-200/40 py-6 text-md uppercase font-bold tracking-widest">Sin registros en este periodo</td></tr>'
                : p.historial.map(h => `
                    <tr class="border-b border-white/5 last:border-0 hover:bg-white/5 transition-colors">
                        <td class="py-2.5 px-3 text-center"><span class="text-white font-bold text-md">${h.cantidad}</span></td>
                        <td class="py-2.5 px-3 text-center text-blue-100/90 text-md font-semibold">${fmtFecha(h.fecha)}</td>
                        <td class="py-2.5 px-3 text-center text-md">
                            ${h.tipo === 'Venta' 
                                ? '<span class="px-2 py-1 rounded-md bg-blue-500/10 text-blue-300 font-bold border border-blue-500/20 uppercase tracking-widest text-md">VENTA</span>'
                                : '<span class="px-2 py-1 rounded-md bg-purple-500/10 text-purple-300 font-bold border border-purple-500/20 uppercase tracking-widest text-md">ORDEN</span>'}
                        </td>
                        <td class="py-2.5 px-3 text-center text-blue-200/60 text-md font-mono uppercase tracking-widest">${h.folio || 'N/A'}</td>
                    </tr>
                `).join('');

            Swal.fire({
                html: `
                    <div class="text-left">
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-black text-white uppercase mb-1 tracking-tighter">${p.nombre}</h3>
                            <p class="text-blue-100/70 text-md uppercase">${p.descripcion || 'SIN DESCRIPCIÓN'}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="col-span-2 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex flex-col items-center justify-center text-center">
                                <p class="text-md font-black text-emerald-400/50 uppercase tracking-widest mb-1">Total Vendido</p>
                                <p class="text-emerald-300 font-black text-4xl">${new Intl.NumberFormat().format(p.cantidad_vendida)}</p>
                                <!-- <p class="text-md text-emerald-400/40 uppercase mt-1">unidades (ventas + órdenes)</p> -->
                            </div>
                            <div class="p-3 rounded-2xl bg-white/5 border border-white/10">
                                <p class="text-md font-black text-blue-300/40 uppercase tracking-widest mb-1">Última Venta</p>
                                <p class="text-white font-bold text-md">${fmtFecha(p.ultima_venta)}</p>
                            </div>
                            <div class="p-3 rounded-2xl bg-white/5 border border-white/10">
                                <p class="text-md font-black text-blue-300/40 uppercase tracking-widest mb-1">Última Compra</p>
                                <p class="text-white font-bold text-md">${p.ultima_compra ? p.ultima_compra.split('-').reverse().join('/') : 'Sin registro'}</p>
                            </div>
                            <div class="p-3 rounded-2xl bg-white/5 border border-white/10">
                                <p class="text-md font-black text-blue-300/40 uppercase tracking-widest mb-1">Marca</p>
                                <p class="text-white font-bold text-md uppercase">${p.marca || 'N/A'}</p>
                            </div>
                            <div class="p-3 rounded-2xl bg-white/5 border border-white/10">
                                <p class="text-md font-black text-blue-300/40 uppercase tracking-widest mb-1">Stock Actual</p>
                                <p class="text-white font-bold text-md">${new Intl.NumberFormat().format(p.stock)}</p>
                            </div>
                            <div class="p-3 rounded-2xl bg-green-500/10 border border-green-500/20">
                                <p class="text-md font-black text-green-400/50 uppercase tracking-widest mb-1">Precio Venta</p>
                                <p class="text-green-300 font-bold text-md">$${new Intl.NumberFormat('en-US', {minimumFractionDigits:2}).format(p.precio_venta)}</p>
                            </div>
                            <div class="p-3 rounded-2xl bg-blue-500/10 border border-blue-500/20">
                                <p class="text-md font-black text-blue-400/50 uppercase tracking-widest mb-1">Precio Compra</p>
                                <p class="text-blue-300 font-bold text-md">$${new Intl.NumberFormat('en-US', {minimumFractionDigits:2}).format(p.precio_compra)}</p>
                            </div>
                            <div class="col-span-2 p-3 rounded-2xl bg-white/5 border border-white/10">
                                <p class="text-md font-black text-blue-300/40 uppercase tracking-widest mb-1">Aplicación</p>
                                <p class="text-white font-semibold text-md uppercase leading-relaxed">${p.aplicacion || 'N/A'}</p>
                            </div>
                        </div>

                        {{-- HISTORIAL DE TRANSACCIONES --}}
                        <div class="mt-4 bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                            <div class="bg-white/5 px-4 py-3 border-b border-white/10">
                                <p class="text-md font-black text-blue-300 uppercase tracking-widest">Historial de Movimientos</p>
                                <!-- <p class="text-md text-blue-200/50 uppercase mt-0.5">Últimos registros en el periodo seleccionado (Top 50)</p> -->
                            </div>
                            <div class="max-h-56 overflow-y-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-white/5 sticky top-0 backdrop-blur-md">
                                        <tr>
                                            <th class="px-3 py-2.5 text-md font-black text-blue-200/60 uppercase tracking-widest text-center">Cant.</th>
                                            <th class="px-3 py-2.5 text-md font-black text-blue-200/60 uppercase tracking-widest text-center">Fecha</th>
                                            <th class="px-3 py-2.5 text-md font-black text-blue-200/60 uppercase tracking-widest text-center">Tipo</th>
                                            <th class="px-3 py-2.5 text-md font-black text-blue-200/60 uppercase tracking-widest text-center">Folio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${filasHistorial}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'CERRAR',
                confirmButtonColor: '#10b981',
                background: 'rgba(15, 23, 42, 0.97)',
                color: '#fff',
                width: '600px',
                customClass: {
                    popup: 'backdrop-blur-xl border border-white/20 rounded-[2.5rem] p-6',
                    confirmButton: 'px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-md'
                }
            });
        }

        function abrirFiltros() {
            const marcas = @json($marcas);
            const marcaActual = @json($marca ?? '');
            const periodoActual = @json($periodo);
            const fechaInicioActual = '{{ $fecha_inicio ? \Carbon\Carbon::parse($fecha_inicio)->format("Y-m-d") : "" }}';
            const fechaFinActual = '{{ $fecha_fin ? \Carbon\Carbon::parse($fecha_fin)->format("Y-m-d") : "" }}';

            let options = '<option value="">TODAS</option>';
            marcas.forEach(m => {
                options += `<option value="${m}" ${m === marcaActual ? 'selected' : ''}>${m}</option>`;
            });

            Swal.fire({
                title: 'MÁS VENDIDOS',
                html: `
                    <div class="text-left space-y-4">
                        <div>
                            <p class="text-blue-200 text-sm mb-2 uppercase font-bold">1. Selecciona el periodo</p>
                            <select id="mv-periodo" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none uppercase">
                                <option value="completo" ${periodoActual==='completo'?'selected':''} class="bg-slate-800">HISTORIAL COMPLETO</option>
                                <option value="hoy" ${periodoActual==='hoy'?'selected':''} class="bg-slate-800">HOY</option>
                                <option value="semanal" ${periodoActual==='semanal'?'selected':''} class="bg-slate-800">SEMANAL (LUNES A HOY)</option>
                                <option value="quincenal" ${periodoActual==='quincenal'?'selected':''} class="bg-slate-800">QUINCENAL (2 SEMANAS)</option>
                                <option value="mensual" ${periodoActual==='mensual'?'selected':''} class="bg-slate-800">MENSUAL (MES ACTUAL)</option>
                                <option value="personalizado" ${periodoActual==='personalizado'?'selected':''} class="bg-slate-800">PERSONALIZADO (FECHAS)</option>
                            </select>
                        </div>
                        <div id="mv-div-fechas" class="${periodoActual==='personalizado'?'':'hidden'} grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-blue-200 text-xs mb-1 uppercase font-bold">Inicio</p>
                                <input type="date" id="mv-fecha-inicio" value="${fechaInicioActual}" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white text-sm">
                            </div>
                            <div>
                                <p class="text-blue-200 text-xs mb-1 uppercase font-bold">Fin</p>
                                <input type="date" id="mv-fecha-fin" value="${fechaFinActual}" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white text-sm">
                            </div>
                        </div>
                        <div>
                            <p class="text-blue-200 text-sm mb-2 uppercase font-bold">2. Selecciona la marca (Opcional)</p>
                            <select id="mv-marca" class="w-full">${options}</select>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'VER RESULTADOS',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#475569',
                background: '#1e293b',
                color: '#fff',
                customClass: {
                    popup: 'rounded-3xl border border-white/20 shadow-2xl overflow-visible',
                    title: 'text-xl font-black uppercase tracking-tighter'
                },
                didOpen: () => {
                    $('#mv-marca').select2({ width: '100%', dropdownParent: Swal.getPopup() });
                    $('#mv-periodo').on('change', function() {
                        if ($(this).val() === 'personalizado') {
                            $('#mv-div-fechas').removeClass('hidden');
                        } else {
                            $('#mv-div-fechas').addClass('hidden');
                        }
                    });
                },
                preConfirm: () => {
                    const periodo = $('#mv-periodo').val();
                    const marca   = $('#mv-marca').val();
                    const fi      = $('#mv-fecha-inicio').val();
                    const ff      = $('#mv-fecha-fin').val();
                    if (periodo === 'personalizado' && (!fi || !ff)) {
                        Swal.showValidationMessage('DEBES SELECCIONAR AMBAS FECHAS');
                        return false;
                    }
                    return { periodo, marca, fi, ff };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const { periodo, marca, fi, ff } = result.value;
                    let url = '{{ route("productos.mas_vendidos") }}?periodo=' + periodo;
                    if (marca) url += '&marca=' + encodeURIComponent(marca);
                    if (fi)    url += '&fecha_inicio=' + fi;
                    if (ff)    url += '&fecha_fin=' + ff;
                    
                    const search = new URLSearchParams(window.location.search).get('search');
                    if (search) url += '&search=' + encodeURIComponent(search);

                    window.location.href = url;
                }
            });
        }
    </script>
@endpush
