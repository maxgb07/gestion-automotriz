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

    <!-- Actions Section -->
    <div class="flex flex-col md:flex-row justify-end gap-4 mb-6">
        <button onclick="abrirModalReporte()" class="w-fit px-8 py-3 h-[50px] bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m-9 9h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"></path>
            </svg>
            GENERAR REPORTE
        </button>
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
                                        <div class="text-md text-blue-300/40 font-bold uppercase tracking-widest">{{ $cliente->rfc ?? 'SIN RFC' }}</div>
                                        @if($cliente->celular)
                                            <div class="text-md font-bold text-white/80">
                                                <span class="text-blue-300/40 uppercase tracking-widest">Celular:</span> {{ $cliente->celular }}
                                            </div>
                                        @endif
                                        @if($cliente->telefono)
                                            <div class="text-md font-bold text-white/80">
                                                <span class="text-blue-300/40 uppercase tracking-widest">Teléfono:</span> {{ $cliente->telefono }}
                                            </div>
                                        @endif
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
                        <label class="block text-sm font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">FECHA DE PAGO *</label>
                        <input type="date" id="modal_fecha_pago" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" value="${new Date().toLocaleDateString('en-CA')}">
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
                const popup = Swal.getPopup();
                const metodo = popup.querySelector('#modal_metodo_pago').value;
                const monto  = popup.querySelector('#modal_monto').value;
                const factura = popup.querySelector('#modal_requiere_factura').value;
                const referencia = popup.querySelector('#modal_referencia').value;
                const fechaPago  = popup.querySelector('#modal_fecha_pago').value;
 
                if (!metodo) {
                    Swal.showValidationMessage('Debe seleccionar un método de pago');
                    return false;
                }
 
                if (metodo !== 'CRÉDITO 15 DÍAS' && (!monto || parseFloat(monto) <= 0)) {
                    Swal.showValidationMessage('El monto debe ser mayor a 0');
                    return false;
                }

                if (!fechaPago) {
                    Swal.showValidationMessage('Debe seleccionar una fecha de pago');
                    return false;
                }
 
                return { 
                    metodo_pago: metodo, 
                    monto: metodo === 'CRÉDITO 15 DÍAS' ? 0 : parseFloat(monto), 
                    requiere_factura: factura,
                    referencia: referencia,
                    fecha_pago: fechaPago
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
                .then(r => {
                    if (!r.ok) {
                        return r.text().then(text => {
                            let devMsg = 'HTTP ' + r.status + ' — ' + r.url;
                            try {
                                const json = JSON.parse(text);
                                devMsg += '\n' + JSON.stringify(json, null, 2);
                            } catch(e) {
                                devMsg += '\n' + text.substring(0, 500);
                            }
                            console.error('[PAGO ERROR]', devMsg);
                            throw new Error('generic');
                        });
                    }
                    return r.json();
                })
                .then(response => {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡PAGO REGISTRADO!',
                            text: response.message,
                            background: '#1e293b',
                            color: '#fff',
                            confirmButtonText: 'CERRAR',
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        console.error('[PAGO ERROR]', response);
                        Swal.fire({
                            icon: 'error',
                            title: 'Ocurrió un problema',
                            text: 'No fue posible registrar el pago. Por favor intenta de nuevo.',
                            background: '#1e293b',
                            color: '#fff',
                            confirmButtonText: 'ENTENDIDO',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                })
                .catch((err) => {
                    if (err.message !== 'generic') {
                        console.error('[PAGO ERROR]', err);
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Ocurrió un problema',
                        text: 'No fue posible registrar el pago. Por favor intenta de nuevo.',
                        background: '#1e293b',
                        color: '#fff',
                        confirmButtonText: 'ENTENDIDO',
                        confirmButtonColor: '#ef4444'
                    });
                });
            }
        });
    }
 
    function toggleMontoPago(metodo, saldo) {
        const popup = Swal.getPopup();
        if (!popup) return;
        const inputMonto = popup.querySelector('#modal_monto');
        if (metodo === 'CRÉDITO 15 DÍAS') {
            if (parseFloat(inputMonto.value) > 0) {
                inputMonto.dataset.oldValue = inputMonto.value;
            }
            inputMonto.value = 0;
            inputMonto.readOnly = true;
            inputMonto.classList.add('bg-white/5', 'text-slate-500');
        } else {
            inputMonto.readOnly = false;
            inputMonto.classList.remove('bg-white/5', 'text-slate-500');
            if (parseFloat(inputMonto.value) === 0) {
                inputMonto.value = inputMonto.dataset.oldValue ? inputMonto.dataset.oldValue : saldo;
            }
        }
    }

    function abrirModalReporte() {
        Swal.fire({
            title: 'REPORTE DE COBRANZA',
            html: `
                <div class="p-2 space-y-4 text-left">
                    <p class="text-blue-300/40 text-md font-black uppercase tracking-widest mb-4 border-b border-white/5 pb-2">Seleccione el contenido del reporte</p>
                    <div class="grid grid-cols-1 gap-3">
                        <button onclick="generarReporte('AMBOS')" class="group p-4 bg-white/5 hover:bg-blue-600/20 border border-white/10 hover:border-blue-500/50 rounded-2xl transition-all duration-300 flex items-center justify-between">
                            <div class="text-left">
                                <span class="block text-white font-black uppercase tracking-tighter text-md group-hover:text-blue-400 leading-tight">ORDENES Y VENTAS</span>
                                <span class="text-md text-blue-300/40 font-bold uppercase tracking-widest">REPORTE CONSOLIDADO COMPLETO</span>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </button>

                        <button onclick="generarReporte('ORDENES')" class="group p-4 bg-white/5 hover:bg-emerald-600/20 border border-white/10 hover:border-emerald-500/50 rounded-2xl transition-all duration-300 flex items-center justify-between">
                            <div class="text-left">
                                <span class="block text-white font-black uppercase tracking-tighter text-md group-hover:text-emerald-400 leading-tight">SOLO ÓRDENES</span>
                                <span class="text-md text-emerald-300/40 font-bold uppercase tracking-widest">REPORTES DE TALLER / SERVICIO</span>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                        </button>

                        <button onclick="generarReporte('VENTAS')" class="group p-4 bg-white/5 hover:bg-indigo-600/20 border border-white/10 hover:border-indigo-500/50 rounded-2xl transition-all duration-300 flex items-center justify-between">
                            <div class="text-left">
                                <span class="block text-white font-black uppercase tracking-tighter text-md group-hover:text-indigo-400 leading-tight">SOLO VENTAS</span>
                                <span class="text-md text-indigo-300/40 font-bold uppercase tracking-widest">REPORTE DE MOSTRADOR Y REFACCIONES</span>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                        </button>
                    </div>
                </div>
            `,
            width: '500px',
            background: '#1e293b',
            color: '#fff',
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                popup: 'rounded-[2.5rem] border border-white/20 shadow-2xl',
                title: 'text-2xl font-black uppercase tracking-tighter pt-10 px-8 text-left'
            }
        });
    }

    function generarReporte(tipo) {
        Swal.close();
        window.open(`{{ route('creditos.reporte_cobranza') }}?tipo=${tipo}`, '_blank');
    }

    // --- Lógica de Pago en Lote ---
    let docsSeleccionadosLote = [];

    function toggleAllCheckboxes(clienteId, elem) {
        let isChecked = elem.checked;
        const checkboxes = document.querySelectorAll(`.doc-checkbox-${clienteId}`);
        checkboxes.forEach(cb => cb.checked = isChecked);
        updateLoteTotal(clienteId);
    }

    function updateLoteTotal(clienteId) {
        let total = 0;
        let count = 0;
        docsSeleccionadosLote = [];
        const checkboxes = document.querySelectorAll(`.doc-checkbox-${clienteId}`);
        
        // Comprobar estado global de checkboxes
        let allChecked = true;
        
        checkboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.saldo);
                count++;
                docsSeleccionadosLote.push({
                    id: cb.dataset.id,
                    tipo: cb.dataset.tipo
                });
            } else {
                allChecked = false;
            }
        });

        const checkAll = document.getElementById(`check-all-${clienteId}`);
        if(checkAll && checkboxes.length > 0) {
            checkAll.checked = allChecked;
        }

        const countElem = document.getElementById(`lote-count-${clienteId}`);
        const totalElem = document.getElementById(`lote-total-${clienteId}`);
        const actionBar = document.getElementById(`lote-action-bar-${clienteId}`);

        if (count > 0) {
            actionBar.classList.remove('hidden');
            countElem.innerText = count;
            totalElem.innerText = '$' + new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(total);
        } else {
            actionBar.classList.add('hidden');
        }
    }

    function toggleMontoLote(metodo, maxSaldo) {
        const popup = Swal.getPopup();
        if (!popup) return;
        const inputMonto = popup.querySelector('#modal_lote_monto');
        if (metodo === 'CRÉDITO 15 DÍAS') {
            inputMonto.value = 0;
            inputMonto.readOnly = true;
            inputMonto.classList.add('bg-white/5', 'text-slate-500');
            Swal.showValidationMessage('No puede seleccionar crédito a 15 días en pagos masivos.');
        } else {
            Swal.resetValidationMessage();
            inputMonto.readOnly = false;
            inputMonto.classList.remove('bg-white/5', 'text-slate-500');
            if (parseFloat(inputMonto.value || 0) === 0) {
                inputMonto.value = maxSaldo.toFixed(2);
            }
        }
    }

    function prepararPagoLote(clienteId) {
        const items = [];
        let totalSaldo = 0;
        const checkboxes = document.querySelectorAll(`.doc-checkbox-${clienteId}:checked`);
        
        checkboxes.forEach(cb => {
            const id = cb.dataset.id;
            const tipo = cb.dataset.tipo;
            const saldo = parseFloat(cb.dataset.saldo || 0);
            items.push({ id, tipo, saldo });
            totalSaldo += saldo;
        });

        if (items.length === 0) return;

        Swal.fire({
            title: `COBRAR EN LOTE (${items.length} DOCS)`,
            background: '#1e293b',
            color: '#fff',
            html: `
                <div class="p-4 text-left space-y-5">
                    <div class="bg-blue-600/20 border border-blue-500/30 rounded-2xl p-4 flex justify-between items-center mb-6">
                        <div class="text-left font-black tracking-widest text-blue-200 uppercase text-[10px]">Total Seleccionado</div>
                        <div class="text-2xl font-black font-mono text-white">$${totalSaldo.toLocaleString('es-MX', {minimumFractionDigits: 2})}</div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Método</label>
                            <select id="modal_lote_metodo" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none uppercase" onchange="toggleMontoLote(this.value, ${totalSaldo})">
                                <option value="EFECTIVO" class="text-black">EFECTIVO</option>
                                <option value="TRANSFERENCIA" class="text-black" selected>TRANSFERENCIA</option>
                                <option value="TARJETA DE DÉBITO" class="text-black">TARJETA DE DÉBITO</option>
                                <option value="TARJETA DE CRÉDITO" class="text-black">TARJETA DE CRÉDITO</option>
                                <option value="OTRO / MIXTO" class="text-black">OTRO / MIXTO</option>
                                <option value="CRÉDITO 15 DÍAS" class="text-black">CRÉDITO 15 DÍAS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-red-400/60 uppercase tracking-widest mb-2 ml-1 italic">Monto manual a cobrar</label>
                            <input type="number" id="modal_lote_monto" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold text-xl focus:ring-2 focus:ring-blue-500 outline-none" value="${totalSaldo.toFixed(2)}" step="0.01">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">REQUIERE FACTURA</label>
                            <select id="modal_lote_factura" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none uppercase">
                                <option value="NO" class="text-black">NO</option>
                                <option value="SI" class="text-black">SI</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Fecha Pago</label>
                            <input type="date" id="modal_lote_fecha" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold text-sm focus:ring-2 focus:ring-blue-500 outline-none" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Ref / Notas</label>
                        <input type="text" id="modal_lote_referencia" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none" placeholder="...">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'PROCESAR PAGO',
            cancelButtonText: 'CANCELAR',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#ef4444',
            customClass: {
                container: 'backdrop-blur-sm',
                popup: 'rounded-[2.5rem] border border-white/10 shadow-2xl',
                confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-[10px]',
                cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase tracking-widest text-[10px]'
            },
            preConfirm: () => {
                const popup = Swal.getPopup();
                const metodo = popup.querySelector('#modal_lote_metodo').value;
                const monto = popup.querySelector('#modal_lote_monto').value;
                const factura = popup.querySelector('#modal_lote_factura').value;
                const referencia = popup.querySelector('#modal_lote_referencia').value;
                const fecha = popup.querySelector('#modal_lote_fecha').value;

                console.log("SENDING BATCH PAYMENT:", { items, metodo, monto, fecha });

                if (!metodo) { Swal.showValidationMessage('Seleccione método'); return false; }
                if (metodo !== 'CRÉDITO 15 DÍAS' && (!monto || parseFloat(monto) <= 0)) {
                    Swal.showValidationMessage('Monto inválido');
                    return false;
                }

                return { documentos: items, metodo_pago: metodo, monto_total: metodo === 'CRÉDITO 15 DÍAS' ? 0 : parseFloat(monto), requiere_factura: factura, referencia, fecha_pago: fecha };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Procesando...', background: '#1e293b', color: '#fff', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                fetch(`{{ route('creditos.pago_lote') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(result.value)
                })
                .then(r => r.json())
                .then(response => {
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'PAGO REGISTRADO', text: response.message, background: '#1e293b', color: '#fff' }).then(() => { window.location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'ERROR', text: response.message, background: '#1e293b', color: '#fff' });
                    }
                });
            }
        });
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
