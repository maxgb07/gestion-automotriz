@extends('layouts.app')

@section('title', 'Registrar Compra')

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
    <div class="max-w-6xl mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('compras.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al historial
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Nueva Compra</h1>
        </div>
        <form action="{{ route('compras.store') }}" method="POST" id="compra-form">
            @csrf
            
            <div class="space-y-12">
                <!-- Sección 1: Datos Generales -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl mb-8">
                    <h2 class="text-xl font-bold text-white mb-6 border-b border-white/10 pb-4 uppercase tracking-tight">Datos Generales</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <label for="proveedor_id" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Proveedor *</label>
                            <select name="proveedor_id" id="proveedor_id" class="block w-full" required>
                                <option value="" disabled selected>SELECCIONA PROVEEDOR</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="factura" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Folio de Factura</label>
                            <input type="text" name="factura" id="factura" value="{{ old('factura') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm placeholder-blue-200/30" placeholder="EJ: F-12345">
                        </div>

                        <div>
                            <label for="fecha_compra" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Fecha de Compra *</label>
                            <input type="date" name="fecha_compra" id="fecha_compra" value="{{ date('Y-m-d') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Detalle de Productos -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                    <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                        <h2 class="text-xl font-bold text-white uppercase tracking-tight">Detalle de Productos</h2>
                        <button type="button" onclick="addRow()" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-6 py-2.5 focus:outline-none inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Agregar Producto
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left" id="productos-table">
                            <thead class="bg-white/5 border-b border-white/10">
                                <tr>
                                    <th class="px-4 py-4 text-xs font-bold text-blue-200 uppercase tracking-wider w-24 text-center">Cantidad</th>
                                    <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-wider text-center">Producto</th>
                                    <th class="px-4 py-4 text-xs font-bold text-blue-200 uppercase tracking-wider w-36 text-center">Precio Compra</th>
                                    <th class="px-4 py-4 text-xs font-bold text-blue-200 uppercase tracking-wider w-36 text-center">Precio Venta</th>
                                    <th class="px-4 py-4 text-xs font-bold text-blue-200 uppercase tracking-wider w-40 text-center">Subtotal</th>
                                    <th class="px-4 py-4 text-xs font-bold text-blue-200 uppercase tracking-wider w-16 text-center"></th>
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

                    <!-- Footer de la Tabla: Total -->
                    <div class="bg-white/5 p-8 border-t border-white/10 w-full flex flex-col items-end text-right">
                        <span class="text-blue-200 text-sm uppercase font-semibold mb-1">Total de la Compra</span>
                        <div class="text-4xl font-black text-white" id="total-general">$0.00</div>
                    </div>
                </div>

                <!-- Sección 3: Acciones -->
                <div class="flex items-center justify-center gap-6 py-12 mt-10 border-t border-white/5">
                    <button type="submit" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-sm px-10 py-4 focus:outline-none inline-flex items-center min-w-[220px] justify-center uppercase tracking-widest">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Compra
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
                <input type="number" name="productos[INDEX][cantidad]" value="1" min="1" oninput="calculateRow(this)" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm text-sm text-center">
            </td>
            <td class="px-6 py-4 text-center">
                <select name="productos[INDEX][id]" onchange="updateProductData(this)" class="select-product block w-full" required>
                    <option value="" disabled selected>SELECCIONAR...</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" data-compra="{{ $producto->precio_compra }}" data-venta="{{ $producto->precio_venta }}">
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-4 text-center">
                <input type="number" step="any" name="productos[INDEX][precio_compra]" value="0.00" min="0.00" oninput="calculateRow(this)" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm text-sm text-center">
            </td>
            <td class="px-4 py-4 text-center">
                <input type="number" step="any" name="productos[INDEX][precio_venta]" value="0.00" min="0.00" oninput="calculateRow(this)" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm text-sm font-bold text-center">
            </td>
            <td class="px-4 py-4 text-center">
                <span class="text-white font-mono text-base font-bold subtotal">$0.00</span>
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
    <script>
        $(document).ready(function() {
            let rowIndex = 0;

            // Inicializar Select2 para Proveedor
            $('#proveedor_id').select2({
                placeholder: 'SELECCIONA PROVEEDOR',
                width: '100%',
                dropdownParent: $('#compra-form')
            });

            // Función para inicializar Select2 en una fila específica
            window.initSelect2 = function(row) {
                $(row).find('.select-product').select2({
                    placeholder: 'SELECCIONAR...',
                    width: '100%',
                    dropdownParent: $('#compra-form')
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
                
                // Inicializar Select2 para el nuevo producto después de añadir al DOM
                initSelect2(newRow);
                
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

            window.updateProductData = function(select) {
                const option = select.options[select.selectedIndex];
                const row = select.closest('tr');
                const pCompra = row.querySelector('[name*="[precio_compra]"]');
                const pVenta = row.querySelector('[name*="[precio_venta]"]');
                
                if (option.dataset.compra) {
                    pCompra.value = option.dataset.compra;
                    pVenta.value = option.dataset.venta;
                }
                
                calculateRow(select);
            };

            window.calculateRow = function(input) {
                const row = input.closest('tr');
                const cant = row.querySelector('[name*="[cantidad]"]').value || 0;
                const price = row.querySelector('[name*="[precio_compra]"]').value || 0;
                const subtotalSpan = row.querySelector('.subtotal');
                
                const subtotal = parseFloat(cant) * parseFloat(price);
                subtotalSpan.textContent = '$' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                calculateTotal();
            };

            window.calculateTotal = function() {
                let total = 0;
                document.querySelectorAll('.subtotal').forEach(span => {
                    const value = span.textContent.replace('$', '').replace(/,/g, '');
                    total += parseFloat(value) || 0;
                });
                
                document.getElementById('total-general').textContent = '$' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            };

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

            // Agregar una fila inicial
            addRow();
        });
    </script>
@endpush
