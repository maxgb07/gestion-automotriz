@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white uppercase">Gestión de Proveedores</h1>
            <p class="text-blue-200">Administración de proveedores para compras e inventario</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('proveedores.create') }}" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuevo Proveedor
            </a>
        </div>
    </div>

    <!-- Search Box -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
        <form action="{{ route('proveedores.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="BUSCAR POR NOMBRE, CONTACTO, TELÉFONO O EMAIL..." class="block w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm uppercase">
            </div>
            <button type="submit" class="w-fit px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all">
                BUSCAR
            </button>
            @if(request('buscar'))
                <a href="{{ route('proveedores.index') }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center">
                    LIMPIAR
                </a>
            @endif
        </form>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white/10 backdrop-blur-xl rounded-3xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Nombre / Empresa</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Contacto</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Teléfono / Email</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Dirección</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($proveedores as $proveedor)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3 text-center">
                                    <!-- <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-blue-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/20">
                                        {{ substr($proveedor->nombre, 0, 1) }}
                                    </div> -->
                                    <div class="text-white font-bold uppercase group-hover:text-blue-300 transition-colors">{{ $proveedor->nombre }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-blue-100 uppercase">{{ $proveedor->contacto ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-blue-100 text-sm text-center font-medium text-white">
                                <div class="flex flex-col items-center">
                                    <span>{{ $proveedor->telefono ?? 'S/T' }}</span>
                                    <span class="text-white text-sm text-blue-100 lowercase">{{ $proveedor->email ?? 'Sin email' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-blue-100 text-xs uppercase line-clamp-1 mx-auto text-center">{{ $proveedor->direccion ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('proveedores.edit', $proveedor) }}" class="p-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-xl transition-all" title="EDITAR">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="eliminarProveedor({{ $proveedor->id }}, '{{ $proveedor->nombre }}')" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-xl transition-all" title="ELIMINAR">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $proveedor->id }}" action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-blue-300/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-xl font-medium text-blue-200 uppercase">SIN PROVEEDORES REGISTRADOS</p>
                                    <p class="text-sm text-blue-200/50 mt-2 uppercase">AGREGA PROVEEDORES PARA PODER REALIZAR COMPRAS DE INVENTARIO.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($proveedores->hasPages())
            <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                {{ $proveedores->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

<script>
    function eliminarProveedor(id, nombre) {
        Swal.fire({
            title: '¿ELIMINAR PROVEEDOR?',
            text: `ESTÁS A PUNTO DE ELIMINAR A "${nombre}". ESTA ACCIÓN NO SE PUEDE DESHACER.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'SÍ, ELIMINAR',
            cancelButtonText: 'CANCELAR',
            background: 'rgba(15, 23, 42, 0.95)',
            color: '#fff',
            customClass: {
                popup: 'backdrop-blur-xl border border-white/20 rounded-3xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
@endsection
