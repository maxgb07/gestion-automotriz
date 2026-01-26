@extends('layouts.app')

@section('title', 'Historial de Ventas')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.5rem !important;
            height: 38px !important;
            padding-top: 4px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white !important;
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
        }
        .select2-dropdown {
            background-color: white !important;
            border-radius: 0.5rem !important;
            border: 1px solid rgba(0,0,0,0.1) !important;
            z-index: 9999 !important;
        }
        .select2-results__option {
            color: black !important;
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6 !important;
            color: white !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-radius: 4px !important;
            color: black !important;
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
            <div class="md:flex-[3] w-full">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Seleccionar Cliente</label>
                <select name="cliente_id" id="cliente_id_filter" class="select2-filter">
                    <option value="">TODOS LOS CLIENTES</option>
                    @foreach(\App\Models\Cliente::orderBy('nombre')->get() as $cliente)
                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:flex-1 w-full">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Método de Pago</label>
                <select name="metodo_pago" id="metodo_pago_filter" class="select2-filter">
                    <option value="">TODOS</option>
                    <option value="EFECTIVO" {{ request('metodo_pago') == 'EFECTIVO' ? 'selected' : '' }}>EFECTIVO</option>
                    <option value="TARJETA" {{ request('metodo_pago') == 'TARJETA' ? 'selected' : '' }}>TARJETA</option>
                    <option value="TRANSFERENCIA" {{ request('metodo_pago') == 'TRANSFERENCIA' ? 'selected' : '' }}>TRANSFERENCIA</option>
                    <option value="CREDITO" {{ request('metodo_pago') == 'CREDITO' ? 'selected' : '' }}>CRÉDITO</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-fit px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase">
                    BUSCAR
                </button>
                @if(request('buscar') || request('cliente_id') || request('metodo_pago'))
                    <a href="{{ route('ventas.index') }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center uppercase">
                        LIMPIAR
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Ventas Table -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10 font-bold uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Folio</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Fecha</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Cliente</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Total / Saldo</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Método de Pago</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Estado</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($ventas as $venta)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-bold text-sm uppercase">{{ $venta->folio }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-medium uppercase text-sm">{{ $venta->fecha->translatedFormat('d M, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-blue-100 font-bold uppercase text-sm group-hover:text-blue-300 transition-colors">{{ $venta->cliente->nombre }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <p class="text-white font-bold text-sm">${{ number_format($venta->total, 2) }}</p>
                                <p @class([
                                    'text-[11px] font-bold uppercase',
                                    'text-red-400' => $venta->saldo_pendiente > 0,
                                    'text-green-400' => $venta->saldo_pendiente == 0
                                ])>
                                    Saldo: ${{ number_format($venta->saldo_pendiente, 2) }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-medium uppercase text-xs">
                                    {{ $venta->metodo_pago }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center uppercase tracking-widest font-black">
                                @php
                                    $color = match($venta->estado) {
                                        'PAGADA' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                        'PENDIENTE' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                        'CANCELADA' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-sm border {{ $color }}">
                                    {{ $venta->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('ventas.show', $venta) }}" class="p-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-300 rounded-lg border border-blue-500/10 transition-all" title="VER DETALLE">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('ventas.pdf', $venta) }}" target="_blank" class="p-2 bg-green-500/10 hover:bg-green-500/20 text-green-300 rounded-lg border border-green-500/10 transition-all" title="IMPRIMIR COMPROBANTE">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>
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
            $('.select2-filter').select2({
                width: '100%',
                placeholder: 'SELECCIONAR...',
                allowClear: true
            });
        });
    </script>
@endpush
