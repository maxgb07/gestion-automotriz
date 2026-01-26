@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-12">
        <!-- Welcome Section -->
        <div class="mb-16 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight uppercase">Gestión Automotriz Integral</h1>
            <div class="w-24 h-1.5 bg-gradient-to-r from-blue-500 to-purple-600 mx-auto rounded-full mb-4"></div>
            <p class="text-blue-200 text-lg md:text-xl uppercase font-black tracking-widest">Control Total de Taller y Refaccionaria</p>
        </div>

        <!-- Quick Access Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Clientes Card -->
            <a href="{{ route('clientes.index') }}" class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/20 cursor-pointer block">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-500/20 rounded-xl group-hover:bg-blue-500/30 transition-colors">
                        <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Clientes</h3>
                <p class="text-blue-200 text-sm">Gestión de clientes y sus vehículos</p>
            </a>

            <!-- Ventas Card -->
            <a href="{{ route('ventas.index') }}" class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/20 cursor-pointer block">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-500/20 rounded-xl group-hover:bg-purple-500/30 transition-colors">
                        <svg class="w-8 h-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Ventas</h3>
                <p class="text-purple-200 text-sm">Registro de ventas y gestión de créditos</p>
            </a>

            <!-- Órdenes de Servicio Card -->
            <a href="{{ route('ordenes.index') }}" class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/20 cursor-pointer block">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-500/20 rounded-xl group-hover:bg-blue-500/30 transition-colors">
                        <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Órdenes de Servicio</h3>
                <p class="text-blue-200 text-sm">Recepción, reparación y entrega de vehículos</p>
            </a>

            <!-- Inventario Card -->
            <a href="{{ route('productos.index') }}" class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-green-500/20 cursor-pointer block">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-500/20 rounded-xl group-hover:bg-green-500/30 transition-colors">
                        <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Inventario</h3>
                <p class="text-blue-200 text-sm">Control de productos y existencias</p>
            </a>

            <!-- Servicios Card -->
            <a href="{{ route('servicios.index') }}" class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-indigo-500/20 cursor-pointer block">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-indigo-500/20 rounded-xl group-hover:bg-indigo-500/30 transition-colors">
                        <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Servicios</h3>
                <p class="text-blue-200 text-sm">Catálogo de servicios del taller</p>
            </a>

            <!-- Proveedores Card -->
            <a href="{{ route('proveedores.index') }}" class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-orange-500/20 cursor-pointer block">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-500/20 rounded-xl group-hover:bg-orange-500/30 transition-colors">
                        <svg class="w-8 h-8 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Proveedores</h3>
                <p class="text-blue-200 text-sm">Gestión de proveedores locales</p>
            </a>

            <!-- Compras Card -->
            <a href="{{ route('compras.index') }}" class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-yellow-500/20 cursor-pointer block">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-yellow-500/20 rounded-xl group-hover:bg-yellow-500/30 transition-colors">
                        <svg class="w-8 h-8 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Compras</h3>
                <p class="text-blue-200 text-sm">Registro de compras y abastecimiento</p>
            </a>

            <!-- Reportes Card -->
            <a href="{{ route('reportes.index') }}" class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-cyan-500/20 cursor-pointer block">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-cyan-500/20 rounded-xl group-hover:bg-cyan-500/30 transition-colors">
                        <svg class="w-8 h-8 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Reportes y Corte</h3>
                <p class="text-blue-200 text-sm">Corte diario y balances históricos</p>
            </a>
        </div>

        <div class="mt-16 bg-blue-500/20 backdrop-blur-xl rounded-2xl p-8 border border-blue-500/30">
            <div class="flex flex-col items-center text-center gap-4 max-w-3xl mx-auto">
                <div class="p-3 bg-blue-500/30 rounded-xl">
                    <svg class="w-8 h-8 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white mb-2 tracking-wide uppercase">Operaciones Centralizadas</h3>
                    <p class="text-blue-100/80 leading-relaxed uppercase text-[10px] font-bold">Desde la recepción de vehículos y el seguimiento de reparaciones, hasta el control total de refacciones y ventas integradas.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
