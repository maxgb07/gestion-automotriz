@extends('layouts.app')

@section('title', 'Detalle de Compra: ' . ($compra->folio ?? 'N/A'))

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
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto py-4">
        <!-- Encabezado con Estado -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('compras.index') }}" class="p-2 bg-white/5 hover:bg-white/10 rounded-xl border border-white/10 transition-colors text-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-3xl font-black text-white uppercase tracking-tighter">{{ $compra->folio ?? 'OC-XXXXX' }}</h1>
                        <span class="px-4 py-1 rounded-full text-md font-black border bg-green-500/20 text-green-300 border-green-500/30 tracking-widest uppercase">
                            REGISTRADA
                        </span>
                    </div>
                    <p class="text-blue-200/60 text-md font-bold uppercase tracking-widest">
                        FECHA DE COMPRA: {{ \Carbon\Carbon::parse($compra->fecha_compra)->translatedFormat('d M, Y') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                 <div class="bg-white/10 backdrop-blur-xl rounded-2xl px-8 py-3 border border-white/20 shadow-xl">
                    <span class="text-[10px] text-blue-200 uppercase font-black tracking-[0.2em] block mb-1 text-right">Total Factura</span>
                    <span class="text-3xl font-black text-white leading-none">${{ number_format($compra->total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 text-left">
            <!-- Bloque Izquierdo: Info Proveedor -->
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden h-full relative">
                <div class="p-6 border-b border-white/10 bg-white/5 relative z-10">
                    <h3 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Información del Proveedor</h3>
                </div>
                <div class="p-8 space-y-6 relative z-10">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl font-black uppercase shadow-2xl shadow-blue-500/40 border border-white/20">
                            {{ substr($compra->proveedor->nombre, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-2xl font-black text-white uppercase leading-tight">{{ $compra->proveedor->nombre }}</p>
                            <p class="text-md text-blue-200/60 uppercase font-bold tracking-widest mt-1">{{ $compra->proveedor->email ?? 'SIN EMAIL' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloque Derecho: Datos de Registro -->
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden h-full">
                <div class="p-6 border-b border-white/10 bg-white/5">
                    <h3 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Datos de la Compra</h3>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1">Folio OC</p>
                            <p class="text-lg text-white font-black uppercase">{{ $compra->folio ?? '---' }}</p>
                        </div>
                        <div>
                            <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1">Número de Factura</p>
                            <p class="text-lg text-white font-black uppercase">{{ $compra->factura ?? 'SIN FACTURA' }}</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1">Fecha de Compra</p>
                            <p class="text-lg text-white font-black uppercase">{{ \Carbon\Carbon::parse($compra->fecha_compra)->translatedFormat('d F, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-md text-blue-200/40 font-black uppercase tracking-widest mb-1">Registrado en Sistema</p>
                            <p class="text-lg text-white font-black uppercase">{{ $compra->created_at->translatedFormat('d F, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Artículos -->
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8 text-left">
            <div class="p-6 border-b border-white/10 bg-white/5">
                <h2 class="text-xl font-bold text-white uppercase tracking-tight">Detalle de Adquisición</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-center">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-center">Cant.</th>
                            <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Producto</th>
                            <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Descripción</th>
                            <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-center">P. Unitario</th>
                            <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($compra->detalles as $detalle)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-8 py-5 text-center">
                                    <span class="text-white font-mono font-bold text-lg">
                                        {{ $detalle->cantidad }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-left">
                                    <span class="text-white font-black uppercase text-lg leading-tight">{{ $detalle->producto->nombre }}</span>
                                </td>
                                <td class="px-8 py-5 text-left">
                                    <span class="text-md text-blue-200 font-bold uppercase tracking-wide">{{ $detalle->producto->descripcion ?? '---' }}</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-blue-100 font-mono text-lg font-bold">${{ number_format($detalle->precio_compra, 2) }}</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span class="text-white font-black font-mono text-xl tracking-tighter">${{ number_format($detalle->cantidad * $detalle->precio_compra, 2) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-white/5 border-t border-white/10">
                        <tr class="bg-white/10">
                            <td colspan="4" class="px-8 py-8 text-right">
                                <span class="text-blue-200 text-lg uppercase font-black tracking-[0.2em]">Total de Factura</span>
                            </td>
                            <td class="px-8 py-8 text-right">
                                <span class="text-3xl font-black text-white tracking-tighter">${{ number_format($compra->total, 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Nota informativa Premium -->
        <div class="bg-blue-600/10 border border-blue-500/20 backdrop-blur-xl rounded-2xl p-6 flex items-start gap-5 shadow-inner">
            <div class="p-3 bg-blue-500/20 rounded-xl text-blue-400 shrink-0 shadow-lg shadow-blue-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-blue-300 text-xs font-black uppercase tracking-[0.3em] mb-1">Nota del Sistema</p>
                <div class="text-md text-blue-100 uppercase font-bold leading-relaxed">
                    Esta compra ha sido procesada exitosamente. Se han actualizado automáticamente las existencias y los precios de costo en el inventario maestro. 
                    El historial permanece inalterable para fines de auditoría.
                </div>
            </div>
        </div>
    </div>
@endsection
