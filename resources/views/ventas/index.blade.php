@extends('layouts.app')

@section('title', 'Historial de Ventas')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 0.75rem !important;
            height: 50px !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white !important;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.05em;
            padding-left: 16px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px !important;
            top: 0 !important;
            right: 10px !important;
        }
        .select2-dropdown {
            background-color: #ffffff !important;
            border-radius: 0.75rem !important;
            border: none !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
            z-index: 9999 !important;
            margin-top: 5px !important;
        }
        .select2-results__option {
            color: black !important;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: 800;
            padding: 12px 16px !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2563eb !important;
            color: white !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-radius: 8px !important;
            color: black !important;
            padding: 8px !important;
        }
    </style>
@endpush

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white uppercase">Historial de Ventas</h1>
            <p class="text-blue-200">Gestión y control de ingresos</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('ventas.create') }}" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Venta
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
        <form action="{{ route('ventas.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="md:flex-[3] relative w-full">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Buscar Venta</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="FOLIO O CLIENTE..." class="block w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 backdrop-blur-sm uppercase text-md font-bold">
                </div>
            </div>

            <div class="md:flex-1 w-full">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Método de Pago</label>
                <select name="metodo_pago" id="metodo_pago_filter" class="select2-filter">
                    <option value="">TODOS</option>
                    <option value="CREDITO" {{ request('metodo_pago') == 'CREDITO' ? 'selected' : '' }}>CRÉDITO</option>
                    <option value="EFECTIVO" {{ request('metodo_pago') == 'EFECTIVO' ? 'selected' : '' }}>EFECTIVO</option>
                    <option value="TARJETA DE DÉBITO" {{ request('metodo_pago') == 'TARJETA DE DÉBITO' ? 'selected' : '' }}>TARJETA DE DÉBITO</option>
                    <option value="TARJETA DE CRÉDITO" {{ request('metodo_pago') == 'TARJETA DE CRÉDITO' ? 'selected' : '' }}>TARJETA DE CRÉDITO</option>
                    <option value="TRANSFERENCIA" {{ request('metodo_pago') == 'TRANSFERENCIA' ? 'selected' : '' }}>TRANSFERENCIA</option>
                    <option value="CHEQUE" {{ request('metodo_pago') == 'CHEQUE' ? 'selected' : '' }}>CHEQUE</option>
                    <option value="PRESTAMO" {{ request('metodo_pago') == 'PRESTAMO' ? 'selected' : '' }}>PRÉSTAMO</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-fit px-8 py-3 h-[50px] bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase flex items-center justify-center">
                    BUSCAR
                </button>
                @if(request('buscar') || request('metodo_pago'))
                    <a href="{{ route('ventas.index') }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center uppercase">
                        LIMPIAR
                    </a>
                @endif
            </div>
        </form>
    </div>
    <!-- Tabs de Filtrado -->
    <div class="flex flex-wrap items-center gap-2 mb-4 mt-8">
        @php
            $currentPeriod = request('periodo');
            // Si no hay periodo específico ni otros filtros, el activo es 'hoy'
            if (!request()->filled('periodo') && !request()->filled('buscar') && !request()->filled('cliente_id') && !request()->filled('metodo_pago')) {
                $currentPeriod = 'hoy';
            } else {
                $currentPeriod = $currentPeriod ?? 'todos';
            }

            $tabs = [
                'todos' => 'Todos',
                'mes' => 'Mes',
                'semana' => 'Semana',
                'hoy' => 'Hoy'
            ];
        @endphp

        @foreach($tabs as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['periodo' => $key]) }}" 
               class="px-6 py-2 rounded-xl border transition-all duration-300 font-bold uppercase text-xs tracking-widest
               {{ $currentPeriod == $key 
                  ? 'bg-blue-600 border-blue-500 text-white shadow-lg shadow-blue-600/20 scale-105' 
                  : 'bg-white/5 border-white/10 text-blue-200 hover:bg-white/10 hover:border-white/20' 
               }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Ventas Table -->
    @php
        $currentSort = request('sort', 'folio');
        $currentDir = request('direction', 'desc');

        $sortUrl = function($column) use ($currentSort, $currentDir) {
            $newDir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDir]);
        };

        $sortIcon = function($column) use ($currentSort, $currentDir) {
            $isActive = ($currentSort === $column);
            $upColor = ($isActive && $currentDir === 'asc') ? 'text-blue-400' : 'text-blue-200/20';
            $downColor = ($isActive && $currentDir === 'desc') ? 'text-blue-400' : 'text-blue-200/20';
            
            return '
                <span class="inline-flex flex-col ml-2 transform translate-y-0.5">
                    <svg class="w-2 h-2 ' . $upColor . ' fill-current" viewBox="0 0 24 24"><path d="M12 5l-8 8h16l-8-8z"/></svg>
                    <svg class="w-2 h-2 ' . $downColor . ' fill-current" viewBox="0 0 24 24"><path d="M12 19l8-8H4l8 8z"/></svg>
                </span>
            ';
        };
    @endphp
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10 font-bold uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('folio') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Folio {!! $sortIcon('folio') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('fecha') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Fecha {!! $sortIcon('fecha') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('cliente') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Cliente {!! $sortIcon('cliente') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('total') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Total / Saldo {!! $sortIcon('total') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('metodo') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Método de Pago {!! $sortIcon('metodo') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('estado') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Estado {!! $sortIcon('estado') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('factura') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Factura {!! $sortIcon('factura') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($ventas as $venta)
                    <tr class="hover:bg-white/5 transition-colors group"
                        data-venta="{{ json_encode([
                            'folio' => $venta->folio,
                            'fecha' => $venta->fecha->translatedFormat('d M, Y'),
                            'cliente' => $venta->cliente->nombre,
                            'total' => number_format($venta->total, 2),
                            'saldo' => number_format($venta->saldo_pendiente, 2),
                            'metodo' => $venta->metodo_pago,
                            'estado' => $venta->estado,
                            'detalles' => $venta->detalles->map(fn($d) => [
                                'nombre' => $d->producto?->nombre ?? $d->servicio?->nombre ?? 'N/A',
                                'descripcion' => $d->producto?->descripcion ?? $d->servicio?->descripcion ?? '---',
                                'cantidad' => $d->cantidad,
                                'subtotal' => number_format($d->subtotal, 2)
                            ]),
                            'observaciones' => $venta->observaciones ?? '',
                            'factura' => $venta->folio_factura ?? '',
                        ]) }}">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-bold text-md uppercase">{{ $venta->folio }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-medium uppercase text-md">{{ $venta->fecha->translatedFormat('d M, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-blue-100 font-bold uppercase text-md group-hover:text-blue-300 transition-colors">{{ $venta->cliente->nombre }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <p class="text-white font-bold text-md">${{ number_format($venta->total, 2) }}</p>
                                @if($venta->saldo_pendiente > 0 || $venta->estado === 'PENDIENTE')
                                    <p class="text-md font-bold uppercase text-red-400">
                                        Saldo: ${{ number_format($venta->saldo_pendiente, 2) }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-medium uppercase text-md">
                                    {{ $venta->metodo_pago }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center uppercase tracking-widest font-black">
                                @php
                                    $color = match($venta->estado) {
                                        'PAGADA' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                        'PENDIENTE' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                        'CANCELADA' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                        'PRESTAMO' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                        'DEVUELTO' => 'bg-teal-500/20 text-teal-300 border-teal-500/30',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-sm border {{ $color }}">
                                    {{ $venta->estado == 'PENDIENTE' ? 'PENDIENTE DE PAGO' : ($venta->estado == 'PRESTAMO' ? 'EN PRÉSTAMO' : $venta->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($venta->requiere_factura === 'SI')
                                    <span @class([
                                        'px-2 py-1 rounded-lg text-md font-black uppercase tracking-widest border',
                                        'bg-teal-500/20 text-teal-400 border-teal-400/50' => $venta->folio_factura,
                                        'bg-amber-500/10 text-amber-300 border-amber-500/20' => !$venta->folio_factura
                                    ])>
                                        SÍ
                                    </span>
                                    @if($venta->folio_factura)
                                        <p class="text-md text-teal-400 font-bold mt-1 uppercase">{{ $venta->folio_factura }}</p>
                                    @endif
                                @else
                                    <span class="text-white/20 text-md font-bold uppercase tracking-widest bg-white/5 px-2 py-1 rounded-lg border border-white/10">
                                        NO
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center gap-2">
                                    @if($venta->estado === 'PENDIENTE')
                                        <button onclick="registrarPagoVenta({{ $venta->id }}, '{{ $venta->folio }}', {{ $venta->saldo_pendiente }})" 
                                                class="p-2 bg-green-500/10 hover:bg-green-500/20 text-green-300 rounded-lg border border-green-500/10 transition-all cursor-pointer" 
                                                title="REGISTRAR PAGO">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @endif



                                    <button onclick="vistaRapida(this)" class="p-2 bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 rounded-lg border border-purple-500/10 transition-all cursor-pointer" title="VISTA RÁPIDA">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('ventas.show', $venta) }}" class="p-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-300 rounded-lg border border-blue-500/10 transition-all cursor-pointer" title="VER DETALLE">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    @if($venta->estado !== 'CANCELADA')
                                        @if($venta->requiere_factura === 'SI' || ($venta->requiere_factura === 'NO' && $venta->estado === 'PAGADA') || ($venta->requiere_factura === 'NO' && $venta->estado === 'PENDIENTE'))
                                            <button onclick="abrirModalFactura({{ $venta->id }}, '{{ $venta->folio_factura }}')" 
                                                    class="p-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 rounded-lg border border-amber-500/10 transition-all cursor-pointer"
                                                    title="REGISTRAR FACTURA">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </button>
                                        @endif

                                        <a href="{{ route('ventas.pdf', $venta) }}" 
                                            target="_blank" class="p-2 bg-green-500/10 hover:bg-green-500/20 text-green-300 rounded-lg border border-green-500/10 transition-all cursor-pointer" title="IMPRIMIR COMPROBANTE">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                        </a>

                                        @if($venta->estado === 'PRESTAMO')
                                            <button onclick="resolverPrestamo({{ $venta->id }}, '{{ $venta->folio }}')" 
                                                    class="p-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-300 rounded-lg border border-blue-500/10 transition-all cursor-pointer" 
                                                    title="RESOLVER PRÉSTAMO">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                                                </svg>
                                            </button>
                                        @endif

                                        <button onclick="cancelarVenta({{ $venta->id }}, '{{ $venta->folio }}')" 
                                                class="p-2 bg-red-500/10 hover:bg-red-500/20 text-red-300 rounded-lg border border-red-500/10 transition-all cursor-pointer"
                                                title="CANCELAR VENTA">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <button onclick="verMotivo('{{ addslashes($venta->motivo_cancelacion) }}', '{{ $venta->cancelado_at ? $venta->cancelado_at->format('d/m/Y H:i') : 'N/A' }}')" 
                                                class="p-2 bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 rounded-lg border border-purple-500/10 transition-all cursor-pointer"
                                                title="VER MOTIVO DE CANCELACIÓN">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-blue-300/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xl font-medium text-blue-200 uppercase tracking-tighter">No hay registros de ventas</p>
                                    <p class="text-[10px] text-blue-200/50 mt-2 uppercase tracking-widest font-black">Comienza registrando tu primera venta para ver el historial.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($ventas->hasPages())
            <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                {{ $ventas->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#cliente_id_filter').select2({
                width: '100%',
                placeholder: 'SELECCIONAR...',
                allowClear: true,
                dropdownParent: $('#cliente_id_filter').parent()
            });

            $('.select2-filter').select2({
                width: '100%'
            });
        });

        function abrirModalFactura(ventaId, folioActual) {
            Swal.fire({
                title: 'REGISTRAR FACTURA',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="p-4 space-y-4 text-left">
                        <div class="flex items-center bg-amber-500/10 p-4 rounded-xl border border-amber-500/20 mb-4">
                            <svg class="w-6 h-6 text-amber-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-amber-200/80 font-bold uppercase tracking-wider">Captura el folio de la factura emitida para esta venta.</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">FOLIO DE FACTURA *</label>
                            <input type="text" id="modal_folio_factura" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all uppercase" value="${folioActual !== 'null' ? folioActual : ''}" placeholder="EJ: F-1234">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'GUARDAR FACTURA',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#ef4444',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl transition-all duration-300',
                    title: 'text-xl font-black uppercase tracking-tighter pt-6',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm'
                },
                preConfirm: () => {
                    const folio = document.getElementById('modal_folio_factura').value;
                    if (!folio) {
                        Swal.showValidationMessage('El folio es obligatorio');
                        return false;
                    }
                    return { folio_factura: folio };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Guardando...',
                        background: '#1e293b',
                        color: '#fff',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: `/ventas/${ventaId}/facturar`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            folio_factura: result.value.folio_factura
                        },
                        success: function(data) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡LISTO!',
                                text: data.message,
                                background: '#1e293b',
                                color: '#fff',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            const data = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'ERROR',
                                text: data.message || 'Error al procesar la solicitud',
                                background: '#1e293b',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        }

        function cancelarVenta(ventaId, folio) {
            Swal.fire({
                title: '¿CANCELAR VENTA?',
                text: `Se cancelará la venta ${folio} y el stock de los productos se restaurará. ESTA ACCIÓN NO SE PUEDE DESHACER.`,
                icon: 'warning',
                background: '#1e293b',
                color: '#fff',
                input: 'textarea',
                inputLabel: 'MOTIVO DE CANCELACIÓN *',
                inputPlaceholder: 'Ingresa el motivo detallado...',
                inputAttributes: {
                    'aria-label': 'Motivo de cancelación'
                },
                showCancelButton: true,
                confirmButtonText: 'SÍ, CANCELAR VENTA',
                cancelButtonText: 'NO, VOLVER',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#475569',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl transition-all duration-300',
                    title: 'text-xl font-black uppercase tracking-tighter pt-6',
                    input: 'bg-white/5 border-white/10 rounded-xl text-white focus:ring-red-500 uppercase text-xs p-4 h-32',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm'
                },
                preConfirm: (motivo) => {
                    if (!motivo) {
                        Swal.showValidationMessage('El motivo de cancelación es obligatorio');
                        return false;
                    }
                    return motivo;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Cancelando...',
                        background: '#1e293b',
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: `/ventas/${ventaId}/cancelar`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            motivo_cancelacion: result.value
                        },
                        success: function(data) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡VENTA CANCELADA!',
                                text: data.message,
                                background: '#1e293b',
                                color: '#fff',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            const data = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'ERROR',
                                text: data.message || 'Error al cancelar la venta',
                                background: '#1e293b',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        }

        function vistaRapida(btn) {
            const row = btn.closest('tr');
            const v = JSON.parse(row.dataset.venta);
            const estadoColor = v.estado === 'PAGADA' ? '#4ade80' : v.estado === 'PENDIENTE' ? '#fbbf24' : '#f87171';

            Swal.fire({
                title: v.folio,
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div style="text-align:left; font-size:14px; margin-top:6px;">
                        <table style="width:100%; border-collapse:collapse; margin-bottom:15px;">
                            <tr>
                                <td style="padding:4px; color:#93c5fd; font-size:14px; text-transform:uppercase; font-weight:700; width:65%">Cliente</td>
                                <td style="padding:4px; color:#93c5fd; font-size:14px; text-transform:uppercase; font-weight:700; width:35%; text-align:right;">Fecha</td>
                            </tr>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <td style="padding:0 4px 8px 4px; font-size:14px">${v.cliente}</td>
                                <td style="padding:0 4px 8px 4px; font-size:14px; text-align:right;">${v.fecha}</td>
                            </tr>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <td style="padding:7px 4px; color:#93c5fd; font-size:14px; text-transform:uppercase; font-weight:700;">Método</td>
                                <td style="padding:7px 4px; font-size:14px; text-align:right;">${v.metodo}</td>
                            </tr>
                        </table>

                        <p style="color:#93c5fd; font-size:14px; text-transform:uppercase; font-weight:700; margin-bottom:8px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:4px;">Detalle de Artículos</p>
                        <table style="width:100%; border-collapse:collapse; margin-bottom:15px; font-size:12px;">
                            <thead>
                                <tr style="color:#93c5fd; text-align:center;">
                                    <th style="padding:4px; font-size:14px; border-bottom:1px solid rgba(255,255,255,0.1);">CANT</th>
                                    <th style="padding:4px; font-size:14px; border-bottom:1px solid rgba(255,255,255,0.1); text-align:left;">PRODUCTO/SERVICIO</th>
                                    <th style="padding:4px; font-size:14px; border-bottom:1px solid rgba(255,255,255,0.1); text-align:right;">IMPORTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${v.detalles.map(d => `
                                    <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                        <td style="padding:6px 4px; text-align:center; font-size:14px;">${d.cantidad}</td>
                                        <td style="padding:6px 4px; text-transform:uppercase;">
                                            <div style="font-size:14px;">${d.nombre}</div>
                                            <div style="font-size:14px;">${d.descripcion}</div>
                                        </td>
                                        <td style="padding:6px 4px; text-align:right; font-size:14px;">$${d.subtotal}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>

                        <table style="width:100%; border-collapse:collapse;">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <td style="padding:7px 4px; color:#93c5fd; font-size:14px; text-transform:uppercase; font-size:14px; width:38%">Total</td>
                                <td style="padding:7px 4px; font-size:14px; color:#4ade80; text-align:right;">$${ v.total}</td>
                            </tr>
                            ${parseFloat(v.saldo) > 0 ? `
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <td style="padding:7px 4px; color:#f87171; font-size:14px; text-transform:uppercase; font-size:14px;">Saldo Pendiente</td>
                                <td style="padding:7px 4px; font-size:14px; color:#f87171; text-align:right;">$${ v.saldo}</td>
                            </tr>` : ''}
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <td style="padding:7px 4px; color:#93c5fd; font-size:14px; text-transform:uppercase; font-size:14px;">Estado</td>
                                <td style="padding:7px 4px; text-align:right; font-size:14px;"><span style="color:${estadoColor};">${v.estado}</span></td>
                            </tr>
                            ${v.factura ? `
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <td style="padding:7px 4px; color:#93c5fd; font-size:14px; text-transform:uppercase; font-size:14px;">Factura</td>
                                <td style="padding:7px 4px; font-size:14px; text-align:right;">${v.factura}</td>
                            </tr>` : ''}
                            ${v.observaciones ? `
                            <tr>
                                <td colspan="2" style="padding:10px 4px;">
                                    <p style="color:#93c5fd; font-size:14px; text-transform:uppercase; font-size:14px; margin-bottom:4px;">Observaciones:</p>
                                    <p style="margin:0; font-size:14px; color:#cbd5e1; font-style:italic;">${v.observaciones}</p>
                                </td>
                            </tr>` : ''}
                        </table>
                    </div>
                `,
                showCancelButton: false,
                confirmButtonText: 'CERRAR',
                confirmButtonColor: '#475569',
                customClass: {
                    popup: 'rounded-3xl border border-white/20 shadow-2xl',
                    title: 'text-xl font-black uppercase tracking-tighter'
                }
            });
        }

        function verMotivo(motivo, fecha) {
            Swal.fire({
                title: 'MOTIVO DE CANCELACIÓN',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="p-6 text-left space-y-4">
                        <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-2xl">
                            <p class="text-[10px] text-red-300 font-bold uppercase tracking-widest mb-1">Fecha de Cancelación:</p>
                            <p class="text-white font-mono italic">${fecha}</p>
                        </div>
                        <div class="bg-white/5 border border-white/10 p-4 rounded-2xl">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Motivo detallado:</p>
                            <p class="text-blue-100 font-bold uppercase leading-relaxed text-sm">${motivo}</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'ENTENDIDO',
                confirmButtonColor: '#6366f1',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl transition-all duration-300',
                    title: 'text-xl font-black uppercase tracking-tighter pt-6',
                    confirmButton: 'rounded-xl px-12 py-3 font-bold uppercase tracking-widest text-sm'
                }
            });
        }

        function registrarPagoVenta(ventaId, folio, saldoPendiente) {
            const fechaHoy = "{{ date('Y-m-d\TH:i') }}";

            Swal.fire({
                title: 'REGISTRAR PAGO - ' + folio,
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="text-left mt-4 space-y-6">
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/10 text-center mb-6">
                            <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-widest mb-1">Saldo Pendiente</p>
                            <p class="text-3xl font-black text-white">$${new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(saldoPendiente)}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1 text-center">Monto a abonar *</label>
                                <input type="number" step="0.01" id="pago_monto" value="${saldoPendiente}" max="${saldoPendiente}" 
                                    class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-black text-xl text-center focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all shadow-inner" required>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1 text-center">Fecha del Pago *</label>
                                <input type="datetime-local" id="pago_fecha" value="${fechaHoy}" 
                                    class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all uppercase shadow-inner" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1 text-center">Método de Pago *</label>
                                <select id="pago_metodo" class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all uppercase shadow-inner cursor-pointer" required>
                                    <option value="EFECTIVO" style="color: black !important;">EFECTIVO</option>
                                    <option value="TARJETA DE DÉBITO" style="color: black !important;">TARJETA DE DÉBITO</option>
                                    <option value="TARJETA DE CRÉDITO" style="color: black !important;">TARJETA DE CRÉDITO</option>
                                    <option value="TRANSFERENCIA" style="color: black !important;">TRANSFERENCIA</option>
                                    <option value="CHEQUE" style="color: black !important;">CHEQUE</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1 text-center">Referencia</label>
                                <input type="text" id="pago_referencia" class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/20 shadow-inner" placeholder="PAGO VENTA ...">
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'REGISTRAR PAGO',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#475569',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl transition-all duration-300',
                    title: 'text-xl font-black uppercase tracking-tighter pt-6',
                    confirmButton: 'rounded-xl px-12 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-12 py-3 font-bold uppercase tracking-widest text-sm'
                },
                preConfirm: () => {
                    const monto = document.getElementById('pago_monto').value;
                    const fecha = document.getElementById('pago_fecha').value;
                    const metodo = document.getElementById('pago_metodo').value;
                    const referencia = document.getElementById('pago_referencia').value;

                    if (!monto || monto <= 0) {
                        Swal.showValidationMessage('El monto es inválido');
                        return false;
                    }
                    if (monto > saldoPendiente) {
                        Swal.showValidationMessage('El monto no puede exceder el saldo');
                        return false;
                    }
                    if (!fecha) {
                        Swal.showValidationMessage('La fecha es obligatoria');
                        return false;
                    }

                    return { monto, fecha, metodo, referencia };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando pago...',
                        background: '#1e293b',
                        color: '#fff',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(`/ventas/${ventaId}/pagos`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            monto: result.value.monto,
                            fecha_pago: result.value.fecha,
                            metodo_pago: result.value.metodo,
                            referencia: result.value.referencia
                        })
                    })
                    .then(response => response.json().then(data => ({ status: response.status, data })))
                    .then(({ status, data }) => {
                        if (status === 200 || status === 201) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡PAGO REGISTRADO!',
                                text: data.message,
                                background: '#1e293b',
                                color: '#fff',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Error al procesar el pago');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'ERROR',
                            text: error.message || 'Error al procesar el pago',
                            background: '#1e293b',
                            color: '#fff'
                        });
                    });
                }
            });
        }

        function resolverPrestamo(ventaId, folio) {
            Swal.fire({
                title: 'RESOLUCIÓN DE PRÉSTAMO',
                text: `¿Cómo desea finalizar el préstamo ${folio}?`,
                icon: 'question',
                background: '#1e293b',
                color: '#fff',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: '<div class="flex flex-col"><span>DEVOLVER MATERIAL</span><small class="opacity-70">Reintegrar al stock</small></div>',
                denyButtonText: '<div class="flex flex-col"><span>CONVERTIR A PAGO</span><small class="opacity-70">El cliente lo compra</small></div>',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#10b981',
                denyButtonColor: '#3b82f6',
                cancelButtonColor: '#475569',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl transition-all duration-300',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs min-w-[180px]',
                    denyButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs min-w-[180px]',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // DEVOLUCIÓN FÍSICA
                    procesarResolucion(ventaId, { resolucion: 'devolucion' });
                } else if (result.isDenied) {
                    // CONVERTIR A PAGO
                    abrirModalPagoPrestamo(ventaId, folio);
                }
            });
        }

        function abrirModalPagoPrestamo(ventaId, folio) {
            Swal.fire({
                title: 'PROCESANDO DATOS...',
                background: '#1e293b',
                didOpen: () => Swal.showLoading()
            });

            // Obtener detalles de la venta
            $.get(`/ventas/${ventaId}`, function(response) {
                const venta = response.venta;
                let rowsHtml = '';
                let totalInicial = 0;
                
                venta.detalles.forEach(detalle => {
                    const nombre = detalle.producto ? detalle.producto.nombre : (detalle.servicio ? detalle.servicio.nombre : 'SERVICIO');
                    const subtotal = detalle.cantidad * detalle.precio_unitario;
                    totalInicial += subtotal;
                    
                    rowsHtml += `
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="py-3 px-2 text-center text-white font-mono text-md font-bold">${detalle.cantidad}</td>
                            <td class="py-3 px-2 text-left">
                                <p class="text-md font-black text-blue-300 uppercase leading-none">${nombre}</p>
                                <p class="text-md text-white/50 uppercase mt-1 italic">${detalle.producto ? (detalle.producto.descripcion || '') : (detalle.servicio ? (detalle.servicio.descripcion || '') : '')}</p>
                            </td>
                            <td class="py-3 px-2">
                                <div class="flex items-center gap-1 bg-black/20 rounded-lg px-2 border border-white/10 focus-within:border-blue-500 transition-all">
                                    <span class="text-white/40 text-md font-bold">$</span>
                                    <input type="number" step="0.01" 
                                        class="item-precio w-full bg-transparent border-none py-2 text-md text-right text-white font-black focus:ring-0 outline-none" 
                                        data-id="${detalle.id}" 
                                        data-cantidad="${detalle.cantidad}"
                                        value="${detalle.precio_unitario}"
                                        oninput="recalcularTotalModal()">
                                </div>
                            </td>
                            <td class="py-3 px-2 text-right">
                                <span class="item-subtotal text-md font-black text-white font-mono" id="subtotal-${detalle.id}">
                                    $${subtotal.toLocaleString('es-MX', {minimumFractionDigits: 2})}
                                </span>
                            </td>
                        </tr>
                    `;
                });

                Swal.fire({
                    title: `COBRO DE PRÉSTAMO ${folio}`,
                    background: '#1e293b',
                    color: '#fff',
                    width: '850px',
                    html: `
                        <div class="text-left mb-6">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="border-b border-white/20">
                                        <th class="py-2 px-2 text-blue-200 text-xs font-black uppercase tracking-widest text-center" style="width: 10%">Cant.</th>
                                        <th class="py-2 px-2 text-blue-200 text-xs font-black uppercase tracking-widest text-left" style="width: 50%">Descripción</th>
                                        <th class="py-2 px-2 text-blue-200 text-xs font-black uppercase tracking-widest text-center" style="width: 20%">Precio Unit.</th>
                                        <th class="py-2 px-2 text-blue-200 text-xs font-black uppercase tracking-widest text-right" style="width: 20%">Importe</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-items-body">
                                    ${rowsHtml}
                                </tbody>
                                <tfoot>
                                    <tr class="bg-white/5">
                                        <td colspan="3" class="py-4 px-4 text-right">
                                            <span class="text-blue-200 text-sm font-black uppercase tracking-[0.2em]">Total a Pagar</span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <span id="modal-total-final" class="text-2xl font-black text-green-400 font-mono tracking-tighter">
                                                $${totalInicial.toLocaleString('es-MX', {minimumFractionDigits: 2})}
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="grid grid-cols-2 gap-6 text-left p-4 bg-white/5 rounded-2xl border border-white/10">
                            <div>
                                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Método de Pago</label>
                                <select id="res-metodo" class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-4 text-md text-white font-black uppercase outline-none focus:ring-2 focus:ring-blue-500 shadow-inner cursor-pointer">
                                    <option value="EFECTIVO" class="text-black">EFECTIVO</option>
                                    <option value="TRANSFERENCIA" class="text-black">TRANSFERENCIA</option>
                                    <option value="TARJETA DE DÉBITO" class="text-black">TARJETA DE DÉBITO</option>
                                    <option value="TARJETA DE CRÉDITO" class="text-black">TARJETA DE CRÉDITO</option>
                                    <option value="CHEQUE" class="text-black">CHEQUE</option>
                                    <option value="CREDITO" class="text-black">CRÉDITO</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">¿Requiere Factura?</label>
                                <select id="res-factura" class="w-full bg-black/40 border border-white/20 rounded-xl px-4 py-4 text-md text-white font-black uppercase outline-none focus:ring-2 focus:ring-blue-500 shadow-inner cursor-pointer">
                                    <option value="NO" class="text-black" ${venta.requiere_factura === 'NO' ? 'selected' : ''}>NO</option>
                                    <option value="SI" class="text-black" ${venta.requiere_factura === 'SI' ? 'selected' : ''}>SÍ</option>
                                </select>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'CONFIRMAR PAGO',
                    cancelButtonText: 'VOLVER',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#475569',
                    customClass: {
                        container: 'backdrop-blur-sm',
                        popup: 'rounded-[2.5rem] border border-white/10 shadow-2xl',
                        confirmButton: 'rounded-xl px-12 py-4 font-black uppercase tracking-widest text-md shadow-lg shadow-green-900/20',
                        cancelButton: 'rounded-xl px-12 py-4 font-bold uppercase tracking-widest text-md'
                    },
                    preConfirm: () => {
                        const items = {};
                        document.querySelectorAll('.item-precio').forEach(input => {
                            items[input.dataset.id] = { precio: input.value };
                        });
                        return {
                            resolucion: 'pago',
                            metodo_pago: document.getElementById('res-metodo').value,
                            requiere_factura: document.getElementById('res-factura').value,
                            items: items
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        procesarResolucion(ventaId, result.value);
                    }
                });
            });
        }

        // Función para recalcular el subtotal de cada fila y el total general en el modal
        function recalcularTotalModal() {
            let totalGeneral = 0;
            document.querySelectorAll('.item-precio').forEach(input => {
                const id = input.dataset.id;
                const cant = parseFloat(input.dataset.cantidad) || 0;
                const precio = parseFloat(input.value) || 0;
                const subtotal = cant * precio;
                
                totalGeneral += subtotal;
                
                const subtotalEl = document.getElementById(`subtotal-${id}`);
                if (subtotalEl) {
                    subtotalEl.innerText = '$' + subtotal.toLocaleString('es-MX', {minimumFractionDigits: 2});
                }
            });

            const totalEl = document.getElementById('modal-total-final');
            if (totalEl) {
                totalEl.innerText = '$' + totalGeneral.toLocaleString('es-MX', {minimumFractionDigits: 2});
            }
        }

        function procesarResolucion(ventaId, data) {
            Swal.fire({
                title: 'Procesando...',
                background: '#1e293b',
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `/ventas/${ventaId}/resolver`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...data
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡ÉXITO!',
                        text: response.message,
                        background: '#1e293b',
                        color: '#fff',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'ERROR',
                        text: error.message || 'Error al procesar la resolución',
                        background: '#1e293b',
                        color: '#fff'
                    });
                }
            });
        }
    </script>
@endpush
