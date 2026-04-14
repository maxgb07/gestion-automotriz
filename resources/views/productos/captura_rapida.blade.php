@extends('layouts.app')

@section('title', 'Captura Rápida de Inventario')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.75rem !important;
            height: 48px !important;
            padding: 10px 16px !important;
            color: white !important;
            backdrop-filter: blur(4px) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white !important;
            line-height: 28px !important;
            text-transform: uppercase;
            font-size: 1.1rem !important;
            font-weight: 700 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: rgba(191, 219, 254, 0.3) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
        }
        .select2-dropdown {
            background-color: #ffffff !important;
            color: #000000 !important;
            border-radius: 1rem !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2) !important;
        }
        .select2-results__option {
            text-transform: uppercase;
            color: black !important;
            padding: 10px 16px !important;
            font-size: 0.95rem !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2563eb !important;
        }
        .select2-search__field {
            color: #000000 !important;
            text-transform: uppercase;
            padding: 8px !important;
        }

        @keyframes fade-in {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }

        /* Estilos para inputs en tabla */
        .table-input {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.6rem;
            color: white;
            padding: 0.6rem;
            width: 100%;
            transition: all 0.2s;
            text-transform: uppercase;
        }
        .table-input:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .table-input-number {
            text-align: center;
        }

        /* Estilos de agrupación de filas */
        .row-group-header {
            background-color: rgba(255, 255, 255, 0.02);
            border-top: 2px solid rgba(255, 255, 255, 0.05) !important;
        }
        .row-group-fields {
            border-bottom: 2px solid rgba(59, 130, 246, 0.1) !important;
            padding-bottom: 1rem !important;
        }
        
        .field-label {
            display: block;
            font-size: 0.65rem;
            font-weight: 800;
            color: rgba(191, 219, 254, 0.5);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
            padding-left: 0.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-[1700px] mx-auto py-4">
        <!-- Encabezado -->
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('productos.index') }}" class="inline-flex items-center text-md text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al catálogo
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Captura Rápida Masiva</h1>
        </div>

        <div class="space-y-8">
            <!-- Sección: Tabla de Edición -->
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                    <div class="flex items-center gap-4">
                        <h2 class="text-xl font-bold text-white uppercase tracking-tight">Listado de Edición</h2>
                        <span id="contador-items" class="px-3 py-1 bg-blue-600/20 text-blue-300 text-md font-black rounded-full uppercase tracking-widest border border-blue-500/30">
                            0 PRODUCTOS
                        </span>
                    </div>
                    <button type="button" onclick="addRow()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all uppercase tracking-widest flex items-center shadow-lg shadow-blue-900/40 border border-transparent">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nueva Fila
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse" id="productos-table">
                        <tbody class="divide-y divide-white/5">
                            <!-- Los bloques de productos se inyectan en pares de filas -->
                        </tbody>
                    </table>
                </div>

                <div id="footer-acciones" class="bg-white/5 p-10 border-t border-white/10 flex justify-center">
                    <button id="btn-guardar-todo" class="px-16 py-5 bg-brand hover:bg-brand-strong text-white text-lg font-black rounded-2xl shadow-xl shadow-blue-900/40 transition-all active:scale-95 flex items-center uppercase tracking-widest">
                        <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Cambios Masivos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Template de Bloque de Producto (2 Filas) -->
    <template id="block-template">
        <!-- Fila 1: Buscador y Acciones -->
        <tr class="row-group-header animate-fade-in">
            <td colspan="7" class="px-6 py-4 text-left">
                <div class="flex items-center gap-6">
                    <div class="flex-grow">
                        <label class="field-label">1. Seleccionar Producto para Editar</label>
                        <select class="select-product block w-full"></select>
                    </div>
                    <div class="pt-5">
                        <button type="button" onclick="removeBlock(this)" class="p-3 bg-red-500/10 hover:bg-red-500/30 text-red-500 rounded-xl transition-all border border-red-500/20 shadow-lg shadow-red-900/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </td>
        </tr>
        <!-- Fila 2: Inputs de Datos -->
        <tr class="row-group-fields animate-fade-in border-b border-white/10">
            <td class="px-6 pb-6 pt-2 w-48">
                <label class="field-label">Marca</label>
                <input type="text" data-field="marca" class="table-input text-md font-bold" placeholder="MARCA">
            </td>
            <td class="px-2 pb-6 pt-2 flex-grow min-w-[300px]">
                <label class="field-label">Descripción Técnica</label>
                <input type="text" data-field="descripcion" class="table-input text-md font-bold" placeholder="DESCRIPCIÓN COMPLETA">
            </td>
            <td class="px-2 pb-6 pt-2 w-64">
                <label class="field-label">Aplicación / Compatibilidad</label>
                <input type="text" data-field="aplicacion" class="table-input text-md font-bold" placeholder="MODELOS/AÑOS">
            </td>
            <td class="px-2 pb-6 pt-2 w-36">
                <label class="field-label">P. Compra</label>
                <input type="number" step="any" data-field="precio_compra" class="table-input table-input-number text-md font-black" placeholder="0.00" onfocus="this.select()">
            </td>
            <td class="px-2 pb-6 pt-2 w-36">
                <label class="field-label">P. Venta</label>
                <input type="number" step="any" data-field="precio_venta" class="table-input table-input-number text-md font-black text-green-400" placeholder="0.00" onfocus="this.select()">
            </td>
            <td class="px-2 pb-6 pt-2 w-32">
                <label class="field-label">Existencia</label>
                <input type="number" step="any" data-field="stock" class="table-input table-input-number text-md font-black text-blue-400" placeholder="0" onfocus="this.select()">
            </td>
            <td class="px-6 pb-6 pt-2 w-32">
                <label class="field-label">S. Mínimo</label>
                <input type="number" step="any" data-field="stock_minimo" class="table-input table-input-number text-md font-black text-amber-500" placeholder="1" onfocus="this.select()">
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
<script>
$(document).ready(function() {
    
    // Configuración global Select2
    $(document).on('select2:open', () => {
        const searchField = document.querySelector('.select2-search__field');
        if (searchField) searchField.focus();
    });

    window.initSelect2Block = function(headerRow, fieldRow) {
        const select = $(headerRow).find('.select-product');
        
        select.select2({
            placeholder: 'ESCRIBE NOMBRE, MARCA O SKU...',
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
            },
            language: 'es',
            minimumInputLength: 1
        });

        // Auto-open al enfocar vía teclado
        $(headerRow).find('.select2-selection').on('focus', function() {
            $(this).closest('.select2-container').siblings('select:enabled').select2('open');
        });

        // Al seleccionar producto
        select.on('select2:select', function (e) {
            const data = e.params.data;
            const $fieldRow = $(fieldRow);
            
            // Poblar campos de la fila inferior
            $fieldRow.find('input[data-field="marca"]').val(data.marca || '');
            $fieldRow.find('input[data-field="descripcion"]').val(data.descripcion || '');
            $fieldRow.find('input[data-field="aplicacion"]').val(data.aplicacion || '');
            $fieldRow.find('input[data-field="precio_compra"]').val(data.precio_compra || 0);
            $fieldRow.find('input[data-field="precio_venta"]').val(data.precio_venta || 0);
            $fieldRow.find('input[data-field="stock"]').val(data.stock || 0);
            $fieldRow.find('input[data-field="stock_minimo"]').val(data.stock_minimo || 0);

            // Si es el último bloque, añadir uno nuevo
            const $tbody = $('#productos-table tbody');
            const lastBlockRow = $tbody.find('tr').last().prev(); // La penúltima fila es el header del último bloque
            
            if (headerRow === lastBlockRow[0]) {
                setTimeout(() => addRow(), 150);
            }

            // Saltar foco al primer campo editable (Marca)
            setTimeout(() => {
                $fieldRow.find('input[data-field="marca"]').focus().select();
            }, 50);
        });
    };

    window.addRow = function() {
        const tbody = document.querySelector('#productos-table tbody');
        const template = document.getElementById('block-template');
        const clone = template.content.cloneNode(true);
        
        // El template tiene dos filas
        const headerRow = clone.querySelectorAll('tr')[0];
        const fieldRow = clone.querySelectorAll('tr')[1];
        
        tbody.appendChild(headerRow);
        tbody.appendChild(fieldRow);
        
        initSelect2Block(headerRow, fieldRow);
        updateCounter();
    };

    window.removeBlock = function(btn) {
        const headerRow = $(btn).closest('tr');
        const fieldRow = headerRow.next('tr');
        
        if (headerRow.find('select').data('select2')) {
            headerRow.find('select').select2('destroy');
        }
        
        headerRow.remove();
        fieldRow.remove();
        
        updateCounter();
        
        if ($('#productos-table tbody tr').length === 0) {
            addRow();
        }
    };

    function updateCounter() {
        const count = $('#productos-table tbody tr.row-group-header').length;
        $('#contador-items').text(`${count} PRODUCTOS EN LISTA`);
    }

    // Guardado Masivo
    $('#btn-guardar-todo').on('click', function() {
        const finalItems = [];
        const headerRows = document.querySelectorAll('#productos-table tbody tr.row-group-header');
        
        headerRows.forEach(headerRow => {
            const $header = $(headerRow);
            const $fields = $header.next('tr');
            const id = $header.find('.select-product').val();
            
            if (id) {
                finalItems.push({
                    id: id,
                    marca: $fields.find('input[data-field="marca"]').val(),
                    descripcion: $fields.find('input[data-field="descripcion"]').val(),
                    aplicacion: $fields.find('input[data-field="aplicacion"]').val(),
                    precio_compra: $fields.find('input[data-field="precio_compra"]').val(),
                    precio_venta: $fields.find('input[data-field="precio_venta"]').val(),
                    stock: $fields.find('input[data-field="stock"]').val(),
                    stock_minimo: $fields.find('input[data-field="stock_minimo"]').val()
                });
            }
        });

        if (finalItems.length === 0) {
            Toast.fire({
                icon: 'warning',
                title: 'No hay productos configurados para guardar'
            });
            return;
        }

        Swal.fire({
            title: '¿Confirmar Cambios?',
            text: `Se actualizarán ${finalItems.length} productos de forma masiva.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'SÍ, GUARDAR TODO',
            cancelButtonText: 'REVISAR',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#475569',
            background: '#1e293b',
            color: '#fff',
        }).then((result) => {
            if (result.isConfirmed) {
                enviarLote(finalItems);
            }
        });
    });

    function enviarLote(items) {
        $('#btn-guardar-todo').prop('disabled', true).html('<svg class="w-6 h-6 mr-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Guardando...');

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
                        title: '¡Actualización Exitosa!',
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
                    title: 'Error en el Servidor',
                    text: xhr.responseJSON.message || 'Error al guardar los cambios.',
                    background: '#1e293b',
                    color: '#fff'
                });
                $('#btn-guardar-todo').prop('disabled', false).html('<svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Guardar Cambios Masivos');
            }
        });
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        background: '#1e293b',
        color: '#fff'
    });

    // Cargar primer bloque
    addRow();
});
</script>
@endpush
