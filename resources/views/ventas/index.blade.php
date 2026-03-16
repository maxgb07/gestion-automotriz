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
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10 font-bold uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Folio</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Fecha</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Cliente</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Total / Saldo</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Método de Pago</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Estado</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Factura</th>
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
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-sm border {{ $color }}">
                                    {{ $venta->estado == 'PENDIENTE' ? 'PENDIENTE DE PAGO' : $venta->estado }}
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
    </script>
@endpush
