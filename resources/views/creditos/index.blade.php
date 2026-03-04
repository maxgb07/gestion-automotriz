@extends('layouts.app')

@section('title', 'Cuentas por Cobrar')

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
            color: white !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-radius: 8px !important;
            color: black !important;
            padding: 8px !important;
            background-color: #f8fafc !important;
            border: 1px solid rgba(0,0,0,0.1) !important;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white uppercase">Cuentas por Cobrar</h1>
            <p class="text-blue-200">Seguimiento de saldos pendientes y cobranza</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
        <form action="{{ route('creditos.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="md:flex-[3] w-full">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Seleccionar Cliente</label>
                <select name="cliente_id" id="cliente_id" class="select2-select">
                    <option value="">TODOS LOS CLIENTES CON DEUDA</option>
                    @foreach($todosLosClientesConDeuda as $c)
                        <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:flex-1 w-full">
                <!-- Espacio para consistencia con ventas.index -->
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-fit px-8 py-3 h-[50px] bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase flex items-center justify-center">
                    BUSCAR
                </button>
                @if(request('cliente_id'))
                    <a href="{{ route('creditos.index') }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center uppercase">
                        LIMPIAR
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Clients Table -->
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5 font-bold uppercase tracking-widest">
                        <th class="px-6 py-5 text-md font-semibold text-blue-200 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-5 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Documentos</th>
                        <th class="px-6 py-5 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Saldo Total</th>
                        <th class="px-6 py-5 text-md font-semibold text-blue-200 uppercase tracking-wider text-center">Estado</th>
                        <th class="px-6 py-5 text-md font-semibold text-blue-200 uppercase tracking-wider text-right pr-12">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($clientes as $cliente)
                        <tr class="group hover:bg-white/5 transition-all duration-200 cursor-pointer" onclick="toggleDetails({{ $cliente->id }})">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 flex items-center justify-center text-blue-300 font-black border border-blue-500/20 group-hover:scale-110 transition-transform duration-300 text-md">
                                        {{ substr($cliente->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-white group-hover:text-blue-300 transition-colors text-md">{{ $cliente->nombre }}</div>
                                        <div class="text-[10px] text-blue-300/40 font-bold uppercase tracking-widest">{{ $cliente->rfc ?? 'SIN RFC' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="px-3 py-1 rounded-lg bg-blue-500/10 text-blue-300 text-md font-black border border-blue-500/20">
                                    {{ $cliente->cant_documentos }} FOLIOS
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="font-black text-white text-md tracking-tighter">${{ number_format($cliente->saldo_total, 2) }}</div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($cliente->estado_color == 'rojo')
                                    <span class="flex items-center justify-center gap-2 text-red-400 font-black text-md uppercase tracking-widest">
                                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 shadow-[0_0_10px_rgba(239,68,68,0.5)] animate-pulse"></span>
                                        VENCIDO
                                    </span>
                                @elseif($cliente->estado_color == 'amarillo')
                                    <span class="flex items-center justify-center gap-2 text-yellow-400 font-black text-md uppercase tracking-widest">
                                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 shadow-[0_0_10px_rgba(250,204,21,0.5)]"></span>
                                        POR VENCER
                                    </span>
                                @else
                                    <span class="flex items-center justify-center gap-2 text-emerald-400 font-black text-md uppercase tracking-widest">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                                        AL DÍA
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right" onclick="event.stopPropagation()">
                                <div class="flex justify-end gap-2">
                                    {{-- WhatsApp --}}
                                    {{-- <button onclick="sendWhatsApp('{{ $cliente->id }}', '{{ $cliente->telefono }}')"
                                            class="p-2 bg-green-500/10 hover:bg-green-500/20 text-green-400 rounded-lg border border-green-500/10 transition-all cursor-pointer" 
                                            title="Enviar WhatsApp">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                    </button> --}}
                                    
                                    <!-- Estado de Cuenta -->
                                    <a href="{{ route('creditos.pdf', $cliente) }}" target="_blank"
                                       class="p-2 bg-green-500/10 hover:bg-green-500/20 text-green-300 rounded-lg border border-green-500/10 transition-all cursor-pointer" 
                                       title="Estado de Cuenta PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>
 
                                    <!-- Seguimiento -->
                                    <button onclick="openSeguimiento('{{ $cliente->id }}', '{{ $cliente->nombre }}')"
                                            class="p-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-lg border border-blue-500/10 transition-all cursor-pointer" 
                                            title="Comentarios de Seguimiento">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Sub-detalle Desplegable -->
                        <tr id="details-{{ $cliente->id }}" class="hidden bg-white/[0.02] border-b border-white/5">
                            <td colspan="5" class="px-12 py-8">
                                <div id="content-{{ $cliente->id }}" class="space-y-4 animate-fadeIn">
                                    <div class="flex items-center justify-center p-8">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4 opacity-20">
                                    <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-xs font-black uppercase tracking-[0.3em]">No hay clientes con saldo pendiente</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- El modal manual ha sido removido para usar SweetAlert dinámico -->

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let clienteActivoId = null;

    function toggleDetails(id) {
        const row = document.getElementById(`details-${id}`);
        const content = document.getElementById(`content-${id}`);

        if (row.classList.contains('hidden')) {
            // Cerrar otros abiertos si fuera necesario (opcional)
            row.classList.remove('hidden');
            
            // Cargar por AJAX
            fetch(`{{ url('/creditos/') }}/${id}`)
                .then(r => r.text())
                .then(html => {
                    content.innerHTML = html;
                });
        } else {
            row.classList.add('hidden');
        }
    }

    function openSeguimiento(id, nombre) {
        clienteActivoId = id;
        
        Swal.fire({
            title: nombre,
            html: `
                <div class="text-left">
                    <p class="text-blue-300/40 text-[10px] font-black uppercase tracking-widest mb-4">Seguimiento de Cobranza</p>
                    
                    <div id="swal-historial" class="space-y-4 max-h-[300px] overflow-y-auto pr-2 mb-6 scrollbar-thin bg-black/20 p-4 rounded-2xl border border-white/5">
                        <div class="text-center py-8 opacity-20">
                            <span class="text-[10px] font-black uppercase tracking-widest text-white">Cargando historial...</span>
                        </div>
                    </div>

                    <div class="space-y-3 pt-4 border-t border-white/5">
                        <label class="block text-[10px] font-black text-blue-300/40 uppercase tracking-[0.2em] ml-2">Nuevo Comentario</label>
                        <textarea id="swal-nuevo-comentario" rows="3" 
                                  class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-blue-300/20 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-sm uppercase"
                                  placeholder="ESCRIBA EL RESULTADO DE LA GESTIÓN..."></textarea>
                    </div>
                </div>
            `,
            width: '600px',
            background: '#1e293b',
            color: '#fff',
            showCancelButton: true,
            confirmButtonText: 'GUARDAR SEGUIMIENTO',
            cancelButtonText: 'CERRAR',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#475569',
            customClass: {
                popup: 'rounded-[2rem] border border-white/10 shadow-2xl',
                title: 'text-2xl font-black uppercase tracking-tighter pt-8 px-8 text-left border-b border-white/10 pb-4'
            },
            didOpen: () => {
                cargarHistorialSwal(id);
            },
            preConfirm: () => {
                const comentario = document.getElementById('swal-nuevo-comentario').value;
                if (!comentario) {
                    Swal.showValidationMessage('Debe escribir un comentario');
                    return false;
                }
                return { comentario: comentario };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                guardarComentario(id, result.value.comentario);
            }
        });
    }

    function cargarHistorialSwal(id) {
        const div = document.getElementById('swal-historial');
        fetch(`{{ url('/creditos/') }}/${id}/historial`)
            .then(r => r.text())
            .then(html => {
                div.innerHTML = html;
            });
    }

    function guardarComentario(id, comentario) {
        fetch(`{{ url('/creditos/') }}/${id}/comentario`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ comentario: comentario })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡GUARDADO!',
                    text: 'El comentario se registró correctamente',
                    background: '#1e293b',
                    color: '#fff',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }


    function sendWhatsApp(id, telefono) {
        if (!telefono) {
            Swal.fire('Error', 'El cliente no tiene un número registrado.', 'error');
            return;
        }
        
        // Limpiar teléfono
        telefono = telefono.replace(/\D/g, '');
        if (telefono.length === 10) telefono = '52' + telefono;

        const urlEstadoCuenta = `{{ url('/creditos/') }}/${id}/pdf`;
        const mensaje = encodeURIComponent(`Hola, le envío su estado de cuenta actualizado de nuestro taller automotriz. Puede consultarlo aquí: ${urlEstadoCuenta}`);
        
        window.open(`https://wa.me/${telefono}?text=${mensaje}`, '_blank');
    }

    function showDocDetails(data) {
        let itemsHtml = `
            <div class="mt-6 text-left text-base">
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1 bg-white/5 p-6 rounded-2xl border border-white/10">
                        <p class="text-blue-300 text-xs font-black uppercase tracking-widest mb-1 opacity-60">Cliente</p>
                        <p class="text-white text-xl font-black uppercase tracking-tight">${data.cliente}</p>
                    </div>
                    ${data.vehiculo ? `
                    <div class="flex-1 bg-white/5 p-6 rounded-2xl border border-white/10">
                        <p class="text-blue-300 text-xs font-black uppercase tracking-widest mb-1 opacity-60">Vehículo</p>
                        <p class="text-emerald-400 text-xl font-black uppercase tracking-tight">${data.vehiculo}</p>
                    </div>
                    ` : ''}
                </div>

                <div class="bg-white/5 rounded-2xl overflow-hidden border border-white/10 shadow-xl">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white/10 text-base">
                            <tr>
                                <th class="px-4 py-3 text-xs font-black text-blue-200 uppercase tracking-widest">Cant.</th>
                                <th class="px-4 py-3 text-xs font-black text-blue-200 uppercase tracking-widest">Nombre</th>
                                <th class="px-4 py-3 text-xs font-black text-blue-200 uppercase tracking-widest">Descripción</th>
                                <th class="px-4 py-3 text-xs font-black text-blue-200 uppercase tracking-widest text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-base">
                            ${data.items.map(item => `
                                <tr>
                                    <td class="px-4 py-3 font-bold text-white/70">${item.cantidad}</td>
                                    <td class="px-4 py-3 text-white font-black uppercase">${item.nombre}</td>
                                    <td class="px-4 py-3 text-sm text-white/50 font-medium uppercase italic">${item.descripcion || '---'}</td>
                                    <td class="px-4 py-3 font-mono text-right font-black text-white">$${parseFloat(item.subtotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot class="bg-white/10">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-xs font-black text-blue-200 uppercase tracking-widest text-right">Total Documento:</td>
                                <td class="px-4 py-4 text-white text-2xl font-black text-right font-mono tracking-tighter">$${data.total}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        `;

        Swal.fire({
            title: `${data.tipo}: ${data.folio}`,
            html: itemsHtml,
            width: '800px',
            background: '#1e293b',
            color: '#fff',
            confirmButtonText: 'CERRAR',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-[2rem] border border-white/20 shadow-2xl',
                title: 'text-2xl font-black uppercase tracking-tighter pt-8 px-8 text-left border-b border-white/10 pb-6'
            }
        });
    }

    function abrirModalPago(id, tipo, total, saldo) {
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
                            <option value="CHEQUE" class="text-black">CHEQUE</option>
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
                    <div>
                        <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">¿REQUIERE FACTURA?</label>
                        <select id="modal_requiere_factura" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase">
                            <option value="NO" class="text-black">NO</option>
                            <option value="SI" class="text-black">SI</option>
                        </select>
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
            confirmButtonColor: '#2563eb',
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
                const factura = document.getElementById('modal_requiere_factura').value;
                const referencia = document.getElementById('modal_referencia').value;

                if (!metodo) {
                    Swal.showValidationMessage('Debe seleccionar un método de pago');
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
                    fecha_pago: new Date().toISOString().split('T')[0]
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

                const url = tipo === 'VENTA' ? `/ventas/${id}/pagos` : `/ordenes/${id}/pagos`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(result.value)
                })
                .then(r => r.json())
                .then(response => {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡PAGO REGISTRADO!',
                            text: response.message,
                            background: '#1e293b',
                            color: '#fff',
                            showConfirmButton: true,
                            confirmButtonText: 'VER PDF'
                        }).then(r => {
                            if (r.isConfirmed && response.pdf_url) {
                                window.open(response.pdf_url, '_blank');
                            }
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ERROR',
                            text: response.message ?? 'Error al procesar el pago.',
                            background: '#1e293b',
                            color: '#fff'
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'ERROR DE RED',
                        text: 'No se pudo conectar con el servidor.',
                        background: '#1e293b',
                        color: '#fff'
                    });
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

    document.addEventListener('DOMContentLoaded', function() {
        $('#cliente_id').select2({
            width: '100%',
            placeholder: 'SELECCIONAR CLIENTE...',
            allowClear: true
        });
    });
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
    
    @keyframes zoomIn {
        from { opacity: 0; transform: translate(-50%, -45%) scale(0.95); }
        to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    }
    .animate-zoomIn { animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    
    .scrollbar-thin::-webkit-scrollbar { width: 4px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>
@endpush
@endsection
