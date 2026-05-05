@extends('layouts.app')

@section('title', 'Editar Compra')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Estilos personalizados para Select2 en tema dark/glass */
        .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 0.75rem !important;
            height: 42px !important;
            padding: 8px 12px !important;
            backdrop-filter: blur(4px) !important;
            color: white !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white !important;
            line-height: 24px !important;
            text-transform: uppercase;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: rgba(191, 219, 254, 0.5) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        /* El dropdown debe tener texto negro para ser legible */
        .select2-dropdown {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.75rem !important;
            color: #000000 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        /* Estilo para el Switch tipo iOS */
        .ios-switch-container {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }
        .ios-switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
        }
        .ios-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .ios-slider {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #475569 !important; /* Slate-600 para que sea visible */
            transition: .3s;
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .ios-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 2px;
            background-color: white !important;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        input:checked + .ios-slider {
            background-color: #22c55e !important; /* Green-500 */
        }
        input:checked + .ios-slider:before {
            transform: translateX(18px);
        }

        .select2-search__field {
            color: #000000 !important;
            text-transform: uppercase;
        }

        .select2-results__option {
            color: #000000 !important;
            text-transform: uppercase;
            padding: 8px 12px !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2563eb !important;
        }

        /* Fix para selects nativos si no usan select2 aún */
        select option {
            color: black !important;
            background-color: white !important;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('compras.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al historial
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Editar Compra {{ $compra->folio }}</h1>
        </div>
        <form action="{{ route('compras.update', $compra) }}" method="POST" id="compra-form">
            @csrf
            @method('PUT')
            
            <div class="space-y-12">
                <!-- Sección 1: Datos Generales -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl mb-8">
                    <h2 class="text-xl font-bold text-white mb-6 border-b border-white/10 pb-4 uppercase tracking-tight">Datos Generales</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div>
                            <label for="proveedor_id" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Proveedor *</label>
                            <select name="proveedor_id" id="proveedor_id" class="block w-full" required>
                                <option value="" disabled selected>SELECCIONA PROVEEDOR</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" 
                                            data-dias="{{ $proveedor->dias_credito }}" 
                                            data-descuento="{{ $proveedor->porcentaje_descuento_global }}"
                                            data-descuento-extra="{{ $proveedor->porcentaje_descuento_extra }}"
                                            {{ $compra->proveedor_id == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="factura" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Folio de Factura</label>
                            <input type="text" name="factura" id="factura" value="{{ old('factura', $compra->factura) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm placeholder-blue-200/30" placeholder="EJ: F-12345">
                        </div>

                        <div>
                            <label for="fecha_compra" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Fecha Compra *</label>
                            <input type="date" name="fecha_compra" id="fecha_compra" value="{{ old('fecha_compra', $compra->fecha_compra) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
                        </div>

                        <div>
                            <label for="fecha_vencimiento" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Vencimiento *</label>
                            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="{{ old('fecha_vencimiento', $compra->fecha_vencimiento) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Detalle de Productos -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                    <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                        <h2 class="text-xl font-bold text-white uppercase tracking-tight">Detalle de Productos</h2>
                        <div class="flex gap-3">
                            <button type="button" onclick="abrirModalNuevoProducto()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Nuevo Producto
                            </button>
                            <button type="button" onclick="addRow()" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-6 py-2.5 focus:outline-none inline-flex items-center transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Fila Manual
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left" id="productos-table">
                            <thead class="bg-slate-800/90 backdrop-blur-md border-b border-white/10 sticky top-[70px] z-10 shadow-lg">
                                <tr>
                                    <th class="px-2 py-4 text-md font-bold text-blue-200 uppercase tracking-widest w-24 text-center">Cant</th>
                                    <th class="px-6 py-4 text-md font-bold text-blue-200 uppercase tracking-wider text-center">Producto</th>
                                    <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-wider w-32 text-center">P. Compra</th>
                                    <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-wider w-32 text-center">P. Venta</th>
                                    <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-wider w-28 text-center">Descuento 1</th>
                                    <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-wider w-28 text-center">Descuento 2</th>
                                    <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-wider w-28 text-center">Interno</th>
                                    <th class="px-4 py-4 text-md font-bold text-blue-200 uppercase tracking-wider w-36 text-right">Subtotal</th>
                                    <th class="px-4 py-4 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <!-- Filas dinámicas aquí -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="no-products-msg" class="p-12 text-center text-blue-200/50 uppercase italic text-sm">
                        No has agregado productos a esta compra
                    </div>

                    <!-- Footer de la Tabla: Resumen -->
                    <div class="bg-white/5 p-8 border-t border-white/10 w-full flex justify-end">
                        <!-- Lado Derecho: Totales -->
                        <div class="flex flex-col items-end text-right space-y-2 w-full max-w-sm">
                            <!-- Cargos Adicionales -->
                            <div class="flex flex-col gap-2 w-full mb-4 border-b border-white/10 pb-4">
                                <div class="flex flex-col w-full mb-3">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-blue-200 text-[10px] uppercase font-black tracking-[0.15em]">Gastos de Maniobra</span>
                                        <input type="number" step="any" name="monto_maniobra" id="monto_maniobra" value="{{ number_format($compra->monto_maniobra, 2, '.', '') }}" oninput="calculateTotal()" onfocus="this.select()" class="w-32 px-3 py-1 bg-white/10 border border-white/20 rounded-lg text-white text-right focus:ring-2 focus:ring-blue-500 transition-all font-mono">
                                    </div>
                                    <div class="flex items-center">
                                        <label class="ios-switch-container group">
                                            <div class="ios-switch">
                                                <input type="checkbox" name="aplica_descuento_maniobra" id="aplica_descuento_maniobra" onchange="calculateTotal()" {{ $compra->aplica_descuento_maniobra ? 'checked' : '' }}>
                                                <span class="ios-slider"></span>
                                            </div>
                                            <span class="ml-3 text-[10px] font-black text-blue-200/40 uppercase tracking-widest group-hover:text-blue-200 transition-colors">Aplica descuento</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex flex-col w-full">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-blue-200 text-[10px] uppercase font-black tracking-[0.15em]">Costo de Seguro</span>
                                        <input type="number" step="any" name="monto_seguro" id="monto_seguro" value="{{ number_format($compra->monto_seguro, 2, '.', '') }}" oninput="calculateTotal()" onfocus="this.select()" class="w-32 px-3 py-1 bg-white/10 border border-white/20 rounded-lg text-white text-right focus:ring-2 focus:ring-blue-500 transition-all font-mono">
                                    </div>
                                    <div class="flex items-center">
                                        <label class="ios-switch-container group">
                                            <div class="ios-switch">
                                                <input type="checkbox" name="aplica_descuento_seguro" id="aplica_descuento_seguro" onchange="calculateTotal()" {{ $compra->aplica_descuento_seguro ? 'checked' : '' }}>
                                                <span class="ios-slider"></span>
                                            </div>
                                            <span class="ml-3 text-[10px] font-black text-blue-200/40 uppercase tracking-widest group-hover:text-blue-200 transition-colors">Aplica descuento</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between w-full">
                                <span class="text-blue-200 text-sm uppercase font-semibold">Subtotal (Bruto)</span>
                                <span class="text-white text-lg font-bold" id="resumen-subtotal">$0.00</span>
                            </div>
                            <div class="flex justify-between w-full border-b border-white/10 pb-2">
                                <span class="text-blue-200 text-sm uppercase font-semibold">IVA (16%)</span>
                                <span class="text-white text-lg font-bold" id="resumen-iva">$0.00</span>
                            </div>
                            <div class="flex justify-between w-full pt-2">
                                <span class="text-blue-100 text-sm uppercase font-black">Total Factura</span>
                                <span class="text-white text-xl font-black" id="total-factura">$0.00</span>
                            </div>
                            
                            <!-- Desgloses de Descuentos -->
                            <div class="flex justify-between w-full pt-4 text-amber-400">
                                <span class="text-xs uppercase font-bold">1. Descuento Global</span>
                                <span class="text-lg font-bold" id="monto-desc-global">$0.00</span>
                            </div>
                            <div class="flex justify-between w-full text-amber-400">
                                <span class="text-xs uppercase font-bold">2. Descuento Extra Global</span>
                                <span class="text-lg font-bold" id="monto-desc-extra">$0.00</span>
                            </div>
                            <div class="flex justify-between w-full text-amber-400 border-b border-white/10 pb-4">
                                <span class="text-xs uppercase font-bold">3. Descuento Interno (Productos)</span>
                                <span class="text-lg font-bold" id="monto-desc-interno">$0.00</span>
                            </div>

                            <!-- Pronto Pago -->
                            <div class="flex justify-between items-center w-full pt-4 pb-4 border-b border-white/10">
                                <span class="text-green-400 text-[10px] uppercase font-black tracking-widest">Desc. Financiero / Pronto Pago (%)</span>
                                <div class="flex items-center gap-2">
                                    <input type="number" step="any" name="porcentaje_pronto_pago" id="porcentaje_pronto_pago" value="{{ old('porcentaje_pronto_pago', number_format($compra->porcentaje_pronto_pago ?? 0, 2, '.', '')) }}" min="0" max="100" oninput="calculateTotal()" onfocus="this.select()" class="w-16 px-2 py-1 bg-white/10 border border-green-500/50 rounded-lg text-white text-right focus:ring-2 focus:ring-green-500 transition-all font-mono">
                                    <span class="text-green-400 text-lg font-bold" id="monto-pronto-pago">-$0.00</span>
                                </div>
                            </div>

                            <div class="flex justify-between w-full pt-4">
                                <span class="text-blue-200 text-sm uppercase font-black mt-1 tracking-widest">Saldo Pendiente</span>
                                <span class="text-4xl font-black text-white" id="total-general">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Acciones -->
                <div class="flex items-center justify-center gap-6 py-12 mt-10 border-t border-white/5">
                    <button type="submit" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-sm px-10 py-4 focus:outline-none inline-flex items-center min-w-[220px] justify-center uppercase tracking-widest">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('compras.index') }}" class="inline-flex items-center justify-center px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg border border-white/20 transition-all min-w-[200px] text-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Template para nuevas filas -->
    <template id="row-template">
        <tr class="hover:bg-white/5 transition-colors">
            <td class="px-4 py-4 text-center">
                <input type="number" step="any" name="productos[INDEX][cantidad]" value="1" min="0.1" oninput="calculateRow(this)" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm text-md text-center">
            </td>
            <td class="px-6 py-4 text-center">
                <select name="productos[INDEX][id]" class="select-product block w-full" required>
                    <!-- Opciones cargadas por AJAX vía Select2 -->
                </select>
            </td>
            <td class="px-4 py-4 text-center">
                <input type="number" step="any" name="productos[INDEX][precio_compra]" value="0.00" min="0.00" oninput="calculateRow(this)" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm text-md text-center">
            </td>
            <td class="px-4 py-4 text-center">
                <input type="number" step="any" name="productos[INDEX][precio_venta]" value="0.00" min="0.00" oninput="calculateRow(this)" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm text-md font-bold text-center">
            </td>
            <td class="px-4 py-4 text-center">
                <input type="number" step="any" name="productos[INDEX][descuento_porcentaje]" value="0.00" min="0" max="100" oninput="calculateRow(this)" class="row-desc1 block w-full px-2 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-xs text-center">
            </td>
            <td class="px-4 py-4 text-center">
                <input type="number" step="any" name="productos[INDEX][descuento_extra_porcentaje]" value="0.00" min="0" max="100" oninput="calculateRow(this)" class="row-desc2 block w-full px-2 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-xs text-center">
            </td>
            <td class="px-4 py-4 text-center">
                <input type="number" step="any" name="productos[INDEX][descuento_interno_porcentaje]" value="0.00" min="0" max="100" oninput="calculateRow(this)" class="row-desc-int block w-full px-2 py-3 bg-white/10 border border-amber-500/50 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all text-xs text-center" placeholder="0.00">
            </td>
            <td class="px-4 py-4 text-center">
                <span class="text-white text-md font-bold subtotal" data-value="0">$0.00</span>
            </td>
            <td class="px-4 py-4 text-center">
                <button type="button" onclick="removeRow(this)" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let rowIndex = 0;

            // Arreglo global: cuando Select2 se abre, forzar el foco interno en su input text de búsqueda
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });

            // Inicializar Select2 para Proveedor
            $('#proveedor_id').select2({
                placeholder: 'SELECCIONA PROVEEDOR',
                width: '100%',
                dropdownParent: $('#compra-form'),
                language: 'es'
            }).on('change', function() {
                const opt = $(this).find('option:selected');
                const dias = parseInt(opt.data('dias')) || 0;
                const descGlobal = parseFloat(opt.data('descuento')) || 0;
                const descExtra = parseFloat(opt.data('descuento-extra')) || 0;

                // Update date
                if(dias >= 0) {
                    const fCompraVal = document.getElementById('fecha_compra').value;
                    if(fCompraVal) {
                        const fCompra = new Date(fCompraVal + 'T00:00:00');
                        fCompra.setDate(fCompra.getDate() + dias);
                        const y = fCompra.getFullYear();
                        const m = String(fCompra.getMonth() + 1).padStart(2, '0');
                        const d = String(fCompra.getDate()).padStart(2, '0');
                        document.getElementById('fecha_vencimiento').value = `${y}-${m}-${d}`;
                    }
                }

                // Update Hidden Inputs and Display
                $('#porcentaje_descuento').val(descGlobal);
                $('#porcentaje_descuento_extra').val(descExtra);
                $('#display-pct-global').text(descGlobal + '%');
                $('#display-pct-extra').text(descExtra + '%');
                
                calculateTotal();
            });

            // Auto-seleccionar texto al hacer foco en inputs numéricos
            $(document).on('focus', 'input[type="number"]', function() {
                this.select();
            });

            // Recalcular vencimiento si cambia la fecha de compra
            $('#fecha_compra').on('change', function() {
                $('#proveedor_id').trigger('change');
            });

            // Función para inicializar Select2 en una fila específica vía AJAX
            window.initSelect2 = function(row) {
                const selectElement = $(row).find('.select-product');
                
                selectElement.select2({
                    placeholder: 'BUSCAR PRODUCTO...',
                    width: '100%',
                    dropdownParent: $('#compra-form'),
                    ajax: {
                        url: '{{ route('productos.buscar') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term // término de búsqueda
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.results // {id, text, precio_compra, precio_venta}
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1,
                    language: 'es',
                    templateResult: formatProductInfo,
                    templateSelection: formatProductSelection
                });

                // Abrir automáticamente el buscador cuando la celda reciba el foco via Tabulador
                $(row).find('.select2-selection').on('focus', function() {
                    $(this).closest('.select2-container').siblings('select:enabled').select2('open');
                });

                // Escuchar el evento de selección para llenar los campos de precios
                selectElement.on('select2:select', function (e) {
                    const data = e.params.data;
                    const tr = this.closest('tr');
                    
                    const pCompra = tr.querySelector('[name*="[precio_compra]"]');
                    const pVenta = tr.querySelector('[name*="[precio_venta]"]');
                    const cant = tr.querySelector('[name*="[cantidad]"]');

                    if (data.precio_compra !== undefined) {
                        pCompra.value = data.precio_compra;
                        pVenta.value = data.precio_venta;
                    }
                    
                    calculateRow(this);
                    
                    // UX Auto-Row: Si es la última fila y acabamos de seleccionar un producto válido, agregar una nueva fila automáticamente.
                    const tbody = document.querySelector('#productos-table tbody');
                    if (tr === tbody.lastElementChild && pCompra.value !== "") {
                        setTimeout(() => { addRow(); }, 150); // Ligero delay para que el usuario sienta la fluidez
                    }
                    
                    // Pasar el foco al precio de compra de la fila actual para que el usuario pueda validarlo
                    pCompra.select();
                });
            };

            window.addRow = function() {
                const tbody = document.querySelector('#productos-table tbody');
                const template = document.getElementById('row-template');
                const clone = template.content.cloneNode(true);
                
                // Reemplazar INDEX en los nombres de los campos
                const inputs = clone.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.name = input.name.replace('INDEX', rowIndex);
                });
                
                const newRow = clone.querySelector('tr');
                tbody.appendChild(newRow);
                
                // Aplicar descuentos del proveedor actual a la nueva fila
                const opt = $('#proveedor_id').find('option:selected');
                if(opt.val()){
                    newRow.querySelector('.row-desc1').value = opt.data('descuento') || 0;
                    newRow.querySelector('.row-desc2').value = opt.data('descuento-extra') || 0;
                }
                
                // Inicializar Select2 para el nuevo producto después de añadir al DOM
                initSelect2(newRow);
                
                rowIndex++;
                checkEmpty();
            };

            window.addExistingRow = function(data) {
                const tbody = document.querySelector('#productos-table tbody');
                const template = document.getElementById('row-template');
                const clone = template.content.cloneNode(true);
                
                const inputs = clone.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.name = input.name.replace('INDEX', rowIndex);
                });
                
                const newRow = clone.querySelector('tr');
                
                // Rellenar datos
                newRow.querySelector('[name*="[cantidad]"]').value = data.cantidad;
                newRow.querySelector('[name*="[precio_compra]"]').value = data.precio_compra;
                newRow.querySelector('[name*="[precio_venta]"]').value = data.precio_venta;
                newRow.querySelector('[name*="[descuento_porcentaje]"]').value = data.descuento_porcentaje;
                newRow.querySelector('[name*="[descuento_extra_porcentaje]"]').value = data.descuento_extra_porcentaje;
                newRow.querySelector('[name*="[descuento_interno_porcentaje]"]').value = data.descuento_interno_porcentaje || 0;

                // Crear Option de Select2
                const select = newRow.querySelector('.select-product');
                const option = new Option(data.text, data.id, true, true);
                select.appendChild(option);

                tbody.appendChild(newRow);
                
                initSelect2(newRow);
                calculateRow(newRow.querySelector('[name*="[cantidad]"]'));
                
                rowIndex++;
                checkEmpty();
            };

            window.removeRow = function(btn) {
                const row = $(btn).closest('tr');
                // Destruir instancias de Select2 antes de remover el elemento
                row.find('select').each(function() {
                    if ($(this).data('select2')) {
                        $(this).select2('destroy');
                    }
                });
                row.remove();
                calculateTotal();
                checkEmpty();
            };

            window.calculateRow = function(input) {
                const row = input.closest('tr');
                const cant = parseFloat(row.querySelector('[name*="[cantidad]"]').value) || 0;
                const price = parseFloat(row.querySelector('[name*="[precio_compra]"]').value) || 0;
                const desc1 = parseFloat(row.querySelector('[name*="[descuento_porcentaje]"]').value) || 0;
                const desc2 = parseFloat(row.querySelector('[name*="[descuento_extra_porcentaje]"]').value) || 0;
                const subtotalSpan = row.querySelector('.subtotal');
                
                const base = cant * price;
                
                subtotalSpan.dataset.value = base;
                subtotalSpan.textContent = '$' + base.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                calculateTotal();
            };

            window.calculateTotal = function() {
                let grossSubtotal = 0;
                let rowsData = [];
                let maxPctGlobal = 0;
                let maxPctExtra = 0;

                document.querySelectorAll('#productos-table tbody tr').forEach(row => {
                    const cant = parseFloat(row.querySelector('[name*="[cantidad]"]')?.value) || 0;
                    const price = parseFloat(row.querySelector('[name*="[precio_compra]"]')?.value) || 0;
                    const pctInt = parseFloat(row.querySelector('.row-desc-int')?.value) || 0;
                    
                    if (cant && price) {
                        const base = cant * price;
                        grossSubtotal += base;
                        
                        rowsData.push({
                            rowTotal: base, // Base SIN IVA
                            pctInt: pctInt
                        });

                        maxPctGlobal = parseFloat(row.querySelector('.row-desc1')?.value) || maxPctGlobal;
                        maxPctExtra = parseFloat(row.querySelector('.row-desc2')?.value) || maxPctExtra;
                    }
                });
                
                const montoManiobra = parseFloat(document.getElementById('monto_maniobra')?.value) || 0;
                const montoSeguro = parseFloat(document.getElementById('monto_seguro')?.value) || 0;
                const aplicaM = document.getElementById('aplica_descuento_maniobra')?.checked;
                const aplicaS = document.getElementById('aplica_descuento_seguro')?.checked;

                const grossSubtotalGeneral = grossSubtotal + montoManiobra + montoSeguro;

                // Base descontable (SIN IVA)
                const discountableTotal = grossSubtotal + (aplicaM ? montoManiobra : 0) + (aplicaS ? montoSeguro : 0);

                let remaining = discountableTotal;
                
                // 1. Global
                const montoGlobal = remaining * (maxPctGlobal / 100);
                remaining -= montoGlobal;

                // 2. Extra Global
                const montoExtra = remaining * (maxPctExtra / 100);
                remaining -= montoExtra;

                // 3. Interno (Productos)
                let sumInternalDiscount = 0;
                const factorCascadaGlobal = (1 - (maxPctGlobal/100)) * (1 - (maxPctExtra/100));
                
                rowsData.forEach(data => {
                    const rowRemaining = data.rowTotal * factorCascadaGlobal;
                    sumInternalDiscount += (rowRemaining * (data.pctInt / 100));
                });

                const totalDescuentosComerciales = montoGlobal + montoExtra + sumInternalDiscount;
                
                // Base Imponible Real (Subtotal - Descuentos Comerciales)
                const baseImponible = grossSubtotalGeneral - totalDescuentosComerciales;

                const iva = baseImponible * 0.16;
                const totalFactura = baseImponible + iva;

                // Descuento Financiero (Pronto Pago)
                const pctProntoPago = parseFloat(document.getElementById('porcentaje_pronto_pago')?.value) || 0;
                const montoProntoPago = totalFactura * (pctProntoPago / 100);

                const saldoPendiente = Math.max(0, totalFactura - montoProntoPago);
                
                // Formatear moneda helper
                const fmt = (val) => '$' + val.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

                document.getElementById('resumen-subtotal').textContent = fmt(grossSubtotalGeneral);
                document.getElementById('resumen-iva').textContent = fmt(iva);
                document.getElementById('total-factura').textContent = fmt(totalFactura);
                
                document.getElementById('monto-desc-global').textContent = '-' + fmt(montoGlobal);
                document.getElementById('monto-desc-extra').textContent = '-' + fmt(montoExtra);
                document.getElementById('monto-desc-interno').textContent = '-' + fmt(sumInternalDiscount);
                
                const spanProntoPago = document.getElementById('monto-pronto-pago');
                if(spanProntoPago) spanProntoPago.textContent = '-' + fmt(montoProntoPago);

                document.getElementById('total-general').textContent = fmt(saldoPendiente);
            };

            // Formato visual para los resultados de la búsqueda Ajax
            function formatProductInfo (producto) {
                if (producto.loading) {
                    return producto.text;
                }
                return $('<div>' + producto.text + '</div>');
            }

            // Formato visual de lo que queda seleccionado en la caja
            function formatProductSelection (producto) {
                return producto.text;
            }

            window.checkEmpty = function() {
                const tbody = document.querySelector('#productos-table tbody');
                const msg = document.getElementById('no-products-msg');
                if (tbody && tbody.children.length > 0) {
                    msg.classList.add('hidden');
                } else {
                    msg.classList.remove('hidden');
                    document.getElementById('total-general').textContent = '$0.00';
                }
            };

            // Antes de enviar el formulario (interceptando el click del botón submit para evitar la validación HTML5 de la fila vacía)
            $('#compra-form button[type="submit"]').on('click', function(e) {
                const tbody = document.querySelector('#productos-table tbody');
                const rows = tbody.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const select = row.querySelector('.select-product');
                    // Si el select de esta fila está vacío, le quitamos el 'required' y removemos la fila
                    if (!select.value) {
                        select.removeAttribute('required');
                        row.remove();
                    }
                });
                
                // Si la tabla quedó sin filas válidas, no dejamos continuar
                if (tbody.children.length === 0) {
                    e.preventDefault();
                    alert('Debe agregar al menos un producto a la compra.');
                    addRow();
                }
            });

            // Cargar filas existentes o agregar una inicial
            @if(count($compra->detalles) > 0)
                @foreach($compra->detalles as $detalle)
                    addExistingRow({!! json_encode([
                        'id' => $detalle->producto_id,
                        'text' => $detalle->producto->nombre,
                        'cantidad' => $detalle->cantidad,
                        'precio_compra' => $detalle->precio_compra,
                        'precio_venta' => $detalle->precio_venta_sugerido,
                        'descuento_porcentaje' => $detalle->descuento_porcentaje,
                        'descuento_extra_porcentaje' => $detalle->descuento_extra_porcentaje,
                        'descuento_interno_porcentaje' => $detalle->descuento_interno_porcentaje
                    ]) !!});
                @endforeach
                calculateTotal(); // recalculate everything just in case
            @else
                addRow();
            @endif
        });

        // --- Registro Rápido de Producto ---
        function abrirModalNuevoProducto() {
            Swal.fire({
                title: 'REGISTRAR NUEVO PRODUCTO',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="space-y-4 text-left mt-4">
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">SKU / CLAVE *</label>
                            <input type="text" id="swal-nombre" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-md font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="EJ: WX333">
                        </div>
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">MARCA</label>
                            <input type="text" id="swal-marca" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-md font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="EJ: WAGNER">
                        </div>
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">DESCRIPCIÓN</label>
                            <textarea id="swal-descripcion" rows="2" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-md font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="EJ: BALATAS FRENO DE DISCO"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">PRECIO COMPRA</label>
                                <input type="number" id="swal-costo" step="0.01" value="0.00" onfocus="this.select()" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-md font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">PRECIO VENTA</label>
                                <input type="number" id="swal-precio" step="0.01" value="0.00" onfocus="this.select()" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-md font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">STOCK MÍNIMO</label>
                            <input type="number" id="swal-stock-minimo" step="1" value="1" onfocus="this.select()" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-md font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'REGISTRAR',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#475569',
                customClass: {
                    popup: 'rounded-3xl border border-white/20 shadow-2xl',
                    title: 'text-xl font-black uppercase tracking-tighter'
                },
                didOpen: () => {
                    setTimeout(() => document.getElementById('swal-nombre').focus(), 100);
                },
                preConfirm: () => {
                    const nombre = document.getElementById('swal-nombre').value;
                    // El SKU asume el mismo valor del nombre si es el estándar usado en BD
                    const sku = nombre; 
                    const costo = document.getElementById('swal-costo').value;
                    const precio = document.getElementById('swal-precio').value;
                    const descripcion = document.getElementById('swal-descripcion').value;
                    const marca = document.getElementById('swal-marca').value;
                    const stockMinimo = document.getElementById('swal-stock-minimo').value;

                    if (!nombre) {
                        Swal.showValidationMessage('Todos los campos obligatorios (*) deben estar llenos');
                        return false;
                    }

                    return { sku, nombre, costo, precio, descripcion, marca, stockMinimo };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { sku, nombre, costo, precio, descripcion, marca, stockMinimo } = result.value;
                    
                    Swal.fire({
                        title: 'Guardando...',
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: '{{ route("productos.store") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            sku: sku,
                            nombre: nombre,
                            marca: marca,
                            descripcion: descripcion,
                            precio_compra: costo,
                            precio_venta: precio,
                            stock: 0,
                            stock_minimo: stockMinimo
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Producto Registrado!',
                                    text: 'Ya puedes buscarlo en la tabla.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Alerta de éxito completada, enfocamos la última fila para que el usuario busque
                                    const tbody = document.querySelector('#productos-table tbody');
                                    let lastRow = tbody.lastElementChild;
                                    
                                    // Si la tabla estuviera mágicamente limpia, agregamos fila
                                    if (!lastRow) {
                                        addRow();
                                        lastRow = tbody.lastElementChild;
                                    }
                                    
                                    // Comprobar si la última fila ya tiene un producto; si es así, hacemos otra limpia
                                    const selectVal = lastRow.querySelector('.select-product').value;
                                    if (selectVal) {
                                        addRow();
                                        lastRow = tbody.lastElementChild;
                                    }

                                    // Enfocar para escribir
                                    $(lastRow).find('.select2-selection').focus();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON.message || 'No se pudo registrar el producto', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
