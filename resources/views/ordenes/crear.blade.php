@extends('layouts.app')

@section('title', 'Nueva Orden de Servicio')

@section('content')
    <div class="max-w-4xl mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('ordenes.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al historial
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Registro de Entrada</h1>
        </div>

        <form action="{{ route('ordenes.store') }}" method="POST" id="orden-servicio-form">
            @csrf
            
            <div class="space-y-8">
                <!-- Card principal -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
                    <div class="space-y-6">
                        <!-- Fila Cliente -->
                        <div class="space-y-2">
                            <label for="cliente_id" class="block text-xs font-black text-blue-200 uppercase tracking-widest ml-1">Seleccionar Cliente *</label>
                            <div class="flex gap-4 items-center">
                                <div class="flex-grow min-w-0">
                                    <select name="cliente_id" id="cliente_id" class="block w-full select2-ajax-clientes" required>
                                        <option value="">BUSCAR CLIENTE...</option>
                                    </select>
                                </div>
                                <div class="flex-shrink-0 w-[220px]">
                                    <button type="button" onclick="nuevoCliente()" class="w-full text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-[10px] px-4 py-3 focus:outline-none inline-flex items-center justify-center uppercase tracking-widest h-[48px] whitespace-nowrap">
                                        <!-- <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg> -->
                                        Nuevo Cliente
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Fila Vehículo -->
                        <div class="space-y-2">
                            <label for="vehiculo_id" class="block text-xs font-black text-blue-200 uppercase tracking-widest ml-1">Seleccionar Vehículo *</label>
                            <div class="flex gap-4 items-center">
                                <div class="flex-grow min-w-0">
                                    <select name="vehiculo_id" id="vehiculo_id" class="block w-full select2-ajax-vehiculos" required disabled>
                                        <option value="">SELECCIONA UN CLIENTE PRIMERO...</option>
                                    </select>
                                </div>
                                <div class="flex-shrink-0 w-[220px]">
                                    <button type="button" id="btn-nuevo-vehiculo" onclick="nuevoVehiculo()" class="w-full text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-[10px] px-4 py-3 focus:outline-none items-center justify-center uppercase tracking-widest h-[48px] whitespace-nowrap">
                                        <!-- <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg> -->
                                        Nuevo Vehículo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 space-y-6 mb-8">

                        <!-- Fecha de Entrada -->
                        <div class="space-y-2">
                            <label for="fecha_entrada" class="block text-xs font-black text-blue-200 uppercase tracking-widest ml-1">Fecha de Entrada *</label>
                            <input type="datetime-local" name="fecha_entrada" id="fecha_entrada" value="{{ date('Y-m-d\TH:i') }}" class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all shadow-inner" required>
                        </div>

                        <!-- Kilometraje -->
                        <div class="space-y-2">
                            <label for="kilometraje_entrada" class="block text-xs font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Kilometraje de Entrada</label>
                            <input type="number" name="kilometraje_entrada" id="kilometraje_entrada" class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all shadow-inner" placeholder="P. Ej: 50000">
                        </div>

                        <!-- Reporte de Falla -->
                        <div class="space-y-2">
                            <label for="falla_reportada" class="block text-xs font-black text-blue-200 uppercase tracking-widest mb-1 ml-1">Reporte de Falla / Motivo de Revisión *</label>
                            <textarea name="falla_reportada" id="falla_reportada" rows="2" class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all shadow-inner uppercase" placeholder="DESCRIPCIÓN DEL REPORTE O MOTIVO DE VISITA..." required></textarea>
                        </div>

                        <!-- Observaciones Adicionales -->
                        <div class="space-y-2">
                            <label for="observaciones" class="block text-xs font-black text-blue-200 uppercase tracking-widest mb-1 ml-1">Observaciones Iniciales (Opcional)</label>
                            <textarea name="observaciones" id="observaciones" rows="3" class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all shadow-inner uppercase" placeholder="DETALLES VISIBLES, GOLPES, NIVEL DE GASOLINA, ETC..."></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-6 pt-12 mt-10 border-t border-white/10">
                        <button type="submit" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-sm px-10 py-4 focus:outline-none inline-flex items-center min-w-[220px] justify-center uppercase tracking-widest">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Orden de Servicio
                        </button>
                        <a href="{{ route('ordenes.index') }}" class="inline-flex items-center justify-center px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg border border-white/20 transition-all min-w-[200px] text-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.75rem !important;
            height: 48px !important;
            padding-top: 10px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white !important;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
        }
        .select2-dropdown {
            background-color: white !important;
            border-radius: 1rem !important;
            border: 1px solid rgba(0,0,0,0.1) !important;
            z-index: 9999 !important;
            overflow: hidden !important;
        }
        .select2-results__option {
            color: black !important;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            padding: 12px 16px !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6 !important;
            color: white !important;
        }
        .select2-search--dropdown .select2-search__field {
            border-radius: 0.5rem !important;
            color: #000000 !important;
            font-weight: 700 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2 Clientes
            const clientSelect = $('.select2-ajax-clientes').select2({
                ajax: {
                    url: '{{ route("clientes.buscar") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return { id: item.id, text: item.nombre + ' (' + item.celular + ')' };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: 'BUSCAR CLIENTE...',
                minimumInputLength: 0,
                allowClear: true,
                language: {
                    noResults: function() { return "No se encontraron clientes"; },
                    searching: function() { return "Buscando..."; }
                }
            });

            // Inicializar Select2 Vehículos
            const vehicleSelect = $('.select2-ajax-vehiculos').select2({
                ajax: {
                    url: '{{ route("vehiculos.buscar") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term, cliente_id: $('#cliente_id').val() };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {id: item.id, text: item.marca + ' ' + item.modelo + ' (' + (item.placas ? item.placas : 'Placas N/D') + ')'};
                            })
                        };
                    },
                    cache: true
                },
                placeholder: 'SELECCIONA UN CLIENTE PRIMERO...',
                language: {
                    noResults: function() { 
                        return $('#cliente_id').val() 
                            ? "El cliente no tiene vehículos registrados" 
                            : "Selecciona un cliente primero"; 
                    },
                    searching: function() { return "Buscando..."; }
                }
            });

            // Habilitar Vehículos al seleccionar Cliente
            $('#cliente_id').on('change', function() {
                const clienteId = $(this).val();
                
                // Limpiar vehículo actual
                vehicleSelect.val(null).trigger('change');

                if (clienteId) {
                    $('#vehiculo_id').prop('disabled', false);
                    $('#btn-nuevo-vehiculo').removeClass('hidden');
                    $('.select2-ajax-vehiculos').select2('open');
                } else {
                    $('#vehiculo_id').prop('disabled', true);
                    $('#btn-nuevo-vehiculo').addClass('hidden');
                }
            });
        });

        function nuevoCliente() {
            Swal.fire({
                title: 'REGISTRAR NUEVO CLIENTE',
                html: `
                    <div class="space-y-4 text-left p-2">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Nombre Completo *</label>
                            <input type="text" id="swal-nombre" class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold uppercase focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Celular *</label>
                            <input type="text" id="swal-celular" class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">RFC (Opcional)</label>
                            <input type="text" id="swal-rfc" class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold uppercase focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'REGISTRAR',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#3b82f6',
                preConfirm: () => {
                    const nombre = document.getElementById('swal-nombre').value;
                    const celular = document.getElementById('swal-celular').value;
                    const rfc = document.getElementById('swal-rfc').value;
                    if (!nombre || !celular) {
                        Swal.showValidationMessage('Nombre y celular son obligatorios');
                        return false;
                    }
                    return { nombre, celular, rfc };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("clientes.store") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ...result.value
                        },
                        success: function(response) {
                            // Asumimos que el store redirige, pero para AJAX necesitamos que devuelva el ID o usar otra ruta.
                            // Como el store actual de ClienteController es standard redireccion, 
                            // tendríamos que modificarlo o usar una ruta específica para AJAX.
                            // Por brevedad, lo buscamos de nuevo o usamos un alert.
                            Swal.fire('¡Éxito!', 'Cliente registrado. Por favor búscalo por su nombre.', 'success');
                        },
                        error: function() {
                            Swal.fire('Error', 'No se pudo registrar el cliente. Verifica los datos.', 'error');
                        }
                    });
                }
            });
        }

        function nuevoVehiculo() {
            const clienteId = $('#cliente_id').val();
            if (!clienteId) return;

            Swal.fire({
                title: 'REGISTRAR NUEVO VEHÍCULO',
                html: `
                    <div class="grid grid-cols-2 gap-4 text-left p-2">
                        <div class="col-span-1">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Marca *</label>
                            <input type="text" id="swal-marca" class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold uppercase focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Modelo *</label>
                            <input type="text" id="swal-modelo" class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold uppercase focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Año *</label>
                            <input type="number" id="swal-anio" class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Placas</label>
                            <input type="text" id="swal-placas" class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold uppercase focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'REGISTRAR',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#3b82f6',
                preConfirm: () => {
                    const marca = document.getElementById('swal-marca').value;
                    const modelo = document.getElementById('swal-modelo').value;
                    const anio = document.getElementById('swal-anio').value;
                    const placas = document.getElementById('swal-placas').value;
                    if (!marca || !modelo || !anio) {
                        Swal.showValidationMessage('Marca, modelo y año son obligatorios');
                        return false;
                    }
                    return { marca, modelo, anio, placas };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("clientes") }}/' + clienteId + '/vehiculos',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ...result.value
                        },
                        success: function() {
                            Swal.fire('¡Éxito!', 'Vehículo registrado. Ya puedes seleccionarlo.', 'success');
                            $('.select2-ajax-vehiculos').select2('open');
                        },
                        error: function() {
                            Swal.fire('Error', 'No se pudo registrar el vehículo.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
