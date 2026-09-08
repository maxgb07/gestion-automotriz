@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Main Content -->
    <div class="w-full">
        <!-- Welcome Section -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight uppercase">{{ config('app.name') }}</h1>
            <div class="w-24 h-1.5 bg-gradient-to-r from-blue-500 to-purple-600 mx-auto rounded-full"></div>
        </div>

        <!-- Accesos rápidos -->
        <h2 class="text-white/70 text-sm font-bold uppercase tracking-widest mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
            <a href="{{ route('ventas.create') }}" class="group bg-gradient-to-br from-purple-600/30 to-purple-800/30 backdrop-blur-xl rounded-2xl p-8 border border-purple-500/30 hover:from-purple-600/40 hover:to-purple-800/40 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/20 cursor-pointer flex items-center gap-4">
                <div class="p-4 bg-purple-500/30 rounded-xl group-hover:bg-purple-500/40 transition-colors">
                    <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-white uppercase">Nueva Venta</span>
            </a>

            <a href="{{ route('ordenes.create') }}" class="group bg-gradient-to-br from-blue-600/30 to-blue-800/30 backdrop-blur-xl rounded-2xl p-8 border border-blue-500/30 hover:from-blue-600/40 hover:to-blue-800/40 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/20 cursor-pointer flex items-center gap-4">
                <div class="p-4 bg-blue-500/30 rounded-xl group-hover:bg-blue-500/40 transition-colors">
                    <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-white uppercase">Nueva Orden</span>
            </a>

            <a href="{{ route('compras.create') }}" class="group bg-gradient-to-br from-yellow-600/30 to-yellow-800/30 backdrop-blur-xl rounded-2xl p-8 border border-yellow-500/30 hover:from-yellow-600/40 hover:to-yellow-800/40 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-yellow-500/20 cursor-pointer flex items-center gap-4">
                <div class="p-4 bg-yellow-500/30 rounded-xl group-hover:bg-yellow-500/40 transition-colors">
                    <svg class="w-8 h-8 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-white uppercase">Nueva Compra</span>
            </a>
        </div>

        <!-- Pendientes por atender -->
        <h2 class="text-white/70 text-sm font-bold uppercase tracking-widest mb-4">Pendientes por Atender</h2>
        <div class="space-y-8">

            <!-- Órdenes por entregar -->
            <div id="ordenes-pendientes" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 overflow-hidden scroll-mt-24">
                <div class="flex items-center justify-between p-6 border-b border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-500/20 rounded-xl">
                            <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white uppercase">Órdenes por Entregar</h3>
                    </div>
                    <span class="text-3xl font-black text-white">{{ $ordenesPendientesTotal }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Fecha</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Folio</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Cliente</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Vehículo (Placas)</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($ordenesPendientes as $orden)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-3 text-sm text-white">{{ \Carbon\Carbon::parse($orden->fecha_entrada)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3 text-sm text-white">{{ $orden->folio }}</td>
                                    <td class="px-6 py-3 text-sm text-white">{{ optional($orden->cliente)->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 text-sm text-white">
                                        @if($orden->vehiculo)
                                            {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} ({{ $orden->vehiculo->placas ?? 'S/P' }})
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-white text-right">${{ number_format($orden->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-blue-200/60">No hay órdenes pendientes de entrega.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($ordenesPendientes->hasPages())
                    <div class="p-4 border-t border-white/10">
                        {{ $ordenesPendientes->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>

            <!-- Stock bajo o agotado -->
            <div id="stock-bajo" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 overflow-hidden scroll-mt-24">
                <div class="flex items-center justify-between p-6 border-b border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-red-500/20 rounded-xl">
                            <svg class="w-6 h-6 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white uppercase">Stock Bajo o Agotado</h3>
                    </div>
                    <span class="text-3xl font-black text-white">{{ $stockBajoTotal }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Nombre</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Descripción</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Aplicación</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-center">Clasificación</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-center">Stock</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-center">Mínimo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($stockBajo as $producto)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-3 text-sm text-white">{{ $producto->nombre }}</td>
                                    <td class="px-6 py-3 text-sm text-white">{{ $producto->descripcion }}</td>
                                    <td class="px-6 py-3 text-sm text-white">{{ $producto->aplicacion }}</td>
                                    <td class="px-6 py-3 text-sm text-white text-center">{{ $producto->clasificacion }}</td>
                                    <td class="px-6 py-3 text-sm text-center {{ $producto->stock <= 0 ? 'text-red-400 font-bold' : 'text-white' }}">{{ $producto->stock }}</td>
                                    <td class="px-6 py-3 text-sm text-white text-center">{{ $producto->stock_minimo }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-blue-200/60">No hay productos A/B con stock bajo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($stockBajo->hasPages())
                    <div class="p-4 border-t border-white/10">
                        {{ $stockBajo->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>

            <!-- Cuentas por pagar por vencer -->
            <div id="cuentas-por-pagar" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 overflow-hidden scroll-mt-24">
                <div class="flex items-center justify-between p-6 border-b border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-yellow-500/20 rounded-xl">
                            <svg class="w-6 h-6 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white uppercase">Cuentas por Pagar</h3>
                    </div>
                    <span class="text-3xl font-black text-white">{{ $cuentasPorPagarTotal }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Fecha de Vencimiento</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Proveedor</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-center">Total de Facturas</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-right">Total a Pagar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($cuentasPorPagar as $fila)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-3 text-sm text-white">{{ \Carbon\Carbon::parse($fila->fecha_vencimiento)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3 text-sm text-white">{{ $fila->proveedor }}</td>
                                    <td class="px-6 py-3 text-sm text-white text-center">{{ $fila->total_facturas }}</td>
                                    <td class="px-6 py-3 text-sm text-white text-right">${{ number_format($fila->total_pagar, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-blue-200/60">No hay cuentas por pagar próximas a vencer.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($cuentasPorPagar->hasPages())
                    <div class="p-4 border-t border-white/10">
                        {{ $cuentasPorPagar->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>

            <!-- Cuentas por cobrar vencidas -->
            <div id="cuentas-por-cobrar" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 overflow-hidden scroll-mt-24">
                <div class="flex items-center justify-between p-6 border-b border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-rose-500/20 rounded-xl">
                            <svg class="w-6 h-6 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white uppercase">Cuentas por Cobrar</h3>
                    </div>
                    <span class="text-3xl font-black text-white">{{ $cuentasPorCobrarTotal }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Fecha de Vencimiento</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-left">Cliente</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-center">Total de Documentos Vencidos</th>
                                <th class="px-6 py-3 text-xs font-semibold text-blue-200 uppercase tracking-wider text-right">Total $</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($cuentasPorCobrar as $fila)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-3 text-sm text-white">{{ \Carbon\Carbon::parse($fila->fecha_vencimiento)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-3 text-sm text-white">{{ $fila->cliente }}</td>
                                    <td class="px-6 py-3 text-sm text-white text-center">{{ $fila->total_documentos }}</td>
                                    <td class="px-6 py-3 text-sm text-white text-right">${{ number_format($fila->total_saldo, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-blue-200/60">No hay créditos de cliente vencidos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($cuentasPorCobrar->hasPages())
                    <div class="p-4 border-t border-white/10">
                        {{ $cuentasPorCobrar->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        // Al paginar cualquiera de las tablas de "Pendientes por Atender" la URL
        // trae un hash (#ordenes-pendientes, #stock-bajo, etc). El navegador debería
        // saltar solo a esa sección, pero en la práctica el salto no ocurre de forma
        // confiable (contenido que aún se está reflow-eando, orden de eventos del
        // navegador, etc), así que forzamos el scroll manualmente como respaldo.
        function irAHashDashboard() {
            if (!window.location.hash) return;
            const el = document.querySelector(window.location.hash);
            if (el) {
                el.scrollIntoView({ behavior: 'instant', block: 'start' });
            }
        }
        irAHashDashboard();
        window.addEventListener('load', irAHashDashboard);
    </script>
@endsection
