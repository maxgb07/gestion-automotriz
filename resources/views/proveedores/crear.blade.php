@extends('layouts.app')

@section('title', 'Nuevo Proveedor')

@section('content')
    <div class="max-w-4xl mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('proveedores.index') }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al listado
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Nuevo Proveedor</h1>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
            <form action="{{ route('proveedores.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Nombre / Razón Social *</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">
                    </div>

                    <div>
                        <label for="contacto" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Nombre de Contacto 1</label>
                        <input type="text" name="contacto" id="contacto" value="{{ old('contacto') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">
                    </div>

                    <div>
                        <label for="contacto_secundario" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Nombre de Contacto 2</label>
                        <input type="text" name="contacto_secundario" id="contacto_secundario" value="{{ old('contacto_secundario') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Teléfono Contacto 1</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
                    </div>

                    <div>
                        <label for="telefono_secundario" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Teléfono Contacto 2</label>
                        <input type="text" name="telefono_secundario" id="telefono_secundario" value="{{ old('telefono_secundario') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Email Contacto 1</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm lowercase">
                    </div>

                    <div>
                        <label for="email_secundario" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Email Contacto 2</label>
                        <input type="email" name="email_secundario" id="email_secundario" value="{{ old('email_secundario') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm lowercase">
                    </div>

                    <div class="md:col-span-2">
                        <label for="marcas_productos" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Marcas / Productos que vende</label>
                        <input type="text" name="marcas_productos" id="marcas_productos" value="{{ old('marcas_productos') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">
                    </div>

                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Dirección</label>
                        <textarea name="direccion" id="direccion" rows="2" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">{{ old('direccion') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-blue-100 mb-2 uppercase">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-6 py-12 mt-10 border-t border-white/5">
                    <button type="submit" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-sm px-10 py-4 focus:outline-none inline-flex items-center min-w-[220px] justify-center uppercase tracking-widest">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Registrar Proveedor
                    </button>
                    <a href="{{ route('proveedores.index') }}" class="inline-flex items-center justify-center px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg border border-white/20 transition-all min-w-[200px] text-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
