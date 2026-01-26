@extends('layouts.app')

@section('title', 'Registrar Venta')

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
        /* Color negro para los selectores nativos */
        select option {
            background-color: white !important;
            color: black !important;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-6xl mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('ventas.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al historial
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Nueva Venta</h1>
        </div>

        <form action="{{ route('ventas.store') }}" method="POST" id="venta-form">
            @csrf
            
            <div class="space-y-8">
                <!-- Sección 1: Datos Generales -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <label for="cliente_id" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Cliente *</label>
                            <select name="cliente_id" id="cliente_id" class="block w-full" required>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ $publicoGeneral && $cliente->id == $publicoGeneral->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fecha" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Fecha *</label>
                            <input type="datetime-local" name="fecha" id="fecha" value="{{ date('Y-m-d\TH:i') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>

                        <div>
                            <label for="metodo_pago" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Método de Pago *</label>
                            <select name="metodo_pago" id="metodo_pago" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase" required>
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="TARJETA">TARJETA</option>
                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                <option value="CREDITO">CRÉDITO (15 DÍAS)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Detalle de la Venta -->
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden mb-8">
                    <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                        <h2 class="text-xl font-bold text-white uppercase tracking-tight">Artículos y Servicios</h2>
                        <div class="flex gap-3">
                            <button type="button" onclick="abrirModalNuevoItem()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Nuevo Item
                            </button>
                            <button type="button" onclick="addRow()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all uppercase tracking-widest flex items-center justify-center cursor-pointer shadow-lg shadow-blue-900/40">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Agregar Fila
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-center border-collapse" id="items-table">
                            <thead class="bg-white/5 border-b border-white/10">
                                <tr>
                                    <th class="px-2 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest w-28">Cantidad</th>
                                    <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest">Tipo</th>
                                    <th class="px-3 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest w-80">Clave</th>
                                    <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest w-80">Descripción</th>
                                    <th class="px-4 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest w-32">Precio</th>
                                    <!-- <th class="px-4 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest w-28">Descuento</th> -->
                                    <th class="px-6 py-4 text-xs font-bold text-blue-200 uppercase tracking-widest w-40 text-right">Importe</th>
                                    <th class="px-4 py-4 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white/5 p-8 border-t border-white/10 flex justify-end">
                        <div class="w-full md:w-80 text-right">
                            <span class="text-blue-200 text-xs uppercase font-black tracking-[0.2em] mb-2 block" style="font-size: 1.5rem; font-weight: 500;">Total a Pagar</span>
                            <div class="font-black text-white hover:text-blue-400 transition-all duration-300 leading-none tracking-tighter" id="total-general" style="font-size: 1.5rem; font-weight: 500;">$0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Acciones -->
                <div class="flex items-center justify-center gap-6 py-12 mt-10 border-t border-white/5">
                    <button type="submit" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-sm px-10 py-4 focus:outline-none inline-flex items-center min-w-[220px] justify-center uppercase tracking-widest">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Finalizar Venta
                    </button>
                    <a href="{{ route('ventas.index') }}" class="inline-flex items-center justify-center px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg border border-white/20 transition-all min-w-[200px] text-center">
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
            <td class="px-3 py-4">
                <input type="number" name="items[INDEX][cantidad]" value="1" min="1" step="any" oninput="calculateRow(this)" class="block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-center text-sm font-bold focus:ring-1 focus:ring-blue-500/50 outline-none transition-all" required>
            </td>
            <td class="px-3 py-4">
                <select name="items[INDEX][tipo]" onchange="changeType(this)" class="tipo-select block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-md uppercase focus:outline-none">
                    <option value="producto" class="text-black bg-white">PRODUCTO</option>
                    <option value="servicio" class="text-black bg-white">SERVICIO</option>
                </select>
            </td>
            <td class="px-3 py-4">
                <select name="items[INDEX][id]" onchange="updateItemData(this)" class="item-select block w-full" required>
                    <option value="" disabled selected>SELECCIONAR...</option>
                </select>
            </td>
            <td class="px-3 py-4">
                <input type="text" name="items[INDEX][descripcion]" class="descripcion-input block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-md uppercase focus:outline-none" readonly>
            </td>
            <td class="px-3 py-4">
                <input type="number" step="any" name="items[INDEX][precio_unitario]" value="0.00" oninput="calculateRow(this)" class="block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-center text-sm font-bold focus:ring-1 focus:ring-blue-500/50 outline-none" required>
            </td>
            <!-- <td class="px-3 py-4">
                <div class="relative">
                    <input type="number" name="items[INDEX][descuento_porcentaje]" value="0" min="0" max="100" step="any" oninput="calculateRow(this)" class="block w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-center text-xs font-bold focus:ring-1 focus:ring-blue-500/50 outline-none">
                </div>
            </td> -->
            <td class="px-3 py-4 text-right">
                <input type="number" step="any" name="items[INDEX][subtotal]" value="0.00" oninput="calculateTotal()" class="subtotal-input block w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-right text-sm font-black font-mono focus:ring-1 focus:ring-blue-500/50 outline-none" required>
            </td>
            <td class="px-3 py-4 text-center">
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
        const PRODUCTOS = @json($productos);
        const SERVICIOS = @json($servicios);
        let rowIndex = 0;

        $(document).ready(function() {
            $('#cliente_id').select2({ width: '100%' });
            addRow();

            // Manejo de envío de formulario vía AJAX
            $('#venta-form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                
                Swal.fire({
                    title: '¿Finalizar Venta?',
                    text: "Se registrará la venta en el sistema.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, finalizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Procesando...',
                            html: 'Por favor espere un momento...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: '¡Venta Exitosa!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#3b82f6',
                                    }).then(() => {
                                        // Abrir PDF en nueva pestaña
                                        if (response.pdf_url) {
                                            window.open(response.pdf_url, '_blank');
                                        }
                                        // Redirigir al historial o recargar para limpiar
                                        window.location.href = "{{ route('ventas.index') }}";
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error(xhr);
                                let errorMessage = 'Error desconocido al procesar la venta.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                Swal.fire({
                                    title: 'Error',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444',
                                });
                            }
                        });
                    }
                });
            });
        });

        function addRow() {
            const tbody = document.querySelector('#items-table tbody');
            const template = document.getElementById('row-template');
            const clone = template.content.cloneNode(true);
            
            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', rowIndex);
            });

            const newRow = clone.querySelector('tr');
            tbody.appendChild(newRow);
            
            // Inicializar Select2 y opciones
            const typeSelect = newRow.querySelector('.tipo-select');
            changeType(typeSelect);
            
            rowIndex++;
        }

        function changeType(select) {
            const row = select.closest('tr');
            const itemSelect = row.querySelector('.item-select');
            const type = select.value;
            const data = type === 'producto' ? PRODUCTOS : SERVICIOS;

            if ($(itemSelect).data('select2')) {
                $(itemSelect).select2('destroy');
            }

            itemSelect.innerHTML = '<option value="" disabled selected>SELECCIONAR...</option>';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nombre + ' - ' + item.descripcion;
                option.dataset.precio = item.precio_venta || item.precio || 0;
                option.dataset.descripcion = item.descripcion || item.nombre;
                itemSelect.appendChild(option);
            });

            $(itemSelect).select2({ width: '100%' });
        }

        function updateItemData(select) {
            const row = select.closest('tr');
            const option = select.options[select.selectedIndex];
            const precioInput = row.querySelector('[name*="[precio_unitario]"]');
            const descInput = row.querySelector('.descripcion-input');
            
            if (option.dataset.precio) {
                precioInput.value = option.dataset.precio;
            }
            if (option.dataset.descripcion) {
                descInput.value = option.dataset.descripcion;
            }
            calculateRow(select);
        }

        function calculateRow(input) {
            const row = input.closest('tr');
            const cant = parseFloat(row.querySelector('[name*="[cantidad]"]').value) || 0;
            const price = parseFloat(row.querySelector('[name*="[precio_unitario]"]').value) || 0;
            const descPorc = 0;//parseFloat(row.querySelector('[name*="[descuento_porcentaje]"]').value) || 0;
            const subtotalInput = row.querySelector('.subtotal-input');
            
            const baseRowTotal = cant * price;
            const discountAmount = baseRowTotal * (descPorc / 100);
            const finalRowSubtotal = baseRowTotal - discountAmount;
            
            subtotalInput.value = finalRowSubtotal.toFixed(2);
            
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            document.getElementById('total-general').textContent = '$' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            calculateTotal();
        }

        // --- Registro Rápido de Ítems ---
        function toggleSwalFields(tipo) {
            const divStock = document.getElementById('div-stock');
            const labelNombre = document.getElementById('label-nombre');
            if (tipo === 'servicio') {
                divStock.classList.add('hidden');
                labelNombre.textContent = 'NOMBRE DEL SERVICIO *';
            } else {
                divStock.classList.remove('hidden');
                labelNombre.textContent = 'SKU / CLAVE *';
            }
        }

        function abrirModalNuevoItem() {
            Swal.fire({
                title: 'REGISTRAR NUEVO ÍTEM',
                background: '#1e293b',
                color: '#fff',
                html: `
                    <div class="flex gap-8 justify-center mb-6 p-4 bg-white/5 rounded-2xl border border-white/10">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="swal-tipo" value="producto" checked onchange="toggleSwalFields(this.value)" class="w-5 h-5 text-blue-500 bg-white/10 border-white/20 focus:ring-blue-500 focus:ring-offset-slate-800">
                            <span class="text-md font-black uppercase tracking-widest text-blue-100 group-hover:text-white transition-colors">Producto</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="swal-tipo" value="servicio" onchange="toggleSwalFields(this.value)" class="w-5 h-5 text-blue-500 bg-white/10 border-white/20 focus:ring-blue-500 focus:ring-offset-slate-800">
                            <span class="text-md font-black uppercase tracking-widest text-blue-100 group-hover:text-white transition-colors">Servicio</span>
                        </label>
                    </div>
                    <div class="space-y-4 text-left">
                        <div>
                            <label id="label-nombre" class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">SKU / CLAVE *</label>
                            <input type="text" id="swal-nombre" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="EJ: BALATA-TR-01">
                        </div>
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">DESCRIPCIÓN</label>
                            <textarea id="swal-descripcion" rows="2" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold uppercase focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="DESCRIPCIÓN DEL PRODUCTO O SERVICIO"></textarea>
                        </div>
                        <div>
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">PRECIO VENTA *</label>
                            <input type="number" id="swal-precio" step="0.01" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                        </div>
                        <div id="div-stock">
                            <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-1 ml-1 text-center">EXISTENCIA INICIAL *</label>
                            <input type="number" id="swal-stock" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-center text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" value="1">
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
                preConfirm: () => {
                    const tipo = document.querySelector('input[name="swal-tipo"]:checked').value;
                    const nombre = document.getElementById('swal-nombre').value;
                    const precio = document.getElementById('swal-precio').value;
                    const stock = document.getElementById('swal-stock').value;
                    const descripcion = document.getElementById('swal-descripcion').value;

                    if (!nombre || !precio || (tipo === 'producto' && !stock)) {
                        Swal.showValidationMessage('Todos los campos marcados con * son obligatorios');
                        return false;
                    }

                    return { tipo, nombre, precio, stock, descripcion };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { tipo, nombre, precio, stock, descripcion } = result.value;
                    const url = tipo === 'producto' ? '{{ route("productos.store") }}' : '{{ route("servicios.store") }}';
                    const data = {
                        _token: '{{ csrf_token() }}',
                        nombre: nombre,
                        descripcion: descripcion,
                        [tipo === 'producto' ? 'precio_venta' : 'precio']: precio,
                        stock: stock,
                        stock_minimo: 0 // Default para registro rápido
                    };

                    Swal.fire({
                        title: 'Guardando...',
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: data,
                        success: function(response) {
                            if (response.success) {
                                // Actualizar variables locales
                                const newItem = response.data;
                                if (tipo === 'producto') {
                                    PRODUCTOS.push(newItem);
                                } else {
                                    SERVICIOS.push(newItem);
                                }

                                // Notificar éxito
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Registrado!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Si hay una fila vacía o recién agregada, podríamos intentar seleccionarlo,
                                // o simplemente dejar que el usuario lo busque en la siguiente fila.
                                // Por facilidad, solo refrescamos los Select2 si existen.
                                $('.item-select').each(function() {
                                    const row = this.closest('tr');
                                    const rowTipo = row.querySelector('.tipo-select').value;
                                    if (rowTipo === tipo) {
                                        const option = new Option(`${newItem.nombre} - ${newItem.descripcion || ''}`, newItem.id, false, false);
                                        option.dataset.precio = newItem.precio_venta || newItem.precio || 0;
                                        option.dataset.descripcion = newItem.descripcion || newItem.nombre;
                                        $(this).append(option);
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON.message || 'No se pudo registrar el ítem', 'error');
                        }
                    });
                }
            });
        }
        // --- Prevención de salida accidental ---
        let isSubmitting = false;

        // Detectar si el formulario se está enviando
        const ventaForm = document.getElementById('venta-form');
        if (ventaForm) {
            ventaForm.addEventListener('submit', () => { isSubmitting = true; });
        }

        function hasUnsavedChanges() {
            const rowCount = document.querySelectorAll('#items-table tbody tr').length;
            const totalText = document.getElementById('total-general').textContent;
            const totalValue = parseFloat(totalText.replace('$', '').replace(',', '')) || 0;
            return rowCount > 0 && totalValue > 0;
        }

        // Alerta nativa para cierre de pestaña o recarga
        window.addEventListener('beforeunload', function(e) {
            if (!isSubmitting && hasUnsavedChanges()) {
                e.preventDefault();
                e.returnValue = ''; // El navegador mostrará su propio mensaje estándar
            }
        });

        // Alerta SweetAlert2 para navegación interna (clics en enlaces)
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            // Ignorar si es el botón de cancelar del formulario (que ya tiene su redirección) 
            // o si es una descarga/nueva pestaña
            if (link && !isSubmitting && hasUnsavedChanges() && !link.hasAttribute('download') && link.target !== '_blank') {
                const href = link.href;
                // Solo interceptar si es un enlace interno
                if (href && href.startsWith(window.location.origin) && !href.includes('#')) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Venta sin finalizar?',
                        text: "Tienes artículos en la lista. Si sales ahora, se perderán los cambios.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'SÍ, SALIR',
                        cancelButtonText: 'QUEDARME',
                        background: '#1e293b',
                        color: '#fff',
                        customClass: {
                            popup: 'rounded-3xl border border-white/20 shadow-2xl',
                            title: 'text-xl font-black uppercase tracking-tighter'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            isSubmitting = true; 
                            window.location.href = href;
                        }
                    });
                }
            }
        });
    </script>
@endpush
