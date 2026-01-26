@extends('layouts.app')

@section('title', 'Corte del Día')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
        <div>
            <a href="{{ route('reportes.index') }}" class="inline-flex items-center text-blue-300 hover:text-white transition-colors mb-4 group">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Reportes
            </a>
            <h1 class="text-4xl font-bold text-white uppercase tracking-tight">Corte del Día</h1>
            <p class="text-blue-200 mt-2 uppercase font-black tracking-widest text-sm">{{ \Carbon\Carbon::today()->isoFormat('LL') }}</p>
        </div>
        
        <a href="{{ route('reportes.corte.pdf') }}" target="_blank" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Imprimir Corte
        </a>
    </div>

    <!-- Resumen de Totales -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
            <p class="text-blue-100/60 uppercase font-black tracking-widest text-xs mb-2">Total de Ventas</p>
            <p class="text-3xl font-black text-white font-mono tracking-tighter">${{ number_format($ventas->sum('total'), 2) }}</p>
        </div>
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
            <p class="text-blue-100/60 uppercase font-black tracking-widest text-xs mb-2">Total de Órdenes</p>
            <p class="text-3xl font-black text-white font-mono tracking-tighter">${{ number_format($ordenes->sum('total'), 2) }}</p>
        </div>
    </div>

    <!-- Tabla Detallada -->
    <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Hora</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Tipo</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Folio</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Estado</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Método de Pago</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Total</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @php 
                        $combined = $ventas->map(fn($v) => ['item' => $v, 'tipo' => 'VENTA', 'hora' => $v->created_at])
                            ->concat($ordenes->map(fn($o) => ['item' => $o, 'tipo' => 'ORDEN', 'hora' => $o->created_at]))
                            ->sortBy('hora');
                        $totalG = 0;
                        $saldoG = 0;
                    @endphp

                    @forelse($combined as $mov)
                        @php 
                            $totalG += $mov['item']->total;
                            $saldoG += $mov['item']->saldo_pendiente;
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-white/70">{{ $mov['hora']->format('H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black tracking-widest uppercase {{ $mov['tipo'] == 'VENTA' ? 'bg-purple-500/20 text-purple-300' : 'bg-blue-500/20 text-blue-300' }}">
                                    {{ $mov['tipo'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-black text-white">{{ $mov['item']->folio }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold uppercase {{ $mov['item']->estado == 'ENTREGADO' || $mov['item']->estado == 'COMPLETADO' ? 'text-green-400' : 'text-yellow-400' }}">
                                    {{ $mov['item']->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-white/70 uppercase">
                                @if($mov['tipo'] == 'VENTA')
                                    {{ $mov['item']->metodo_pago }}
                                @else
                                    {{ $mov['item']->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: 'PENDIENTE' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono font-black text-white text-sm">${{ number_format($mov['item']->total, 2) }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-white/50 text-sm">${{ number_format($mov['item']->saldo_pendiente, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <p class="text-blue-200/30 uppercase font-black tracking-widest italic">No hay movimientos registrados el día de hoy</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-white/10 border-t border-white/20">
                    <tr class="font-black text-white uppercase tracking-widest">
                        <td colspan="5" class="px-6 py-6 text-right text-blue-200">Gran Total del Día:</td>
                        <td class="px-6 py-6 font-mono text-xl">${{ number_format($totalG, 2) }}</td>
                        <td class="px-6 py-6 font-mono text-xl text-white/60">${{ number_format($saldoG, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
