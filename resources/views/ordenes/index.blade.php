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
                    <option value="">TODOS</option>
                    <option value="ENTREGADO" {{ request('estado') == 'ENTREGADO' ? 'selected' : '' }}>ENTREGADO</option>
                    <option value="FINALIZADO" {{ request('estado') == 'FINALIZADO' ? 'selected' : '' }}>FINALIZADO</option>
                    <option value="PENDIENTE DE PAGO" {{ request('estado') == 'PENDIENTE DE PAGO' ? 'selected' : '' }}>PENDIENTE PAGO</option>
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
    </div>

    <!-- Tabs de Filtrado -->
    <div class="flex flex-wrap items-center gap-2 mb-4">
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
    @php
        $currentSort = request('sort', 'folio');
        $currentDir = request('direction', 'desc');

        $sortUrl = function($column) use ($currentSort, $currentDir) {
            $newDir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDir]);
        };

        $sortIcon = function($column) use ($currentSort, $currentDir) {
            $isActive = ($currentSort === $column);
            $upColor = ($isActive && $currentDir === 'asc') ? 'text-blue-400' : 'text-blue-200/20';
            $downColor = ($isActive && $currentDir === 'desc') ? 'text-blue-400' : 'text-blue-200/20';
            
            return '
                <span class="inline-flex flex-col ml-2 transform translate-y-0.5">
                    <svg class="w-2 h-2 ' . $upColor . ' fill-current" viewBox="0 0 24 24"><path d="M12 5l-8 8h16l-8-8z"/></svg>
                    <svg class="w-2 h-2 ' . $downColor . ' fill-current" viewBox="0 0 24 24"><path d="M12 19l8-8H4l8 8z"/></svg>
                </span>
            ';
        };
    @endphp
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="bg-white/5 border-b border-white/10 font-bold uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('folio') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Folio {!! $sortIcon('folio') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('entrada') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Entrada {!! $sortIcon('entrada') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('cliente') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Cliente {!! $sortIcon('cliente') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('vehiculo') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Vehículo {!! $sortIcon('vehiculo') !!}
                            </a>
                        </th>
                        <!-- <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Kilometraje</th> -->
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('total') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Total / Saldo {!! $sortIcon('total') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('factura') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Factura {!! $sortIcon('factura') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">
                            <a href="{{ $sortUrl('estado') }}" class="inline-flex items-center hover:text-white transition-colors">
                                Estado {!! $sortIcon('estado') !!}
                            </a>
                        </th>
                        <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($ordenes as $orden)
                        <tr class="hover:bg-white/5 transition-colors group"
                            @if($orden->estado !== 'RECEPCION')
                            data-orden="{{ json_encode([
                                'folio' => $orden->folio,
                                'fecha' => \Carbon\Carbon::parse($orden->fecha_entrada)->translatedFormat('d M, Y'),
                                'cliente' => $orden->cliente->nombre ?? 'N/A',
                                'telefono' => $orden->cliente->celular ?: ($orden->cliente->telefono ?: 'N/A'),
                                'vehiculo' => [
                                    'marca' => $orden->vehiculo->marca ?? 'N/A',
                                    'modelo' => $orden->vehiculo->modelo ?? 'N/A',
                                    'anio' => $orden->vehiculo->anio ?? 'N/A',
                                    'placas' => $orden->placas ?: ($orden->vehiculo->placas ?? 'N/A'),
                                    'km' => $orden->kilometraje_entrega ?: ($orden->kilometraje_entrada ?: 0)
                                ],
                                'detalles' => $orden->detalles->map(fn($d) => [
                                    'cantidad' => number_format($d->cantidad, 2),
                                    'tipo_pill_bg' => $d->producto_id ? 'bg-blue-500/20' : 'bg-purple-500/20',
                                    'tipo_pill_text' => $d->producto_id ? 'text-blue-300' : 'text-purple-300',
                                    'tipo' => $d->producto_id ? 'PRODUCTO' : 'SERVICIO',
                                    'clave' => $d->producto_id ? ($d->producto->nombre ?? 'N/A') : ($d->servicio->nombre ?? 'N/A'),
                                    'descripcion' => $d->producto_id ? ($d->producto->descripcion ?? '---') : ($d->servicio->descripcion ?? '---'),
                                    'notas' => $d->notas ?? '---',
                                    'precio' => number_format($d->precio_unitario, 2),
                                    'importe' => number_format($d->subtotal, 2)
                                ])->values(),
                                'total' => number_format($orden->total, 2),
                                'estado' => $orden->estado,
                                'mecanico' => $orden->mecanico ? mb_strtoupper($orden->mecanico) : 'NO ASIGNADO',
                                'falla_reportada' => $orden->falla_reportada ?? 'NO ESPECIFICADA',
                                'observaciones_post' => $orden->observaciones_post_reparacion ?? ''
                            ]) }}"
                            @endif
                        >
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
                                    @if($orden->estado !== 'RECEPCION')
                                        <button onclick="vistaRapidaOrden(this)" class="p-2 bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 rounded-lg border border-purple-500/10 transition-all cursor-pointer" title="VISTA RÁPIDA">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        
                                        @if(in_array(auth()->id(), [1, 4]) && ($orden->estado === 'FINALIZADO' || ($orden->estado === 'PENDIENTE DE PAGO' && $orden->pagos->count() === 0)))
                                            <button onclick="revertirOrden({{ $orden->id }}, '{{ $orden->folio }}')" 
                                                    class="p-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-400 rounded-lg border border-orange-500/20 transition-all cursor-pointer" 
                                                    title="REGRESAR A REPARACIÓN">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    @endif

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

                                    @if(in_array($orden->estado, ['FINALIZADO', 'PENDIENTE DE PAGO']))
                                        <button onclick="abrirModalPago({{ $orden->id }}, {{ $orden->total }}, {{ $orden->saldo_pendiente }}, '{{ $orden->requiere_factura }}')" 
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

                                    @if(in_array($orden->estado, ['FINALIZADO', 'ENTREGADO']))
                                        <button onclick="compartirWhatsapp('{{ $orden->cliente->celular }}', '{{ $orden->share_token }}', '{{ $orden->folio }}', '{{ $orden->vehiculo->marca }}', '{{ $orden->vehiculo->modelo }}')" 
                                                class="p-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all cursor-pointer" 
                                                title="ENVIAR POR WHATSAPP">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.863-9.864.001-2.63-1.019-5.1-2.875-6.958C16.29 1.925 13.82 .906 11.999.906c-5.441 0-9.865 4.422-9.867 9.867-.001 1.765.467 3.488 1.355 5.017l-.989 3.615 3.702-.971c1.51.823 3.072 1.226 4.757 1.22zM17.51 14.9c-.29-.145-1.713-.846-1.978-.941-.264-.096-.457-.145-.65.145-.19.29-.738.941-.905 1.133-.166.19-.333.213-.622.068-2.905-1.455-4.807-4.3-5.385-5.293-.166-.29-.018-.446.126-.59.13-.13.29-.338.435-.507.145-.17.193-.29.29-.483.097-.19.048-.362-.024-.507-.072-.145-.65-1.57-.89-2.15-.234-.567-.472-.489-.65-.498-.17-.008-.362-.01-.555-.01-.19 0-.507.072-.772.362-.265.29-1.013.99-1.013 2.415 0 1.42 1.037 2.797 1.182 2.99.145.19 2.04 3.115 4.938 4.368.689.298 1.229.476 1.649.61.692.22 1.322.19 1.82.115.556-.084 1.712-.7 1.953-1.376.24-.676.24-1.255.168-1.376-.07-.12-.26-.19-.55-.336z"/>
                                            </svg>
                                        </button>
                                    @endif
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

        function abrirModalPago(ordenId, total, saldo, requiereFacturaActual = 'NO') {
            let pagos = [
                { metodo: '', monto: saldo, referencia: '' }
            ];

            const renderPagos = () => {
                let html = `
                    <div class="p-2 space-y-4 text-left">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-md font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">FECHA PAGO *</label>
                                <input type="date" id="modal_fecha_pago" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-mono" value="${new Date().toISOString().split('T')[0]}">
                            </div>
                            <div>
                                <label class="block text-md font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">¿REQUIERE FACTURA?</label>
                                <select id="modal_requiere_factura" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase">
                                    <option value="NO" ${requiereFacturaActual === 'NO' ? 'selected' : ''} class="text-black">NO</option>
                                    <option value="SI" ${requiereFacturaActual === 'SI' ? 'selected' : ''} class="text-black">SI</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-between items-center bg-white/5 p-4 rounded-xl border border-white/5 mb-4">
                            <span class="text-md font-black text-slate-500 uppercase tracking-widest">Saldo Pendiente:</span>
                            <span class="text-xl font-black text-blue-400 font-mono italic">$ ${new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(saldo)}</span>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-white/10 bg-white/5 mb-4">
                            <table class="w-full text-center border-collapse">
                                <thead class="bg-white/5 border-b border-white/10 text-md font-bold text-blue-300 uppercase tracking-widest">
                                    <tr>
                                        <th class="px-4 py-3 text-center">MÉTODO</th>
                                        <th class="px-4 py-3 w-40 text-center">MONTO</th>
                                        <th class="px-4 py-3 text-center">REFERENCIA</th>
                                        <th class="px-2 py-3 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody id="lista-pagos" class="divide-y divide-white/5 text-white">
                                    ${pagos.map((p, index) => `
                                        <tr class="group hover:bg-white/5 transition-colors">
                                            <td class="px-2 py-3">
                                                <select onchange="window.updatePago(${index}, 'metodo', this.value)" class="w-full px-2 py-2 bg-slate-800 border border-white/10 rounded-lg text-white text-md font-bold focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all uppercase">
                                                    <option value="">-- SELECCIONAR --</option>
                                                    <option value="EFECTIVO" ${p.metodo === 'EFECTIVO' ? 'selected' : ''}>EFECTIVO</option>
                                                    <option value="CHEQUE" ${p.metodo === 'CHEQUE' ? 'selected' : ''}>CHEQUE</option>
                                                    <option value="TRANSFERENCIA" ${p.metodo === 'TRANSFERENCIA' ? 'selected' : ''}>TRANSFERENCIA</option>
                                                    <option value="TARJETA DE DÉBITO" ${p.metodo === 'TARJETA DE DÉBITO' ? 'selected' : ''}>TARJETA DE DÉBITO</option>
                                                    <option value="TARJETA DE CRÉDITO" ${p.metodo === 'TARJETA DE CRÉDITO' ? 'selected' : ''}>TARJETA DE CRÉDITO</option>
                                                    <option value="CRÉDITO 15 DÍAS" ${p.metodo === 'CRÉDITO 15 DÍAS' ? 'selected' : ''}>CRÉDITO 15 DÍAS</option>
                                                </select>
                                            </td>
                                            <td class="px-2 py-3">
                                                <input type="number" step="0.01" value="${parseFloat(p.monto).toFixed(2)}" onfocus="this.select()" oninput="window.updatePago(${index}, 'monto', this.value)" ${p.metodo === 'CRÉDITO 15 DÍAS' ? 'readonly' : ''} class="w-full px-2 py-2 bg-slate-800 border border-white/10 rounded-lg text-white text-md font-bold focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all text-center font-mono">
                                            </td>
                                            <td class="px-2 py-3">
                                                <input type="text" value="${p.referencia}" oninput="window.updatePago(${index}, 'referencia', this.value)" class="w-full px-2 py-2 bg-slate-800 border border-white/10 rounded-lg text-white text-md font-bold uppercase focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all" placeholder="REFERENCIA">
                                            </td>
                                            <td class="px-2 py-3 text-center">
                                                ${pagos.length > 1 ? `
                                                    <button onclick="window.removePago(${index})" class="p-1.5 bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white rounded-lg transition-all">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                ` : ''}
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>

                        <div id="resumen-pago" class="p-4 bg-white/5 rounded-2xl border border-white/10 mt-4 space-y-2">
                            <div class="flex justify-between items-center text-md font-black uppercase tracking-widest">
                                <span class="text-slate-500">Total Ingresado:</span>
                                <span id="total-ingresado" class="text-white text-md">$ 0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-md font-black uppercase tracking-widest">
                                <span class="text-slate-500">Restante:</span>
                                <span id="monto-restante" class="text-green-400 text-md">$ 0.00</span>
                            </div>
                        </div>

                        <div class="flex justify-center mt-4">
                            <button type="button" onclick="window.addPago()" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-2xl transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40 border border-white/10">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Agregar Otro Método
                            </button>
                        </div>
                    </div>
                `;
                return html;
            };

            const updateResumen = () => {
                const totalIngresado = pagos.reduce((acc, p) => acc + (parseFloat(p.monto) || 0), 0);
                const restante = saldo - totalIngresado;

                const totalEl = document.getElementById('total-ingresado');
                const restanteEl = document.getElementById('monto-restante');

                if (totalEl) totalEl.textContent = `$ ${new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(totalIngresado)}`;
                if (restanteEl) {
                    restanteEl.textContent = `$ ${new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(restante)}`;
                    if (restante < 0) {
                        restanteEl.classList.remove('text-green-400');
                        restanteEl.classList.add('text-red-400');
                    } else {
                        restanteEl.classList.remove('text-red-400');
                        restanteEl.classList.add('text-green-400');
                    }
                }
            };

            window.updatePago = (index, field, value) => {
                if (field === 'metodo' && value === 'CRÉDITO 15 DÍAS') {
                    pagos[index].monto = 0;
                }
                pagos[index][field] = value;
                
                if (field === 'metodo' && value !== 'CRÉDITO 15 DÍAS' && pagos[index].monto == 0) {
                    const sumOthers = pagos.reduce((acc, p, i) => i !== index ? acc + (parseFloat(p.monto) || 0) : acc, 0);
                    pagos[index].monto = Math.max(0, saldo - sumOthers);
                }

                const popup = Swal.getPopup();
                if (popup && field === 'metodo') {
                    Swal.update({ html: renderPagos() });
                }
                updateResumen();
            };

            window.addPago = () => {
                const totalIngresado = pagos.reduce((acc, p) => acc + (parseFloat(p.monto) || 0), 0);
                const nuevoMonto = Math.max(0, saldo - totalIngresado);
                pagos.push({ metodo: '', monto: nuevoMonto, referencia: '' });
                Swal.update({ html: renderPagos() });
                updateResumen();
            };

            window.removePago = (index) => {
                pagos.splice(index, 1);
                Swal.update({ html: renderPagos() });
                updateResumen();
            };

            Swal.fire({
                title: 'REGISTRAR PAGO',
                background: '#1e293b',
                color: '#fff',
                html: renderPagos(),
                showCancelButton: true,
                confirmButtonText: 'REGISTRAR PAGO',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#ef4444',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl w-[95%] max-w-4xl',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm'
                },
                width: '850px',
                didOpen: () => {
                    updateResumen();
                },
                preConfirm: () => {
                    const totalIngresado = pagos.reduce((acc, p) => acc + (parseFloat(p.monto) || 0), 0);
                    const fecha = document.getElementById('modal_fecha_pago').value;
                    const factura = document.getElementById('modal_requiere_factura').value;

                    if (pagos.some(p => p.metodo === '')) {
                        Swal.showValidationMessage('Todos los pagos deben tener un método seleccionado');
                        return false;
                    }

                    if (totalIngresado > saldo + 0.01) {
                        Swal.showValidationMessage('La suma de los pagos no puede superar el saldo pendiente');
                        return false;
                    }

                    if (totalIngresado <= 0 && !pagos.some(p => p.metodo === 'CRÉDITO 15 DÍAS')) {
                        Swal.showValidationMessage('Debe ingresar al menos un monto de pago');
                        return false;
                    }

                    if (!fecha) {
                        Swal.showValidationMessage('Debe seleccionar la fecha de pago');
                        return false;
                    }

                    return { 
                        pagos: pagos.map(p => ({
                            metodo_pago: p.metodo,
                            monto: p.monto,
                            referencia: p.referencia
                        })),
                        requiere_factura: factura,
                        fecha_pago: fecha
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
                                    title: '¡LISTO!',
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
                delete window.addPago;
                delete window.removePago;
                delete window.updatePago;
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



        const popupClass = 'rounded-3xl border-none shadow-2xl';

        function revertirOrden(id, folio) {
            Swal.fire({
                title: '¿REGRESAR A REPARACIÓN?',
                text: `La orden ${folio} volverá al estado de taller. Esta acción solo es permitida por administración.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'SÍ, REGRESAR A TALLER',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#475569',
                background: '#1e293b',
                color: '#fff',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl transition-all duration-300',
                    title: 'text-xl font-black uppercase tracking-tighter pt-6',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        background: '#1e293b',
                        color: '#fff',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: `/ordenes/${id}/revertir`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡LISTO!',
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
                                text: data.message || 'Error al procesar la solicitud',
                                background: '#1e293b',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        }

        function vistaRapidaOrden(btn) {
            try {
                const row = btn.closest('tr');
                if (!row.dataset.orden) {
                    console.error("Falta el atributo data-orden en el renglón.");
                    return;
                }
                
                const o = JSON.parse(row.dataset.orden);
                let estadoColor = '#ef4444'; // Default red
                if (o.estado === 'FINALIZADO') estadoColor = '#2dd4bf'; // teal
                if (o.estado === 'REPARACION') estadoColor = '#f59e0b'; // amber
                if (o.estado === 'ENTREGADO') estadoColor = '#4ade80'; // green

                Swal.fire({
                    title: 'ORDEN ' + o.folio,
                    width: '1400px',
                    background: '#1e293b',
                    color: '#fff',
                    html: `
                        <div style="text-align:left; margin-top:6px;">
                            <!-- Fechas y Cliente -->
                            <div class="grid grid-cols-2 gap-4 mb-4 border-b border-white/10 pb-4">
                                <div>
                                    <p class="text-blue-300 uppercase font-black text-md">Fecha de Reparación</p>
                                    <p class="text-white text-md">${o.fecha}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-300 uppercase font-black text-md">Estado</p>
                                    <p class="text-md font-bold" style="color: ${estadoColor}">${o.estado}</p>
                                </div>
                                <div class="col-span-2 grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-blue-300 uppercase font-black text-md mb-1">Cliente</p>
                                        <p class="text-white text-md font-bold uppercase">${o.cliente}</p>
                                    </div>
                                    <div>
                                        <p class="text-blue-300 uppercase font-black text-md mb-1">Teléfono / Celular</p>
                                        <p class="text-white text-md font-bold uppercase">${o.telefono}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Datos del Vehículo -->
                            <div class="mb-4 border-b border-white/10 pb-4">
                                <p class="text-blue-300 uppercase font-black text-md mb-2">Datos del Vehículo</p>
                                <div class="grid grid-cols-3 gap-2 bg-white/5 p-3 rounded-xl border border-white/10 text-center">
                                    <div>
                                        <p class="text-blue-200 uppercase font-black text-md">Marca/Modelo</p>
                                        <p class="text-white text-md font-bold whitespace-nowrap">${o.vehiculo.marca} ${o.vehiculo.modelo} (${o.vehiculo.anio})</p>
                                    </div>
                                    <div class="border-l border-white/10">
                                        <p class="text-blue-200 uppercase font-black text-md">Placas</p>
                                        <p class="text-white text-md font-bold">${o.vehiculo.placas}</p>
                                    </div>
                                    <div class="border-l border-white/10">
                                        <p class="text-blue-200 uppercase font-black text-md">KM Entrega</p>
                                        <p class="text-white text-md font-bold">${o.vehiculo.km}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Falla Reportada -->
                            <div class="mb-4">
                                <p class="text-blue-300 uppercase font-black text-md mb-2">Falla Reportada / Motivo de Ingreso</p>
                                <div class="bg-amber-500/5 border border-amber-500/10 rounded-xl p-3">
                                    <p class="text-amber-200/80 text-md font-bold uppercase leading-relaxed">
                                        "${o.falla_reportada}"
                                    </p>
                                </div>
                            </div>

                            <!-- Detalle de Reparación -->
                            <div class="flex justify-between items-end mb-2">
                                <p class="text-blue-300 uppercase font-black text-md">Detalle de Reparación / Cotización</p>
                                ${o.mecanico !== 'NO ASIGNADO' ? `
                                <div class="flex items-center gap-2 bg-blue-500/20 px-4 py-2 rounded-xl border border-blue-500/30">
                                    <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-xs font-black text-blue-100 uppercase tracking-widest">MECÁNICO: <span class="text-white">${o.mecanico}</span></span>
                                </div>
                                ` : ''}
                            </div>
                            <div class="overflow-x-auto rounded-xl border border-white/10 mb-4 bg-white/5">
                                <table class="w-full text-center border-collapse">
                                    <thead class="bg-white/5 border-b border-white/10 text-sm font-bold text-blue-200 uppercase tracking-widest text-left">
                                        <tr>
                                            <th class="px-2 py-4 w-28 text-center whitespace-nowrap">CANTIDAD</th>
                                            <th class="px-6 py-4 whitespace-nowrap">TIPO</th>
                                            <th class="px-6 py-4 whitespace-nowrap">CLAVE</th>
                                            <th class="px-6 py-4 whitespace-nowrap">DESCRIPCIÓN</th>
                                            <th class="px-6 py-4 whitespace-nowrap">NOTAS</th>
                                            <th class="px-4 py-4 w-32 text-center whitespace-nowrap">PRECIO</th>
                                            <th class="px-6 py-4 w-40 text-right whitespace-nowrap">IMPORTE</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5 text-md text-white text-left">
                                        ${o.detalles.length === 0 ? '<tr><td colspan="7" class="px-8 py-16 text-center text-blue-200/30 uppercase font-black tracking-widest italic">NO HAY CONCEPTOS</td></tr>' : o.detalles.map(d => `
                                            <tr class="hover:bg-white/5 transition-colors">
                                                <td class="px-3 py-4 text-center font-bold text-white whitespace-nowrap">${d.cantidad}</td>
                                                <td class="px-3 py-4 whitespace-nowrap">
                                                    <span class="inline-block px-2 py-1 text-md font-black uppercase tracking-wider rounded-lg ${d.tipo_pill_bg} ${d.tipo_pill_text}">
                                                        ${d.tipo}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-4 font-bold text-white whitespace-nowrap">${d.clave}</td>
                                                <td class="px-3 py-4 font-bold text-white">${d.descripcion}</td>
                                                <td class="px-3 py-4 font-medium text-blue-200/60 uppercase">${d.notas}</td>
                                                <td class="px-3 py-4 text-center font-mono font-bold text-blue-100 whitespace-nowrap">$${d.precio}</td>
                                                <td class="px-3 py-4 text-right font-mono font-black text-white whitespace-nowrap">$${d.importe}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                    <tfoot class="bg-white/5 border-t border-white/10">
                                        <tr>
                                            <td colspan="6" class="px-8 py-6 text-right">
                                                <span class="text-blue-200 text-lg uppercase font-black tracking-[0.2em] mb-2 block">TOTAL:</span>
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <div class="text-lg font-black text-white leading-none tracking-tighter">$${o.total}</div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Observaciones Post-Reparación -->
                            <div class="mb-4 mt-4">
                                <p class="text-blue-300 uppercase font-black text-md mb-2">Observaciones Post-Reparación</p>
                                <div class="bg-white/5 border border-white/10 rounded-xl p-3">
                                    <p class="text-white/80 text-md font-bold uppercase leading-relaxed">
                                        ${o.observaciones_post ? o.observaciones_post : '<span class="italic text-white/30">SIN OBSERVACIONES</span>'}
                                    </p>
                                </div>
                            </div>

                        </div>
                    `,
                    showCancelButton: false,
                    confirmButtonText: 'CERRAR',
                    confirmButtonColor: '#475569',
                    customClass: {
                        popup: 'rounded-3xl border border-white/20 shadow-2xl',
                        title: 'text-2xl font-black uppercase pt-4',
                        confirmButton: 'rounded-xl px-12 py-3 font-bold uppercase tracking-widest text-md mt-4'
                    }
                });
            } catch (e) {
                console.error("Error al abrir la modal:", e);
                Swal.fire('Error', 'No se pudieron procesar los detalles visuales de esta orden.', 'error');
            }
        }

        function compartirWhatsapp(celularOriginal, token, folio, marca, modelo) {
            let celular = celularOriginal ? celularOriginal.replace(/\D/g, '') : '';
            if (celular.startsWith('52') && celular.length === 12) {
                celular = celular.substring(2);
            } else if (celular.startsWith('521') && celular.length === 13) {
                celular = celular.substring(3);
            }
            
            const linkCompartir = `{{ url('orden/compartir') }}/${token}`;
            const vehiculoStr = `${marca} ${modelo}`.trim().toUpperCase();
            const mensajeDefecto = `¡Hola! Esperamos que se encuentre muy bien. Le escribimos para notificarle que el servicio de su ${vehiculoStr} ha finalizado con éxito y ya está listo para ser entregado.\n\nLe recordamos que nuestro horario de atención es:\n* Lunes a Viernes: 09:00 a 17:30 hrs.\n* Sábados: 09:00 a 14:00 hrs.\n\nPuede revisar el reporte y comprobante detallado de su orden en este enlace: ${linkCompartir}\n\nAgradecemos su preferencia y quedamos a su disposición para cualquier duda.`;

            Swal.fire({
                title: 'COMPARTIR ORDEN POR WHATSAPP',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="p-4 space-y-4 text-left">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-1">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">LADA</label>
                                <select id="swal_lada" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                    <option value="52" class="text-black" selected>MX (+52)</option>
                                    <option value="1" class="text-black">US/CA (+1)</option>
                                    <option value="502" class="text-black">GT (+502)</option>
                                    <option value="503" class="text-black">SV (+503)</option>
                                    <option value="504" class="text-black">HN (+504)</option>
                                    <option value="505" class="text-black">NI (+505)</option>
                                    <option value="506" class="text-black">CR (+506)</option>
                                    <option value="507" class="text-black">PA (+507)</option>
                                    <option value="57" class="text-black">CO (+57)</option>
                                    <option value="54" class="text-black">AR (+54)</option>
                                    <option value="56" class="text-black">CL (+56)</option>
                                    <option value="51" class="text-black">PE (+51)</option>
                                    <option value="591" class="text-black">BO (+591)</option>
                                    <option value="593" class="text-black">EC (+593)</option>
                                    <option value="595" class="text-black">PY (+595)</option>
                                    <option value="598" class="text-black">UY (+598)</option>
                                    <option value="58" class="text-black">VE (+58)</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">TELÉFONO CELULAR</label>
                                <input type="text" id="swal_celular" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" value="${celular}" placeholder="EJ. 5512345678">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">MENSAJE A ENVIAR</label>
                            <textarea id="swal_mensaje" rows="7" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm">${mensajeDefecto}</textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'ENVIAR POR WHATSAPP',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm'
                },
                preConfirm: () => {
                    const lada = document.getElementById('swal_lada').value;
                    const tel = document.getElementById('swal_celular').value.replace(/\D/g, '');
                    const msg = document.getElementById('swal_mensaje').value;

                    if (!tel) {
                        Swal.showValidationMessage('El número de teléfono es obligatorio');
                        return false;
                    }

                    return { lada, tel, msg };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { lada, tel, msg } = result.value;
                    const encodedMsg = encodeURIComponent(msg);
                    const url = `https://wa.me/${lada}${tel}?text=${encodedMsg}`;
                    window.open(url, '_blank');
                }
            });
        }
    </script>
@endpush
