@extends('layouts.app')

@section('title', 'Expediente del Cliente')

@section('content')
<script>
    window.confirmarEliminarVehiculo = function(id, descripcion) {
        Swal.fire({
            title: '¿Desactivar vehículo?',
            text: `El vehículo "${descripcion}" será marcado como inactivo. Podrás reactivarlo después si es necesario.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar',
            background: 'rgba(15, 23, 42, 0.95)',
            color: '#fff',
            customClass: {
                popup: 'backdrop-blur-xl border border-white/20 rounded-3xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-vehiculo-${id}`).submit();
            }
        });
    };

    window.activarVehiculo = function(id, descripcion) {
        Swal.fire({
            title: '¿Reactivar vehículo?',
            text: `El vehículo "${descripcion}" volverá a estar activo.`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, reactivar',
            cancelButtonText: 'Cancelar',
            background: 'rgba(15, 23, 42, 0.95)',
            color: '#fff',
            customClass: {
                popup: 'backdrop-blur-xl border border-white/20 rounded-3xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`restore-vehiculo-${id}`).submit();
            }
        });
    };
</script>

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('clientes.index') }}" class="p-2 text-blue-200 hover:text-white hover:bg-white/10 rounded-xl transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Expediente del Cliente</h1>
                <p class="text-blue-200/50 text-xs font-bold uppercase tracking-widest mt-1">Gestión detallada y vinculación de unidades</p>
            </div>
        </div>

    </div>

    <div class="space-y-8">
        
        <!-- 1. INFORMACIÓN DEL CLIENTE (Full Width) -->
        <div class="bg-white/10 backdrop-blur-xl rounded-[2.5rem] border border-white/20 shadow-2xl p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-500/40">
                        <span class="text-3xl font-black text-white uppercase">{{ substr($cliente->nombre, 0, 2) }}</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-4">
                            <h2 class="text-3xl font-black text-white uppercase leading-none">{{ $cliente->nombre }}</h2>
                            <a href="{{ route('clientes.edit', $cliente) }}" class="p-1.5 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all" title="Editar Información">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="flex gap-4 mt-2 ml-1">
                            <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-white text-[10px] font-bold uppercase tracking-widest">ID: #{{ str_pad($cliente->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="px-3 py-1 bg-blue-500/20 border border-blue-500/30 rounded-lg text-white text-[10px] font-bold uppercase tracking-widest">RFC: {{ $cliente->rfc ?? 'SIN RFC' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-2">
                <!-- Teléfono Celular -->
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-[0.2em]">Teléfono Celular</p>
                    <div class="flex items-center gap-3">
                        <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-white font-bold">{{ $cliente->celular }}</span>
                    </div>
                </div>

                <!-- Teléfono Alterno -->
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-[0.2em]">Teléfono Fijo</p>
                    <div class="flex items-center gap-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-white font-bold">{{ $cliente->telefono ?? '---' }}</span>
                    </div>
                </div>

                <!-- Correo -->
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-[0.2em]">Correo Electrónico</p>
                    <div class="flex items-center gap-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-white font-bold truncate text-sm">{{ $cliente->email ?? 'SIN CORREO' }}</span>
                    </div>
                </div>

                <!-- Dirección (Full Row) -->
                <div class="md:col-span-2 lg:col-span-3 mt-4 p-4 bg-white/5 rounded-2xl border border-white/5">
                    <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-[0.2em] mb-2">Dirección Completa</p>
                    <p class="text-white uppercase font-bold leading-relaxed">
                        {{ $cliente->direccion }}{{ $cliente->codigo_postal ? ', CP ' . $cliente->codigo_postal : '' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. TABLA DE VEHÍCULOS (Full Width) -->
        <div class="bg-white/10 backdrop-blur-xl rounded-[2.5rem] border border-white/20 shadow-2xl overflow-hidden">
            <div class="p-8 border-b border-white/10 flex flex-col md:flex-row justify-between items-center bg-white/5 gap-4">
                <div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight">Listado de Vehículos</h3>
                    <p class="text-blue-200/50 text-xs font-bold uppercase tracking-widest mt-1">Gestión de unidades activas e inactivas</p>
                </div>
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <div class="flex bg-white/5 p-1 rounded-xl border border-white/10">
                        <a href="{{ route('clientes.show', [$cliente, 'v_status' => 'activos']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all {{ $vStatus === 'activos' ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-100 hover:text-white' }}">
                            Activos
                        </a>
                        <a href="{{ route('clientes.show', [$cliente, 'v_status' => 'inactivos']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all {{ $vStatus === 'inactivos' ? 'bg-red-600 text-white shadow-lg' : 'text-blue-100 hover:text-white' }}">
                            Inactivos
                        </a>
                    </div>
                    <a href="{{ route('vehiculos.create', $cliente) }}" class="btn-premium-blue px-6 py-2 text-white text-[10px] font-black rounded-lg shadow-lg shadow-blue-500/20 transition-all uppercase tracking-widest flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Añadir Nuevo Vehículo
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if($vehiculos->count() > 0)
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-blue-200/40 uppercase tracking-[0.3em] bg-white/5">
                            <th class="px-8 py-5 text-center">Vehículo / Modelo</th>
                            <th class="px-8 py-5 text-center">Placas / Serie</th>
                            <th class="px-8 py-5 text-center">Recorrido</th>
                            <th class="px-8 py-5 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($vehiculos as $vehiculo)
                        <tr class="group hover:bg-white/5 transition-all">
                            <td class="px-8 py-6 text-center">
                                <div class="flex items-center justify-center gap-4">
                                    <div class="w-12 h-12 bg-white/5 rounded-xl flex items-center justify-center border border-white/10 group-hover:border-blue-500/50 transition-colors">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-lg font-black text-white uppercase leading-none">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
                                        <p class="text-blue-200/40 text-[10px] font-bold mt-2 uppercase tracking-[0.2em]">Año: {{ $vehiculo->anio }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="space-y-2 flex flex-col items-center">
                                    <span class="inline-block px-3 py-1 bg-blue-500/10 text-blue-300 rounded-lg text-[10px] font-bold uppercase tracking-widest border border-blue-500/20">Placas: {{ $vehiculo->placas ?? '----' }}</span>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-[0.15em]">VIN: {{ $vehiculo->numero_serie ?? 'NO REGISTRADO' }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="inline-block px-4 py-2 bg-white/5 rounded-xl border border-white/5">
                                    <span class="text-white font-mono font-black text-lg">{{ number_format($vehiculo->kilometraje ?? 0) }}</span>
                                    <span class="text-[9px] text-blue-200/30 font-black ml-1 uppercase">KM</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex justify-center items-center gap-3 transition-all">
                                    @if($vehiculo->trashed())
                                        <button type="button" onclick="activarVehiculo({{ $vehiculo->id }}, '{{ $vehiculo->marca }} {{ $vehiculo->modelo }}')" class="p-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg transition-all" title="Reactivar Vehículo">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        <form id="restore-vehiculo-{{ $vehiculo->id }}" action="{{ route('vehiculos.restore', $vehiculo->id) }}" method="POST" class="hidden">
                                            @csrf
                                        </form>
                                    @else
                                        <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="p-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-lg transition-all" title="Editar Vehículo">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form id="delete-vehiculo-{{ $vehiculo->id }}" action="{{ route('vehiculos.destroy', $vehiculo) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmarEliminarVehiculo({{ $vehiculo->id }}, '{{ $vehiculo->marca }} {{ $vehiculo->modelo }}')" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg transition-all" title="Desactivar Vehículo">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="py-24 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-white/5 rounded-[2rem] mb-6 border border-white/10">
                        <svg class="w-12 h-12 text-blue-200/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-black text-white uppercase tracking-tight">Sin Unidades Registradas</h4>
                    <p class="text-blue-200/30 text-xs font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">No hay vehículos asociados a este expediente actualmente.</p>
                </div>
                @endif
            </div>
        </div>

    </div>
@push('styles')
    <style>
        .btn-premium-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            border: none !important;
            display: inline-flex !important;
            cursor: pointer !important;
        }
        .btn-premium-blue:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.5) !important;
        }
    </style>
@endpush
@endsection
