@extends('layouts.app')

@section('title', 'Listado de Clientes')

@section('content')
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Gestión de Clientes</h1>
                <p class="text-blue-200">Listado, búsqueda y administración de clientes</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex bg-white/5 p-1 rounded-xl border border-white/10">
                    <a href="{{ route('clientes.index', ['status' => 'activos']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $status === 'activos' ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-100 hover:text-white' }}">
                        Activos
                    </a>
                    <a href="{{ route('clientes.index', ['status' => 'inactivos']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $status === 'inactivos' ? 'bg-red-600 text-white shadow-lg' : 'text-blue-100 hover:text-white' }}">
                        Inactivos
                    </a>
                </div>
                <a href="{{ route('clientes.create') }}" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nuevo Cliente
                </a>
            </div>
        </div>

        <!-- Search Box -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
            <form action="{{ route('clientes.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="BUSCAR POR NOMBRE, RFC O CELULAR..." class="block w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm uppercase">
                </div>
                <button type="submit" class="w-fit px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all uppercase">
                    BUSCAR
                </button>
                @if(request('buscar'))
                    <a href="{{ route('clientes.index') }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center uppercase">
                        LIMPIAR
                    </a>
                @endif
            </form>
        </div>

        <!-- Clients Table -->
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl overflow-hidden border border-white/20 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Nombre / Razón Social</th>
                            <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">RFC</th>
                            <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Contacto</th>
                            <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($clientes as $cliente)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-left">
                                    <div class="flex items-left justify-left gap-3">
                                        <!-- <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/20">
                                            {{ substr($cliente->nombre, 0, 1) }}
                                        </div> -->
                                        <div class="text-white font-medium group-hover:text-blue-300 transition-colors uppercase text-left text-sm">{{ $cliente->nombre }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 bg-white/5 rounded-lg text-blue-100 text-sm font-mono border border-white/10 uppercase">
                                        {{ $cliente->rfc ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-blue-100 text-sm text-center font-medium text-white">
                                    <div class="flex flex-col items-center">
                                        <span>{{ $cliente->celular ?? 'S/T' }}</span>
                                        <span class="text-white text-sm text-blue-100 lowercase">{{ $cliente->email ?? 'Sin email' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center items-center gap-3">
                                        @if($cliente->trashed())
                                            <button onclick="activarCliente({{ $cliente->id }}, '{{ $cliente->nombre }}')" class="p-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg transition-all" title="Activar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                            <form id="restore-form-{{ $cliente->id }}" action="{{ route('clientes.restore', $cliente->id) }}" method="POST" class="hidden">
                                                @csrf
                                            </form>
                                        @else
                                            <a href="{{ route('clientes.show', $cliente) }}" class="p-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-all" title="Ver detalle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('clientes.edit', $cliente) }}" class="p-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-lg transition-all" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <button onclick="desactivarCliente({{ $cliente->id }}, '{{ $cliente->nombre }}')" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg transition-all" title="Desactivar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            </button>
                                            <form id="delete-form-{{ $cliente->id }}" action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-blue-200">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-xl font-medium">No se encontraron clientes</p>
                                        <p class="text-sm opacity-60">Intenta con otros criterios de búsqueda o registra uno nuevo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($clientes->hasPages())
                <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                    {{ $clientes->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>

<script>
    function activarCliente(id, nombre) {
        Swal.fire({
            title: '¿Reactivar cliente?',
            text: `El cliente "${nombre}" volverá a estar activo en el sistema.`,
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
                document.getElementById(`restore-form-${id}`).submit();
            }
        });
    }

    function desactivarCliente(id, nombre) {
        Swal.fire({
            title: '¿Desactivar cliente?',
            text: `El cliente "${nombre}" será marcado como inactivo. Podrás reactivarlo después si es necesario.`,
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
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
@endsection
