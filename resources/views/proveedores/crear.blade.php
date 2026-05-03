@extends('layouts.app')

@section('title', 'Nuevo Proveedor')

@section('content')
    <div class="mx-auto py-4">

        {{-- Encabezado --}}
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('proveedores.index') }}" class="p-2 bg-white/5 hover:bg-white/10 rounded-xl border border-white/10 transition-colors text-blue-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">Nuevo Proveedor</h1>
                <p class="text-blue-200/50 text-xs font-bold uppercase tracking-widest mt-0.5">Completa la información del proveedor por secciones</p>
            </div>
        </div>

        <form action="{{ route('proveedores.store') }}" method="POST">
            @csrf

            <div class="space-y-6">

                {{-- ===== SECCIÓN 1: DATOS GENERALES ===== --}}
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
                    <div class="p-5 border-b border-white/10 bg-white/5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-300 text-sm font-black">1</div>
                        <h2 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Datos Generales</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Nombre / Razón Social *</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase font-bold"
                                placeholder="EJ: DISTRIBUIDORA AUTOMOTRIZ S.A. DE C.V." required>
                        </div>
                        <div>
                            <label for="rfc" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">RFC</label>
                            <input type="text" name="rfc" id="rfc" value="{{ old('rfc') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase font-bold"
                                placeholder="EJ: DAU900101XXX">
                        </div>
                        <div class="md:col-span-3">
                            <label for="marcas_productos" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Marcas / Productos que vende</label>
                            <input type="text" name="marcas_productos" id="marcas_productos" value="{{ old('marcas_productos') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase font-bold"
                                placeholder="EJ: LUK, MOOG, BOSCH, AC DELCO...">
                        </div>
                        <div class="md:col-span-3">
                            <label for="direccion" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Dirección</label>
                            <textarea name="direccion" id="direccion" rows="2"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase font-bold">{{ old('direccion') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ===== SECCIÓN 2: CONTACTO PRINCIPAL ===== --}}
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
                    <div class="p-5 border-b border-white/10 bg-white/5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-300 text-sm font-black">2</div>
                        <h2 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Contacto Principal</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label for="contacto" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Nombre</label>
                            <input type="text" name="contacto" id="contacto" value="{{ old('contacto') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase font-bold"
                                placeholder="NOMBRE COMPLETO">
                        </div>
                        <div>
                            <label for="telefono" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold"
                                placeholder="55 1234 5678">
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all lowercase font-bold"
                                placeholder="contacto@empresa.com">
                        </div>
                    </div>
                </div>

                {{-- ===== SECCIÓN 3: CONTACTO SECUNDARIO ===== --}}
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
                    <div class="p-5 border-b border-white/10 bg-white/5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-300/60 text-sm font-black">3</div>
                        <h2 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Contacto Secundario</h2>
                        <span class="text-xs text-blue-200/30 font-bold uppercase tracking-widest ml-1">(Opcional)</span>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label for="contacto_secundario" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Nombre</label>
                            <input type="text" name="contacto_secundario" id="contacto_secundario" value="{{ old('contacto_secundario') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase font-bold"
                                placeholder="NOMBRE COMPLETO">
                        </div>
                        <div>
                            <label for="telefono_secundario" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Teléfono</label>
                            <input type="text" name="telefono_secundario" id="telefono_secundario" value="{{ old('telefono_secundario') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold"
                                placeholder="55 1234 5678">
                        </div>
                        <div>
                            <label for="email_secundario" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Email</label>
                            <input type="email" name="email_secundario" id="email_secundario" value="{{ old('email_secundario') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all lowercase font-bold"
                                placeholder="contacto2@empresa.com">
                        </div>
                    </div>
                </div>

                {{-- ===== SECCIÓN 4: CONDICIONES COMERCIALES ===== --}}
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
                    <div class="p-5 border-b border-white/10 bg-white/5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-300 text-sm font-black">4</div>
                        <h2 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Condiciones Comerciales</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label for="dias_credito" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Días de Crédito</label>
                            <input type="number" name="dias_credito" id="dias_credito" value="{{ old('dias_credito', 0) }}" min="0"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div>
                            <label for="porcentaje_descuento_global" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">% Descuento Global</label>
                            <input type="number" step="0.01" name="porcentaje_descuento_global" id="porcentaje_descuento_global" value="{{ old('porcentaje_descuento_global', 0.00) }}" min="0" max="100"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div>
                            <label for="porcentaje_descuento_extra" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">% Descuento Extra</label>
                            <input type="number" step="0.01" name="porcentaje_descuento_extra" id="porcentaje_descuento_extra" value="{{ old('porcentaje_descuento_extra', 0.00) }}" min="0" max="100"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        </div>
                        <div class="md:col-span-3">
                            <label for="observaciones" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="3"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all uppercase font-bold"
                                placeholder="NOTAS INTERNAS SOBRE ESTE PROVEEDOR...">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ===== SECCIÓN 5: DATOS BANCARIOS ===== --}}
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl overflow-hidden">
                    <div class="p-5 border-b border-white/10 bg-white/5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/20 border border-teal-500/30 flex items-center justify-center text-teal-300 text-sm font-black">5</div>
                        <h2 class="text-md font-black text-blue-200 uppercase tracking-[0.2em]">Datos Bancarios</h2>
                        <span class="text-xs text-blue-200/30 font-bold uppercase tracking-widest ml-1">(Opcional)</span>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="banco" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Banco</label>
                            <input type="text" name="banco" id="banco" value="{{ old('banco') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all uppercase font-bold"
                                placeholder="EJ: BBVA, SANTANDER, BANAMEX...">
                        </div>
                        <div>
                            <label for="titular_cuenta" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Titular de la Cuenta</label>
                            <input type="text" name="titular_cuenta" id="titular_cuenta" value="{{ old('titular_cuenta') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all uppercase font-bold"
                                placeholder="NOMBRE DEL TITULAR">
                        </div>
                        <div>
                            <label for="cuenta_bancaria" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">Número de Cuenta</label>
                            <input type="text" name="cuenta_bancaria" id="cuenta_bancaria" value="{{ old('cuenta_bancaria') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all font-mono font-bold tracking-widest"
                                placeholder="0000 0000 0000 0000" maxlength="20">
                        </div>
                        <div>
                            <label for="clabe_interbancaria" class="block text-xs font-black text-blue-200/60 uppercase tracking-widest mb-2">CLABE Interbancaria</label>
                            <input type="text" name="clabe_interbancaria" id="clabe_interbancaria" value="{{ old('clabe_interbancaria') }}"
                                class="block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-200/20 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all font-mono font-bold tracking-widest"
                                placeholder="000 000 0000 0000 0000 0" maxlength="18">
                        </div>
                    </div>
                </div>

            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-center gap-6 py-12 mt-6 border-t border-white/5">
                <button type="submit" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-sm px-10 py-4 focus:outline-none inline-flex items-center min-w-[220px] justify-center uppercase tracking-widest">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Registrar Proveedor
                </button>
                <a href="{{ route('proveedores.index') }}" class="inline-flex items-center justify-center px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg border border-white/20 transition-all min-w-[200px] text-center uppercase tracking-widest">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </a>
            </div>
        </form>

    </div>
@endsection
