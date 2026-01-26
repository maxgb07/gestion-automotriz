@extends('layouts.app')

@section('title', 'Módulo de Reportes')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="mb-12 text-center">
        <h1 class="text-4xl font-bold text-white mb-4 tracking-tight uppercase">Módulo de Reportes</h1>
        <div class="w-24 h-1.5 bg-gradient-to-r from-blue-500 to-purple-600 mx-auto rounded-full mb-4"></div>
        <p class="text-blue-200 text-lg uppercase font-black tracking-widest">Información detallada para la toma de decisiones</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Corte del Día -->
        <a href="{{ route('reportes.corte') }}" class="group bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/20 cursor-pointer flex flex-col items-center text-center">
            <div class="p-6 bg-blue-500/20 rounded-2xl mb-6 group-hover:bg-blue-500/30 transition-colors">
                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-black text-white mb-3 uppercase tracking-tighter">Corte del Día</h3>
            <p class="text-blue-200 text-sm uppercase font-bold leading-relaxed">Resumen detallado de los ingresos y operaciones de hoy</p>
            <div class="mt-6 px-6 py-2 bg-blue-600 text-white text-xs font-black rounded-xl uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">Ver Ahora</div>
        </a>

        <!-- Reporte de Ventas -->
        <a href="{{ route('reportes.ventas') }}" class="group bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/20 cursor-pointer flex flex-col items-center text-center">
            <div class="p-6 bg-purple-500/20 rounded-2xl mb-6 group-hover:bg-purple-500/30 transition-colors">
                <svg class="w-12 h-12 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-black text-white mb-3 uppercase tracking-tighter">Reporte de Ventas</h3>
            <p class="text-blue-200 text-sm uppercase font-bold leading-relaxed">Histórico de productos y servicios facturados por periodo</p>
            <div class="mt-6 px-6 py-2 bg-purple-600 text-white text-xs font-black rounded-xl uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">Generar</div>
        </a>

        <!-- Reporte de Órdenes -->
        <a href="{{ route('reportes.ordenes') }}" class="group bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 hover:bg-white/15 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-green-500/20 cursor-pointer flex flex-col items-center text-center">
            <div class="p-6 bg-green-500/20 rounded-2xl mb-6 group-hover:bg-green-500/30 transition-colors">
                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-black text-white mb-3 uppercase tracking-tighter">Órdenes de Servicio</h3>
            <p class="text-blue-200 text-sm uppercase font-bold leading-relaxed">Control de reparaciones y estados por rango de fechas</p>
            <div class="mt-6 px-6 py-2 bg-green-600 text-white text-xs font-black rounded-xl uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">Generar</div>
        </a>
    </div>
</div>
@endsection
