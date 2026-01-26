@extends('layouts.app')

@section('title', 'Editar Vehículo')

@section('content')
    <div class="max-w-4xl mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('clientes.show', $vehiculo->cliente_id) }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al cliente
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Editar Vehículo</h1>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
            <!-- Client Info for context -->
            <div class="mb-8 p-4 bg-white/5 rounded-2xl border border-white/5">
                <p class="text-blue-200/40 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Editando vehículo de:</p>
                <p class="text-white font-bold uppercase text-lg">{{ $vehiculo->cliente->nombre }}</p>
            </div>

            <form action="{{ route('vehiculos.update', $vehiculo) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="marca" class="block text-sm font-medium text-blue-100 mb-2 uppercase tracking-wide">Marca *</label>
                        <input type="text" name="marca" id="marca" value="{{ old('marca', $vehiculo->marca) }}" placeholder="Ej. TOYOTA" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">
                    </div>
                    <div>
                        <label for="modelo" class="block text-sm font-medium text-blue-100 mb-2 uppercase tracking-wide">Modelo *</label>
                        <input type="text" name="modelo" id="modelo" value="{{ old('modelo', $vehiculo->modelo) }}" placeholder="Ej. COROLLA" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">
                    </div>
                    <div>
                        <label for="anio" class="block text-sm font-medium text-blue-100 mb-2 uppercase tracking-wide">Año *</label>
                        <input type="number" name="anio" id="anio" value="{{ old('anio', $vehiculo->anio) }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
                    </div>
                    <div>
                        <label for="placas" class="block text-sm font-medium text-blue-100 mb-2 uppercase tracking-wide">Placas</label>
                        <input type="text" name="placas" id="placas" value="{{ old('placas', $vehiculo->placas) }}" placeholder="ABC-1234" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm font-mono uppercase">
                    </div>
                    <div>
                        <label for="kilometraje" class="block text-sm font-medium text-blue-100 mb-2 uppercase tracking-wide">Kilometraje</label>
                        <input type="number" name="kilometraje" id="kilometraje" value="{{ old('kilometraje', $vehiculo->kilometraje) }}" placeholder="0" class="block w-full px-4 py-3 bg-white/10 border border-white/10 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
                    </div>
                    <div>
                        <label for="numero_serie" class="block text-sm font-medium text-blue-100 mb-2 uppercase tracking-wide">Número de Serie / VIN</label>
                        <input type="text" name="numero_serie" id="numero_serie" value="{{ old('numero_serie', $vehiculo->numero_serie) }}" placeholder="VIN DE 17 DÍGITOS" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm font-mono uppercase">
                    </div>
                    <div class="md:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-blue-100 mb-2 uppercase tracking-wide">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3" placeholder="Detalles adicionales..." class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm uppercase">{{ old('observaciones', $vehiculo->observaciones) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-6 py-12 mt-10 border-t border-white/5">
                    <button type="submit" class="inline-flex items-center justify-center px-10 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-bold rounded-lg hover:from-blue-600 hover:to-purple-700 shadow-lg shadow-blue-500/20 transition-all min-w-[200px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('clientes.show', $vehiculo->cliente_id) }}" class="inline-flex items-center justify-center px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg border border-white/20 transition-all min-w-[200px] text-center">
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
