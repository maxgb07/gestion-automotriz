@extends('layouts.app')

@section('title', 'Editar Servicio')

@section('content')
    <div class="max-w-4xl mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('servicios.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al listado
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Editar Servicio</h1>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
            <form action="{{ route('servicios.update', $servicio) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Sección: Identificación -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Nombre del Servicio *</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $servicio->nombre) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">
                        </div>

                        <div>
                            <label for="precio" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Precio del Servicio *</label>
                            <div class="relative">
                                <input type="number" step="any" name="precio" id="precio" value="{{ old('precio', $servicio->precio) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm font-bold">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Descripción Detallada</label>
                            <textarea name="descripcion" id="descripcion" rows="3" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase" placeholder="DESCRIBE EL ALCANCE DEL SERVICIO O TRABAJO A REALIZAR">{{ old('descripcion', $servicio->descripcion) }}</textarea>
                        </div>
                    </div>

                    <hr class="border-white/10">

                    <!-- Sección: Multimedia y Notas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Observaciones Internas</label>
                            <textarea name="observaciones" id="observaciones" rows="4" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase" placeholder="NOTAS ADICIONALES PARA USO INTERNO DEL TALLER">{{ old('observaciones', $servicio->observaciones) }}</textarea>
                        </div>

                        <div>
                            <label for="imagen" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Imagen Representativa</label>
                            <div class="relative group h-[116px]">
                                <input type="file" name="imagen" id="imagen" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(event)">
                                <div id="image-placeholder" class="w-full h-full bg-white/10 border-2 border-dashed border-white/20 rounded-xl flex flex-col items-center justify-center group-hover:bg-white/20 group-hover:border-blue-500/50 transition-all backdrop-blur-sm overflow-hidden {{ $servicio->imagen ? 'border-solid border-indigo-500/30' : '' }}">
                                    <img id="preview" src="{{ $servicio->imagen ? asset('storage/' . $servicio->imagen) : '' }}" class="{{ $servicio->imagen ? '' : 'hidden' }} w-full h-full object-contain">
                                    <div id="upload-icon" class="flex flex-col items-center {{ $servicio->imagen ? 'hidden' : '' }}">
                                        <svg class="w-8 h-8 text-indigo-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-xs text-indigo-200 uppercase">Cambiar Imagen</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-6 py-12 mt-10 border-t border-white/5">
                    <button type="submit" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-sm px-10 py-4 focus:outline-none inline-flex items-center min-w-[220px] justify-center uppercase tracking-widest">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('servicios.index') }}" class="inline-flex items-center justify-center px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg border border-white/20 transition-all min-w-[200px] text-center">
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
                placeholder.classList.add('border-solid', 'border-indigo-500/30');
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
