<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-xl animate-fadeIn">
    <table class="w-full text-left border-collapse">
        <thead class="bg-white/5 border-b border-white/10 font-bold uppercase tracking-widest">
            <tr>
                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider">Tipo</th>
                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider">Folio</th>
                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Emisión</th>
                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center text-red-400">Vencimiento</th>
                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Total</th>
                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Saldo</th>
                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Estado</th>
                <th class="px-6 py-4 text-md font-semibold text-blue-200 uppercase tracking-wider text-right pr-12">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @foreach($documentos as $doc)
                <tr class="hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-md {{ $doc->tipo_doc == 'VENTA' ? 'bg-indigo-500/20 text-indigo-300' : 'bg-blue-500/20 text-blue-300' }} text-[9px] font-black uppercase tracking-widest border border-white/5">
                            {{ $doc->tipo_doc }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-white font-black text-md">{{ $doc->folio }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-white/60 text-md">{{ $doc->fecha_doc->format('d/m/Y') }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-md font-black {{ $doc->estado_color == 'rojo' ? 'text-red-400' : ($doc->estado_color == 'amarillo' ? 'text-yellow-400' : 'text-emerald-400') }}">
                            {{ $doc->fecha_vencimiento->format('d/m/Y') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-white/60 text-md">${{ number_format($doc->total, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span @class([
                            'font-black text-md inline-block group-hover:scale-110 transition-transform',
                            'text-red-400' => $doc->estado_color == 'rojo',
                            'text-yellow-400' => $doc->estado_color == 'amarillo',
                            'text-emerald-400' => $doc->estado_color == 'verde',
                        ])>
                            ${{ number_format($doc->saldo_pendiente, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($doc->estado_color == 'rojo')
                            <span class="text-red-400 font-black text-md uppercase tracking-widest">VENCIDO</span>
                        @elseif($doc->estado_color == 'amarillo')
                            <span class="text-yellow-400 font-black text-md uppercase tracking-widest">POR VENCER</span>
                        @else
                            <span class="text-emerald-400 font-black text-md uppercase tracking-widest">AL DÍA</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2 pr-4">
                            @php
                                $modalData = [
                                    'folio' => $doc->folio,
                                    'tipo' => $doc->tipo_doc,
                                    'cliente' => $doc->cliente->nombre,
                                    'items' => $doc->items_json,
                                    'total' => number_format($doc->total, 2),
                                    'vehiculo' => $doc->vehiculo_info
                                ];
                                $routePdf = ($doc->tipo_doc == 'VENTA') ? route('ventas.pdf', $doc->id) : route('ordenes.pdf', $doc->id);
                            @endphp
                            
                            <!-- Ver Detalle (SWAL) -->
                            <button onclick='showDocDetails(@json($modalData))' class="p-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-300 rounded-lg border border-blue-500/10 transition-all" title="Ver Detalle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>

                            <!-- Registrar Pago -->
                            <button onclick="abrirModalPago('{{ $doc->id }}', '{{ $doc->tipo_doc }}', {{ $doc->total }}, {{ $doc->saldo_pendiente }})" 
                                    class="p-2 bg-green-500/10 hover:bg-green-500/20 text-green-300 rounded-lg border border-green-500/10 transition-all" title="Registrar Pago">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>

                            <!-- Imprimir -->
                            <a href="{{ $routePdf }}" target="_blank" class="p-2 bg-green-500/10 hover:bg-green-500/20 text-green-300 rounded-lg border border-green-500/10 transition-all" title="Imprimir Comprobante">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
