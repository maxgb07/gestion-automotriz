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
                                'PENDIENTE DE PAGO' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                'ENTREGADO' => 'bg-green-500/20 text-green-300 border-green-500/30',
                            };
                        @endphp
                        <span class="px-4 py-1 rounded-full text-md font-black border {{ $color }} tracking-widest uppercase">
                            {{ $orden->estado }}
                        </span>
                    </div>
                    <p class="text-blue-200/60 text-md font-bold uppercase tracking-widest">Registrada el {{ $orden->fecha_entrada->translatedFormat('d M, Y h:i A') }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('ordenes.pdf', $orden) }}" target="_blank" class="btn-premium-blue px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg shadow-blue-500/20 transition-all uppercase tracking-widest flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir Comprobante
                </a>
                
                @if($orden->estado !== 'ENTREGADO' && $orden->estado !== 'PENDIENTE DE PAGO')
                    <button onclick="abrirModalEntrega()" class="btn-premium-success px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg shadow-green-500/20 transition-all uppercase tracking-widest flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Finalizar y Entregar
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
                                            <span class="text-white font-bold text-sm">{{ number_format($detalle->cantidad, 2) }}</span>
                                        </td>
                                        <td class="px-3 py-4">
                                            <span class="inline-block px-2 py-1 text-sm font-black uppercase tracking-wider rounded-lg
                                                {{ $detalle->producto_id ? 'bg-blue-500/20 text-blue-300' : 'bg-purple-500/20 text-purple-300' }}">
                                                {{ $detalle->producto_id ? 'PRODUCTO' : 'SERVICIO' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-white font-bold text-sm">{{ $detalle->producto?->nombre ?? $detalle->servicio?->nombre ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-3 py-4">
                                            <p class="text-white font-bold text-sm">{{ $detalle->producto?->descripcion ?? $detalle->servicio?->descripcion ?? '---' }}</p>
                                        </td>
                                        <td class="px-3 py-4 text-center">
                                            <span class="text-blue-100 font-mono text-sm font-bold">${{ number_format($detalle->precio_unitario, 2) }}</span>
                                        </td>
                                        <!-- <td class="px-3 py-4 text-center font-mono">
                                            <span class="text-blue-100/60 text-xs font-bold">{{ number_format($detalle->descuento_porcentaje, 1) }}%</span>
                                        </td> -->
                                        <td class="px-3 py-4 text-right font-mono">
                                            <span class="text-white font-black text-sm existing-subtotal" data-valor="{{ $detalle->subtotal }}">${{ number_format($detalle->subtotal, 2) }}</span>
                                        </td>
                                        @if($orden->estado !== 'ENTREGADO' && $orden->estado !== 'PENDIENTE DE PAGO')
                                            <td class="px-4 py-3 text-center">
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
                                    <td colspan="5" class="px-8 py-6 text-right">
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
                                <button onclick="abrirModalPago()" class="btn-premium-success px-3 py-1.5 text-white text-md font-black rounded-lg transition-all uppercase tracking-widest flex items-center">
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
            const divStock = document.getElementById('div-stock');
            const labelNombre = document.getElementById('label-nombre');
            if (tipo === 'servicio') {
                divStock.classList.add('hidden');
                labelNombre.textContent = 'NOMBRE DEL SERVICIO *';
            } else {
                divStock.classList.remove('hidden');
                labelNombre.textContent = 'SKU / CLAVE *';
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

                    if (!nombre || !precio || (tipo === 'producto' && !stock)) {
                        Swal.showValidationMessage('Todos los campos marcados con * son obligatorios');
                        return false;
                    }

                    return { tipo, nombre, precio, stock, descripcion };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { tipo, nombre, precio, stock, descripcion } = result.value;
                    const url = tipo === 'producto' ? '{{ route("productos.store") }}' : '{{ route("servicios.store") }}';
                    const data = {
                        _token: '{{ csrf_token() }}',
                        nombre: nombre,
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
                const descuento_porcentaje = 0; //row.querySelector('[name*="[descuento_porcentaje]"]').value;

                if (!item_id || !cantidad || !precio_unitario) {
                    valid = false;
                } else {
                    items.push({ tipo, item_id, cantidad, precio_unitario, descuento_porcentaje });
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
                success: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Items agregados',
                        showConfirmButton: false,
                        timer: 1500
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
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">Imagen (JPG, PNG) *</label>
                            <input type="file" id="swal-imagen" accept="image/*" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
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
                    const file = document.getElementById('swal-imagen').files[0];
                    const desc = document.getElementById('swal-desc').value;
                    if (!file) {
                        Swal.showValidationMessage('Debes seleccionar una imagen');
                        return false;
                    }
                    return { file, desc };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('imagen', result.value.file);
                    formData.append('descripcion', result.value.desc);

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
                        error: () => Swal.fire('Error', 'No se pudo subir la imagen', 'error')
                    });
                }
            });
        }

        function abrirModalPago() {
            Swal.fire({
                title: 'REGISTRAR ABONO / PAGO',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="space-y-4 text-left p-2">
                        <div class="border border-white/10 p-3 rounded-xl mb-4">
                            <p class="text-md text-slate-500 font-bold uppercase">Saldo Actual</p>
                            <p class="text-md text-white font-mono">$ {{ number_format($orden->saldo_pendiente, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Monto a Abonar *</label>
                            <input type="number" id="swal-monto-abono" step="0.01" value="{{ $orden->saldo_pendiente }}" max="{{ $orden->saldo_pendiente }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Fecha *</label>
                                <input type="date" id="swal-fecha" value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Método *</label>
                                <select id="swal-metodo" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                    <option value="EFECTIVO">EFECTIVO</option>
                                    <option value="TARJETA">TARJETA</option>
                                    <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Referencia</label>
                            <input type="text" id="swal-ref" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="OPCIONAL...">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'REGISTRAR PAGO',
                confirmButtonColor: '#10b981',
                preConfirm: () => {
                    const monto = document.getElementById('swal-monto-abono').value;
                    const fecha_pago = document.getElementById('swal-fecha').value;
                    const metodo_pago = document.getElementById('swal-metodo').value;
                    const referencia = document.getElementById('swal-ref').value;
                    if (!monto || !fecha_pago || !metodo_pago) {
                        Swal.showValidationMessage('Monto, fecha y método son obligatorios');
                        return false;
                    }
                    return { monto, fecha_pago, metodo_pago, referencia };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("ordenes.pagos.store", $orden) }}',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', ...result.value },
                        success: () => {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Pago Registrado!',
                                text: 'El abono se ha guardado correctamente.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                isSubmitting = true;
                                location.reload();
                            });
                        },
                        error: (xhr) => Swal.fire('Error', xhr.responseJSON.message || 'Error al registrar pago', 'error')
                    });
                }
            });
        }

        function abrirModalEntrega() {
            Swal.fire({
                title: 'ENTREGA DE VEHÍCULO',
                text: 'Ingresa los datos finales y registra el pago para cerrar la orden.',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="space-y-4 text-left p-2">
                        <div class="border border-white/10 p-3 rounded-xl mb-4">
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-md text-slate-500 font-bold uppercase">Total:</span>
                                <span class="text-md text-white font-mono">$ {{ number_format($orden->total, 2) }}</span>
                            </div>
                            <!--<div class="flex justify-between items-center">
                                <span class="text-md text-blue-500 font-bold uppercase">Saldo Pendiente:</span>
                                <span class="text-md font-black text-blue-600 font-mono">$ {{ number_format($orden->saldo_pendiente, 2) }}</span>
                            </div>-->
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Km de Entrega *</label>
                                <input type="number" id="swal-km-final" value="{{ $orden->kilometraje_entrada }}" min="{{ $orden->kilometraje_entrada }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Fecha de Entrega *</label>
                                <input type="datetime-local" id="swal-fecha-entrega" value="{{ date('Y-m-d\TH:i') }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-4 mt-4">
                            <p class="text-md text-slate-400 font-black uppercase tracking-widest mb-3">Información de Pago</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Método de Pago</label>
                                    <select id="swal-metodo-pago" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" onchange="validarMetodoPago(this)">
                                        <option value="EFECTIVO">EFECTIVO</option>
                                        <option value="TARJETA">TARJETA</option>
                                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                        <option value="CRÉDITO 15 DÍAS">CRÉDITO 15 DÍAS</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Monto Pagado</label>
                                    <input type="number" id="swal-monto-pago" step="0.01" value="0.00" max="{{ $orden->saldo_pendiente }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Referencia / Notas de Pago</label>
                                <input type="text" id="swal-ref-pago" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="EJ. TRANSFERENCIA BANAMEX...">
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-4 mt-4">
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Observaciones Post-Reparación (Opcional)</label>
                            <textarea id="swal-obs-post" rows="2" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="EJ. SE RECOMIENDA CAMBIO DE FRENOS EN 5000 KM..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Confirmar Placas</label>
                                <input type="text" id="swal-placas" value="{{ $orden->vehiculo->placas }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Confirmar VIN</label>
                                <input type="text" id="swal-vin" value="{{ $orden->vehiculo->numero_serie }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Mecánico que atendió *</label>
                            <select id="swal-mecanico" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="">-- SELECCIONAR MECÁNICO --</option>
                                <option value="ALEJANDRO">ALEJANDRO</option>
                                <option value="DANIEL">DANIEL</option>
                                <option value="ELEAZAR">ELEAZAR</option>
                                <option value="RAFAEL">RAFAEL</option>
                            </select>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'FINALIZAR Y GENERAR COMPROBANTE',
                confirmButtonColor: '#10b981',
                preConfirm: () => {
                    const kilometraje_entrega = document.getElementById('swal-km-final').value;
                    const fecha_entrega = document.getElementById('swal-fecha-entrega').value;
                    const monto_pago = document.getElementById('swal-monto-pago').value;
                    const metodo_pago = document.getElementById('swal-metodo-pago').value;
                    const referencia_pago = document.getElementById('swal-ref-pago').value;
                    const observaciones_post_reparacion = document.getElementById('swal-obs-post').value;
                    const placas = document.getElementById('swal-placas').value;
                    const numero_serie = document.getElementById('swal-vin').value;
                    const mecanico = document.getElementById('swal-mecanico').value;

                    if (!kilometraje_entrega || !fecha_entrega || !mecanico) {
                        Swal.showValidationMessage('Kilometraje, fecha y MECÁNICO son obligatorios');
                        return false;
                    }
                    return { kilometraje_entrega, fecha_entrega, monto_pago, metodo_pago, referencia_pago, observaciones_post_reparacion, placas, numero_serie, mecanico, entrega: true };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
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
                                    title: '¡Orden Finalizada!',
                                    text: response.message,
                                    confirmButtonText: 'ENTENDIDO'
                                }).then(() => {
                                    isSubmitting = true;
                                    if (response.pdf_url) {
                                        window.open(response.pdf_url, '_blank');
                                    }
                                    location.reload();
                                });
                            }
                        },
                        error: (xhr) => Swal.fire('Error', xhr.responseJSON?.message || 'Error al finalizar entrega', 'error')
                    });
                }
            });
        }

        function validarMetodoPago(select) {
            if (select.value === 'CRÉDITO 15 DÍAS') {
                const montoInput = document.getElementById('swal-monto-pago');
                if (parseFloat(montoInput.value) > 0) {
                    Swal.fire({
                        title: 'ATENCIÓN',
                        text: 'Al seleccionar CRÉDITO 15 DÍAS, el monto pagado debe ser 0.',
                        icon: 'warning',
                        confirmButtonColor: '#3b82f6'
                    });
                    montoInput.value = 0;
                }
                montoInput.setAttribute('readonly', true);
                montoInput.classList.add('bg-slate-200');
            } else {
                const montoInput = document.getElementById('swal-monto-pago');
                montoInput.removeAttribute('readonly');
                montoInput.classList.remove('bg-slate-200');
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
    </script>
@endpush
