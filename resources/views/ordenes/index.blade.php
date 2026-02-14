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
                    <option value="ENTREGADO" {{ request('estado') == 'ENTREGADO' ? 'selected' : '' }}>ENTREGADO</option>
                    <option value="FINALIZADO" {{ request('estado') == 'FINALIZADO' ? 'selected' : '' }}>FINALIZADO</option>
                    <option value="PENDIENTE DE PAGO" {{ request('estado') == 'PENDIENTE DE PAGO' ? 'selected' : '' }}>PENDIENTE DE PAGO</option>
                    <option value="RECEPCION" {{ request('estado') == 'RECEPCION' ? 'selected' : '' }}>RECEPCIÓN</option>
                    <option value="REPARACION" {{ request('estado') == 'REPARACION' ? 'selected' : '' }}>REPARACIÓN</option>
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
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Folio</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Entrada</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Cliente</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Vehículo</th>
                        <!-- <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Kilometraje</th> -->
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Total / Saldo</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Factura</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Estado</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($ordenes as $orden)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-bold text-md uppercase">{{ $orden->folio }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-medium uppercase text-md">{{ $orden->fecha_entrada->translatedFormat('d M, Y') }}</span>
                                <!-- <p class="text-sm text-blue-200/40 font-bold">{{ $orden->fecha_entrada->format('h:i A') }}</p> -->
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-blue-100 font-bold uppercase text-md block group-hover:text-blue-300 transition-colors">{{ $orden->cliente->nombre }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-white font-bold uppercase text-md block group-hover:text-blue-300 transition-colors">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ({{ $orden->vehiculo->placas }})</span>
                            </td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-bold text-sm">{{ number_format($orden->kilometraje_entrada) }} KM</span>
                            </td> -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <p class="text-white font-bold text-md">${{ number_format($orden->total, 2) }}</p>
                                @if($orden->saldo_pendiente > 0)
                                    <p class="text-red-400 text-md font-black uppercase">Saldo: ${{ number_format($orden->saldo_pendiente, 2) }}</p>
                                @endif
                                @php
                                    $metodos = $orden->pagos->pluck('metodo_pago')->unique()->filter()->implode(', ');
                                @endphp
                                <p class="{{ $metodos ? 'text-blue-300' : 'text-blue-200/30' }} text-xs font-bold uppercase mt-1">
                                    {{ $metodos ?: 'N/D' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 rounded text-md font-black {{ $orden->requiere_factura === 'SI' ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : 'bg-slate-500/20 text-slate-400 border border-slate-500/30' }}">
                                    {{ $orden->requiere_factura ?? 'NO' }}
                                </span>
                                @if($orden->folio_factura)
                                    <p class="text-md text-teal-400 font-bold mt-1 uppercase">Folio: {{ $orden->folio_factura }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center uppercase tracking-widest font-black">
                                @php
                                    $color = match($orden->estado) {
                                        'RECEPCION' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                        'REPARACION' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                        'FINALIZADO' => 'bg-teal-500/20 text-teal-400 border-teal-400/50',
                                        'PENDIENTE DE PAGO' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                        'ENTREGADO' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-md border {{ $color }}">
                                    {{ $orden->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('ordenes.show', $orden) }}" class="p-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-300 rounded-lg border border-blue-500/10 transition-all cursor-pointer" title="VER DETALLE / REPARACIÓN">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    @if($orden->estado !== 'RECEPCION')
                                        @php
                                            $esReparacion = $orden->estado === 'REPARACION';
                                            $esFinalizado = $orden->estado === 'FINALIZADO';
                                        @endphp
                                        <a href="{{ $esReparacion ? route('ordenes.cotizacion.pdf', $orden) : route('ordenes.pdf', $orden) }}" 
                                           target="_blank" 
                                           class="p-2 {{ $esReparacion ? 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border-amber-500/10' : 'bg-green-500/10 hover:bg-green-500/20 text-green-300 border-green-500/10' }} rounded-lg border transition-all cursor-pointer" 
                                           title="{{ $esReparacion ? 'IMPRIMIR COTIZACIÓN' : 'IMPRIMIR ORDEN' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                        </a>
                                    @endif

                                    @if($orden->estado === 'FINALIZADO')
                                        <button onclick="abrirModalPago({{ $orden->id }}, {{ $orden->total }}, {{ $orden->saldo_pendiente }})" 
                                                class="p-2 bg-green-500/10 hover:bg-green-500/20 text-green-300 rounded-lg border border-green-500/10 transition-all cursor-pointer" 
                                                title="REGISTRAR PAGO">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @endif

                                    @if($orden->requiere_factura === 'SI' || ($orden->requiere_factura === 'NO' && $orden->estado === 'ENTREGADO') || ($orden->requiere_factura === 'NO' && $orden->estado === 'PENDIENTE DE PAGO'))
                                        <button onclick="abrirModalFactura({{ $orden->id }}, '{{ $orden->folio_factura }}')" 
                                                class="p-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 rounded-lg border border-amber-500/10 transition-all cursor-pointer" 
                                                title="REGISTRAR FACTURA">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </button>
                                    @endif

                                    <button onclick="abrirModalDatosVehiculo({{ $orden->id }}, '{{ $orden->placas ?: $orden->vehiculo->placas }}', {{ $orden->kilometraje_entrega ?: 0 }}, '{{ $orden->numero_serie ?: $orden->vehiculo->numero_serie }}', '{{ $orden->mecanico }}')" 
                                            class="p-2 bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 rounded-lg border border-purple-500/10 transition-all cursor-pointer" 
                                            title="DATOS VEHÍCULO (PLACAS/KM/VIN/MECÁNICO)">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                                        </svg>
                                    </button>
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

        function abrirModalDatosVehiculo(ordenId, placasActuales, kmActual, vinActual, mecanicoActual) {
            Swal.fire({
                title: 'DATOS DEL VEHÍCULO',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="p-4 space-y-4 text-left">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">PLACAS</label>
                                <input type="text" id="modal_placas" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase" value="${placasActuales}" placeholder="P. EJ. ABC-1234">
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">KM ENTREGA</label>
                                <input type="number" id="modal_km_entrega" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" value="${kmActual}" min="0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">VIN (NÚMERO DE SERIE)</label>
                            <input type="text" id="modal_vin" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase" value="${vinActual}" placeholder="VIN DEL VEHÍCULO">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">MECÁNICO ASIGNADO</label>
                            <select id="modal_mecanico" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase">
                                <option value="" class="text-black">-- SELECCIONAR --</option>
                                <option value="ALEJANDRO" class="text-black" ${mecanicoActual && mecanicoActual.trim().toUpperCase() === 'ALEJANDRO' ? 'selected' : ''}>ALEJANDRO</option>
                                <option value="DANIEL" class="text-black" ${mecanicoActual && mecanicoActual.trim().toUpperCase() === 'DANIEL' ? 'selected' : ''}>DANIEL</option>
                                <option value="ELEAZAR" class="text-black" ${mecanicoActual && mecanicoActual.trim().toUpperCase() === 'ELEAZAR' ? 'selected' : ''}>ELEAZAR</option>
                                <option value="RAFAEL" class="text-black" ${mecanicoActual && mecanicoActual.trim().toUpperCase() === 'RAFAEL' ? 'selected' : ''}>RAFAEL</option>
                            </select>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'GUARDAR CAMBIOS',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#ef4444',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm'
                },
                preConfirm: () => {
                    const placas = document.getElementById('modal_placas').value;
                    const km = document.getElementById('modal_km_entrega').value;
                    const vin = document.getElementById('modal_vin').value;
                    const mecanico = document.getElementById('modal_mecanico').value;
                    
                    if (!placas && !km && !vin && !mecanico) {
                        Swal.showValidationMessage('Al menos uno de los campos debe tener datos');
                        return false;
                    }

                    return { placas: placas, kilometraje_entrega: km, numero_serie: vin, mecanico: mecanico };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Guardando...',
                        background: '#1e293b',
                        color: '#fff',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch(`/ordenes/${ordenId}/datos-vehiculo`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(result.value)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡ÉXITO!',
                                text: data.message,
                                background: '#1e293b',
                                color: '#fff',
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'rounded-3xl border border-white/10 shadow-2xl'
                                }
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ERROR',
                                text: data.message,
                                background: '#1e293b',
                                color: '#fff',
                                customClass: {
                                    popup: 'rounded-3xl border border-white/10 shadow-2xl'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'ERROR',
                            text: 'Ocurrió un error inesperado al procesar la solicitud.',
                            background: '#1e293b',
                            color: '#fff',
                            customClass: {
                                popup: 'rounded-3xl border border-white/10 shadow-2xl'
                            }
                        });
                    });
                }
            });
        }

        function abrirModalPago(ordenId, total, saldo) {
            Swal.fire({
                title: 'REGISTRAR PAGO',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="p-4 space-y-4 text-left">
                        <div class="flex justify-between items-center bg-white/5 p-4 rounded-xl border border-white/5 mb-4">
                            <span class="text-md font-black text-slate-500 uppercase tracking-widest">TOTAL A PAGAR:</span>
                            <span class="text-xl font-black text-green-400 font-mono italic">$ ${new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(saldo)}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">MÉTODO DE PAGO *</label>
                            <select id="modal_metodo_pago" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase" onchange="toggleMontoPago(this.value, ${saldo})">
                                <option value="" class="text-black">-- SELECCIONA UNA OPCIÓN --</option>
                                <option value="EFECTIVO" class="text-black">EFECTIVO</option>
                                <option value="TRANSFERENCIA" class="text-black">TRANSFERENCIA</option>
                                <option value="TARJETA DE DÉBITO" class="text-black">TARJETA DE DÉBITO</option>
                                <option value="TARJETA DE CRÉDITO" class="text-black">TARJETA DE CRÉDITO</option>
                                <option value="CRÉDITO 15 DÍAS" class="text-black">CRÉDITO 15 DÍAS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">MONTO A PAGAR *</label>
                            <input type="number" id="modal_monto" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" value="${parseFloat(saldo).toFixed(2)}" step="0.01">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">¿REQUIERE FACTURA?</label>
                            <select id="modal_requiere_factura" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase">
                                <option value="NO" class="text-black">NO</option>
                                <option value="SI" class="text-black">SI</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-2 ml-1 text-center">REFERENCIA / NOTAS</label>
                            <input type="text" id="modal_referencia" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="EJ: ÚLTIMOS 4 DÍGITOS, FOLIO, ETC.">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'REGISTRAR PAGO',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#ef4444',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm'
                },
                preConfirm: () => {
                    const metodo = document.getElementById('modal_metodo_pago').value;
                    const monto = document.getElementById('modal_monto').value;
                    const factura = document.getElementById('modal_requiere_factura').value;
                    const referencia = document.getElementById('modal_referencia').value;

                    if (!metodo) {
                        Swal.showValidationMessage('Debe seleccionar un método de pago');
                        return false;
                    }

                    if (metodo !== 'CRÉDITO 15 DÍAS' && (!monto || monto <= 0)) {
                        Swal.showValidationMessage('El monto debe ser mayor a 0');
                        return false;
                    }

                    return { 
                        metodo_pago: metodo, 
                        monto: monto, 
                        requiere_factura: factura,
                        referencia: referencia,
                        fecha_pago: new Date().toISOString().split('T')[0]
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando pago...',
                        background: '#1e293b',
                        color: '#fff',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: `/ordenes/${ordenId}/pagos`,
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ _token: '{{ csrf_token() }}', ...result.value }),
                        success: (response) => {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡PAGO REGISTRADO!',
                                    text: response.message,
                                    background: '#1e293b',
                                    color: '#fff',
                                    showConfirmButton: true,
                                    confirmButtonText: 'VER PDF'
                                }).then((result) => {
                                    if (result.isConfirmed && response.pdf_url) {
                                        window.open(response.pdf_url, '_blank');
                                    }
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'ERROR',
                                    text: response.message,
                                    background: '#1e293b',
                                    color: '#fff'
                                });
                            }
                        },
                        error: (error) => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'ERROR',
                                text: 'Error al procesar el pago.',
                                background: '#1e293b',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        }

        function abrirModalFactura(ordenId, folioActual) {
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
                            <p class="text-xs text-amber-200/80 font-bold uppercase tracking-wider">Captura el folio de la factura emitida para esta orden.</p>
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
                    popup: 'rounded-3xl border border-white/10 shadow-2xl',
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
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: `/ordenes/${ordenId}/facturar`,
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ _token: '{{ csrf_token() }}', ...result.value }),
                        success: (response) => {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡LISTO!',
                                    text: response.message,
                                    background: '#1e293b',
                                    color: '#fff'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'ERROR',
                                    text: response.message,
                                    background: '#1e293b',
                                    color: '#fff'
                                });
                            }
                        },
                        error: (error) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'ERROR',
                                text: 'Error al registrar la factura',
                                background: '#1e293b',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        }

        function toggleMontoPago(metodo, saldo) {
            const inputMonto = document.getElementById('modal_monto');
            if (metodo === 'CRÉDITO 15 DÍAS') {
                inputMonto.value = 0;
                inputMonto.readOnly = true;
                inputMonto.classList.add('bg-white/5', 'text-slate-500');
            } else {
                inputMonto.value = saldo;
                inputMonto.readOnly = false;
                inputMonto.classList.remove('bg-white/5', 'text-slate-500');
            }
        }

        const popupClass = 'rounded-3xl border-none shadow-2xl';
    </script>
@endpush
