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
                    {{ $venta->estado == 'PENDIENTE' ? 'PENDIENTE DE PAGO' : $venta->estado }}
                </span>
            </div>
        </div>

        @if($venta->estado === 'CANCELADA')
            <div class="mb-8 bg-red-500/10 border border-red-500/20 rounded-3xl p-6 backdrop-blur-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-20 h-20 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
                <div class="flex items-start gap-4 h-full relative z-10">
                    <div class="p-3 bg-red-500/20 rounded-2xl text-red-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h3 class="text-red-300 font-black uppercase tracking-widest text-sm mb-1">VENTA CANCELADA</h3>
                        <p class="text-white font-bold text-lg uppercase leading-tight">{{ $venta->motivo_cancelacion }}</p>
                        <p class="text-red-300/60 text-[10px] font-black uppercase tracking-[0.2em] mt-3 italic">
                            Cancelado el {{ $venta->cancelado_at ? $venta->cancelado_at->format('d/m/Y H:i') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

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

            @if($venta->observaciones)
            <!-- Observaciones -->
            <div class="w-full mb-8">
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-6 border border-blue-400/20 shadow-2xl flex items-start gap-4">
                    <div class="p-3 bg-blue-500/10 rounded-2xl text-blue-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-blue-300 text-xs font-black uppercase tracking-widest mb-1">Observaciones</p>
                        <p class="text-white font-bold uppercase text-md leading-relaxed">{{ $venta->observaciones }}</p>
                    </div>
                </div>
            </div>
            @endif

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
                                                {{ $detalle->producto?->nombre ?? $detalle->servicio?->sku ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-white font-bold uppercase text-md block">
                                                {{ $detalle->producto?->nombre ?? $detalle->servicio?->nombre ?? 'N/A' }}
                                            </span>
                                            <span class="text-md text-blue-200/40 uppercase tracking-widest mt-1 block line-clamp-1">
                                                {{ $detalle->producto?->descripcion ?? $detalle->servicio?->descripcion ?? '---' }}
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
            <!-- Seguimiento de Pagos (Saldo e Historial) -->
            <div class="w-full mb-8">
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                    <div class="p-6 border-b border-white/10 bg-white/5 flex items-center justify-between">
                        <h3 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Gestión de Abonos</h3>
                        @if($venta->saldo_pendiente > 0 && $venta->estado !== 'CANCELADA')
                            <button onclick="abrirModalPago()" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-md font-black rounded-lg transition-all uppercase tracking-widest flex items-center shadow-lg shadow-green-900/40">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Registrar Pago
                            </button>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="mb-6 @if($venta->saldo_pendiente > 0) bg-red-500/5 border-red-500/20 @else bg-green-500/5 border-green-500/20 @endif border rounded-2xl p-4 text-center">
                            <p class="text-md text-white/40 uppercase font-black tracking-[0.3em] mb-1">Saldo Pendiente</p>
                            <p @class([
                                'text-2xl font-black font-mono tracking-tighter',
                                'text-red-400' => $venta->saldo_pendiente > 0,
                                'text-green-400' => $venta->saldo_pendiente == 0
                            ])>${{ number_format($venta->saldo_pendiente, 2) }}</p>
                        </div>

                        @if($venta->pagos->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr class="border-b border-white/10 uppercase tracking-widest text-[10px] font-black text-blue-200/40">
                                            <th class="px-2 py-3 text-center">Fecha</th>
                                            <th class="px-2 py-3 text-center">Método</th>
                                            <th class="px-2 py-3 text-center">Referencia</th>
                                            <th class="px-2 py-3 text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach($venta->pagos as $pago)
                                            <tr class="hover:bg-white/5 transition-colors group">
                                                <td class="px-2 py-3 text-md text-white/70 font-bold uppercase text-center">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                                <td class="px-2 py-3 text-md text-blue-300/50 font-black uppercase text-center">
                                                    <span class="px-2 py-0.5 rounded bg-blue-500/10 border border-blue-500/20">
                                                        {{ $pago->metodo_pago }}
                                                    </span>
                                                </td>
                                                <td class="px-2 py-3 text-md text-white/30 font-mono uppercase italic text-center">{{ $pago->referencia ?: '-' }}</td>
                                                <td class="px-2 py-3 text-right text-md font-black text-white font-mono tracking-tighter">${{ number_format($pago->monto, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-md text-white/20 text-center uppercase font-black tracking-widest py-4 italic">No hay abonos registrados para esta venta</p>
                        @endif
                    </div>
                </div>
            </div>
</div>
        </div>
    </div>

    <script>
        function abrirModalPago() {
            const ventaId = {{ $venta->id }};
            const folio = "{{ $venta->folio }}";
            const saldoPendiente = {{ $venta->saldo_pendiente }};
            const fechaHoy = "{{ date('Y-m-d\TH:i') }}";

            Swal.fire({
                title: 'REGISTRAR PAGO - ' + folio,
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="text-left mt-4 space-y-6">
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/10 text-center mb-6">
                            <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-widest mb-1">Saldo Pendiente</p>
                            <p class="text-3xl font-black text-white">$${new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(saldoPendiente)}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1 text-center">Monto a abonar *</label>
                                <input type="number" step="0.01" id="pago_monto" value="${saldoPendiente}" max="${saldoPendiente}" 
                                    class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-black text-xl text-center focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all shadow-inner" required>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1 text-center">Fecha del Pago *</label>
                                <input type="datetime-local" id="pago_fecha" value="${fechaHoy}" 
                                    class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all uppercase shadow-inner" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1 text-center">Método de Pago *</label>
                                <select id="pago_metodo" class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all uppercase shadow-inner cursor-pointer" required>
                                    <option value="EFECTIVO" style="color: black !important;">EFECTIVO</option>
                                    <option value="TARJETA DE DÉBITO" style="color: black !important;">TARJETA DE DÉBITO</option>
                                    <option value="TARJETA DE CRÉDITO" style="color: black !important;">TARJETA DE CRÉDITO</option>
                                    <option value="TRANSFERENCIA" style="color: black !important;">TRANSFERENCIA</option>
                                    <option value="CHEQUE" style="color: black !important;">CHEQUE</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-blue-200 uppercase tracking-widest ml-1 text-center">Referencia</label>
                                <input type="text" id="pago_referencia" class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-bold text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all uppercase placeholder-white/20 shadow-inner" placeholder="PAGO VENTA ...">
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'REGISTRAR PAGO',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#475569',
                customClass: {
                    container: 'backdrop-blur-sm',
                    popup: 'rounded-3xl border border-white/10 shadow-2xl transition-all duration-300',
                    title: 'text-xl font-black uppercase tracking-tighter pt-6',
                    confirmButton: 'rounded-xl px-12 py-3 font-bold uppercase tracking-widest text-sm',
                    cancelButton: 'rounded-xl px-12 py-3 font-bold uppercase tracking-widest text-sm'
                },
                preConfirm: () => {
                    const monto = document.getElementById('pago_monto').value;
                    const fecha = document.getElementById('pago_fecha').value;
                    const metodo = document.getElementById('pago_metodo').value;
                    const referencia = document.getElementById('pago_referencia').value;

                    if (!monto || monto <= 0) {
                        Swal.showValidationMessage('El monto es inválido');
                        return false;
                    }
                    if (monto > saldoPendiente) {
                        Swal.showValidationMessage('El monto no puede exceder el saldo');
                        return false;
                    }
                    if (!fecha) {
                        Swal.showValidationMessage('La fecha es obligatoria');
                        return false;
                    }

                    return { monto, fecha, metodo, referencia };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando pago...',
                        background: '#1e293b',
                        color: '#fff',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(`/ventas/${ventaId}/pagos`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            monto: result.value.monto,
                            fecha_pago: result.value.fecha,
                            metodo_pago: result.value.metodo,
                            referencia: result.value.referencia
                        })
                    })
                    .then(response => response.json().then(data => ({ status: response.status, data })))
                    .then(({ status, data }) => {
                        if (status === 200 || status === 201) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡PAGO REGISTRADO!',
                                text: data.message,
                                background: '#1e293b',
                                color: '#fff',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Error al procesar el pago');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'ERROR',
                            text: error.message || 'Error al procesar el pago',
                            background: '#1e293b',
                            color: '#fff'
                        });
                    });
                }
            });
        }
    </script>
@endsection
