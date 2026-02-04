@extends('layouts.app')

@section('title', 'Detalle de Compra')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <a href="{{ route('compras.index') }}" class="inline-flex items-center text-blue-300 hover:text-white transition-colors mb-4">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    VOLVER AL HISTORIAL
                </a>
                <h1 class="text-3xl font-bold text-white uppercase">{{ $compra->folio ?? 'DETALLE DE COMPRA' }}</h1>
                <p class="text-blue-200">Factura: {{ $compra->factura ?? 'SIN FACTURA' }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl px-6 py-3 border border-white/20">
                <span class="text-xs text-blue-200 uppercase block">Total Factura</span>
                <span class="text-2xl font-black text-white">${{ number_format($compra->total, 2) }}</span>
            </div>
        </div>

        <!-- Card principal -->
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8">
            <div class="p-8 border-b border-white/10 grid grid-cols-1 md:grid-cols-2 gap-8 bg-white/5">
                <div>
                    <h3 class="text-xs font-bold text-blue-300 uppercase mb-3 tracking-widest">Información del Proveedor</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold uppercase shadow-lg shadow-blue-500/20">
                            {{ substr($compra->proveedor->nombre, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-lg font-bold text-white uppercase">{{ $compra->proveedor->nombre }}</p>
                            <p class="text-sm text-blue-200/70 lowercase">{{ $compra->proveedor->email ?? 'sin email registrado' }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-blue-300 uppercase mb-3 tracking-widest">Datos de Registro</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-blue-200/60 uppercase text-xs">Folio OC:</span>
                            <span class="text-white font-bold uppercase text-xs">{{ $compra->folio ?? '---' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-200/60 uppercase text-xs">Factura:</span>
                            <span class="text-white font-bold uppercase text-xs">{{ $compra->factura ?? 'SIN FACTURA' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-200/60 uppercase text-xs">Fecha Compra:</span>
                            <span class="text-white font-bold uppercase text-xs">{{ \Carbon\Carbon::parse($compra->fecha_compra)->translatedFormat('d F, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-200/60 uppercase text-xs">Fecha Registro:</span>
                            <span class="text-white font-bold uppercase text-xs">{{ $compra->created_at->translatedFormat('d F, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-0">
                <table class="w-full text-left">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-bold text-blue-200 uppercase tracking-widest text-center">Producto</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-blue-200 uppercase tracking-widest text-center">Cantidad</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-blue-200 uppercase tracking-widest text-center">P. Unitario</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-blue-200 uppercase tracking-widest text-center">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($compra->detalles as $detalle)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-8 py-4 text-center">
                                    <div class="flex flex-col">
                                        <span class="text-white font-bold uppercase">{{ $detalle->producto->nombre }}</span>
                                        <span class="text-[10px] text-blue-300 font-mono">SKU: {{ $detalle->producto->sku ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-3 py-1 bg-white/5 rounded-lg text-white font-bold border border-white/10">{{ $detalle->cantidad }}</span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="text-blue-100">${{ number_format($detalle->precio_compra, 2) }}</span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="text-white font-bold">${{ number_format($detalle->cantidad * $detalle->precio_compra, 2) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-white/5">
                        <tr>
                            <td colspan="3" class="px-8 py-6 text-right">
                                <span class="text-blue-200 font-bold uppercase tracking-widest">Total de la Compra:</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="text-2xl font-black text-white">${{ number_format($compra->total, 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Nota informativa -->
        <div class="bg-blue-600/10 border border-blue-500/20 rounded-2xl p-6 flex items-start gap-4">
            <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-100 uppercase leading-relaxed">
                Esta compra actualizó automáticamente las existencias y los precios en el inventario al momento de su registro. 
                Los precios de venta sugeridos se guardaron individualmente para cada producto en esta transacción.
            </div>
        </div>
    </div>
@endsection
