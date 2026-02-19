@extends('layouts.app')

@section('title', 'Captura por Lotes de Inventario')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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
        select option {
            background-color: white !important;
            color: black !important;
        }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
    </style>
@endpush

@section('content')
    <div class="mx-auto py-4">
        <!-- Encabezado -->
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('productos.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al catálogo
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Captura por Lotes</h1>
        </div>

        <div class="space-y-8">
            <!-- Sección 1: Entrada de Productos (Una sola línea con proporciones 2/4, 1/4, 1/4) -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 mb-8 border border-white/20 shadow-2xl">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="md:w-1/2 w-full">
                        <label class="block text-md font-medium text-blue-100 mb-2 uppercase">1. Producto (Código - Descripción)</label>
                        <select id="producto_selector" class="block w-full"></select>
                    </div>
                    <div class="md:w-1/4 w-full">
                        <label class="block text-md font-medium text-blue-100 mb-2 uppercase">2. Existencia</label>
                        <input type="number" id="cantidad_entrada" step="any" 
                            class="block w-full px-4 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-lg font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="0.00">
                    </div>
                    <div class="md:w-1/4 w-full">
                        <button id="btn-agregar" class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center shadow-lg shadow-blue-900/40 border border-transparent">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Agregar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Tabla de Trabajo (Estilo Ventas) -->
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                    <h2 class="text-xl font-bold text-white uppercase tracking-tight">Lista de Conteos</h2>
                    <span id="contador-items" class="px-4 py-1.5 bg-blue-600/20 text-blue-200 text-xs font-black rounded-full uppercase tracking-widest border border-blue-500/30">
                        0 Productos
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th class="px-6 py-4 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Producto / Marca</th>
                                <th class="px-6 py-4 text-md font-bold text-blue-200 uppercase tracking-widest text-left">Descripción</th>
                                <th class="px-6 py-4 text-md font-bold text-blue-200 uppercase tracking-widest">Existencia Física</th>
                                <th class="px-6 py-4 text-md font-bold text-blue-200 uppercase tracking-widest">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-lote" class="divide-y divide-white/10">
                            <tr id="empty-row">
                                <td colspan="4" class="px-6 py-12 text-center text-blue-200/50 italic">
                                    No hay artículos en la lista. Busca y agrega productos para comenzar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="footer-acciones" class="hidden bg-white/5 p-8 border-t border-white/10 flex justify-end">
                    <button id="btn-guardar-todo" class="px-10 py-4 bg-brand hover:bg-brand-strong text-white text-sm font-black rounded-xl shadow-lg shadow-blue-900/40 transition-all active:scale-95 flex items-center uppercase tracking-widest">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Inventario
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    let itemsLote = {};

    // Inicializar Select2 con AJAX
    $('#producto_selector').select2({
        placeholder: 'SELECCIONAR...',
        width: '100%',
        ajax: {
            url: "{{ route('productos.buscar') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return data;
            },
            cache: true
        }
    });

    // Enfoque automático a cantidad al seleccionar producto
    $('#producto_selector').on('select2:select', function (e) {
        setTimeout(() => $('#cantidad_entrada').focus(), 50);
    });

    // Agregar con Enter en cantidad
    $('#cantidad_entrada').on('keypress', function(e) {
        if (e.which == 13) {
            $('#btn-agregar').click();
        }
    });

    // Lógica para agregar a la tabla
    $('#btn-agregar').on('click', function() {
        const data = $('#producto_selector').select2('data')[0];
        const cantidad = $('#cantidad_entrada').val();

        if (!data || cantidad === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Selecciona un producto e ingresa la cantidad física.',
                confirmButtonColor: '#3b82f6',
                background: '#1e293b',
                color: '#fff'
            });
            return;
        }

        const id = data.id;
        const nombre = data.nombre;
        const descripcion = data.descripcion;
        const marca = data.marca;

        // Agregar o actualizar en el objeto temporal
        itemsLote[id] = {
            id: id,
            nombre: nombre,
            descripcion: descripcion,
            marca: marca,
            cantidad: cantidad
        };

        renderTabla();
        resetInputs();
    });

    function renderTabla() {
        const tbody = $('#tabla-lote');
        const keys = Object.keys(itemsLote);
        
        if (keys.length === 0) {
            tbody.html(`
                <tr id="empty-row">
                    <td colspan="4" class="px-6 py-12 text-center text-blue-200/50 italic">
                        No hay artículos en la lista. Busca y agrega productos para comenzar.
                    </td>
                </tr>
            `);
            $('#footer-acciones').addClass('hidden');
        } else {
            let html = '';
            keys.forEach(id => {
                const item = itemsLote[id];
                html += `
                    <tr class="hover:bg-white/5 transition-colors animate-fade-in">
                        <td class="px-6 py-4 text-left">
                            <div class="text-md font-bold text-white uppercase">${item.nombre}</div>
                            <div class="text-md text-blue-200/60 uppercase italic">${item.marca || 'SIN MARCA'}</div>
                        </td>
                        <td class="px-6 py-4 text-left">
                            <div class="text-md text-blue-200/80 uppercase">${item.descripcion}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-md font-black text-white">${item.cantidad}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <button onclick="removeItem(${id})" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.html(html);
            $('#footer-acciones').removeClass('hidden');
        }
        
        $('#contador-items').text(`${keys.length} Productos`);
    }

    window.removeItem = function(id) {
        delete itemsLote[id];
        renderTabla();
    };

    function resetInputs() {
        $('#producto_selector').val(null).trigger('change');
        $('#cantidad_entrada').val('');
    }

    // Guardado Final
    $('#btn-guardar-todo').on('click', function() {
        const items = Object.values(itemsLote);
        
        Swal.fire({
            title: '¿Guardar Inventario?',
            text: `Se actualizarán ${items.length} productos en el sistema.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'SÍ, GUARDAR',
            cancelButtonText: 'CANCELAR',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#475569',
            background: '#1e293b',
            color: '#fff',
        }).then((result) => {
            if (result.isConfirmed) {
                enviarLote(items);
            }
        });
    });

    function enviarLote(items) {
        $('#btn-guardar-todo').prop('disabled', true).html('<svg class="w-5 h-5 mr-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Guardando...');

        $.ajax({
            url: "{{ route('productos.inventario.update_lote') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                items: items
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        background: '#1e293b',
                        color: '#fff',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.href = "{{ route('productos.index') }}";
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON.message || 'Error al guardar el lote.',
                    background: '#1e293b',
                    color: '#fff'
                });
                $('#btn-guardar-todo').prop('disabled', false).html('<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Guardar Inventario');
            }
        });
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        background: '#1e293b',
        color: '#fff'
    });
});
</script>
@endpush
