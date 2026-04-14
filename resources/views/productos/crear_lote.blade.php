@extends('layouts.app')

@section('title', 'Registro Masivo de Productos')

@push('styles')
    <style>
        .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos para inputs */
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
            border-color: #4f46e5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        .table-input-number { text-align: center; }

        .row-group-header {
            background-color: rgba(255, 255, 255, 0.02);
            border-top: 2px solid rgba(255, 255, 255, 0.05) !important;
        }
        .row-group-fields {
            border-bottom: 2px solid rgba(79, 70, 229, 0.1) !important;
            padding-bottom: 1rem !important;
        }
        
        .field-label {
            display: block;
            font-size: 0.85rem; /* Ajustado para ser más legible como text-md aproximado */
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
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Registro de Productos por Lote</h1>
        </div>

        <div class="space-y-8">
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                    <div class="flex items-center gap-4">
                        <h2 class="text-md font-bold text-white uppercase tracking-tight">Nuevos Productos</h2>
                        <span id="contador-items" class="px-3 py-1 bg-indigo-600/20 text-indigo-300 text-md font-black rounded-full uppercase tracking-widest border border-indigo-500/30">
                            0 PRODUCTOS
                        </span>
                    </div>
                    <button type="button" onclick="addRow()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-md font-black rounded-xl transition-all uppercase tracking-widest flex items-center shadow-lg shadow-indigo-900/40 border border-transparent">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Agregar Producto
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse" id="productos-table">
                        <tbody class="divide-y divide-white/5">
                            <!-- Filas inyectadas por JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Footer con espacio extremadamente amplio -->
                <div id="footer-acciones" class="bg-white/5 py-60 border-t border-white/10 flex justify-center">
                    <button id="btn-registrar-todo" class="px-16 py-6 bg-indigo-600 hover:bg-indigo-700 text-white text-md font-black rounded-2xl shadow-xl shadow-indigo-900/40 transition-all active:scale-95 flex items-center uppercase tracking-widest">
                        <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Registrar Productos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Template de Bloque de Producto (2 Filas) -->
    <template id="block-template">
        <tr class="row-group-header animate-fade-in">
            <td colspan="7" class="px-6 py-4 text-left">
                <div class="flex items-center gap-6">
                    <div class="flex-grow">
                        <label class="field-label text-md">1. Nombre / Clave del Producto (Único)</label>
                        <input type="text" data-field="nombre" class="table-input text-md font-black tracking-tight placeholder-white/20" placeholder="ESCRIBE EL NOMBRE O CLAVE AQUÍ..." autocomplete="off">
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
        <tr class="row-group-fields animate-fade-in border-b border-white/10">
            <td class="px-6 pb-6 pt-2 w-48">
                <label class="field-label text-md">Marca</label>
                <input type="text" data-field="marca" class="table-input text-md font-bold" placeholder="MARCA">
            </td>
            <td class="px-2 pb-6 pt-2 flex-grow min-w-[300px]">
                <label class="field-label text-md">Descripción Técnica</label>
                <input type="text" data-field="descripcion" class="table-input text-md font-bold" placeholder="DESCRIPCIÓN">
            </td>
            <td class="px-2 pb-6 pt-2 w-64">
                <label class="field-label text-md">Aplicación</label>
                <input type="text" data-field="aplicacion" class="table-input text-md font-bold" placeholder="MODELOS/AÑOS">
            </td>
            <td class="px-2 pb-6 pt-2 w-36">
                <label class="field-label text-md">P. Compra</label>
                <input type="number" step="any" data-field="precio_compra" class="table-input table-input-number text-md font-black" placeholder="0.00" onfocus="this.select()">
            </td>
            <td class="px-2 pb-6 pt-2 w-36">
                <label class="field-label text-md">P. Venta</label>
                <input type="number" step="any" data-field="precio_venta" class="table-input table-input-number text-md font-black text-green-400" placeholder="0.00" onfocus="this.select()">
            </td>
            <td class="px-2 pb-6 pt-2 w-32">
                <label class="field-label text-md">S. Inicial</label>
                <input type="number" step="any" data-field="stock" class="table-input table-input-number text-md font-black text-blue-400" placeholder="0" onfocus="this.select()">
            </td>
            <td class="px-6 pb-6 pt-2 w-32">
                <label class="field-label text-md">S. Mínimo</label>
                <input type="number" step="any" data-field="stock_minimo" class="table-input table-input-number text-md font-black text-amber-500" value="1" onfocus="this.select()">
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    
    window.addRow = function() {
        const tbody = document.querySelector('#productos-table tbody');
        const template = document.getElementById('block-template');
        const clone = template.content.cloneNode(true);
        
        const headerRow = clone.querySelectorAll('tr')[0];
        const fieldRow = clone.querySelectorAll('tr')[1];
        
        tbody.appendChild(headerRow);
        tbody.appendChild(fieldRow);
        
        $(headerRow).find('input[data-field="nombre"]').on('input', function() {
            const $tbody = $('#productos-table tbody');
            const lastHeaderRow = $tbody.find('tr.row-group-header').last();
            
            if (headerRow === lastHeaderRow[0] && $(this).val().trim() !== '') {
                if (!$(this).data('addedNext')) {
                    addRow();
                    $(this).data('addedNext', true);
                }
            }
        });

        $(headerRow).find('input[data-field="nombre"]').on('keypress', function(e) {
            if (e.which === 13) {
                $(fieldRow).find('input[data-field="marca"]').focus();
            }
        });

        updateCounter();
    };

    window.removeBlock = function(btn) {
        const headerRow = $(btn).closest('tr');
        const fieldRow = headerRow.next('tr');
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

    $('#btn-registrar-todo').on('click', function() {
        const items = [];
        const headerRows = document.querySelectorAll('#productos-table tbody tr.row-group-header');
        
        headerRows.forEach(headerRow => {
            const $header = $(headerRow);
            const $fields = $header.next('tr');
            const nombre = $header.find('input[data-field="nombre"]').val().trim();
            
            if (nombre) {
                items.push({
                    nombre: nombre,
                    marca: $fields.find('input[data-field="marca"]').val(),
                    descripcion: $fields.find('input[data-field="descripcion"]').val(),
                    aplicacion: $fields.find('input[data-field="aplicacion"]').val(),
                    precio_compra: $fields.find('input[data-field="precio_compra"]').val() || 0,
                    precio_venta: $fields.find('input[data-field="precio_venta"]').val() || 0,
                    stock: $fields.find('input[data-field="stock"]').val() || 0,
                    stock_minimo: $fields.find('input[data-field="stock_minimo"]').val() || 0
                });
            }
        });

        if (items.length === 0) {
            Swal.fire({ icon: 'warning', title: 'LISTA VACÍA', text: 'Agrega al menos un nombre de producto.', background: '#1e293b', color: '#fff' });
            return;
        }

        Swal.fire({
            title: '¿REGISTRAR LOTE?',
            text: `Se intentarán dar de alta ${items.length} productos nuevos.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'SÍ, REGISTRAR',
            cancelButtonText: 'REVISAR',
            confirmButtonColor: '#4f46e5',
            background: '#1e293b',
            color: '#fff',
        }).then((result) => {
            if (result.isConfirmed) {
                enviarLote(items);
            }
        });
    });

    function enviarLote(items) {
        $('#btn-registrar-todo').prop('disabled', true).html('<svg class="w-6 h-6 mr-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Registrando...');

        $.ajax({
            url: "{{ route('productos.guardar_lote_nuevos') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                items: items
            },
            success: function(response) {
                if (response.success) {
                    let text = response.message;
                    if (response.duplicates && response.duplicates.length > 0) {
                        text += "\n\nDUPLICADOS OMITIDOS:\n" + response.duplicates.join(', ');
                    }

                    Swal.fire({
                        icon: response.duplicates && response.duplicates.length > 0 ? 'warning' : 'success',
                        title: 'PROCESO FINALIZADO',
                        text: text,
                        background: '#1e293b',
                        color: '#fff',
                        confirmButtonColor: '#4f46e5'
                    }).then(() => {
                        window.location.href = "{{ route('productos.index') }}";
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: 'ERROR', text: xhr.responseJSON.message || 'Error al procesar el lote.', background: '#1e293b', color: '#fff' });
                $('#btn-registrar-todo').prop('disabled', false).html('Registrar Lote de Productos');
            }
        });
    }

    addRow();
});
</script>
@endpush
