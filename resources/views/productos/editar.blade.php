@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
    <div class="max-w-4xl mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('productos.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al listado
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Editar Producto</h1>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
            <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Sección: Identificación -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-blue-100 mb-2 uppercase">SKU / Clave *</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $producto->nombre) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">
                        </div>

                        <div>
                            <label for="codigo_barras" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Código de Barras</label>
                            <input type="text" name="codigo_barras" id="codigo_barras" value="{{ old('codigo_barras', $producto->codigo_barras) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">
                        </div>

                        <div>
                            <label for="marca" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Marca</label>
                            <input type="text" name="marca" id="marca" value="{{ old('marca', $producto->marca) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase" placeholder="EJ: LUK, MOOG, AC DELCO...">
                        </div>

                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Descripción / Nombre Completo</label>
                            <textarea name="descripcion" id="descripcion" rows="2" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase" placeholder="OPCIONAL: INGRESA UNA DESCRIPCIÓN DETALLADA DEL PRODUCTO">{{ old('descripcion', $producto->descripcion) }}</textarea>
                        </div>
                    </div>

                    <hr class="border-white/10">

                    <!-- Sección: Aplicación e Imagen -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="aplicacion" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Aplicación (Vehículos)</label>
                            <textarea name="aplicacion" id="aplicacion" rows="4" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase" placeholder="EJ: NISSAN TSURU III, VW SEDAN...">{{ old('aplicacion', $producto->aplicacion) }}</textarea>
                        </div>

                        <div>
                            <label for="imagen" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Imagen del Producto</label>
                            <div class="relative group h-[116px]">
                                <input type="file" name="imagen" id="imagen" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(event)">
                                <div id="image-placeholder" class="w-full h-full bg-white/10 border-2 border-dashed border-white/20 rounded-xl flex flex-col items-center justify-center group-hover:bg-white/20 group-hover:border-blue-500/50 transition-all backdrop-blur-sm overflow-hidden {{ $producto->imagen ? 'border-solid border-blue-500/30' : 'border-dashed' }}">
                                    <img id="preview" src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : '' }}" class="{{ $producto->imagen ? '' : 'hidden' }} w-full h-full object-contain">
                                    <div id="upload-icon" class="flex flex-col items-center {{ $producto->imagen ? 'hidden' : '' }}">
                                        <svg class="w-8 h-8 text-blue-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-xs text-blue-200 uppercase">Cambiar Imagen</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-white/10">

                    <!-- Sección: Precios y Stock -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label for="precio_compra" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Precio Compra *</label>
                            <input type="number" step="any" name="precio_compra" id="precio_compra" value="{{ old('precio_compra', $producto->precio_compra) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm" required>
                        </div>

                        <div>
                            <label for="precio_venta" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Precio Venta *</label>
                            <input type="number" step="any" name="precio_venta" id="precio_venta" value="{{ old('precio_venta', $producto->precio_venta) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm font-bold">
                        </div>

                        <div>
                            <label for="stock" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Existencia *</label>
                            <input type="number" name="stock" id="stock" value="{{ old('stock', $producto->stock) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
                        </div>

                        <div>
                            <label for="stock_minimo" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Stock Mínimo *</label>
                            <input type="number" name="stock_minimo" id="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">{{ old('observaciones', $producto->observaciones) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-6 py-12 mt-10 border-t border-white/5">
                    <button type="submit" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-sm px-10 py-4 focus:outline-none inline-flex items-center min-w-[220px] justify-center uppercase tracking-widest">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('productos.index') }}" class="inline-flex items-center justify-center px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg border border-white/20 transition-all min-w-[200px] text-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('preview');
                const icon = document.getElementById('upload-icon');
                const placeholder = document.getElementById('image-placeholder');
                
                preview.src = reader.result;
                preview.classList.remove('hidden');
                icon.classList.add('hidden');
                placeholder.classList.remove('border-dashed');
                placeholder.classList.add('border-solid', 'border-blue-500/30');
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
