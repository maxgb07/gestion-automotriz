@extends('layouts.app')

@section('title', 'Orden de Servicio: ' . $orden->folio)

@section('content')
    <div class="max-w-7xl mx-auto py-4">
        <!-- Encabezado con Estado -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('ordenes.index') }}" class="p-2 bg-white/5 hover:bg-white/10 rounded-xl border border-white/10 transition-colors text-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-3xl font-black text-white uppercase tracking-tighter">{{ $orden->folio }}</h1>
                        @php
                            $color = match($orden->estado) {
                                'RECEPCION' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                'REPARACION' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                'FINALIZADO' => 'bg-teal-500/20 text-teal-400 border-teal-400/50',
                                'PENDIENTE DE PAGO' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                'ENTREGADO' => 'bg-green-500/20 text-green-300 border-green-500/30',
                            };
                        @endphp
                        <span class="px-4 py-1 rounded-full text-md font-black border {{ $color }} tracking-widest uppercase">
                            {{ $orden->estado }}
                        </span>
                        @if($orden->requiere_factura === 'SI')
                            <span class="px-4 py-1 rounded-full text-md font-black border bg-teal-500/20 text-teal-400 border-teal-400/50 tracking-widest uppercase">
                                FACTURA
                                @if($orden->folio_factura)
                                    : {{ $orden->folio_factura }}
                                @endif
                            </span>
                        @endif
                    </div>
                    <p class="text-blue-200/60 text-md font-bold uppercase tracking-widest">Registrada el {{ $orden->fecha_entrada->translatedFormat('d M, Y h:i A') }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button onclick="abrirModalDatosVehiculo({{ $orden->id }}, '{{ $orden->placas ?: $orden->vehiculo->placas }}', {{ $orden->kilometraje_entrega ?: 0 }}, '{{ $orden->numero_serie ?: $orden->vehiculo->numero_serie }}', '{{ $orden->mecanico }}')" class="btn-premium-purple px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg shadow-purple-500/20 transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                    Datos Vehículo
                </button>
                @php
                    $esReparacion = $orden->estado === 'REPARACION';
                @endphp
                @if($orden->estado !== 'RECEPCION')
                    <a href="{{ $esReparacion ? route('ordenes.cotizacion.pdf', $orden) : route('ordenes.pdf', $orden) }}" target="_blank" class="{{ $esReparacion ? 'btn-premium-amber shadow-amber-500/20' : 'btn-premium-blue shadow-blue-500/20' }} px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        {{ $esReparacion ? 'Imprimir Cotización' : 'Imprimir Comprobante' }}
                    </a>
                @endif
                
                @if($orden->estado === 'REPARACION')
                    <button onclick="abrirModalFinalizarReparacion()" class="btn-premium-teal px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg shadow-teal-500/20 transition-all uppercase tracking-widest flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Finalizar Reparación
                    </button>
                @endif

                @if($orden->requiere_factura === 'SI')
                    <button onclick="abrirModalFactura({{ $orden->id }}, '{{ $orden->folio_factura }}')" class="btn-premium-amber px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg shadow-amber-500/20 transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Registrar Factura
                    </button>
                @endif

                @if($orden->estado === 'FINALIZADO')
                    <button onclick="abrirModalPago({{ $orden->id }}, {{ $orden->total }}, {{ $orden->saldo_pendiente }}, true)" class="btn-premium-success px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg shadow-green-500/20 transition-all uppercase tracking-widest flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Registrar Pago
                    </button>
                @endif
            </div>
        </div>

        <div class="space-y-8 mb-8">
                <!-- Info Cliente -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                    <div class="p-6 border-b border-white/10 bg-white/5">
                        <h3 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Información del Cliente</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1">Nombre</p>
                            <p class="text-white font-bold text-md uppercase">{{ $orden->cliente->nombre }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1">Teléfono</p>
                                <p class="text-white font-bold text-md uppercase">{{ $orden->cliente->celular }}</p>
                            </div>
                            <div>
                                <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1">RFC</p>
                                <p class="text-white font-bold text-md uppercase">{{ $orden->cliente->rfc ?? '---' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Vehículo -->
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                    <div class="p-6 border-b border-white/10 bg-white/5">
                        <h3 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Datos del Vehículo</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-black text-md uppercase leading-tight">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</p>
                                <p class="text-blue-200/40 text-md font-bold uppercase tracking-widest">{{ $orden->vehiculo->anio }} • PLACAS: {{ $orden->vehiculo->placas ?? '---' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <!-- <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                                <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1">Km Entrada</p>
                                <p class="text-white font-bold text-md uppercase">{{ number_format($orden->kilometraje_entrada) }} KM</p>
                            </div> -->
                            <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                                <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1">Km Entrega</p>
                                <p class="text-white font-bold text-md uppercase">{{ number_format($orden->kilometraje_entrega) }} KM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Falla -->
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl overflow-hidden group mb-8">
                    <div class="p-6 border-b border-white/10 bg-white/5">
                        <h3 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Falla Reportada</h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-amber-500/5 border border-amber-500/10 rounded-xl p-4">
                            <p class="text-amber-200/80 text-md font-medium uppercase leading-relaxed italic">
                                "{{ $orden->falla_reportada }}"
                            </p>
                        </div>
                        @if($orden->observaciones)
                           <div class="mt-4">
                                <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1 ml-1">Observaciones de Recepción</p>
                                <p class="text-white/60 text-md uppercase font-bold">{{ $orden->observaciones }}</p>
                           </div>
                        @endif
                        @if($orden->observaciones_post_reparacion)
                           <div class="mt-4">
                                <p class="text-md text-green-400/40 font-black uppercase tracking-widest mb-1 ml-1">Observaciones Post-Reparación</p>
                                <p class="text-white/60 text-md uppercase font-bold">{{ $orden->observaciones_post_reparacion }}</p>
                           </div>
                        @endif
                    </div>
                </div>
                <!-- Gestión de Items (Productos y Servicios) -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                    <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                        <h2 class="text-xl font-bold text-white uppercase tracking-tight">Detalle de Reparación / Cotización</h2>
                        @if($orden->mecanico && ($orden->estado === 'ENTREGADO' || $orden->estado === 'PENDIENTE DE PAGO'))
                            <div class="flex items-center gap-2 bg-blue-500/20 px-4 py-2 rounded-xl border border-blue-500/30">
                                <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-xs font-black text-blue-100 uppercase tracking-widest">Mecánico: <span class="text-white">{{ $orden->mecanico }}</span></span>
                            </div>
                        @endif
                        @if($orden->estado !== 'ENTREGADO' && $orden->estado !== 'PENDIENTE DE PAGO')
                            <div class="flex gap-3">
                                <button type="button" onclick="abrirModalNuevoItem()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Nuevo Item
                                </button>
                                <button type="button" onclick="addRow()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Agregar Fila
                                </button>
                                <button type="button" onclick="guardarItems()" class="btn-premium-success px-6 py-2.5 text-white text-xs font-black rounded-xl shadow-lg shadow-green-500/20 transition-all uppercase tracking-widest flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Guardar Items
                                </button>
                                @if($orden->detalles->count() > 0)
                                    <a href="{{ route('ordenes.cotizacion.pdf', $orden) }}" target="_blank" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-xl shadow-lg shadow-amber-900/40 transition-all uppercase tracking-widest flex items-center justify-center" style="background-color: #d97706;">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Cotización
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-center border-collapse" id="items-table">
                            <thead class="bg-white/5 border-b border-white/10">
                                <tr>
                                    <th class="px-2 py-4 text-sm font-bold text-blue-200 uppercase tracking-widest w-28">Cantidad</th>
                                    <th class="px-6 py-4 text-sm font-bold text-blue-200 uppercase tracking-widest">Tipo</th>
                                    <th class="px-6 py-4 text-sm font-bold text-blue-200 uppercase tracking-widest">Clave</th>
                                    <th class="px-6 py-4 text-sm font-bold text-blue-200 uppercase tracking-widest">Descripción</th>
                                    <th class="px-6 py-4 text-sm font-bold text-blue-200 uppercase tracking-widest">Notas</th>
                                    <th class="px-4 py-4 text-sm font-bold text-blue-200 uppercase tracking-widest w-32">Precio</th>
                                    <!-- <th class="px-4 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest w-28">Descuento</th> -->
                                    <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest w-40 text-right">Importe</th>
                                    @if($orden->estado !== 'ENTREGADO' && $orden->estado !== 'PENDIENTE DE PAGO')
                                        <th class="px-4 py-4 w-16"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse($orden->detalles as $detalle)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-3 py-4 text-center">
                                            <span class="text-white font-bold text-md">{{ number_format($detalle->cantidad, 2) }}</span>
                                        </td>
                                        <td class="px-3 py-4">
                                            <span class="inline-block px-2 py-1 text-md font-black uppercase tracking-wider rounded-lg
                                                {{ $detalle->producto_id ? 'bg-blue-500/20 text-blue-300' : 'bg-purple-500/20 text-purple-300' }}">
                                                {{ $detalle->producto_id ? 'PRODUCTO' : 'SERVICIO' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-white font-bold text-md">{{ $detalle->producto?->nombre ?? $detalle->servicio?->nombre ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-white font-bold text-md">{{ $detalle->producto?->descripcion ?? $detalle->servicio?->descripcion ?? '---' }}</p>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-blue-200/60 font-medium text-md uppercase">{{ $detalle->notas ?? '---' }}</p>
                                        </td>
                                        <td class="px-3 py-4 text-center">
                                            <span class="text-blue-100 font-mono text-md font-bold">${{ number_format($detalle->precio_unitario, 2) }}</span>
                                        </td>
                                        <!-- <td class="px-3 py-4 text-center font-mono">
                                            <span class="text-blue-100/60 text-xs font-bold">{{ number_format($detalle->descuento_porcentaje, 1) }}%</span>
                                        </td> -->
                                        <td class="px-3 py-4 text-right font-mono">
                                            <span class="text-white font-black text-md existing-subtotal" data-valor="{{ $detalle->subtotal }}">${{ number_format($detalle->subtotal, 2) }}</span>
                                        </td>
                                        @if($orden->estado !== 'ENTREGADO' && $orden->estado !== 'PENDIENTE DE PAGO')
                                            <td class="px-4 py-3 text-center flex items-center justify-center gap-1">
                                                <button type="button" class="p-2 text-white/20 hover:text-blue-400 transition-colors" 
                                                        onclick="abrirModalEditarItem({{ $detalle->id }}, '{{ $detalle->producto_id ? 'producto' : 'servicio' }}', {{ $detalle->producto_id ?? $detalle->servicio_id }}, {{ $detalle->cantidad }}, {{ $detalle->precio_unitario }}, '{{ addslashes($detalle->notas) }}')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                </button>
                                                <form action="{{ route('ordenes.detalles.destroy', [$orden, $detalle]) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 text-white/20 hover:text-red-400 transition-colors" onclick="return confirm('¿Eliminar este item?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-8 py-16 text-center">
                                            <p class="text-sm text-blue-200/30 uppercase font-black tracking-widest italic">No se han registrado artículos o servicios aún</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-white/5 border-t border-white/10">
                                <tr>
                                    <td colspan="6" class="px-8 py-6 text-right">
                                        <span class="text-blue-200 text-lg uppercase font-black tracking-[0.2em] mb-2 block">Total:</span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div id="total-reparacion" class="text-lg font-black text-white hover:text-blue-400 transition-all duration-300 leading-none tracking-tighter">${{ number_format($orden->total, 2) }}</div>
                                    </td>
                                    @if($orden->estado !== 'ENTREGADO' && $orden->estado !== 'PENDIENTE DE PAGO')
                                        <td></td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                    <!-- Evidencia Fotográfica -->
                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                        <div class="p-6 border-b border-white/10 bg-white/5 flex items-center justify-between">
                            <h3 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Evidencia Fotográfica</h3>
                            @if($orden->estado !== 'ENTREGADO' && $orden->estado !== 'PENDIENTE DE PAGO')
                                <button onclick="abrirModalImagen()" class="btn-premium-blue px-3 py-1.5 text-white text-md font-black rounded-lg shadow-lg shadow-blue-500/20 transition-all uppercase tracking-widest flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Subir Imagen
                                </button>
                            @endif
                        </div>
                        <div class="p-6">
                            @if($orden->imagenes->count() > 0)
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach($orden->imagenes as $img)
                                        <div class="relative group aspect-square rounded-xl overflow-hidden border border-white/10 shadow-lg">
                                            <img src="{{ asset('storage/' . $img->ruta) }}" class="w-full h-full object-cover" title="{{ $img->descripcion }}">
                                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                                <button onclick="verImagen('{{ asset('storage/' . $img->ruta) }}', '{{ $img->descripcion }}')" class="p-2 bg-blue-500/20 text-blue-300 rounded-lg hover:bg-blue-500/40 transition-colors">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                                @if($orden->estado !== 'ENTREGADO' && $orden->estado !== 'PENDIENTE DE PAGO')
                                                    <form action="{{ route('ordenes.imagenes.destroy', [$orden, $img]) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-2 bg-red-500/20 text-red-300 rounded-lg hover:bg-red-500/40 transition-colors" onclick="return confirm('¿Eliminar imagen?')">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-8 text-center border-2 border-dashed border-white/5 rounded-2xl">
                                    <p class="text-md text-white/20 uppercase font-black tracking-widest">Sin imágenes cargadas</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Seguimiento de Pagos -->
                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                        <div class="p-6 border-b border-white/10 bg-white/5 flex items-center justify-between">
                            <h3 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Gestión de Abonos</h3>
                            @if($orden->saldo_pendiente > 0)
                                <button onclick="abrirModalPago({{ $orden->id }}, {{ $orden->total }}, {{ $orden->saldo_pendiente }})" class="btn-premium-success px-3 py-1.5 text-white text-md font-black rounded-lg transition-all uppercase tracking-widest flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Registrar Pago
                                </button>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="mb-6 @if($orden->saldo_pendiente > 0) bg-red-500/5 border-red-500/20 @else bg-green-500/5 border-green-500/20 @endif border rounded-2xl p-4 text-center">
                                <p class="text-md text-white/40 uppercase font-black tracking-[0.3em] mb-1">Saldo Pendiente</p>
                                <p @class([
                                    'text-2xl font-black font-mono tracking-tighter',
                                    'text-red-400' => $orden->saldo_pendiente > 0,
                                    'text-green-400' => $orden->saldo_pendiente == 0
                                ])>${{ number_format($orden->saldo_pendiente, 2) }}</p>
                            </div>

                            @if($orden->pagos->count() > 0)
                                <div class="overflow-x-auto custom-scrollbar">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="border-b border-white/10 uppercase tracking-widest text-[8px] font-black text-blue-200/40">
                                                <th class="px-2 py-3 text-center">Fecha</th>
                                                <th class="px-2 py-3 text-center">Método</th>
                                                <th class="px-2 py-3 text-center">Referencia</th>
                                                <th class="px-2 py-3 text-center">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5">
                                            @foreach($orden->pagos as $pago)
                                                <tr class="hover:bg-white/5 transition-colors group">
                                                    <td class="px-2 py-3 text-md text-white/70 font-bold uppercase text-center">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                                    <td class="px-2 py-3 text-md text-blue-300/50 font-black uppercase text-center">{{ $pago->metodo_pago }}</td>
                                                    <td class="px-2 py-3 text-md text-white/30 font-mono uppercase italic text-center">{{ $pago->referencia ?: '-' }}</td>
                                                    <td class="px-2 py-3 text-center text-md font-black text-white font-mono tracking-tighter">${{ number_format($pago->monto, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-md text-white/20 text-center uppercase font-black tracking-widest py-4">No hay abonos registrados</p>
                            @endif
                        </div>
                    </div>
        </div>
    </div>

    <!-- Template para nuevas filas -->
    <template id="row-template">
        <tr class="hover:bg-white/5 transition-colors">
            <td class="px-3 py-4">
                <input type="number" name="items[INDEX][cantidad]" value="1" min="1" step="any" oninput="calculateRow(this)" class="block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-center text-sm font-bold focus:ring-1 focus:ring-blue-500/50 outline-none transition-all" required>
            </td>
            <td class="px-3 py-4">
                <select name="items[INDEX][tipo]" onchange="changeType(this)" class="tipo-select block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-xs uppercase focus:outline-none">
                    <option value="producto" class="text-black bg-white">PRODUCTO</option>
                    <option value="servicio" class="text-black bg-white">SERVICIO</option>
                </select>
            </td>
            <td class="px-3 py-4">
                <select name="items[INDEX][item_id]" onchange="updateItemData(this)" class="item-select block w-full" required>
                    <option value="" disabled selected>SELECCIONAR...</option>
                </select>
            </td>
            <td class="px-3 py-4">
                <input type="text" name="items[INDEX][descripcion]" class="descripcion-input block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-xs uppercase focus:outline-none" readonly>
            </td>
            <td class="px-3 py-4">
                <input type="text" name="items[INDEX][notas]" class="block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-xs uppercase focus:ring-1 focus:ring-blue-500/50 outline-none transition-all" placeholder="NOTA OPCIONAL...">
            </td>
            <td class="px-3 py-4">
                <input type="number" step="any" name="items[INDEX][precio_unitario]" value="0.00" oninput="calculateRow(this)" class="block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-center text-sm font-bold focus:ring-1 focus:ring-blue-500/50 outline-none" required>
            </td>
            <!-- <td class="px-3 py-4">
                <div class="relative">
                    <input type="number" name="items[INDEX][descuento_porcentaje]" value="0" min="0" max="100" step="any" oninput="calculateRow(this)" class="block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-center text-xs font-bold focus:ring-1 focus:ring-blue-500/50 outline-none">
                </div>
            </td> -->
            <td class="px-3 py-4 text-right">
                <input type="number" step="any" name="items[INDEX][subtotal]" value="0.00" oninput="calculateTotal()" class="subtotal-input block w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-right text-sm font-black font-mono focus:ring-1 focus:ring-blue-500/50 outline-none" required>
            </td>
            <td class="px-3 py-4 text-center">
                <button type="button" onclick="removeRow(this)" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(59, 130, 246, 0.5); border-radius: 10px; }
        .btn-premium-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #9333ea 100%) !important;
            border: none !important;
            display: inline-flex !important;
            cursor: pointer !important;
        }
        .btn-premium-gradient:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.5) !important;
        }
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
        .btn-premium-amber {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            border: none !important;
            display: inline-flex !important;
            cursor: pointer !important;
        }
        .btn-premium-amber:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(217, 119, 6, 0.5) !important;
        }
        .btn-premium-teal {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%) !important;
            border: none !important;
            display: inline-flex !important;
            cursor: pointer !important;
        }
        .btn-premium-teal:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(13, 148, 136, 0.5) !important;
        }
        .btn-premium-purple {
            background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%) !important;
            border: none !important;
            display: inline-flex !important;
            cursor: pointer !important;
        }
        .btn-premium-purple:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(168, 85, 247, 0.5) !important;
        }
        /* Estilos Select2 idénticos a Ventas */
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
        /* Color negro para los selectores nativos */
        select option {
            background-color: white !important;
            color: black !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function verImagen(url, descripcion) {
            Swal.fire({
                imageUrl: url,
                imageAlt: descripcion || 'Evidencia de reparación',
                title: descripcion || '',
                confirmButtonColor: '#3b82f6',
                customClass: { popup: 'rounded-3xl border border-white/10' }
            });
        }

        let rowIndex = 0;
        const PRODUCTOS = @json($productos);
        const SERVICIOS = @json($servicios);

        function addRow() {
            const tbody = document.querySelector('#items-table tbody');
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
                option.textContent = item.nombre + ' - ' + item.descripcion;
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
            const descInput = row.querySelector('.descripcion-input');
            
            if (option.dataset.precio) {
                precioInput.value = option.dataset.precio;
            }
            if (option.dataset.descripcion) {
                descInput.value = option.dataset.descripcion;
            }
            calculateRow(row.querySelector('[name*="[cantidad]"]'));
        }

        function calculateRow(input) {
            const row = input.closest('tr');
            const cant = parseFloat(row.querySelector('[name*="[cantidad]"]').value) || 0;
            const price = parseFloat(row.querySelector('[name*="[precio_unitario]"]').value) || 0;
            const descPorc = 0; //parseFloat(row.querySelector('[name*="[descuento_porcentaje]"]').value) || 0;
            const subtotalInput = row.querySelector('.subtotal-input');
            
            const baseRowTotal = cant * price;
            const discountAmount = baseRowTotal * (descPorc / 100);
            const finalRowSubtotal = baseRowTotal - discountAmount;
            
            subtotalInput.value = finalRowSubtotal.toFixed(2);
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            // Sumar items existentes
            document.querySelectorAll('.existing-subtotal').forEach(el => {
                total += parseFloat(el.dataset.valor) || 0;
            });
            // Sumar nuevos items (inputs)
            document.querySelectorAll('#items-table .subtotal-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            document.getElementById('total-reparacion').textContent = '$' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            calculateTotal();
        }

        // --- Registro Rápido de Ítems ---
        function toggleSwalFields(tipo) {
            const popup = Swal.getPopup();
            if (!popup) return;

            const divStock = popup.querySelector('#div-stock');
            const divMarca = popup.querySelector('#div-marca');
            const labelNombre = popup.querySelector('#label-nombre');

            if (tipo === 'servicio') {
                if (divStock) divStock.classList.add('hidden');
                if (divMarca) divMarca.classList.add('hidden');
                if (labelNombre) labelNombre.textContent = 'NOMBRE DEL SERVICIO *';
            } else {
                if (divStock) divStock.classList.remove('hidden');
                if (divMarca) divMarca.classList.remove('hidden');
                if (labelNombre) labelNombre.textContent = 'SKU / CLAVE *';
            }
        }

        function abrirModalNuevoItem() {
            Swal.fire({
                title: 'REGISTRAR NUEVO ÍTEM',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="flex gap-8 justify-center mb-6 p-4 bg-white/5 rounded-2xl border border-white/10">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="swal-tipo" value="producto" checked onchange="toggleSwalFields(this.value)" class="w-5 h-5 text-blue-500 bg-white/10 border-white/20 focus:ring-blue-500 focus:ring-offset-slate-800">
                            <span class="text-md font-black uppercase tracking-widest text-blue-100 group-hover:text-white transition-colors">Producto</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="swal-tipo" value="servicio" onchange="toggleSwalFields(this.value)" class="w-5 h-5 text-blue-500 bg-white/10 border-white/20 focus:ring-blue-500 focus:ring-offset-slate-800">
                            <span class="text-md font-black uppercase tracking-widest text-blue-100 group-hover:text-white transition-colors">Servicio</span>
                        </label>
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
                        <div id="div-marca">
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">MARCA</label>
                            <input type="text" id="swal-marca" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="MARCA DEL PRODUCTO">
                        </div>
                        <div id="div-stock">
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
                    const tipo = document.querySelector('input[name="swal-tipo"]:checked').value;
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
                        stock_minimo: 0 // Default para registro rápido
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
                                // Actualizar variables locales
                                const newItem = response.data;
                                if (tipo === 'producto') {
                                    PRODUCTOS.push(newItem);
                                } else {
                                    SERVICIOS.push(newItem);
                                }

                                // Notificar éxito
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Registrado!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Refrescar los Select2 si existen
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

        function guardarItems() {
            const items = [];
            const rows = document.querySelectorAll('#items-table tbody tr');
            let valid = true;
            let newItemsCount = 0;

            rows.forEach(row => {
                // Solo procesar filas que tienen los inputs de edición (nuevas filas)
                const tipoSelect = row.querySelector('.tipo-select');
                if (!tipoSelect) return; // Saltar filas existentes que no tienen tipo-select

                const tipo = tipoSelect.value;
                const item_id = row.querySelector('.item-select').value;
                const cantidad = row.querySelector('[name*="[cantidad]"]').value;
                const precio_unitario = row.querySelector('[name*="[precio_unitario]"]').value;
                const notas = row.querySelector('[name*="[notas]"]').value;
                const descuento_porcentaje = 0; //row.querySelector('[name*="[descuento_porcentaje]"]').value;

                if (!item_id || !cantidad || !precio_unitario) {
                    valid = false;
                } else {
                    items.push({ tipo, item_id, cantidad, precio_unitario, descuento_porcentaje, notas });
                    newItemsCount++;
                }
            });

            if (!valid) {
                Swal.fire('Error', 'Todos los campos marcados son obligatorios', 'error');
                return;
            }

            if (items.length === 0) {
                Swal.fire('Atención', 'Debes agregar al menos un item para guardar', 'warning');
                return;
            }

            $.ajax({
                url: '{{ route("ordenes.detalles.store", $orden) }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', items: items },
                success: (response) => {
                    const isWarning = response.message && response.message.includes('ADVERTENCIA');
                    Swal.fire({
                        icon: isWarning ? 'warning' : 'success',
                        title: isWarning ? 'Items agregados con advertencias' : 'Items agregados',
                        text: response.message || 'Items agregados correctamente',
                        showConfirmButton: isWarning,
                        timer: isWarning ? null : 1500
                    }).then(() => {
                        isSubmitting = true;
                        location.reload();
                    });
                },
                error: (xhr) => Swal.fire('Error', xhr.responseJSON.message || 'Error al agregar items', 'error')
            });
        }

        function abrirModalImagen() {
            Swal.fire({
                title: 'SUBIR EVIDENCIA FOTOGRÁFICA',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="space-y-4 text-left p-2">
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">Imágenes (JPG, PNG) *</label>
                            <input type="file" id="swal-imagen" accept="image/*" multiple class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">Descripción corta</label>
                            <input type="text" id="swal-desc" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Ej: Falla motor, pieza dañada...">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'SUBIR',
                confirmButtonColor: '#3b82f6',
                preConfirm: () => {
                    const files = document.getElementById('swal-imagen').files;
                    const desc = document.getElementById('swal-desc').value;
                    if (files.length === 0) {
                        Swal.showValidationMessage('Debes seleccionar al menos una imagen');
                        return false;
                    }
                    return { files, desc };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    for (let i = 0; i < result.value.files.length; i++) {
                        formData.append('imagenes[]', result.value.files[i]);
                    }
                    formData.append('descripcion', result.value.desc);

                    Swal.fire({
                        title: 'Subiendo...',
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: '{{ route("ordenes.imagenes.store", $orden) }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: () => {
                            isSubmitting = true;
                            location.reload();
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || 'No se pudo subir la imagen';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });
        }


        function abrirModalFinalizarReparacion() {
            Swal.fire({
                title: 'FINALIZAR REPARACIÓN',
                background: '#1e293b',
                color: '#fff',
                width: '600px',
                html: `
                    <div class="space-y-4 text-left p-2">
                        <div class="space-y-4 bg-white/5 p-4 rounded-2xl border border-white/5">
                            <p class="text-md font-black text-teal-400 uppercase tracking-[0.2em] mb-2">Datos del Vehículo</p>
                            
                            <div>
                                <label class="block text-md font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">MECÁNICO QUE ATENDIÓ *</label>
                                <select id="swal-mecanico" class="w-full px-4 py-2.5 bg-slate-900 border border-white/10 rounded-xl text-white text-md font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all uppercase">
                                    <option value="" class="text-black">-- SELECCIONAR MECÁNICO --</option>
                                    <option value="ALEJANDRO" class="text-black" {{ trim(strtoupper($orden->mecanico)) === 'ALEJANDRO' ? 'selected' : '' }}>ALEJANDRO</option>
                                    <option value="DANIEL" class="text-black" {{ trim(strtoupper($orden->mecanico)) === 'DANIEL' ? 'selected' : '' }}>DANIEL</option>
                                    <option value="ELEAZAR" class="text-black" {{ trim(strtoupper($orden->mecanico)) === 'ELEAZAR' ? 'selected' : '' }}>ELEAZAR</option>
                                    <option value="RAFAEL" class="text-black" {{ trim(strtoupper($orden->mecanico)) === 'RAFAEL' ? 'selected' : '' }}>RAFAEL</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-md font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">PLACAS</label>
                                    <input type="text" id="swal-placas" value="{{ $orden->placas ?: $orden->vehiculo->placas }}" class="w-full px-4 py-2.5 bg-slate-900 border border-white/10 rounded-xl text-white text-md font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all uppercase font-mono">
                                </div>
                                <div>
                                    <label class="block text-md font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">KM ENTREGA</label>
                                    <input type="number" id="swal-km-final" value="{{ $orden->kilometraje_entrega ?: $orden->kilometraje_entrada }}" min="{{ $orden->kilometraje_entrada }}" class="w-full px-4 py-2.5 bg-slate-900 border border-white/10 rounded-xl text-white text-md font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono">
                                </div>
                            </div>

                            <div>
                                <label class="block text-md font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">VIN (NÚMERO DE SERIE)</label>
                                <input type="text" id="swal-vin" value="{{ $orden->numero_serie ?: $orden->vehiculo->numero_serie }}" class="w-full px-4 py-2.5 bg-slate-900 border border-white/10 rounded-xl text-white text-md font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all uppercase font-mono">
                            </div>
                        </div>

                        <div class="space-y-2">
                             <label class="block text-md font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Observaciones Post-Reparación</label>
                             <textarea id="swal-obs-post" rows="4" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-md font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all uppercase" placeholder="OBSERVACIONES SOBRE EL TRABAJO REALIZADO...">{{ $orden->observaciones_post_reparacion }}</textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'FINALIZAR REPARACIÓN',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#ef4444',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-sm'
                },
                preConfirm: () => {
                    const mecanico = document.getElementById('swal-mecanico').value;
                    const placas = document.getElementById('swal-placas').value;
                    const kilometraje_entrega = document.getElementById('swal-km-final').value;
                    const numero_serie = document.getElementById('swal-vin').value;
                    const observaciones_post_reparacion = document.getElementById('swal-obs-post').value;

                    if (!mecanico) {
                        Swal.showValidationMessage('El campo mecánico es obligatorio');
                        return false;
                    }

                    return { 
                        mecanico, 
                        placas, 
                        kilometraje_entrega, 
                        numero_serie, 
                        observaciones_post_reparacion,
                        finalizar_reparacion: true 
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Finalizando reparación...',
                        background: '#1e293b',
                        color: '#fff',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: '{{ route("ordenes.update", $orden) }}',
                        method: 'PUT',
                        data: { _token: '{{ csrf_token() }}', ...result.value },
                        success: (response) => {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡REPARACIÓN FINALIZADA!',
                                    text: response.message,
                                    background: '#1e293b',
                                    color: '#fff',
                                    confirmButtonText: 'ENTENDIDO'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: (xhr) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'ERROR',
                                text: xhr.responseJSON.message || 'Error al finalizar reparación',
                                background: '#1e293b',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        }

        function abrirModalPago(ordenId, total, saldo, siempreMostrarPDF = false) {
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

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">FECHA PAGO *</label>
                                <input type="date" id="modal_fecha_pago" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-mono" value="{{ date('Y-m-d') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">¿REQUIERE FACTURA?</label>
                                <select id="modal_requiere_factura" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase">
                                    <option value="NO" class="text-black">NO</option>
                                    <option value="SI" class="text-black">SI</option>
                                </select>
                            </div>
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
                confirmButtonColor: '#10b981',
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
                    const fecha = document.getElementById('modal_fecha_pago').value;
                    const factura = document.getElementById('modal_requiere_factura').value;
                    const referencia = document.getElementById('modal_referencia').value;

                    if (!metodo) {
                        Swal.showValidationMessage('Debe seleccionar un método de pago');
                        return false;
                    }

                    if (!fecha) {
                        Swal.showValidationMessage('Debe seleccionar la fecha de pago');
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
                                const isFullPayment = parseFloat(result.value.monto) >= parseFloat(saldo);
                                const mostrarPDF = siempreMostrarPDF || isFullPayment;
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: isFullPayment ? '¡PAGO REGISTRADO!' : '¡ABONO REGISTRADO!',
                                    text: response.message,
                                    background: '#1e293b',
                                    color: '#fff',
                                    showConfirmButton: mostrarPDF,
                                    confirmButtonText: 'VER PDF',
                                    timer: mostrarPDF ? null : 1500
                                }).then((finalRes) => {
                                    if (mostrarPDF && finalRes.isConfirmed && response.pdf_url) {
                                        window.open(response.pdf_url, '_blank');
                                    }
                                    location.reload();
                                });
                            }
                        },
                        error: (xhr) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'ERROR',
                                text: xhr.responseJSON.message || 'Error al registrar pago',
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

        function actualizarMontoOrden(select) {
            const total = {{ $orden->total }};
            const montoInput = document.getElementById('swal-monto-pago');
            
            if (select.value === 'CRÉDITO 15 DÍAS') {
                montoInput.value = (0).toFixed(2);
                montoInput.setAttribute('readonly', true);
                montoInput.classList.add('bg-slate-200');
                montoInput.classList.add('text-slate-500');
            } else {
                montoInput.value = total.toFixed(2);
                montoInput.removeAttribute('readonly');
                montoInput.classList.remove('bg-slate-200');
                montoInput.classList.remove('text-slate-500');
            }
        }
        // --- Prevención de salida accidental ---
        let isSubmitting = false;

        function hasUnsavedChanges() {
            // En Órdenes, detectamos si hay filas nuevas buscando elementos con la clase '.tipo-select'
            // ya que las filas existentes vienen renderizadas desde PHP sin ese input de selección
            const newRows = document.querySelectorAll('#items-table .tipo-select').length;
            return newRows > 0;
        }

        // Alerta nativa para cierre de pestaña o recarga
        window.addEventListener('beforeunload', function(e) {
            if (!isSubmitting && hasUnsavedChanges()) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Interceptar clics en enlaces
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && !isSubmitting && hasUnsavedChanges() && !link.hasAttribute('download') && link.target !== '_blank') {
                const href = link.href;
                if (href && href.startsWith(window.location.origin) && !href.includes('#')) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Ítems sin guardar?',
                        text: "Has agregado nuevos elementos a la orden pero no los has guardado. Si sales ahora, se perderán.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'SÍ, SALIR',
                        cancelButtonText: 'QUEDARME Y GUARDAR',
                        background: '#1e293b',
                        color: '#fff',
                        customClass: {
                            popup: 'rounded-3xl border border-white/20 shadow-2xl',
                            title: 'text-xl font-black uppercase tracking-tighter'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            isSubmitting = true;
                            window.location.href = href;
                        }
                    });
                }
            }
        });
        function abrirModalEditarItem(id, tipo, itemId, cantidad, precio, notas) {
            Swal.fire({
                title: 'EDITAR ÍTEM',
                background: '#1e293b',
                color: '#fff',
                width: '600px',
                html: `
                    <div class="space-y-4 text-left p-2">
                        <div class="flex gap-8 justify-center mb-6 p-4 bg-white/5 rounded-2xl border border-white/10">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="edit-tipo" value="producto" ${tipo === 'producto' ? 'checked' : ''} onchange="updateEditItemSelect(this.value)" class="w-5 h-5 text-blue-500 bg-white/10 border-white/20 focus:ring-blue-500">
                                <span class="text-md font-black uppercase tracking-widest text-blue-100">Producto</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="edit-tipo" value="servicio" ${tipo === 'servicio' ? 'checked' : ''} onchange="updateEditItemSelect(this.value)" class="w-5 h-5 text-blue-500 bg-white/10 border-white/20 focus:ring-blue-500">
                                <span class="text-md font-black uppercase tracking-widest text-blue-100">Servicio</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">SELECCIONAR ÍTEM *</label>
                            <div class="select2-container-swal">
                                <select id="edit-item-id" class="w-full">
                                    <!-- Se rellena dinámicamente -->
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">CANTIDAD *</label>
                                <input type="number" id="edit-cantidad" step="0.01" value="${cantidad}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">PRECIO UNITARIO *</label>
                                <input type="number" id="edit-precio" step="0.01" value="${precio}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1 ml-1 text-center">NOTAS / OBSERVACIONES</label>
                            <textarea id="edit-notas" rows="2" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all uppercase" placeholder="NOTAS ADICIONALES...">${notas}</textarea>
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
                    const nuevoTipo = $('input[name="edit-tipo"]:checked').val();
                    const nuevoItemId = $('#edit-item-id').val();
                    const nuevaCantidad = $('#edit-cantidad').val();
                    const nuevoPrecio = $('#edit-precio').val();
                    const nuevasNotas = $('#edit-notas').val();

                    console.log('Edit Modal - Submitting (Normalized):', { nuevoTipo, nuevoItemId, nuevaCantidad, nuevoPrecio });

                    if (!nuevoItemId || !nuevaCantidad || !nuevoPrecio) {
                        Swal.showValidationMessage('Todos los campos marcados con * son obligatorios');
                        return false;
                    }

                    return { 
                        tipo: nuevoTipo.toLowerCase(), 
                        item_id: nuevoItemId, 
                        cantidad: nuevaCantidad, 
                        precio_unitario: nuevoPrecio, 
                        notas: nuevasNotas 
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Actualizando...',
                        background: '#1e293b',
                        color: '#fff',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: `/ordenes/{{ $orden->id }}/detalles/${id}`,
                        method: 'POST',
                        data: { 
                            _token: '{{ csrf_token() }}', 
                            _method: 'PUT',
                            ...result.value 
                        },
                        success: (res) => {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡ACTUALIZADO!',
                                    text: res.message,
                                    background: '#1e293b',
                                    color: '#fff',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    isSubmitting = true;
                                    location.reload();
                                });
                            }
                        },
                        error: (xhr) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'ERROR',
                                text: xhr.responseJSON ? xhr.responseJSON.message : 'Error al actualizar',
                                background: '#1e293b',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        }

        function updateEditItemSelect(tipo, selectedId = null) {
            console.log('updateEditItemSelect called with:', tipo, selectedId);
            const select = $('#edit-item-id');
            const data = (String(tipo).toLowerCase() === 'producto') ? PRODUCTOS : SERVICIOS;
            
            if (select.data('select2')) {
                select.select2('destroy');
            }

            select.empty().append('<option value="" disabled>SELECCIONAR...</option>');
            data.forEach(item => {
                const opt = new Option(`${item.nombre} - ${item.descripcion || ''}`, item.id, false, (selectedId && item.id == selectedId));
                select.append(opt);
            });

            select.select2({
                dropdownParent: Swal.getPopup(),
                width: '100%',
                placeholder: 'BUSCAR ÍTEM...',
                language: {
                    noResults: function() { return "NO SE ENCONTRARON RESULTADOS"; }
                }
            });

            // Ajustar estilos de Select2 SOLO para este modal
            const s2c = select.next('.select2-container');
            s2c.find('.select2-selection--single').css({
                'background-color': 'rgba(255, 255, 255, 0.05)',
                'border': '1px solid rgba(255, 255, 255, 0.1)',
                'height': '48px',
                'border-radius': '0.75rem',
                'display': 'flex',
                'align-items': 'center',
                'color': 'white'
            });
            s2c.find('.select2-selection__rendered').css('color', 'white');
            s2c.find('.select2-selection__arrow').css('top', '10px');
        }

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
                                <input type="text" id="modal_placas_quick" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase" value="${placasActuales}" placeholder="P. EJ. ABC-1234">
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">KM ENTREGA</label>
                                <input type="number" id="modal_km_entrega_quick" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" value="${kmActual}" min="0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">VIN (NÚMERO DE SERIE)</label>
                            <input type="text" id="modal_vin_quick" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase" value="${vinActual}" placeholder="VIN DEL VEHÍCULO">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">MECÁNICO ASIGNADO</label>
                            <select id="modal_mecanico_quick" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase">
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
                    const placas = document.getElementById('modal_placas_quick').value;
                    const km = document.getElementById('modal_km_entrega_quick').value;
                    const vin = document.getElementById('modal_vin_quick').value;
                    const mecanico = document.getElementById('modal_mecanico_quick').value;
                    
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
                                isSubmitting = true;
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
    </script>
@endpush
