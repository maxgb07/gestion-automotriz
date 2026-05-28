@extends('layouts.app')

@section('title', 'Estadísticas (Beta)')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

{{-- Helper Blade para calcular delta y retornar array con clase, icono y texto --}}
@php
    function kpiDelta($actual, $anterior, $esDinero = false) {
        $diff = $actual - $anterior;
        $pct  = $anterior > 0 ? round(($diff / $anterior) * 100, 1) : null;
        $up   = $diff >= 0;
        $cls  = $up ? 'text-emerald-400' : 'text-red-400';
        $icon = $up ? '↑' : '↓';
        $val  = $esDinero
            ? '$' . number_format(abs($diff), 2)
            : number_format(abs($diff));
        $pctStr = $pct !== null ? " ({$pct}%)" : '';
        return ['cls' => $cls, 'icon' => $icon, 'val' => $val, 'pct' => $pctStr, 'up' => $up];
    }
@endphp

@section('content')
{{-- =====================================================================
     ENCABEZADO
===================================================================== --}}
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Estadísticas del Negocio</h1>
        <p class="text-blue-200 text-lg">Resumen financiero y operativo</p>
    </div>
</div>

{{-- =====================================================================
     FILTROS
===================================================================== --}}
<div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-6 mb-8 shadow-2xl">
    <form method="GET" action="{{ route('estadisticas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
        <div>
            <label class="block text-md font-bold text-blue-100 mb-2 uppercase">Día Seleccionado</label>
            <input type="date" name="fecha_dia" value="{{ $fechaDia }}"
                class="block w-full px-4 py-3 bg-black/20 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-md font-bold text-blue-100 mb-2 uppercase">Mes Seleccionado</label>
            <input type="month" name="mes_anio" value="{{ $mesAnio }}"
                class="block w-full px-4 py-3 bg-black/20 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-md font-bold text-blue-100 mb-2 uppercase">Proveedor (Opcional)</label>
            <select name="proveedor_id" class="block w-full px-4 py-3 bg-black/20 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" class="text-black">-- Todos los Proveedores --</option>
                @foreach($proveedores as $prov)
                    <option value="{{ $prov->id }}" class="text-black" {{ $proveedorId == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl shadow-lg shadow-blue-600/30 transition-all uppercase text-md tracking-widest">
                Aplicar Filtros
            </button>
        </div>
    </form>
</div>

{{-- =====================================================================
     KPIs DEL DÍA
===================================================================== --}}
@php
    $dvC = kpiDelta($kpis['dia']['ventas_cantidad'],  $kpis['dia']['prev_ventas_cantidad']);
    $dvT = kpiDelta($kpis['dia']['ventas_total'],     $kpis['dia']['prev_ventas_total'],  true);
    $doC = kpiDelta($kpis['dia']['ordenes_cantidad'], $kpis['dia']['prev_ordenes_cantidad']);
    $doT = kpiDelta($kpis['dia']['ordenes_total'],    $kpis['dia']['prev_ordenes_total'], true);
@endphp

<div class="mb-8">
    <h2 class="text-md font-bold text-white mb-4 uppercase flex items-center gap-2">
        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Hoy — {{ $diaSeleccionado->translatedFormat('d F Y') }}
        <span class="text-md font-normal text-white/40 ml-2">vs {{ $kpis['dia']['prev_label'] }}</span>
    </h2>
    <div class="flex flex-col md:flex-row gap-6">
        {{-- Ventas Hoy --}}
        <div class="flex-1 bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl">
            <h3 class="text-md font-bold text-white mb-4 uppercase tracking-wider border-b border-white/10 pb-2">Ventas Mostrador</h3>
            <div class="flex justify-between items-end mb-3">
                <div>
                    <p class="text-blue-100/50 uppercase font-bold tracking-widest text-md mb-1">Total Ventas</p>
                    <p class="text-4xl font-black text-white font-mono tracking-tighter">{{ number_format($kpis['dia']['ventas_cantidad']) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-blue-100/50 uppercase font-bold tracking-widest text-md mb-1">Total Dinero</p>
                    <p class="text-4xl font-black text-white font-mono tracking-tighter">${{ number_format($kpis['dia']['ventas_total'], 2) }}</p>
                </div>
            </div>
            <div class="border-t border-white/10 pt-3 flex justify-between text-md">
                <span class="{{ $dvC['cls'] }} font-bold">{{ $dvC['icon'] }} {{ $dvC['val'] }} ventas{{ $dvC['pct'] }}</span>
                <span class="{{ $dvT['cls'] }} font-bold">{{ $dvT['icon'] }} {{ $dvT['val'] }}{{ $dvT['pct'] }}</span>
            </div>
        </div>
        {{-- Órdenes Hoy --}}
        <div class="flex-1 bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl">
            <h3 class="text-md font-bold text-white mb-4 uppercase tracking-wider border-b border-white/10 pb-2">Órdenes de Servicio</h3>
            <div class="flex justify-between items-end mb-3">
                <div>
                    <p class="text-emerald-100/50 uppercase font-bold tracking-widest text-md mb-1">Total Órdenes</p>
                    <p class="text-4xl font-black text-white font-mono tracking-tighter">{{ number_format($kpis['dia']['ordenes_cantidad']) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-emerald-100/50 uppercase font-bold tracking-widest text-md mb-1">Total Dinero</p>
                    <p class="text-4xl font-black text-white font-mono tracking-tighter">${{ number_format($kpis['dia']['ordenes_total'], 2) }}</p>
                </div>
            </div>
            <div class="border-t border-white/10 pt-3 flex justify-between text-md">
                <span class="{{ $doC['cls'] }} font-bold">{{ $doC['icon'] }} {{ $doC['val'] }} órdenes{{ $doC['pct'] }}</span>
                <span class="{{ $doT['cls'] }} font-bold">{{ $doT['icon'] }} {{ $doT['val'] }}{{ $doT['pct'] }}</span>
            </div>
        </div>
    </div>
</div>

{{-- =====================================================================
     KPIs DEL MES
===================================================================== --}}
@php
    $mvC = kpiDelta($kpis['mes']['ventas_cantidad'],  $kpis['mes']['prev_ventas_cantidad']);
    $mvT = kpiDelta($kpis['mes']['ventas_total'],     $kpis['mes']['prev_ventas_total'],  true);
    $moC = kpiDelta($kpis['mes']['ordenes_cantidad'], $kpis['mes']['prev_ordenes_cantidad']);
    $moT = kpiDelta($kpis['mes']['ordenes_total'],    $kpis['mes']['prev_ordenes_total'], true);
@endphp

<div class="mb-10">
    <h2 class="text-md font-bold text-white mb-4 uppercase flex items-center gap-2">
        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Mes — {{ $mesSeleccionado->translatedFormat('F Y') }}
        <span class="text-md font-normal text-white/40 ml-2">vs {{ $kpis['mes']['prev_label'] }}</span>
    </h2>
    <div class="flex flex-col md:flex-row gap-6">
        {{-- Ventas Mes --}}
        <div class="flex-1 bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl">
            <h3 class="text-md font-bold text-white mb-4 uppercase tracking-wider border-b border-white/10 pb-2">Ventas Mostrador</h3>
            <div class="flex justify-between items-end mb-3">
                <div>
                    <p class="text-blue-100/50 uppercase font-bold tracking-widest text-md mb-1">Total Ventas</p>
                    <p class="text-4xl font-black text-white font-mono tracking-tighter">{{ number_format($kpis['mes']['ventas_cantidad']) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-blue-100/50 uppercase font-bold tracking-widest text-md mb-1">Total Dinero</p>
                    <p class="text-4xl font-black text-white font-mono tracking-tighter">${{ number_format($kpis['mes']['ventas_total'], 2) }}</p>
                </div>
            </div>
            <div class="border-t border-white/10 pt-3 flex justify-between text-md">
                <span class="{{ $mvC['cls'] }} font-bold">{{ $mvC['icon'] }} {{ $mvC['val'] }} ventas{{ $mvC['pct'] }}</span>
                <span class="{{ $mvT['cls'] }} font-bold">{{ $mvT['icon'] }} {{ $mvT['val'] }}{{ $mvT['pct'] }}</span>
            </div>
        </div>
        {{-- Órdenes Mes --}}
        <div class="flex-1 bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl">
            <h3 class="text-md font-bold text-white mb-4 uppercase tracking-wider border-b border-white/10 pb-2">Órdenes de Servicio</h3>
            <div class="flex justify-between items-end mb-3">
                <div>
                    <p class="text-emerald-100/50 uppercase font-bold tracking-widest text-md mb-1">Total Órdenes</p>
                    <p class="text-4xl font-black text-white font-mono tracking-tighter">{{ number_format($kpis['mes']['ordenes_cantidad']) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-emerald-100/50 uppercase font-bold tracking-widest text-md mb-1">Total Dinero</p>
                    <p class="text-4xl font-black text-white font-mono tracking-tighter">${{ number_format($kpis['mes']['ordenes_total'], 2) }}</p>
                </div>
            </div>
            <div class="border-t border-white/10 pt-3 flex justify-between text-md">
                <span class="{{ $moC['cls'] }} font-bold">{{ $moC['icon'] }} {{ $moC['val'] }} órdenes{{ $moC['pct'] }}</span>
                <span class="{{ $moT['cls'] }} font-bold">{{ $moT['icon'] }} {{ $moT['val'] }}{{ $moT['pct'] }}</span>
            </div>
        </div>
    </div>
</div>

{{-- =====================================================================
     SECCIÓN: COMPARATIVA ENTRE DOS MESES
===================================================================== --}}
<div class="mb-10">
    <h2 class="text-md font-bold text-white mb-4 uppercase flex items-center gap-2">
        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Comparativa de Meses
    </h2>

    <div class="bg-white/5 border border-white/10 rounded-3xl p-6 shadow-xl overflow-x-auto">
        <table class="w-full text-md">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left text-white/50 uppercase font-bold pb-3 pr-6 text-md tracking-widest">Indicador</th>
                    <th class="text-right text-blue-300 uppercase font-bold pb-3 px-6 text-md tracking-widest">{{ $comparativa['mesA']['label'] }}</th>
                    <th class="text-right text-purple-300 uppercase font-bold pb-3 px-6 text-md tracking-widest">{{ $comparativa['mesB']['label'] }}</th>
                    <th class="text-right text-white/50 uppercase font-bold pb-3 pl-6 text-md tracking-widest">Diferencia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @php
                    $rows = [
                        ['Ventas (cantidad)',   'ventas_cantidad',  false],
                        ['Ventas (monto)',      'ventas_total',     true],
                        ['Órdenes (cantidad)', 'ordenes_cantidad', false],
                        ['Órdenes (monto)',    'ordenes_total',    true],
                        ['Compras / Egresos',  'compras_total',    true],
                        ['Ingresos Totales',   'ingresos',         true],
                    ];
                @endphp
                @foreach($rows as [$label, $key, $isDinero])
                    @php
                        $a    = $comparativa['mesA'][$key];
                        $b    = $comparativa['mesB'][$key];
                        $d    = kpiDelta($a, $b, $isDinero);
                        $aFmt = $isDinero ? '$' . number_format($a, 2) : number_format($a);
                        $bFmt = $isDinero ? '$' . number_format($b, 2) : number_format($b);
                    @endphp
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="py-3 pr-6 text-white/70 font-semibold text-md">{{ $label }}</td>
                        <td class="py-3 px-6 text-right text-white font-mono font-bold text-md">{{ $aFmt }}</td>
                        <td class="py-3 px-6 text-right text-white/60 font-mono text-md">{{ $bFmt }}</td>
                        <td class="py-3 pl-6 text-right font-bold text-md {{ $d['cls'] }}">
                            {{ $d['icon'] }} {{ $d['val'] }}{{ $d['pct'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- =====================================================================
     GRÁFICAS
===================================================================== --}}
<div class="grid grid-cols-1 gap-8 mb-8">

    {{-- Gráfica 1: Ingresos vs Egresos Anual --}}
    <div class="bg-white/5 border border-white/10 rounded-3xl p-6 shadow-xl">
        <h3 class="text-md font-bold text-white mb-1 uppercase">Ingresos vs Egresos — {{ $mesSeleccionado->year }}</h3>
        <p class="text-md text-blue-200/50 mb-4 uppercase">Totales mensuales (ventas + órdenes = ingresos)</p>
        <canvas id="chartIngresosEgresos" style="width:100%;height:500px;"></canvas>
    </div>

    {{-- Gráfica 2: Compras por Proveedor (Barras) --}}
    <div class="bg-white/5 border border-white/10 rounded-3xl p-6 shadow-xl">
        <h3 class="text-md font-bold text-white mb-1 uppercase">Compras por Proveedor</h3>
        <p class="text-md text-blue-200/50 mb-4 uppercase">Mes: {{ $mesSeleccionado->translatedFormat('F Y') }}</p>
        @if(count($chartProveedores['labels']) > 0)
            <canvas id="chartProvBars" style="width:100%;height:500px;"></canvas>
        @else
            <div class="flex items-center justify-center h-48">
                <p class="text-white/40 uppercase font-bold text-md">No hay compras en el mes seleccionado.</p>
            </div>
        @endif
    </div>

    {{-- Gráfica 3: Distribución de Compras (Pastel) --}}
    <div class="bg-white/5 border border-white/10 rounded-3xl p-6 shadow-xl">
        <h3 class="text-md font-bold text-white mb-1 uppercase">Distribución de Compras por Proveedor</h3>
        <p class="text-md text-blue-200/50 mb-4 uppercase">Total Mes: ${{ number_format($chartProveedores['total_mes'], 2) }}</p>
        @if(count($chartProveedores['labels']) > 0)
            <canvas id="chartProvPie" style="width:100%;height:500px;"></canvas>
        @else
            <div class="flex items-center justify-center h-48">
                <p class="text-white/40 uppercase font-bold text-md">No hay compras en el mes seleccionado.</p>
            </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Defaults globales Chart.js
    Chart.defaults.color = 'rgba(255,255,255,0.75)';
    Chart.defaults.font.family = "'Inter','system-ui','sans-serif'";
    Chart.defaults.font.size   = 13;

    const fmtUSD = v => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(v);

    // -----------------------------------------------------------------------
    // 1. Ingresos vs Egresos (barras agrupadas)
    // -----------------------------------------------------------------------
    const ctxIE = document.getElementById('chartIngresosEgresos');
    if (ctxIE) {
        ctxIE.height = 500;
        new Chart(ctxIE, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartIngresosEgresos['labels']) !!},
                datasets: [
                    {
                        label: 'Ingresos',
                        data: {!! json_encode($chartIngresosEgresos['ingresos']) !!},
                        backgroundColor: 'rgba(16,185,129,0.65)',
                        borderColor: 'rgb(16,185,129)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Egresos',
                        data: {!! json_encode($chartIngresosEgresos['egresos']) !!},
                        backgroundColor: 'rgba(239,68,68,0.65)',
                        borderColor: 'rgb(239,68,68)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Pagos Realizados',
                        data: {!! json_encode($chartIngresosEgresos['pagos_realizados']) !!},
                        backgroundColor: 'rgba(245,158,11,0.65)',
                        borderColor: 'rgb(245,158,11)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: { label: ctx => ' ' + fmtUSD(ctx.parsed.y) }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.08)' },
                        ticks: { callback: v => '$' + (v/1000).toLocaleString() + 'k' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Colores compartidos proveedores
    const bgColors = [
        'rgba(59,130,246,0.75)','rgba(16,185,129,0.75)','rgba(245,158,11,0.75)',
        'rgba(239,68,68,0.75)','rgba(139,92,246,0.75)','rgba(236,72,153,0.75)',
        'rgba(14,165,233,0.75)','rgba(249,115,22,0.75)','rgba(168,85,247,0.75)',
        'rgba(20,184,166,0.75)'
    ];
    const borderColors = [
        'rgb(59,130,246)','rgb(16,185,129)','rgb(245,158,11)',
        'rgb(239,68,68)','rgb(139,92,246)','rgb(236,72,153)',
        'rgb(14,165,233)','rgb(249,115,22)','rgb(168,85,247)',
        'rgb(20,184,166)'
    ];

    const provLabels    = {!! json_encode($chartProveedores['labels']) !!};
    const provMontos    = {!! json_encode($chartProveedores['montos']) !!};
    const provPorcentajes = {!! json_encode($chartProveedores['porcentajes']) !!};

    // -----------------------------------------------------------------------
    // 2. Compras por Proveedor (barras horizontales para que los nombres se lean bien)
    // -----------------------------------------------------------------------
    const ctxProvBars = document.getElementById('chartProvBars');
    if (ctxProvBars && provLabels.length > 0) {
        ctxProvBars.height = 500;
        new Chart(ctxProvBars, {
            type: 'bar',
            data: {
                labels: provLabels.slice(0, 10),
                datasets: [{
                    label: 'Compras ($)',
                    data: provMontos.slice(0, 10),
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',          // <<< barras HORIZONTALES — nombres legibles
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => ' ' + fmtUSD(ctx.parsed.x) }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.08)' },
                        ticks: { callback: v => '$' + (v/1000).toLocaleString() + 'k' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 12 } }
                    }
                }
            }
        });
    }

    // -----------------------------------------------------------------------
    // 3. Distribución por Proveedor (doughnut)
    // -----------------------------------------------------------------------
    const ctxProvPie = document.getElementById('chartProvPie');
    if (ctxProvPie && provLabels.length > 0) {
        ctxProvPie.height = 500;
        new Chart(ctxProvPie, {
            type: 'doughnut',
            data: {
                labels: provLabels.slice(0, 10),
                datasets: [{
                    data: provPorcentajes.slice(0, 10),
                    backgroundColor: bgColors,
                    borderColor: 'rgba(15,23,42,1)',
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 14, padding: 18, font: { size: 12 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const pct   = ctx.parsed;
                                const monto = provMontos[ctx.dataIndex];
                                return ` ${ctx.label}: ${pct}% (${fmtUSD(monto)})`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }

});
</script>
@endpush
