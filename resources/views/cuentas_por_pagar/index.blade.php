@extends('layouts.app')

@section('title', 'Cuentas por Pagar')

@section('content')
    <div x-data="{ showCalendar: false, calendarInit: false }" x-init="$watch('showCalendar', val => { if(val && !calendarInit) { setTimeout(() => initCalendar(), 100); calendarInit = true; } })">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Cuentas por Pagar</h1>
            <p class="text-blue-200">Gestión de saldos, pagos y notas de crédito con proveedores</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button @click="showCalendar = true" class="w-fit inline-flex items-center px-4 py-2 text-white font-black rounded-lg shadow-lg transition-all text-sm uppercase tracking-widest" style="background-color: #059669;">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Calendario de Pagos
            </button>
            <a href="{{ route('cuentas_por_pagar.pdf_global') }}" target="_blank" class="w-fit inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-lg shadow-lg shadow-indigo-900/40 transition-all text-sm uppercase tracking-widest cursor-pointer" style="background-color: #4f46e5;">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Estado de Cuenta
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
        <form action="{{ route('cuentas_por_pagar.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="md:flex-[3] relative w-full">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Buscar Proveedor</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="NOMBRE DEL PROVEEDOR..." class="block w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm uppercase">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-fit px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase">
                    BUSCAR
                </button>
                @if(request('buscar'))
                    <a href="{{ route('cuentas_por_pagar.index') }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center uppercase text-sm">
                        LIMPIAR
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($proveedores->count() > 0)
        <!-- Grid de Proveedores con Saldo -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($proveedores as $proveedor)
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-6 border border-white/20 shadow-2xl hover:bg-white/15 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-xl font-bold text-white uppercase">{{ $proveedor->nombre }}</h2>
                            @if($proveedor->facturas_vencidas > 0)
                                <span class="px-3 py-1 bg-red-500/20 border border-red-500/50 text-red-300 text-xs font-bold rounded-full uppercase">
                                    {{ $proveedor->facturas_vencidas }} Vencidas
                                </span>
                            @endif
                        </div>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center bg-white/5 p-3 rounded-xl border border-white/10">
                                <span class="text-blue-200 text-sm font-semibold uppercase">Deuda Total</span>
                                <span class="text-white font-black text-xl">${{ number_format($proveedor->total_deuda, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center bg-emerald-500/10 p-3 rounded-xl border border-emerald-500/20">
                                <span class="text-emerald-200 text-sm font-semibold uppercase">Saldo a Favor (NC)</span>
                                <span class="text-emerald-400 font-black text-xl">${{ number_format($proveedor->saldo_favor, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('cuentas_por_pagar.show', $proveedor) }}" class="w-full block text-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase">
                        Ver Estado de Cuenta
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="w-full flex flex-col items-center justify-center min-h-[400px] bg-white/10 backdrop-blur-xl rounded-3xl p-12 border border-white/20 text-center shadow-2xl mt-8">
            <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <svg class="w-12 h-12 text-emerald-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-2xl font-black text-white tracking-tight mb-2 uppercase">¡Todo al Corriente!</p>
            <p class="text-md text-emerald-200/80 uppercase tracking-widest font-semibold">No hay deudas pendientes con ningún proveedor.</p>
        </div>
    @endif

        <!-- Modal del Calendario -->
        <div x-show="showCalendar" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition.opacity>
            <div class="bg-[#111827] rounded-3xl w-full max-w-[90vw] flex flex-col max-h-[95vh] shadow-2xl border border-white/10" @click.away="showCalendar = false">
                <div class="flex justify-between items-center p-6 border-b border-white/10">
                    <h2 class="text-2xl font-bold text-white uppercase tracking-tight">Calendario de Pagos</h2>
                    <button @click="showCalendar = false" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto bg-white/5 rounded-b-3xl flex-1">
                    <style>
                        .fc-theme-standard .fc-scrollgrid { border-color: #e5e7eb; }
                        .fc-theme-standard th, .fc-theme-standard td { border-color: #e5e7eb; }
                        .fc-day-today { background-color: #f3f4f6 !important; }
                        .fc-toolbar-title { font-weight: 700; text-transform: uppercase; }
                        .fc-button-primary { background-color: #4f46e5 !important; border-color: #4338ca !important; }
                        .fc-button-primary:hover { background-color: #4338ca !important; }
                        .fc-event { cursor: pointer; }
                    </style>
                    <div id="calendario" class="bg-white rounded-xl p-4 min-h-[700px] text-gray-800"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <!-- Popper & Tippy (Tooltips) -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script>
        function initCalendar() {
            var calendarEl = document.getElementById('calendario');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                height: 800,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: '{{ route('cuentas_por_pagar.calendario_eventos') }}',
                eventMouseEnter: function(info) {
                    var props = info.event.extendedProps;
                    var content = `
                        <div style="text-align: left; padding: 4px; font-family: ui-sans-serif, system-ui, sans-serif;">
                            <strong style="display: block; color: #a5b4fc; font-size: 1.1em; margin-bottom: 4px;">${props.proveedor}</strong>
                            <span style="display: block; margin-bottom: 2px;">Folio de Factura: ${props.factura}</span>
                            <span style="display: block; color: #fca5a5; font-weight: bold;">Saldo Pendiente: $${props.saldo_pendiente}</span>
                        </div>
                    `;
                    
                    if(!info.event._tippy) {
                        tippy(info.el, {
                            content: content,
                            allowHTML: true,
                            placement: 'top',
                            theme: 'light-border',
                        });
                        info.event._tippy = true;
                    }
                }
            });
            calendar.render();
        }
    </script>
@endsection
