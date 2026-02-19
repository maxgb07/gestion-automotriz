@extends('layouts.app')

@section('title', 'Reporte de Órdenes')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    @php 
        $movs = collect();
        $metodosBase = [
            'EFECTIVO' => 0,
            'CHEQUE' => 0,
            'TRANSFERENCIA' => 0,
            'TARJETA CRÉDITO' => 0,
            'TARJETA DÉBITO' => 0
        ];
        $totalesPorMetodo = $metodosBase;
        
        // Helper para mapear métodos
        $mapearMetodo = function($metodoOriginal) {
            $m = strtoupper($metodoOriginal);
            if(str_contains($m, 'EFECTIVO')) return 'EFECTIVO';
            if(str_contains($m, 'CHEQUE')) return 'CHEQUE';
            if(str_contains($m, 'TRANSFERENCIA')) return 'TRANSFERENCIA';
            if(str_contains($m, 'TARJETA')) {
                if(str_contains($m, 'CREDITO') || str_contains($m, 'CRÉDITO')) return 'TARJETA CRÉDITO';
                return 'TARJETA DÉBITO';
            }
            return $m ?: 'OTRO';
        };

        // Agregar Órdenes entregadas en el periodo
        foreach($ordenes as $o) {
            $metodoOriginal = $o->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'PENDIENTE';
            $metodoMapeado = $mapearMetodo($metodoOriginal);
            
            $movs->push([
                'id' => 'OS-'.$o->id,
                'item' => $o,
                'tipo' => 'ORDEN',
                'label' => 'ORDEN SERVICIO',
                'hora' => $o->created_at,
                'total' => $o->total,
                'metodo' => $metodoOriginal,
                'metodo_mapeado' => $metodoMapeado,
                'folio' => $o->folio,
                'cliente' => $o->cliente->nombre,
                'es_pago' => false,
                'requiere_factura' => $o->requiere_factura,
                'folio_factura' => $o->folio_factura,
                'estado_label' => $o->estado
            ]);
            
            if(isset($totalesPorMetodo[$metodoMapeado])) {
                $totalesPorMetodo[$metodoMapeado] += $o->total;
            } else {
                $totalesPorMetodo[$metodoMapeado] = ($totalesPorMetodo[$metodoMapeado] ?? 0) + $o->total;
            }
        }

        // Abonos en el periodo
        $pagoOrdenesPeriodo = $pagos->groupBy('orden_servicio_id');
        $ordenesPeriodoIds = $ordenes->pluck('id')->toArray();

        foreach($pagoOrdenesPeriodo as $osId => $abonos) {
            if(!in_array($osId, $ordenesPeriodoIds)) {
                $os = $abonos->first()->ordenServicio;
                $montoPeriodo = $abonos->sum('monto');
                $metodoOriginal = $abonos->pluck('metodo_pago')->unique()->implode(', ');
                $metodoMapeado = $mapearMetodo($metodoOriginal);
                
                $totalPaidEver = $os->pagos->sum('monto');
                $esCompleto = $totalPaidEver >= $os->total;

                $movs->push([
                    'id' => 'PO-'.$osId,
                    'item' => $os,
                    'tipo' => 'PAGO_ORDEN',
                    'label' => $esCompleto ? 'ORDEN SERVICIO' : 'ABONO ORDEN',
                    'hora' => $abonos->max('fecha_pago'),
                    'total' => $montoPeriodo,
                    'metodo' => $metodoOriginal,
                    'metodo_mapeado' => $metodoMapeado,
                    'folio' => $os->folio,
                    'cliente' => $os->cliente->nombre,
                    'es_pago' => true,
                    'requiere_factura' => $os->requiere_factura,
                    'folio_factura' => $os->folio_factura,
                    'estado_label' => $esCompleto ? 'ENTREGADO' : $os->estado
                ]);
                
                if(isset($totalesPorMetodo[$metodoMapeado])) {
                    $totalesPorMetodo[$metodoMapeado] += $montoPeriodo;
                } else {
                    $totalesPorMetodo[$metodoMapeado] = ($totalesPorMetodo[$metodoMapeado] ?? 0) + $montoPeriodo;
                }
            }
        }

        $totalGeneralSum = $movs->sum('total');
        $combined = $movs->sortByDesc('hora');
        $totalG = 0;
    @endphp

    <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
        <div>
            <a href="{{ route('reportes.index') }}" class="inline-flex items-center text-blue-300 hover:text-white transition-colors mb-4 group">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Reportes
            </a>
            <h1 class="text-4xl font-bold text-white uppercase tracking-tight">Reporte de Órdenes</h1>
            <p class="text-blue-200 mt-2 uppercase font-black tracking-widest text-base">
                {{ \Carbon\Carbon::parse($fecha_inicio)->isoFormat('LL') }} al {{ \Carbon\Carbon::parse($fecha_fin)->isoFormat('LL') }}
            </p>
        </div>
        
        <div class="flex gap-4">
            <a href="{{ route('reportes.ordenes.pdf', ['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]) }}" target="_blank" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Imprimir Reporte
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-2xl">
        <form action="{{ route('reportes.ordenes') }}" method="GET" class="flex flex-col md:flex-row items-end gap-6">
            <div class="flex-grow">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fecha_inicio }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>
            <div class="flex-grow">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fecha_fin }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>
            <button type="submit" class="px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-md font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center border border-white/10 shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Filtrar Periodo
            </button>
        </form>
    </div>

    <!-- Totales -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-blue-600/20 backdrop-blur-xl rounded-2xl p-5 border border-blue-500/30 shadow-xl">
            <p class="text-blue-200 uppercase font-bold tracking-widest text-md mb-1">Total Órdenes</p>
            <p class="text-2xl font-black text-white font-mono tracking-tighter">${{ number_format($totalGeneralSum, 2) }}</p>
        </div>
        @foreach(['EFECTIVO', 'CHEQUE', 'TRANSFERENCIA', 'TARJETA CRÉDITO', 'TARJETA DÉBITO'] as $metodo)
            @php $totalMetodo = $totalesPorMetodo[$metodo] ?? 0; @endphp
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-5 border border-white/20 shadow-xl">
                <p class="uppercase font-bold tracking-widest text-md mb-1 opacity-60 text-blue-200">{{ $metodo }}</p>
                <p class="text-xl font-black text-white font-mono tracking-tighter">${{ number_format($totalMetodo, 2) }}</p>
            </div>
        @endforeach
    </div>

    <!-- Tabla Detallada -->
    <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-widest">Tipo</th>
                        <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-widest">Fecha</th>
                        <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-widest">Folio</th>
                        <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-widest">Factura</th>
                        <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-widest">Método</th>
                        <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-widest">Total Recibido</th>
                        <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($combined as $mov)
                        @php 
                            $totalG += $mov['total'];
                            
                            $details = $mov['item']->detalles->map(function($d) {
                                return [
                                    'cantidad' => $d->cantidad,
                                    'nombre' => $d->producto_id ? $d->producto->nombre : $d->servicio->nombre,
                                    'descripcion' => $d->producto_id ? $d->producto->descripcion : $d->servicio->descripcion,
                                    'subtotal' => $d->subtotal
                                ];
                            });

                            $modalData = [
                                'folio' => $mov['folio'],
                                'tipo' => $mov['label'],
                                'cliente' => $mov['cliente'],
                                'vehiculo' => ($mov['item']->vehiculo ? $mov['item']->vehiculo->marca . ' ' . $mov['item']->vehiculo->modelo . ' (' . $mov['item']->vehiculo->placas . ')' : 'N/A'),
                                'items' => $details,
                                'total' => number_format($mov['item']->total, 2),
                                'pago_monto' => $mov['es_pago'] ? number_format($mov['total'], 2) : null
                            ];

                            $metodo = $mov['metodo_mapeado'];
                            $hexColor = '#ffffff';
                            if($metodo == 'EFECTIVO') $hexColor = '#34d399';
                            elseif($metodo == 'TRANSFERENCIA') $hexColor = '#60a5fa';
                            elseif($metodo == 'TARJETA CRÉDITO') $hexColor = '#c084fc';
                            elseif($metodo == 'TARJETA DÉBITO') $hexColor = '#f59e0b';
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-4">
                                <span class="px-3 py-1 rounded-lg text-md font-black tracking-widest uppercase {{ $mov['es_pago'] ? 'bg-orange-500/20 text-orange-300' : 'bg-blue-500/20 text-blue-300' }}">
                                    {{ $mov['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-md font-bold text-white/70">{{ \Carbon\Carbon::parse($mov['hora'])->format('d/m/Y') }}</td>
                            <td class="px-4 py-4 text-md font-black text-white">{{ $mov['folio'] }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col items-center">
                                    <span class="text-md font-black {{ ($mov['requiere_factura'] ?? 'NO') == 'SI' ? 'text-blue-400' : 'text-white/30' }}">
                                        {{ $mov['requiere_factura'] ?? 'NO' }}
                                    </span>
                                    @if(($mov['requiere_factura'] ?? 'NO') == 'SI' && ($mov['folio_factura'] ?? null))
                                        <span class="text-md font-black text-white mt-1 uppercase tracking-tighter">
                                            {{ $mov['folio_factura'] }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-md uppercase font-black" style="color: {{ $hexColor }};">
                                {{ $mov['metodo_mapeado'] }}
                            </td>
                            <td class="px-4 py-4 font-mono font-black text-white text-md">${{ number_format($mov['total'], 2) }}</td>
                            <td class="px-4 py-4 text-right">
                                <button onclick='showDetails(@json($modalData))' class="p-2 bg-blue-500/20 hover:bg-blue-500/40 text-blue-400 hover:text-white rounded-lg transition-all group" title="Ver Detalle">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <p class="text-blue-200/30 uppercase font-black tracking-widest italic text-base">No hay órdenes registradas en este periodo</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-white/10 border-t border-white/20">
                    <tr class="font-black text-white uppercase tracking-widest">
                        <td colspan="5" class="px-6 py-6 text-right text-blue-200 text-base">TOTAL:</td>
                        <td class="px-6 py-6 font-mono text-2xl">${{ number_format($totalG, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    function showDetails(data) {
        let itemsHtml = `
            <div class="mt-6 text-left text-base">
                ${data.pago_monto ? `
                <div class="mb-6 bg-orange-500/10 p-6 rounded-2xl border border-orange-500/20 text-center">
                    <p class="text-orange-300 text-xs font-black uppercase tracking-widest mb-1">Monto del Pago Recibido en el Periodo</p>
                    <p class="text-orange-400 text-4xl font-black font-mono tracking-tighter">$${data.pago_monto}</p>
                </div>
                ` : ''}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 bg-white/5 p-6 rounded-2xl border border-white/10">
                    <div>
                        <p class="text-blue-300 text-xs font-black uppercase tracking-widest mb-1 opacity-60">Cliente</p>
                        <p class="text-white text-xl font-black uppercase tracking-tight">${data.cliente}</p>
                    </div>
                    <div>
                        <p class="text-blue-300 text-xs font-black uppercase tracking-widest mb-1 opacity-60">Vehículo</p>
                        <p class="text-white text-xl font-black uppercase tracking-tight">${data.vehiculo}</p>
                    </div>
                </div>

                <div class="bg-white/5 rounded-2xl overflow-hidden border border-white/10">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white/10 text-base">
                            <tr>
                                <th class="px-4 py-3 text-xs font-black text-blue-200 uppercase tracking-widest">Cant.</th>
                                <th class="px-4 py-3 text-xs font-black text-blue-200 uppercase tracking-widest">Nombre</th>
                                <th class="px-4 py-3 text-xs font-black text-blue-200 uppercase tracking-widest">Descripción</th>
                                <th class="px-4 py-3 text-xs font-black text-blue-200 uppercase tracking-widest text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-base">
                            ${data.items.map(item => `
                                <tr>
                                    <td class="px-4 py-3 font-bold text-white/70">${item.cantidad}</td>
                                    <td class="px-4 py-3 text-white font-black uppercase">${item.nombre}</td>
                                    <td class="px-4 py-3 text-sm text-white/50 font-medium uppercase italic">${item.descripcion || '---'}</td>
                                    <td class="px-4 py-3 font-mono text-right font-black text-white">$${parseFloat(item.subtotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot class="bg-white/10">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-xs font-black text-blue-200 uppercase tracking-widest text-right">Total Documento:</td>
                                <td class="px-4 py-4 text-white text-2xl font-black text-right font-mono tracking-tighter">$${data.total}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        `;

        Swal.fire({
            title: `${data.tipo}: ${data.folio}`,
            html: itemsHtml,
            width: '800px',
            background: '#1e293b',
            color: '#fff',
            confirmButtonText: 'CERRAR',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-[2rem] border border-white/20 shadow-2xl',
                title: 'text-2xl font-black uppercase tracking-tighter pt-8 px-8 text-left border-b border-white/10 pb-6'
            }
        });
    }
</script>
@endsection
