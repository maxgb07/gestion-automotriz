@extends('layouts.app')

@section('title', 'Historial de Órdenes de Servicio')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white uppercase">Ordenes de Servicio</h1>
            <p class="text-blue-200">Seguimiento de reparaciones y mantenimiento</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('ordenes.create') }}" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Orden
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
        <form action="{{ route('ordenes.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="md:flex-[3] relative w-full">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Buscar Orden</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="FOLIO, CLIENTE, PLACA O VEHÍCULO..." class="block w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm uppercase">
                </div>
            </div>

            <div class="md:flex-1 w-full">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Estado de la Orden</label>
                <select name="estado" id="estado_filter" class="select2-filter">
                    <option value="">TODOS LOS ESTADOS</option>
                    <option value="RECEPCION" {{ request('estado') == 'RECEPCION' ? 'selected' : '' }}>RECEPCIÓN</option>
                    <option value="REPARACION" {{ request('estado') == 'REPARACION' ? 'selected' : '' }}>REPARACIÓN</option>
                    <option value="PENDIENTE DE PAGO" {{ request('estado') == 'PENDIENTE DE PAGO' ? 'selected' : '' }}>PENDIENTE DE PAGO</option>
                    <option value="ENTREGADO" {{ request('estado') == 'ENTREGADO' ? 'selected' : '' }}>ENTREGADO</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-fit px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase">
                    BUSCAR
                </button>
                @if(request('buscar') || request('estado'))
                    <a href="{{ route('ordenes.index') }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center uppercase">
                        LIMPIAR
                    </a>
                @endif
            </div>
        </form>
    <!-- Tabs de Filtrado -->
    <div class="flex flex-wrap items-center gap-2 mb-4 mt-8">
        @php
            $currentPeriod = request('periodo');
            // Si no hay periodo específico ni otros filtros, el activo es 'hoy'
            if (!request()->filled('periodo') && !request()->filled('buscar') && !request()->filled('estado') && !request()->filled('cliente_id') && !request()->filled('vehiculo_id')) {
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

    <!-- Ordenes Table -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10 font-bold uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Folio</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Entrada</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Cliente</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Vehículo</th>
                        <!-- <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Kilometraje</th> -->
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Total / Saldo</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Método de Pago</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Estado</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($ordenes as $orden)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-bold text-sm uppercase">{{ $orden->folio }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-medium uppercase text-sm">{{ $orden->fecha_entrada->translatedFormat('d M, Y') }}</span>
                                <!-- <p class="text-sm text-blue-200/40 font-bold">{{ $orden->fecha_entrada->format('h:i A') }}</p> -->
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-blue-100 font-bold uppercase text-sm block group-hover:text-blue-300 transition-colors">{{ $orden->cliente->nombre }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-white font-bold uppercase text-sm block group-hover:text-blue-300 transition-colors">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ({{ $orden->vehiculo->placas }})</span>
                            </td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-bold text-sm">{{ number_format($orden->kilometraje_entrada) }} KM</span>
                            </td> -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <p class="text-white font-bold text-sm">${{ number_format($orden->total, 2) }}</p>
                                @if($orden->saldo_pendiente > 0)
                                    <p class="text-red-400 text-sm font-black uppercase">Saldo: ${{ number_format($orden->saldo_pendiente, 2) }}</p>
                                @else
                                    <!-- <p class="text-green-400 text-sm font-black uppercase tracking-tighter">Liquidada</p> -->
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-medium uppercase text-xs">
                                    {{ $orden->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'PAGO PENDIENTE' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center uppercase tracking-widest font-black">
                                @php
                                    $color = match($orden->estado) {
                                        'RECEPCION' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                        'REPARACION' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                        'PENDIENTE DE PAGO' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                        'ENTREGADO' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-sm border {{ $color }}">
                                    {{ $orden->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('ordenes.show', $orden) }}" class="p-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-300 rounded-lg border border-blue-500/10 transition-all" title="VER DETALLE / REPARACIÓN">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('ordenes.pdf', $orden) }}" target="_blank" class="p-2 bg-green-500/10 hover:bg-green-500/20 text-green-300 rounded-lg border border-green-500/10 transition-all" title="IMPRIMIR ORDEN">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4 text-white/5">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xl font-medium text-blue-200 uppercase tracking-tighter">Sin órdenes registradas</p>
                                    <p class="text-sm text-blue-200/50 mt-2 uppercase tracking-widest font-black">Registra la entrada de un vehículo para comenzar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($ordenes->hasPages())
            <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                {{ $ordenes->links('vendor.pagination.custom') }}
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
    </style>
@endpush

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
