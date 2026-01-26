@extends('layouts.app')

@section('title', 'Reporte de Ventas')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="mb-10">
        <a href="{{ route('reportes.index') }}" class="inline-flex items-center text-blue-300 hover:text-white transition-colors mb-4 group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver a Reportes
        </a>
        <h1 class="text-4xl font-bold text-white uppercase tracking-tight">Reporte de Ventas</h1>
    </div>

    <!-- Filtros -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-2xl">
        <form action="{{ route('reportes.ventas') }}" method="GET" class="flex flex-col md:flex-row items-end gap-6">
            <div class="flex-grow">
                <label class="block text-xs font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fecha_inicio }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>
            <div class="flex-grow">
                <label class="block text-xs font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fecha_fin }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-8 py-3 bg-white/10 hover:bg-white/20 text-white text-xs font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center border border-white/10">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filtrar
                </button>
                <a href="{{ route('reportes.ventas.pdf', ['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]) }}" target="_blank" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Fecha</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest text-left">Folio</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest text-left">Cliente</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Método de Pago</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Estado</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Total</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($ventas as $venta)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-white/70">{{ $venta->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm font-black text-white text-left">{{ $venta->folio }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-blue-100/60 text-left">{{ $venta->cliente->nombre }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-white/70 uppercase">{{ $venta->metodo_pago }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-black uppercase {{ $venta->estado == 'COMPLETADO' ? 'text-green-400' : 'text-yellow-400' }}">
                                    {{ $venta->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono font-black text-white text-sm">${{ number_format($venta->total, 2) }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-white/50 text-sm">${{ number_format($venta->saldo_pendiente, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <p class="text-blue-200/30 uppercase font-black tracking-widest italic">No hay ventas registradas en este periodo</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($ventas->count() > 0)
                <tfoot class="bg-white/10 border-t border-white/20">
                    <tr class="font-black text-white uppercase tracking-widest">
                        <td colspan="5" class="px-6 py-6 text-right text-blue-200">Total Periodo:</td>
                        <td class="px-6 py-6 font-mono text-xl">${{ number_format($ventas->sum('total'), 2) }}</td>
                        <td class="px-6 py-6 font-mono text-xl text-white/60">${{ number_format($ventas->sum('saldo_pendiente'), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
