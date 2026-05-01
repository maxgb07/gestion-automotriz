@extends('layouts.app')

@section('title', 'Cuentas por Pagar')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Cuentas por Pagar</h1>
            <p class="text-blue-200">Gestión de saldos, pagos y notas de crédito con proveedores</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
        <form action="{{ route('cuentas_por_pagar.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="md:flex-[3] relative w-full">
                <label class="block text-md font-black text-blue-200 uppercase tracking-widest mb-2 ml-1">Buscar Proveedor</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="NOMBRE DEL PROVEEDOR..." class="block w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm uppercase">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-fit px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase">
                    BUSCAR
                </button>
                @if(request('buscar'))
                    <a href="{{ route('cuentas_por_pagar.index') }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center uppercase text-sm">
                        LIMPIAR
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($proveedores->count() > 0)
        <!-- Grid de Proveedores con Saldo -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($proveedores as $proveedor)
                <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-6 border border-white/20 shadow-2xl hover:bg-white/15 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-xl font-bold text-white uppercase">{{ $proveedor->nombre }}</h2>
                            @if($proveedor->facturas_vencidas > 0)
                                <span class="px-3 py-1 bg-red-500/20 border border-red-500/50 text-red-300 text-xs font-bold rounded-full uppercase">
                                    {{ $proveedor->facturas_vencidas }} Vencidas
                                </span>
                            @endif
                        </div>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center bg-white/5 p-3 rounded-xl border border-white/10">
                                <span class="text-blue-200 text-sm font-semibold uppercase">Deuda Total</span>
                                <span class="text-white font-black text-xl">${{ number_format($proveedor->total_deuda, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center bg-emerald-500/10 p-3 rounded-xl border border-emerald-500/20">
                                <span class="text-emerald-200 text-sm font-semibold uppercase">Saldo a Favor (NC)</span>
                                <span class="text-emerald-400 font-black text-xl">${{ number_format($proveedor->saldo_favor, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('cuentas_por_pagar.show', $proveedor) }}" class="w-full block text-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase">
                        Ver Estado de Cuenta
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="w-full flex flex-col items-center justify-center min-h-[400px] bg-white/10 backdrop-blur-xl rounded-3xl p-12 border border-white/20 text-center shadow-2xl mt-8">
            <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <svg class="w-12 h-12 text-emerald-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-2xl font-black text-white tracking-tight mb-2 uppercase">¡Todo al Corriente!</p>
            <p class="text-md text-emerald-200/80 uppercase tracking-widest font-semibold">No hay deudas pendientes con ningún proveedor.</p>
        </div>
    @endif

@endsection
