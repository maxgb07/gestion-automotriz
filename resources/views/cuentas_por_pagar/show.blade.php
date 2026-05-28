@extends('layouts.app')

@section('title', 'Estado de Cuenta: ' . $proveedor->nombre)

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('cuentas_por_pagar.index') }}" class="inline-flex items-center text-blue-300 hover:text-white transition-colors text-sm font-semibold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Cuentas por Pagar
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Estado de Cuenta</h1>
            <p class="text-blue-200 text-lg">{{ $proveedor->nombre }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('cuentas_por_pagar.pdf', $proveedor) }}" target="_blank" class="w-fit inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-lg shadow-lg shadow-indigo-900/40 transition-all text-sm uppercase tracking-widest" style="background-color: #4f46e5;">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                PDF Estado de Cuenta
            </a>
            <button onclick="document.getElementById('modal-nota-credito').classList.remove('hidden')" class="w-fit inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-black rounded-lg shadow-lg shadow-amber-900/40 transition-all text-sm uppercase tracking-widest cursor-pointer" style="background-color: #d97706;">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Registrar NC / Saldo
            </button>
            <button onclick="document.getElementById('modal-pago').classList.remove('hidden')" class="w-fit inline-flex items-center px-4 py-2 text-white font-black rounded-lg shadow-lg transition-all text-sm uppercase tracking-widest cursor-pointer hover:opacity-90" style="background-color: #059669;">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Registrar Pago
            </button>
        </div>
    </div>

    <!-- Pestañas (Tabs) -->
    <div class="mb-6 flex space-x-1 bg-white/5 p-1 rounded-xl w-fit">
        <button class="tab-btn active px-6 py-2 rounded-lg text-sm font-bold uppercase transition-all text-white bg-blue-600" data-target="tab-pendientes">Facturas Pendientes</button>
        <button class="tab-btn px-6 py-2 rounded-lg text-sm font-bold uppercase transition-all text-blue-200 hover:text-white hover:bg-white/10" data-target="tab-historial">Historial de Pagos</button>
        <button class="tab-btn px-6 py-2 rounded-lg text-sm font-bold uppercase transition-all text-blue-200 hover:text-white hover:bg-white/10" data-target="tab-notas">NC / Saldos a Favor</button>
    </div>

    <!-- Tab: Facturas Pendientes -->
    <div id="tab-pendientes" class="tab-content active bg-white/10 backdrop-blur-xl rounded-3xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <!-- <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Folio OC</th> -->
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Factura</th>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Vencimiento</th>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Subtotal</th>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Desc. Global</th>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Desc. Extra</th>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Desc. Interno</th>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">IVA</th>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Total Factura</th>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Pronto Pago</th>
                        <th class="px-4 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Saldo Pendiente</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($facturas as $factura)
                        <tr class="hover:bg-white/5 transition-colors">
                            <!-- <td class="px-4 py-4 whitespace-nowrap text-center text-white font-medium uppercase text-md">{{ $factura->folio }}</td> -->
                            <td class="px-4 py-4 whitespace-nowrap text-center text-white font-medium uppercase text-md">{{ $factura->factura ?? 'SIN FACTURA' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-md">
                                @php $vencida = $factura->fecha_vencimiento && $factura->fecha_vencimiento < now()->format('Y-m-d'); @endphp
                                <span class="{{ $vencida ? 'text-red-400 font-bold' : 'text-white' }} uppercase">
                                    {{ \Carbon\Carbon::parse($factura->fecha_vencimiento)->translatedFormat('d M, Y') }}
                                    <!-- @if($vencida) (V) @endif -->
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-white font-medium text-md">${{ number_format($factura->subtotal, 2) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-amber-200 text-md">
                                {{ number_format($factura->porcentaje_descuento, 2) }}%<br>
                                <span class="text-md opacity-70">${{ number_format($factura->monto_descuento, 2) }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-amber-200 text-md">
                                {{ number_format($factura->porcentaje_descuento_extra, 2) }}%<br>
                                <span class="text-md opacity-70">${{ number_format($factura->monto_descuento_extra, 2) }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-amber-200 text-md">
                                @php
                                    $base_interna = $factura->subtotal - $factura->monto_descuento - $factura->monto_descuento_extra;
                                    $pct_interno_efectivo = $base_interna > 0 ? ($factura->monto_descuento_interno / $base_interna) * 100 : 0;
                                @endphp
                                {{ number_format($pct_interno_efectivo, 2) }}%<br>
                                <span class="text-md opacity-70">${{ number_format($factura->monto_descuento_interno, 2) }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-blue-200 font-medium text-md">${{ number_format($factura->iva, 2) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-white font-bold text-md">${{ number_format($factura->total, 2) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-green-300 text-md">
                                {{ number_format($factura->porcentaje_pronto_pago ?? 0, 2) }}%<br>
                                <span class="text-md opacity-70">${{ number_format($factura->monto_pronto_pago ?? 0, 2) }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-white font-black text-md text-blue-300">
                                ${{ number_format($factura->saldo_pendiente, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-blue-200 uppercase">NO HAY FACTURAS PENDIENTES</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($facturas->hasPages())
            <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                {{ $facturas->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    <!-- Tab: Historial de Pagos -->
    <div id="tab-historial" class="tab-content hidden space-y-4">
        @forelse($pagosAgrupados as $grupoId => $pagosGrupo)
            @php
                $first = $pagosGrupo->first();
                $totalGrupo = $pagosGrupo->sum('monto');
                $facturasAbonadas = $pagosGrupo->map(function($p) { return $p->compra->factura ?? $p->compra->folio; })->unique()->implode(', ');
                $esGrupo = count($pagosGrupo) > 1 || $first->grupo_pago_id != null;
                $estadoDocs = $first->estado_documentos ?? 'COMPLETO';
            @endphp
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-xl rounded-2xl p-6">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center border border-blue-500/30">
                            <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            @php
                                $totalTransf = $pagosGrupo->where('tipo', 'PAGO NORMAL')->sum('monto');
                                $totalNC = $pagosGrupo->where('tipo', 'APLICACION NC')->sum('monto');
                            @endphp
                            <h4 class="text-white font-black text-lg uppercase">Pago de ${{ number_format($totalGrupo, 2) }}</h4>
                            <p class="text-sm text-blue-200 uppercase">{{ \Carbon\Carbon::parse($first->fecha_pago)->translatedFormat('d M, Y') }} &bull; Forma: <span class="text-white font-bold">{{ $first->forma_pago }}</span>
                                @if($totalNC > 0)
                                <br><span class="text-xs text-emerald-300 font-bold">(Efectivo/Transf: ${{ number_format($totalTransf, 2) }} | Saldo a favor: ${{ number_format($totalNC, 2) }})</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col md:items-end gap-2">
                        @php
                            $numFacturas = $pagosGrupo->unique('compra_id')->count();
                            $groupId = $first->grupo_pago_id ?: 'indiv_' . $first->id;
                        @endphp
                        <p class="text-xs text-blue-200 uppercase font-bold text-center md:text-right w-full">Facturas Pagadas: 
                            <button onclick="verFacturasPaginadas('{{ $groupId }}', 1)" class="text-white hover:text-emerald-300 underline underline-offset-2 transition-colors">
                                Ver {{ $numFacturas }} factura(s)
                            </button>
                        </p>
                        @if($estadoDocs === 'PENDIENTE')
                            <button onclick="abrirModalComplemento('{{ $first->grupo_pago_id }}', {{ $totalTransf > 0 ? $totalTransf : $totalGrupo }})" class="w-fit px-4 py-2 bg-orange-500/20 border border-orange-500/40 hover:bg-orange-500/40 text-orange-300 font-bold rounded-lg transition-all uppercase text-xs">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Pendiente Complemento / NCs
                            </button>
                        @else
                            <span class="w-fit px-4 py-2 bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-bold rounded-lg uppercase text-xs text-center flex items-center justify-center">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Expediente Cerrado
                            </span>
                        @endif
                    </div>
                </div>

                @if($estadoDocs === 'COMPLETO')
                    <div class="mt-4 pt-4 border-t border-white/10 grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                        @if($first->complemento_folio)
                            <div class="bg-black/20 rounded-lg p-3 border border-white/5">
                                <span class="block text-blue-200/60 uppercase font-bold mb-1">Folio REP</span>
                                <span class="text-white font-bold uppercase">{{ $first->complemento_folio }}</span>
                            </div>
                            <div class="bg-black/20 rounded-lg p-3 border border-white/5">
                                <span class="block text-blue-200/60 uppercase font-bold mb-1">Fecha REP</span>
                                <span class="text-white font-bold uppercase">{{ \Carbon\Carbon::parse($first->complemento_fecha)->format('d/m/Y') }}</span>
                            </div>
                            <div class="bg-black/20 rounded-lg p-3 border border-white/5">
                                <span class="block text-blue-200/60 uppercase font-bold mb-1">Monto Amparado</span>
                                <span class="text-white font-bold uppercase">${{ number_format($first->complemento_monto, 2) }}</span>
                            </div>
                        @else
                            <div class="bg-black/20 rounded-lg p-3 border border-white/5 col-span-1 md:col-span-3 text-center flex items-center justify-center">
                                <span class="text-white/50 font-bold uppercase">SIN COMPLEMENTO DE PAGO REGISTRADO</span>
                            </div>
                        @endif

                        @if(!empty($first->ncs_informativas))
                            <div class="bg-indigo-900/40 rounded-lg p-3 border border-indigo-500/30 col-span-1">
                                <span class="block text-indigo-200/60 uppercase font-bold mb-1">NCs (Descuentos)</span>
                                <ul class="text-white font-bold uppercase text-xs space-y-1">
                                    @foreach($first->ncs_informativas as $ncInf)
                                        <li>&bull; {{ $ncInf }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-xl rounded-2xl p-10 text-center">
                <p class="text-blue-200 uppercase font-bold">NO HAY REGISTRO DE PAGOS</p>
            </div>
        @endforelse
    </div>

    <!-- Tab: Notas de Crédito y Saldos -->
    <div id="tab-notas" class="tab-content hidden bg-white/10 backdrop-blur-xl rounded-3xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Folio</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Fecha</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Saldo Disponible</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Observaciones</th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($notasCredito as $nc)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-center text-white uppercase">{{ $nc->folio }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-white uppercase">{{ \Carbon\Carbon::parse($nc->fecha)->translatedFormat('d M, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-emerald-400 font-black uppercase">${{ number_format($nc->saldo_disponible, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-white uppercase">{{ $nc->observaciones }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-bold rounded-full uppercase">{{ $nc->estado }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-blue-200 uppercase">NO HAY NOTAS DE CRÉDITO O SALDOS A FAVOR</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notasCredito->hasPages())
            <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                {{ $notasCredito->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>


    <!-- MODAL: REGISTRAR PAGO -->
    <div id="modal-pago" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm overflow-y-auto flex items-start justify-center p-4">
        <div class="bg-slate-800 rounded-3xl border border-white/20 shadow-2xl w-full max-w-2xl overflow-hidden relative my-4 flex flex-col max-h-[92vh]">
            <!-- Header fijo -->
            <div class="p-5 border-b border-white/10 flex justify-between items-center bg-white/5 flex-shrink-0">
                <h3 class="text-xl font-black text-white uppercase tracking-tight">Registrar Pago a Proveedor</h3>
                <button onclick="document.getElementById('modal-pago').classList.add('hidden')" class="text-blue-200 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Cuerpo scrollable -->
            <form action="{{ route('cuentas_por_pagar.pagos.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="proveedor_id" value="{{ $proveedor->id }}">

                <div class="overflow-y-auto flex-1 p-6 space-y-5">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-blue-100 mb-1.5 uppercase tracking-wider">Monto a Pagar (Dinero)</label>
                            <input type="number" step="0.01" min="0" name="monto_pago" id="monto_pago_input" class="block w-full px-4 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-black text-lg" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-blue-100 mb-1.5 uppercase tracking-wider">Forma de Pago *</label>
                            <select name="forma_pago" id="forma_pago_select" required class="block w-full px-4 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="TRANSFERENCIA" class="text-black">TRANSFERENCIA</option>
                                <option value="EFECTIVO" class="text-black">EFECTIVO</option>
                                <option value="CHEQUE" class="text-black">CHEQUE</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-blue-100 mb-1.5 uppercase tracking-wider">Fecha del Pago *</label>
                            <input type="date" name="fecha_pago" value="{{ date('Y-m-d') }}" required class="block w-full px-4 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-blue-100 mb-1.5 uppercase tracking-wider">Referencia (Dinero)</label>
                            <input type="text" name="referencia" class="block w-full px-4 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                        </div>
                    </div>

                    <!-- NC / Saldo a Favor: multi-selección con checkboxes -->
                    <div id="div_nota_credito" class="bg-emerald-500/10 p-4 rounded-xl border border-emerald-500/20">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-emerald-200 uppercase tracking-wider">Aplicar NC / Saldo a Favor (Opcional)</label>
                            @if($notasCreditoModal->count() > 0)
                                <span id="resumen_nc" class="text-xs font-black text-emerald-300 bg-emerald-500/20 px-3 py-1 rounded-full">Total NC: $0.00</span>
                            @endif
                        </div>
                        @if($notasCreditoModal->count() > 0)
                            <div class="max-h-32 overflow-y-auto bg-black/20 rounded-xl border border-emerald-500/20 p-2 space-y-1">
                                @foreach($notasCreditoModal as $nc)
                                    <label class="flex items-center justify-between p-2.5 rounded-lg hover:bg-emerald-500/10 cursor-pointer border border-transparent hover:border-emerald-500/20 transition-all">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox"
                                                   name="notas_credito_ids[]"
                                                   value="{{ $nc->id }}"
                                                   data-saldo="{{ $nc->saldo_disponible }}"
                                                   class="w-4 h-4 rounded border-emerald-500/40 bg-white/10 text-emerald-500 focus:ring-emerald-500 nc-checkbox">
                                            <span class="text-white font-bold uppercase text-sm">{{ $nc->folio }}</span>
                                        </div>
                                        <span class="text-emerald-400 font-black text-sm">${{ number_format($nc->saldo_disponible, 2) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-emerald-200/50 text-xs uppercase font-bold text-center py-2">No hay NC / Saldos a favor disponibles</p>
                        @endif
                    </div>

                    <!-- Facturas a liquidar -->
                    <div>
                        <label class="block text-xs font-bold text-blue-100 mb-1 uppercase tracking-wider">Selecciona las facturas a liquidar/abonar</label>
                        <p class="text-xs text-blue-200/60 mb-3 uppercase">El monto se distribuirá automáticamente en el orden de las facturas seleccionadas.</p>

                        <div class="max-h-44 overflow-y-auto bg-black/20 rounded-xl border border-white/10 p-2 space-y-1">
                            @foreach($facturasModal as $factura)
                                <label class="flex items-center justify-between p-2.5 rounded-lg hover:bg-white/5 cursor-pointer border border-transparent hover:border-white/10 transition-all">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="facturas[]" value="{{ $factura->id }}" data-saldo="{{ $factura->saldo_pendiente }}" class="w-4 h-4 rounded border-white/20 bg-white/10 text-blue-500 focus:ring-blue-500 factura-checkbox" {{ $loop->first ? 'checked' : '' }}>
                                        <div class="flex flex-col">
                                            <span class="text-white font-bold uppercase text-sm">{{ $factura->factura ?? $factura->folio }}</span>
                                            <span class="text-xs text-blue-200 uppercase">Vence: {{ \Carbon\Carbon::parse($factura->fecha_vencimiento)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                    <span class="text-blue-300 font-black">${{ number_format($factura->saldo_pendiente, 2) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Resumen de totales -->
                    <div class="bg-white/5 rounded-xl border border-white/10 p-4 space-y-2 text-sm">
                        <div class="flex justify-between text-blue-200 uppercase font-semibold">
                            <span>Total Facturas Seleccionadas:</span>
                            <span id="resumen_total_facturas" class="text-white font-black">$0.00</span>
                        </div>
                        <div class="flex justify-between text-emerald-300 uppercase font-semibold">
                            <span>(-) Total NC Aplicadas:</span>
                            <span id="resumen_total_nc" class="font-black">$0.00</span>
                        </div>
                        <div class="flex justify-between border-t border-white/10 pt-2 text-white uppercase font-black text-base">
                            <span>Monto a Pagar (Dinero):</span>
                            <span id="resumen_monto_pagar" class="text-blue-300">$0.00</span>
                        </div>
                    </div>

                </div>

                <!-- Footer fijo con botones -->
                <div class="flex justify-end gap-4 p-5 border-t border-white/10 bg-white/5 flex-shrink-0">
                    <button type="button" onclick="document.getElementById('modal-pago').classList.add('hidden')" class="px-6 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all uppercase text-sm">Cancelar</button>
                    <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase text-sm">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>


    <!-- MODAL: REGISTRAR NOTA DE CRÉDITO / SALDO A FAVOR -->
    <div id="modal-nota-credito" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-3xl border border-white/20 shadow-2xl w-full max-w-md overflow-hidden relative">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                <h3 class="text-xl font-black text-white uppercase tracking-tight">Nueva NC / Saldo a Favor</h3>
                <button onclick="document.getElementById('modal-nota-credito').classList.add('hidden')" class="text-blue-200 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('cuentas_por_pagar.notas_credito.store') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="proveedor_id" value="{{ $proveedor->id }}">

                <div class="space-y-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-blue-100 mb-2 uppercase">Folio NC / Referencia *</label>
                        <input type="text" name="folio" placeholder="Ej. ANTICIPO-01, NC-1020" required class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-100 mb-2 uppercase">Fecha *</label>
                        <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-100 mb-2 uppercase">Monto a Favor *</label>
                        <input type="number" step="0.01" min="0.01" name="monto" required class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-black text-lg text-emerald-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-100 mb-2 uppercase">Observaciones</label>
                        <textarea name="observaciones" rows="2" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="button" onclick="document.getElementById('modal-nota-credito').classList.add('hidden')" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all uppercase text-sm">Cancelar</button>
                    <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition-all uppercase text-sm">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: REGISTRAR COMPLEMENTO DE PAGO -->
    <div id="modal-complemento" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-3xl border border-white/20 shadow-2xl w-full max-w-lg overflow-hidden relative">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                <h3 class="text-xl font-black text-white uppercase tracking-tight">Cerrar Expediente / REP</h3>
                <button onclick="document.getElementById('modal-complemento').classList.add('hidden')" class="text-blue-200 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('cuentas_por_pagar.complementos.store', $proveedor) }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="grupo_pago_id" id="comp_grupo_pago_id" value="">

                <div class="space-y-6 mb-8">

                    <div>
                        <label class="block text-sm font-medium text-blue-100 mb-2 uppercase">Folio del REP</label>
                        <input type="text" name="complemento_folio" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-100 mb-2 uppercase">Fecha del REP</label>
                        <input type="date" name="complemento_fecha" value="{{ date('Y-m-d') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-100 mb-2 uppercase">Monto Amparado</label>
                        <input type="number" step="0.01" min="0" name="complemento_monto" id="comp_monto" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-black text-lg text-emerald-400">
                    </div>
                </div>

                <div class="mb-8 pt-6 border-t border-white/10">
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-indigo-200 uppercase">Notas de Crédito (Descuentos)</label>
                        <button type="button" onclick="agregarInputNC()" class="w-fit inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-lg shadow-lg shadow-indigo-900/40 transition-all text-xs uppercase tracking-widest" style="background-color: #4f46e5;">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Agregar NC
                        </button>
                    </div>
                    <div id="ncs-container" class="space-y-3">
                        <!-- Inputs dinámicos -->
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="button" onclick="document.getElementById('modal-complemento').classList.add('hidden')" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all uppercase text-sm">Cancelar</button>
                    <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition-all uppercase text-sm">Guardar Expediente</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: VER FACTURAS PAGADAS -->
    <div id="modal-facturas-pagadas" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-3xl border border-white/20 shadow-2xl w-full max-w-3xl overflow-hidden relative">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                <h3 class="text-xl font-black text-white uppercase tracking-tight">Facturas Pagadas del Expediente</h3>
                <button onclick="document.getElementById('modal-facturas-pagadas').classList.add('hidden')" class="text-blue-200 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="max-h-[60vh] overflow-y-auto p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Folio OC</th>
                                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Factura</th>
                                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Forma de Pago</th>
                                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Monto Abonado</th>
                            </tr>
                        </thead>
                        <tbody id="lista-facturas-pagadas" class="divide-y divide-white/10">
                        </tbody>
                    </table>
                </div>
                <div id="paginacion-facturas-container" class="px-6 py-4 bg-white/5 border-t border-white/10">
                    <!-- Pagination goes here -->
                </div>
            </div>
            <div class="p-6 border-t border-white/10 bg-white/5 flex justify-end">
                <button onclick="document.getElementById('modal-facturas-pagadas').classList.add('hidden')" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all uppercase text-sm">Cerrar Tabla</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lógica de Pestañas
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active from all
                tabBtns.forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white');
                    b.classList.add('text-blue-200', 'hover:bg-white/10');
                });
                tabContents.forEach(c => c.classList.add('hidden'));

                // Add active to clicked
                btn.classList.add('bg-blue-600', 'text-white');
                btn.classList.remove('text-blue-200', 'hover:bg-white/10');
                document.getElementById(btn.dataset.target).classList.remove('hidden');
            });
        });

        // Lógica de cálculo automático de monto a pagar
        const facturasCheckboxes = document.querySelectorAll('.factura-checkbox');
        const montoPagoInput = document.getElementById('monto_pago_input');

        function calcularMontoAPagar() {
            let totalFacturas = 0;
            facturasCheckboxes.forEach(cb => {
                if (cb.checked) totalFacturas += parseFloat(cb.dataset.saldo || 0);
            });

            // Sumar TODAS las NC checkboxes marcadas
            let saldoNC = 0;
            document.querySelectorAll('.nc-checkbox:checked').forEach(cb => {
                saldoNC += parseFloat(cb.dataset.saldo || 0);
            });

            let montoFinal = totalFacturas - saldoNC;
            if (montoFinal < 0) montoFinal = 0;

            montoPagoInput.value = montoFinal.toFixed(2);

            // Actualizar resúmenes visuales
            const fmt = n => '$' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            const elTotalF = document.getElementById('resumen_total_facturas');
            const elTotalNC = document.getElementById('resumen_total_nc');
            const elMonto = document.getElementById('resumen_monto_pagar');
            const elResumenNC = document.getElementById('resumen_nc');

            if (elTotalF) elTotalF.textContent = fmt(totalFacturas);
            if (elTotalNC) elTotalNC.textContent = fmt(saldoNC);
            if (elMonto) elMonto.textContent = fmt(montoFinal);
            if (elResumenNC) elResumenNC.textContent = 'Total NC: ' + fmt(saldoNC);
        }

        // Inicializar al cargar
        calcularMontoAPagar();

        // Escuchar cambios en facturas y en NC
        facturasCheckboxes.forEach(cb => cb.addEventListener('change', calcularMontoAPagar));
        document.querySelectorAll('.nc-checkbox').forEach(cb => cb.addEventListener('change', calcularMontoAPagar));
    });

    function abrirModalComplemento(grupoId, montoExpediente) {
        document.getElementById('comp_grupo_pago_id').value = grupoId;
        document.getElementById('comp_monto').value = montoExpediente;
        document.getElementById('ncs-container').innerHTML = ''; // Limpiar NCs previas
        document.getElementById('modal-complemento').classList.remove('hidden');
    }

    function agregarInputNC() {
        const container = document.getElementById('ncs-container');
        const id = Date.now();
        const html = `
            <div id="nc-${id}" class="flex gap-2 items-center">
                <input type="text" name="ncs_informativas[]" placeholder="Ej. NC-001 por $100" class="block w-full px-4 py-2 bg-white/5 border border-indigo-500/30 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 uppercase text-sm" required>
                <button type="button" onclick="document.getElementById('nc-${id}').remove()" class="text-red-400 hover:text-red-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    let currentGrupoId = null;

    function verFacturasPaginadas(grupoId, page) {
        if(page === 1) {
            currentGrupoId = grupoId;
        }
        document.getElementById('lista-facturas-pagadas').innerHTML = '<tr><td colspan="4" class="p-6 text-center text-blue-200 uppercase font-bold tracking-widest">Cargando facturas...</td></tr>';
        document.getElementById('paginacion-facturas-container').innerHTML = '';

        fetch(`/cuentas-por-pagar/pagos/${currentGrupoId}/facturas?page=${page}`)
            .then(res => res.json())
            .then(data => {
                const lista = document.getElementById('lista-facturas-pagadas');
                lista.innerHTML = data.html;

                const pagContainer = document.getElementById('paginacion-facturas-container');
                pagContainer.innerHTML = data.pagination;
                
                // Interceptar clicks de paginación para hacerlos por AJAX
                pagContainer.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const num = url.searchParams.get('page');
                        verFacturasPaginadas(currentGrupoId, num);
                    });
                });
                
                if(page === 1) {
                    document.getElementById('modal-facturas-pagadas').classList.remove('hidden');
                }
            });
    }
</script>
@endpush
