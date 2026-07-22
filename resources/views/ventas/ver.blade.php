@extends('layouts.app')

@section('title', 'Detalle de Venta ' . $venta->folio)

@push('styles')
    <style>
        .btn-premium-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            border: none !important;
            display: inline-flex !important;
            cursor: pointer !important;
        }
        .btn-premium-blue:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.5) !important;
        }
        .btn-premium-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border: none !important;
            display: inline-flex !important;
            cursor: pointer !important;
        }
        .btn-premium-success:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.5) !important;
        }
        /* Estilos Select2 idénticos a Ordenes */
        .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 0.75rem !important;
            height: 42px !important;
            padding: 8px 12px !important;
            color: white !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white !important;
            text-transform: uppercase;
        }
        .select2-dropdown {
            background-color: #ffffff !important;
            color: #000000 !important;
            border-radius: 0.75rem !important;
        }
        .select2-results__option {
            text-transform: uppercase;
            color: black !important;
        }
        select option {
            background-color: white !important;
            color: black !important;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')

    <div class="mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('ventas.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors uppercase text-xs font-bold tracking-widest">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Historial
                </a>
            </div>
            <div class="flex items-center gap-4">
                <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Folio: {{ $venta->folio }}</h1>
                @php
                    $color = match($venta->estado) {
                        'PAGADA' => 'bg-green-500/20 text-green-300 border-green-500/30',
                        'PENDIENTE' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                        'CANCELADA' => 'bg-red-500/20 text-red-300 border-red-500/30',
                        'PRESTAMO' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                        'DEVUELTO' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
                    };
                @endphp
                <span class="px-4 py-1.5 rounded-full text-xl font-black uppercase border {{ $color }}">
                    {{ $venta->estado == 'PENDIENTE' ? 'PENDIENTE DE PAGO' : ($venta->estado == 'PRESTAMO' ? 'EN PRÉSTAMO' : $venta->estado) }}
                </span>
            </div>
        </div>

        @if($venta->estado === 'CANCELADA')
            <div class="mb-8 bg-red-500/10 border border-red-500/20 rounded-3xl p-6 backdrop-blur-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-20 h-20 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
                <div class="flex items-start gap-4 h-full relative z-10">
                    <div class="p-3 bg-red-500/20 rounded-2xl text-red-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h3 class="text-red-300 font-black uppercase tracking-widest text-sm mb-1">VENTA CANCELADA</h3>
                        <p class="text-white font-bold text-lg uppercase leading-tight">{{ $venta->motivo_cancelacion }}</p>
                        <p class="text-red-300/60 text-[10px] font-black uppercase tracking-[0.2em] mt-3 italic">
                            Cancelado el {{ $venta->cancelado_at ? $venta->cancelado_at->format('d/m/Y H:i') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-8">
            <!-- Fila 1: Datos Generales -->
            <div class="w-full mb-8">
                <!-- Card: Información del Cliente y Venta -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                         <svg class="w-24 h-24 text-white/5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-left relative z-10">
                        <div class="lg:col-span-1">
                            <p class="text-white font-black text-2xl uppercase leading-tight">{{ $venta->cliente->nombre }}</p>
                            <div class="space-y-1.5 mt-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-200/40 text-md font-black uppercase tracking-widest min-w-[70px]">Teléfono:</span>
                                    <span class="text-blue-100/70 text-md uppercase font-bold">{{ $venta->cliente->telefono ?? 'S/T' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-200/40 text-md font-black uppercase tracking-widest min-w-[70px]">Celular:</span>
                                    <span class="text-blue-100/70 text-md uppercase font-bold">{{ $venta->cliente->celular ?? 'S/C' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-200/40 text-md font-black uppercase tracking-widest min-w-[70px]">Mail:</span>
                                    <span class="text-blue-100/70 text-md lowercase font-bold">{{ $venta->cliente->email ?? 'S/E' }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-blue-200/40 text-md font-black uppercase tracking-[0.2em] mb-2">RFC</p>
                            <p class="text-white font-bold text-lg uppercase">{{ $venta->cliente->rfc ?? 'XAXX010101000' }}</p>
                        </div>
                        <div class="lg:col-span-2">
                            <p class="text-blue-200/40 text-md font-black uppercase tracking-[0.2em] mb-2">Ubicación</p>
                            <p class="text-white font-bold text-sm uppercase leading-relaxed">
                                {{ $venta->cliente->direccion ?? 'DIRECCIÓN NO REGISTRADA' }}{{ $venta->cliente->codigo_postal ? ', CP ' . $venta->cliente->codigo_postal : '' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($venta->observaciones)
            <!-- Observaciones -->
            <div class="w-full mb-8">
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-6 border border-blue-400/20 shadow-2xl flex items-start gap-4">
                    <div class="p-3 bg-blue-500/10 rounded-2xl text-blue-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-blue-300 text-xs font-black uppercase tracking-widest mb-1">Observaciones</p>
                        <p class="text-white font-bold uppercase text-md leading-relaxed">{{ $venta->observaciones }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Fila 2: Resumen de Cuenta -->
            <div class="w-full mb-8">
                <!-- Card: Detalle de Items -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
                    <div class="p-6 border-b border-white/10 bg-white/5 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white uppercase tracking-tight">Resumen de Cuenta</h2>
                        <div class="flex items-center gap-3">
                            @if($venta->estado === 'PRESTAMO' || $venta->estado === 'PENDIENTE')
                                <button type="button" onclick="abrirModalNuevoItem()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-black rounded-lg transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-purple-900/40">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Nuevo Item
                                </button>
                                <button type="button" onclick="addRow()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-lg transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Agregar Fila
                                </button>
                                <button type="button" onclick="guardarItems()" class="btn-premium-success px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg shadow-green-500/20 transition-all uppercase tracking-widest flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Guardar
                                </button>
                            @endif
                            
                            @php
                                $isTicket = !in_array($venta->metodo_pago, ['CREDITO', 'PRESTAMO']); 
                                $urlImpresion = $isTicket ? route('ventas.ticket', $venta) : route('ventas.pdf', $venta);
                            @endphp
                            <a href="{{ $urlImpresion }}" target="_blank" class="btn-premium-blue px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg shadow-blue-500/20 transition-all uppercase tracking-widest flex items-center justify-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Imprimir Comprobante
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-white/5 border-b border-white/10">
                                <tr>
                                    <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-center">Cant.</th>
                                    <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Clave</th>
                                    <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Descripción</th>
                                    <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-center">Precio Unitario</th>
                                    <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-right">Importe</th>
                                    @if($venta->estado === 'PRESTAMO' || $venta->estado === 'PENDIENTE')
                                        <th class="px-4 py-5 w-16"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10" id="items-table-body">
                                @foreach($venta->detalles as $detalle)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-5 text-center">
                                            <span class="text-white font-mono font-bold bg-white/5 px-3 py-1 rounded-lg border border-white/10">{{ $detalle->cantidad }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-white font-bold uppercase text-md font-mono">
                                                {{ $detalle->producto?->nombre ?? $detalle->servicio?->sku ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-white font-bold uppercase text-md block">
                                                {{ $detalle->producto?->nombre ?? $detalle->servicio?->nombre ?? 'N/A' }}
                                            </span>
                                            <span class="text-md text-blue-200/40 uppercase tracking-widest mt-1 block line-clamp-1">
                                                {{ $detalle->producto?->descripcion ?? $detalle->servicio?->descripcion ?? '---' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="text-blue-100 font-mono text-md font-bold">${{ number_format($detalle->precio_unitario, 2) }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-white font-black font-mono text-md existing-subtotal" data-valor="{{ $detalle->subtotal }}">${{ number_format($detalle->subtotal, 2) }}</span>
                                        </td>
                                        @if($venta->estado === 'PRESTAMO' || $venta->estado === 'PENDIENTE')
                                            <td class="px-4 py-5 text-center flex items-center justify-center gap-1">
                                                <button type="button" class="p-2 text-white/20 hover:text-blue-400 transition-colors"
                                                        onclick="abrirModalEditarItem({{ $detalle->id }}, '{{ $detalle->producto_id ? 'producto' : 'servicio' }}', {{ $detalle->producto_id ?? $detalle->servicio_id }}, {{ $detalle->cantidad }}, {{ $detalle->precio_unitario }})">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="p-2 text-white/20 hover:text-red-400 transition-colors"
                                                        onclick="eliminarDetalle({{ $detalle->id }})">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-white/5 border-t border-white/10">
                                <tr class="bg-white/10">
                                    <td colspan="4" class="px-8 py-6 text-right">
                                        <span class="text-blue-200 text-md uppercase font-black tracking-widest">Total de la Venta</span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <span class="text-white font-black text-md tracking-tighter" id="total-main">${{ number_format($venta->total, 2) }}</span>
                                    </td>
                                    @if($venta->estado === 'PRESTAMO' || $venta->estado === 'PENDIENTE')
                                        <td></td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Seguimiento de Pagos -->
            <div class="w-full mb-8">
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                    <div class="p-6 border-b border-white/10 bg-white/5 flex items-center justify-between">
                        <h3 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Gestión de Abonos</h3>
                        @if($venta->saldo_pendiente > 0 && $venta->estado !== 'CANCELADA')
                            <button onclick="abrirModalPago()" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-md font-black rounded-lg transition-all uppercase tracking-widest flex items-center shadow-lg shadow-green-900/40">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Registrar Pago
                            </button>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="mb-6 @if($venta->saldo_pendiente > 0) bg-red-500/5 border-red-500/20 @else bg-green-500/5 border-green-500/20 @endif border rounded-2xl p-4 text-center">
                            <p class="text-md text-white/40 uppercase font-black tracking-[0.3em] mb-1">Saldo Pendiente</p>
                            <p @class([
                                'text-2xl font-black font-mono tracking-tighter',
                                'text-red-400' => $venta->saldo_pendiente > 0,
                                'text-green-400' => $venta->saldo_pendiente == 0
                            ])>${{ number_format($venta->saldo_pendiente, 2) }}</p>
                        </div>

                        @if($venta->pagos->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr class="border-b border-white/10 uppercase tracking-widest text-[10px] font-black text-blue-200/40">
                                            <th class="px-2 py-3 text-center">Fecha</th>
                                            <th class="px-2 py-3 text-center">Método</th>
                                            <th class="px-2 py-3 text-center">Referencia</th>
                                            <th class="px-2 py-3 text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach($venta->pagos as $pago)
                                            <tr class="hover:bg-white/5 transition-colors group">
                                                <td class="px-2 py-3 text-md text-white/70 font-bold uppercase text-center">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                                <td class="px-2 py-3 text-md text-blue-300/50 font-black uppercase text-center">
                                                    <span class="px-2 py-0.5 rounded bg-blue-500/10 border border-blue-500/20">
                                                        {{ $pago->metodo_pago }}
                                                    </span>
                                                </td>
                                                <td class="px-2 py-3 text-md text-white/30 font-mono uppercase italic text-center">{{ $pago->referencia ?: '-' }}</td>
                                                <td class="px-2 py-3 text-right text-md font-black text-white font-mono tracking-tighter">${{ number_format($pago->monto, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-md text-white/20 text-center uppercase font-black tracking-widest py-4 italic">No hay abonos registrados para esta venta</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function abrirModalPago() {
            const ventaId = {{ $venta->id }};
            const folio = "{{ $venta->folio }}";
            const saldoPendiente = {{ $venta->saldo_pendiente }};
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

        // --- Lógica para Agregar Ítems ---
        let rowIndex = 0;
        const PRODUCTOS = @json($productos);
        const SERVICIOS = @json($servicios);

        function addRow() {
            const tbody = document.getElementById('items-table-body');
            const template = document.getElementById('row-template');
            const clone = template.content.cloneNode(true);
            
            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', rowIndex);
            });

            const newRow = clone.querySelector('tr');
            tbody.appendChild(newRow);
            
            const typeSelect = newRow.querySelector('.tipo-select');
            $(typeSelect).select2({ width: '100%' });
            changeType(typeSelect);
            
            rowIndex++;
        }

        function changeType(select) {
            const row = select.closest('tr');
            const itemSelect = row.querySelector('.item-select');
            const type = select.value;
            const data = type === 'producto' ? PRODUCTOS : SERVICIOS;

            if ($(itemSelect).data('select2')) {
                $(itemSelect).select2('destroy');
            }

            itemSelect.innerHTML = '<option value="" disabled selected>SELECCIONAR...</option>';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = (item.nombre || item.sku) + ' - ' + (item.descripcion || '');
                option.dataset.precio = item.precio_venta || item.precio || 0;
                option.dataset.descripcion = item.descripcion || item.nombre;
                itemSelect.appendChild(option);
            });

            $(itemSelect).select2({ width: '100%' });
        }

        function updateItemData(select) {
            const row = select.closest('tr');
            const option = select.options[select.selectedIndex];
            const precioInput = row.querySelector('[name*="[precio_unitario]"]');
            
            if (option.dataset.precio) {
                precioInput.value = option.dataset.precio;
            }
            calculateRow(row.querySelector('[name*="[cantidad]"]'));
        }

        function calculateRow(input) {
            const row = input.closest('tr');
            const cant = parseFloat(row.querySelector('[name*="[cantidad]"]').value) || 0;
            const price = parseFloat(row.querySelector('[name*="[precio_unitario]"]').value) || 0;
            const subtotalInput = row.querySelector('.subtotal-input');
            
            const total = cant * price;
            subtotalInput.value = total.toFixed(2);
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.existing-subtotal').forEach(el => {
                total += parseFloat(el.dataset.valor) || 0;
            });
            document.querySelectorAll('#items-table-body .subtotal-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            document.getElementById('total-main').textContent = '$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2});
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            calculateTotal();
        }

        function guardarItems() {
            const rows = document.querySelectorAll('#items-table-body tr.new-row');
            if (rows.length === 0) return;

            Swal.fire({
                title: '¿Guardar nuevos ítems?',
                text: "Esta acción actualizará el total de la venta.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'SÍ, GUARDAR'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    rows.forEach((row, index) => {
                        formData.append(`items[${index}][tipo]`, row.querySelector('.tipo-select').value);
                        formData.append(`items[${index}][item_id]`, row.querySelector('.item-select').value);
                        formData.append(`items[${index}][cantidad]`, row.querySelector('[name*="[cantidad]"]').value);
                        formData.append(`items[${index}][precio_unitario]`, row.querySelector('[name*="[precio_unitario]"]').value);
                    });

                    fetch('{{ route("ventas.items.store", $venta) }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                }
            });
        }

        function abrirModalEditarItem(id, tipo, itemId, cantidad, precio) {
            Swal.fire({
                title: 'EDITAR ÍTEM',
                background: '#1e293b',
                color: '#fff',
                width: '600px',
                html: `
                    <div class="space-y-4 text-left p-2">
                        <div class="flex gap-8 justify-center mb-6 p-4 bg-white/5 rounded-2xl border border-white/10">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="edit-tipo" value="producto" ${tipo === 'producto' ? 'checked' : ''} onchange="updateEditItemSelect(this.value)" class="w-5 h-5 text-blue-500 bg-white/10 border-white/20 focus:ring-blue-500">
                                <span class="text-md font-black uppercase tracking-widest text-blue-100">Producto</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="edit-tipo" value="servicio" ${tipo === 'servicio' ? 'checked' : ''} onchange="updateEditItemSelect(this.value)" class="w-5 h-5 text-blue-500 bg-white/10 border-white/20 focus:ring-blue-500">
                                <span class="text-md font-black uppercase tracking-widest text-blue-100">Servicio</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">SELECCIONAR ÍTEM *</label>
                            <div class="select2-container-swal">
                                <select id="edit-item-id" class="w-full"></select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">CANTIDAD *</label>
                                <input type="number" id="edit-cantidad" step="any" value="${cantidad}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">PRECIO UNITARIO *</label>
                                <input type="number" id="edit-precio" step="0.01" value="${precio}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'ACTUALIZAR ÍTEM',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#475569',
                customClass: {
                    popup: 'rounded-3xl border border-white/20 shadow-2xl',
                    title: 'text-xl font-black uppercase tracking-tighter'
                },
                didOpen: () => {
                    updateEditItemSelect(tipo, itemId);
                },
                preConfirm: () => {
                    const nuevoTipo    = $('input[name="edit-tipo"]:checked').val();
                    const nuevoItemId  = $('#edit-item-id').val();
                    const nuevaCant    = $('#edit-cantidad').val();
                    const nuevoPrecio  = $('#edit-precio').val();

                    if (!nuevoItemId || !nuevaCant || !nuevoPrecio) {
                        Swal.showValidationMessage('Todos los campos marcados con * son obligatorios');
                        return false;
                    }
                    return { tipo: nuevoTipo, item_id: nuevoItemId, cantidad: nuevaCant, precio_unitario: nuevoPrecio };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Actualizando...', background: '#1e293b', color: '#fff', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    $.ajax({
                        url: `/ventas/{{ $venta->id }}/detalles/${id}`,
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'PUT', ...result.value },
                        success: (res) => {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success', title: '¡ACTUALIZADO!', text: res.message,
                                    background: '#1e293b', color: '#fff', timer: 1500, showConfirmButton: false
                                }).then(() => location.reload());
                            }
                        },
                        error: (xhr) => {
                            Swal.fire({ icon: 'error', title: 'ERROR', text: xhr.responseJSON?.message || 'Error al actualizar', background: '#1e293b', color: '#fff' });
                        }
                    });
                }
            });
        }

        function updateEditItemSelect(tipo, selectedId = null) {
            const select = $('#edit-item-id');
            const data = (String(tipo).toLowerCase() === 'producto') ? PRODUCTOS : SERVICIOS;

            if (select.data('select2')) select.select2('destroy');

            select.empty().append('<option value="" disabled>SELECCIONAR...</option>');
            data.forEach(item => {
                const opt = new Option(`${item.nombre} - ${item.descripcion || ''}`, item.id, false, (selectedId && item.id == selectedId));
                select.append(opt);
            });

            select.select2({
                dropdownParent: Swal.getPopup(),
                width: '100%',
                placeholder: 'BUSCAR ÍTEM...',
                language: { noResults: () => 'NO SE ENCONTRARON RESULTADOS' }
            });

            const s2c = select.next('.select2-container');
            s2c.find('.select2-selection--single').css({ 'background-color': 'rgba(255,255,255,0.05)', 'border': '1px solid rgba(255,255,255,0.1)', 'height': '48px', 'border-radius': '0.75rem', 'display': 'flex', 'align-items': 'center', 'color': 'white' });
            s2c.find('.select2-selection__rendered').css('color', 'white');
            s2c.find('.select2-selection__arrow').css('top', '10px');
        }

        function eliminarDetalle(id) {
            Swal.fire({
                title: '¿Eliminar este ítem?',
                text: 'Esta acción recalculará el total de la venta.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#475569',
                confirmButtonText: 'SÍ, ELIMINAR',
                cancelButtonText: 'CANCELAR',
                background: '#1e293b',
                color: '#fff',
                customClass: { popup: 'rounded-3xl border border-white/20 shadow-2xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Eliminando...', background: '#1e293b', color: '#fff', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    $.ajax({
                        url: `/ventas/{{ $venta->id }}/detalles/${id}`,
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                        success: (res) => {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success', title: '¡ELIMINADO!', text: res.message,
                                    background: '#1e293b', color: '#fff', timer: 1500, showConfirmButton: false
                                }).then(() => location.reload());
                            }
                        },
                        error: (xhr) => {
                            Swal.fire({ icon: 'error', title: 'ERROR', text: xhr.responseJSON?.message || 'Error al eliminar', background: '#1e293b', color: '#fff' });
                        }
                    });
                }
            });
        }

        function abrirModalNuevoItem() {
            Swal.fire({
                title: 'REGISTRAR NUEVO ÍTEM',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="mb-6">
                        <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 text-center">TIPO DE ÍTEM *</label>
                        <select id="swal-tipo" onchange="toggleSwalFields(this.value)" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="" disabled selected class="text-black">SELECCIONA TIPO...</option>
                            <option value="producto" class="text-black">PRODUCTO</option>
                            <option value="servicio" class="text-black">SERVICIO</option>
                        </select>
                    </div>
                    <div class="space-y-4 text-left">
                        <div>
                            <label id="label-nombre" class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">SKU / CLAVE *</label>
                            <input type="text" id="swal-nombre" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="EJ: BALATA-TR-01">
                        </div>
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">DESCRIPCIÓN</label>
                            <textarea id="swal-descripcion" rows="2" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="DESCRIPCIÓN DEL PRODUCTO O SERVICIO"></textarea>
                        </div>
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">PRECIO VENTA *</label>
                            <input type="number" id="swal-precio" step="0.01" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                        </div>
                        <div id="div-marca" class="hidden">
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">MARCA</label>
                            <input type="text" id="swal-marca" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="MARCA DEL PRODUCTO">
                        </div>
                        <div id="div-stock" class="hidden">
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">EXISTENCIA INICIAL *</label>
                            <input type="number" id="swal-stock" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" value="1">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'REGISTRAR',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#475569',
                customClass: {
                    popup: 'rounded-3xl border border-white/20 shadow-2xl',
                    title: 'text-xl font-black uppercase tracking-tighter'
                },
                preConfirm: () => {
                    const selectTipo = document.getElementById('swal-tipo');
                    const tipo = selectTipo ? selectTipo.value : '';

                    if (!tipo) {
                        Swal.showValidationMessage('DEBES SELECCIONAR EL TIPO DE ÍTEM (PRODUCTO O SERVICIO)');
                        return false;
                    }

                    const nombre = document.getElementById('swal-nombre').value;
                    const precio = document.getElementById('swal-precio').value;
                    const stock = document.getElementById('swal-stock').value;
                    const descripcion = document.getElementById('swal-descripcion').value;
                    const marca = document.getElementById('swal-marca').value;

                    if (!nombre || !precio || (tipo === 'producto' && !stock)) {
                        Swal.showValidationMessage('Todos los campos marcados con * son obligatorios');
                        return false;
                    }

                    return { tipo, nombre, precio, stock, descripcion, marca };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { tipo, nombre, precio, stock, descripcion, marca } = result.value;
                    const url = tipo === 'producto' ? '{{ route("productos.store") }}' : '{{ route("servicios.store") }}';
                    const data = {
                        _token: '{{ csrf_token() }}',
                        nombre: nombre,
                        marca: tipo === 'producto' ? marca : null,
                        descripcion: descripcion,
                        [tipo === 'producto' ? 'precio_venta' : 'precio']: precio,
                        stock: stock,
                        stock_minimo: 0
                    };

                    Swal.fire({
                        title: 'Guardando...',
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: data,
                        success: function(response) {
                            if (response.success) {
                                const newItem = response.data;
                                if (tipo === 'producto') {
                                    PRODUCTOS.push(newItem);
                                } else {
                                    SERVICIOS.push(newItem);
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Registrado!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Refrescar los Select2 si existen filas abiertas
                                $('.item-select').each(function() {
                                    const row = this.closest('tr');
                                    const rowTipo = row.querySelector('.tipo-select').value;
                                    if (rowTipo === tipo) {
                                        const option = new Option(`${newItem.nombre} - ${newItem.descripcion || ''}`, newItem.id, false, false);
                                        option.dataset.precio = newItem.precio_venta || newItem.precio || 0;
                                        option.dataset.descripcion = newItem.descripcion || newItem.nombre;
                                        $(this).append(option);
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON.message || 'No se pudo registrar el ítem', 'error');
                        }
                    });
                }
            });
        }

        function toggleSwalFields(tipo) {
            const divStock = document.getElementById('div-stock');
            const labelNombre = document.getElementById('label-nombre');
            if (tipo === 'servicio') {
                divStock?.classList.add('hidden');
                if (labelNombre) labelNombre.textContent = 'NOMBRE DEL SERVICIO *';
            } else {
                divStock?.classList.remove('hidden');
                if (labelNombre) labelNombre.textContent = 'SKU / CLAVE *';
            }
        }
    </script>

    <template id="row-template">
        <tr class="hover:bg-white/5 transition-colors new-row">
            <td class="px-2 py-4">
                <input type="number" name="items[INDEX][cantidad]" value="1" min="0.01" step="any" oninput="calculateRow(this)" class="block w-full px-2 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-center text-sm font-bold outline-none" required>
            </td>
            <td class="px-2 py-4" style="min-width: 140px;">
                <select name="items[INDEX][tipo]" onchange="changeType(this)" class="tipo-select block w-full px-2 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-xs uppercase focus:outline-none">
                    <option value="producto" class="text-black bg-white">PRODUCTO</option>
                    <option value="servicio" class="text-black bg-white">SERVICIO</option>
                </select>
            </td>
            <td class="px-2 py-4" style="min-width: 250px;">
                <select name="items[INDEX][item_id]" onchange="updateItemData(this)" class="item-select block w-full" required>
                    <option value="" disabled selected>SELECCIONAR...</option>
                </select>
            </td>
            <td class="px-2 py-4">
                <input type="number" step="any" name="items[INDEX][precio_unitario]" value="0.00" oninput="calculateRow(this)" class="block w-full px-2 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-center text-sm font-bold outline-none" required>
            </td>
            <td class="px-2 py-4 text-right">
                <input type="number" step="any" name="items[INDEX][subtotal]" value="0.00" class="subtotal-input block w-full px-2 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-right text-sm font-black font-mono outline-none" readonly>
            </td>
            <td class="px-2 py-4 text-center">
                <button type="button" onclick="removeRow(this)" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        </tr>
    </template>

@endsection
