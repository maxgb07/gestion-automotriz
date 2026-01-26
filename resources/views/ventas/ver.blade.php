@extends('layouts.app')

@section('title', 'Detalle de Venta ' . $venta->folio)

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
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('ventas.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors uppercase text-xs font-bold tracking-widest">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Historial
                </a>
            </div>
            <div class="flex items-center gap-4">
                <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Folio: {{ $venta->folio }}</h1>
                @php
                    $color = match($venta->estado) {
                        'PAGADA' => 'bg-green-500/20 text-green-300 border-green-500/30',
                        'PENDIENTE' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                        'CANCELADA' => 'bg-red-500/20 text-red-300 border-red-500/30',
                    };
                @endphp
                <span class="px-4 py-1.5 rounded-full text-xl font-black uppercase border {{ $color }}">
                    {{ $venta->estado }}
                </span>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Fila 1: Datos Generales -->
            <div class="w-full mb-8">
                <!-- Card: Información del Cliente y Venta -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                         <svg class="w-24 h-24 text-white/5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-left relative z-10">
                        <div class="lg:col-span-1">
                            <p class="text-white font-black text-2xl uppercase leading-tight">{{ $venta->cliente->nombre }}</p>
                            <div class="space-y-1.5 mt-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-200/40 text-md font-black uppercase tracking-widest min-w-[70px]">Teléfono:</span>
                                    <span class="text-blue-100/70 text-md uppercase font-bold">{{ $venta->cliente->telefono ?? 'S/T' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-200/40 text-md font-black uppercase tracking-widest min-w-[70px]">Celular:</span>
                                    <span class="text-blue-100/70 text-md uppercase font-bold">{{ $venta->cliente->celular ?? 'S/C' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-blue-200/40 text-md font-black uppercase tracking-widest min-w-[70px]">Mail:</span>
                                    <span class="text-blue-100/70 text-md lowercase font-bold">{{ $venta->cliente->email ?? 'S/E' }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-blue-200/40 text-md font-black uppercase tracking-[0.2em] mb-2">RFC</p>
                            <p class="text-white font-bold text-lg uppercase">{{ $venta->cliente->rfc ?? 'XAXX010101000' }}</p>
                        </div>
                        <div class="lg:col-span-2">
                            <p class="text-blue-200/40 text-md font-black uppercase tracking-[0.2em] mb-2">Ubicación</p>
                            <p class="text-white font-bold text-sm uppercase leading-relaxed">
                                {{ $venta->cliente->direccion ?? 'DIRECCIÓN NO REGISTRADA' }}{{ $venta->cliente->codigo_postal ? ', CP ' . $venta->cliente->codigo_postal : '' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fila 2: Resumen de Cuenta -->
            <div class="w-full mb-8">
                <!-- Card: Detalle de Items -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
                    <div class="p-6 border-b border-white/10 bg-white/5 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white uppercase tracking-tight">Resumen de Cuenta</h2>
                        <a href="{{ route('ventas.pdf', $venta) }}" target="_blank" class="btn-premium-blue px-4 py-2 text-white text-xs font-black rounded-lg shadow-lg shadow-blue-500/20 transition-all uppercase tracking-widest flex items-center justify-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Imprimir Comprobante
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-white/5 border-b border-white/10">
                                <tr>
                                    <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-center">Cant.</th>
                                    <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Clave</th>
                                    <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Descripción</th>
                                    <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-center">Precio Unitario</th>
                                    <!-- <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-center">Descuento</th> -->
                                    <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-right">Importe</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($venta->detalles as $detalle)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-5 text-center">
                                            <span class="text-white font-mono font-bold bg-white/5 px-3 py-1 rounded-lg border border-white/10">{{ $detalle->cantidad }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-white font-bold uppercase text-md font-mono">
                                                {{ $detalle->producto ? $detalle->producto->nombre : $detalle->servicio->sku }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-white font-bold uppercase text-md block">
                                                {{ $detalle->producto ? $detalle->producto->nombre : $detalle->servicio->nombre }}
                                            </span>
                                            <span class="text-md text-blue-200/40 uppercase tracking-widest mt-1 block line-clamp-1">
                                                {{ $detalle->producto ? $detalle->producto->descripcion : $detalle->servicio->descripcion }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="text-blue-100 font-mono text-md font-bold">${{ number_format($detalle->precio_unitario, 2) }}</span>
                                        </td>
                                        <!-- <td class="px-6 py-5 text-center">
                                            <span class="text-blue-100 font-mono text-md font-bold">{{ $detalle->descuento_porcentaje }}%</span>
                                        </td> -->
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-white font-black font-mono text-md">${{ number_format($detalle->subtotal, 2) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-white/5 border-t border-white/10">
                                <!-- @if($venta->descuento > 0)
                                <tr>
                                    <td colspan="5" class="px-8 py-3 text-right">
                                        <span class="text-white text-md uppercase font-bold tracking-widest">Subtotal</span>
                                    </td>
                                    <td class="px-8 py-3 text-right">
                                        <span class="text-white font-mono text-md font-bold">${{ number_format($venta->detalles->sum('subtotal') + $venta->descuento, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="px-8 py-3 text-right">
                                        <span class="text-white text-md uppercase font-bold tracking-widest">Descuento</span>
                                    </td>
                                    <td class="px-8 py-3 text-right">
                                        <span class="text-white font-mono text-md font-bold">-${{ number_format($venta->descuento, 2) }}</span>
                                    </td>
                                </tr>
                                @endif -->
                                <tr class="bg-white/10">
                                    <td colspan="4" class="px-8 py-6 text-right">
                                        <span class="text-blue-200 text-md uppercase font-black tracking-widest">Total de la Venta</span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <span class="text-white font-black text-md tracking-tighter">${{ number_format($venta->total, 2) }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Fila 3: Seguimiento de Pagos (Saldo e Historial combinados) -->
            <div class="w-full mb-8">
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
                    <!-- Cabecera del Card: Saldo Pendiente -->
                    <div class="p-8 border-b border-white/10 bg-white/5 flex flex-col lg:flex-row justify-between items-center gap-8 relative overflow-hidden">
                         <div class="absolute -bottom-6 -right-6 text-white/5 pointer-events-none">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.82v-1.91c-1.39-.24-2.82-.91-3.7-1.81l1.42-1.42c.78.73 1.83 1.25 2.91 1.44v-3.79c-2.02-.69-4.24-1.31-4.24-4.05 0-1.89 1.4-3.5 3.32-3.87V2.91h2.82v1.94c1.3.17 2.45.74 3.25 1.54l-1.42 1.42c-.52-.46-1.18-.81-1.83-.96v3.74c2.2.82 4.24 1.7 4.24 4.19 0 2-1.54 3.4-3.67 3.81zM10.63 8.35c0-.62.46-.94 1.05-.94.49 0 .88.2 1.14.49l.01-.01v1.65c-.39-.18-.75-.32-1.15-.46-.62-.21-1.05-.41-1.05-.73zm2.74 7.3c0 .67-.53 1.05-1.19 1.05-.59 0-1.08-.25-1.37-.62v-1.92c.38.21.84.38 1.43.59.61.21 1.13.44 1.13.9z"/>
                            </svg>
                        </div>

                        <!-- Valores Centrales (Centro - Stacked in Rows) -->
                        <div class="flex-grow flex flex-col items-center justify-center gap-8 relative z-10 py-4">
                            <!-- Fila: Saldo Actual -->
                            <div class="text-center group">
                                <p class="text-md text-blue-200/40 font-black uppercase tracking-[0.25em] mb-2" style="font-size: 1rem; font-weight: bold;">Saldo Pendiente de Cobro</p>
                                <div class="text-2xl font-black {{ $venta->saldo_pendiente > 0 ? 'text-red-400' : 'text-green-400' }} tracking-tighter leading-none transition-transform group-hover:scale-105" style="font-size: 1rem; font-weight: bold;">
                                    ${{ number_format($venta->saldo_pendiente, 2) }}
                                </div>
                            </div>

                            <!-- Fila: Vencimiento -->
                            @if($venta->fecha_vencimiento)
                                <div class="text-center group">
                                    <p class="text-[10px] text-blue-200/40 font-black uppercase tracking-[0.25em] mb-2" style="font-size: 1rem; font-weight: bold;">Fecha de Vencimiento</p>
                                    <div class="flex items-center justify-center gap-4">
                                        <span class="w-4 h-4 rounded-full {{ now()->isAfter($venta->fecha_vencimiento) ? 'bg-red-500 animate-pulse outline outline-[6px] outline-red-500/10' : 'bg-blue-400 outline outline-[6px] outline-blue-400/10' }}" style="font-size: 1rem; font-weight: bold;"></span>
                                        <span class="text-[3rem] font-black uppercase tracking-tight {{ now()->isAfter($venta->fecha_vencimiento) ? 'text-red-400' : 'text-white' }} leading-none transition-transform group-hover:scale-105" style="font-size: 1rem; font-weight: bold;">
                                            {{ $venta->fecha_vencimiento->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Acción (Derecha) -->
                        <div class="lg:w-1/4 flex justify-center lg:justify-end relative z-10">
                            @if($venta->saldo_pendiente > 0)
                                <button type="button" onclick="abrirModalPago()" class="inline-flex items-center px-6 py-2.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-300 text-[10px] font-bold rounded-lg border border-blue-500/20 transition-all uppercase tracking-widest cursor-pointer shadow-lg shadow-blue-500/5 group">
                                    <svg class="w-4 h-4 mr-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Registrar Abono
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Cuerpo del Card: Historial de Abonos (Tabla) -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-white/5 border-b border-white/10">
                                <tr>
                                    <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Fecha de Pago</th>
                                    <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-center">Método</th>
                                    <th class="px-6 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Referencia</th>
                                    <th class="px-8 py-5 text-md font-bold text-blue-200 uppercase tracking-widest text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse($venta->pagos as $pago)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-green-500/10 border border-green-500/20 flex items-center justify-center text-green-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-white font-bold text-md">{{ $pago->fecha_pago->format('d/m/Y') }}</p>
                                                    <p class="text-md text-blue-200/40 uppercase font-bold tracking-wider">{{ $pago->fecha_pago->format('h:i A') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="px-3 py-1 rounded bg-blue-500/10 text-md text-blue-300 font-bold uppercase tracking-widest border border-blue-500/20">
                                                {{ $pago->metodo_pago }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-white/60 font-mono text-md uppercase">{{ $pago->referencia ?? '---' }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-white font-black font-mono text-md">${{ number_format($pago->monto, 2) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-16 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 bg-white/5 border border-dashed border-white/10 rounded-full flex items-center justify-center mb-4 text-white/10">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm-5-8h.01M17 16h.01"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-[10px] text-blue-200/30 uppercase font-black tracking-widest italic">No hay abonos registrados para esta venta</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function abrirModalPago() {
            const saldoPendiente = {{ $venta->saldo_pendiente }};
            const route = "{{ route('ventas.pagos.store', $venta) }}";
            const csrfToken = "{{ csrf_token() }}";
            const fechaHoy = "{{ date('Y-m-d\TH:i') }}";

            Swal.fire({
                title: 'REGISTRAR ABONO',
                html: `
                    <div class="text-left mt-4 space-y-6">
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/10 text-center mb-6">
                            <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-widest mb-1">Saldo Pendiente</p>
                            <p class="text-3xl font-black text-white">$${new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(saldoPendiente)}</p>
                        </div>

                        <form id="form-pago-dinamico" action="${route}" method="POST">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1">Monto a abonar *</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="monto" value="${saldoPendiente}" max="${saldoPendiente}" 
                                            class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-black text-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all shadow-inner" required>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1">Fecha del Pago *</label>
                                    <input type="datetime-local" name="fecha_pago" value="${fechaHoy}" 
                                        class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all uppercase shadow-inner" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1">Método de Pago *</label>
                                    <select name="metodo_pago" class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all uppercase shadow-inner cursor-pointer" required>
                                        <option value="EFECTIVO" style="color: black !important;">EFECTIVO</option>
                                        <option value="TARJETA" style="color: black !important;">TARJETA</option>
                                        <option value="TRANSFERENCIA" style="color: black !important;">TRANSFERENCIA</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1">Referencia</label>
                                    <input type="text" name="referencia" class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/20 shadow-inner" placeholder="EJ. FOLIO 123">
                                </div>
                            </div>
                        </form>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'CONFIRMAR PAGO',
                cancelButtonText: 'DESCARTAR',
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: 'rgba(255, 255, 255, 0.1)',
                background: 'rgba(15, 23, 42, 0.95)',
                color: '#fff',
                width: '600px',
                customClass: {
                    popup: 'backdrop-blur-xl border border-white/20 rounded-[3rem] p-8',
                    confirmButton: 'px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs ml-4 bg-gradient-to-r from-blue-500 to-purple-600',
                    cancelButton: 'px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs border border-white/10'
                },
                preConfirm: () => {
                    const form = document.getElementById('form-pago-dinamico');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-pago-dinamico').submit();
                }
            });
        }
    </script>
@endsection
