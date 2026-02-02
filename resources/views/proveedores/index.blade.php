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
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Proveedor</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Contacto 1</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Contacto 2</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Marcas / Productos</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($proveedores as $proveedor)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <div class="text-white font-bold uppercase group-hover:text-blue-300 transition-colors">{{ $proveedor->nombre }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-blue-100 uppercase font-bold">{{ $proveedor->contacto ?? 'N/A' }}</span>
                                    <span class="text-md ">{{ $proveedor->telefono ?? 'S/T' }}</span>
                                    <span class="text-md lowercase">{{ $proveedor->email ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-blue-100 uppercase font-bold">{{ $proveedor->contacto_secundario ?? 'N/A' }}</span>
                                    <span class="text-md ">{{ $proveedor->telefono_secundario ?? 'S/T' }}</span>
                                    <span class="text-md lowercase">{{ $proveedor->email_secundario ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-blue-100 text-md uppercase line-clamp-1 mx-auto text-center max-w-xs">{{ $proveedor->marcas_productos ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick='verProveedor(@json($proveedor))' class="p-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-xl transition-all" title="VER DETALLES">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
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
    function verProveedor(proveedor) {
        Swal.fire({
            html: `
                <div class="text-left">
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-black text-white uppercase mb-2 tracking-tighter">${proveedor.nombre}</h3>
                        <div class="h-px w-20 bg-gradient-to-r from-transparent via-blue-500/50 to-transparent mx-auto mb-4"></div>
                        <p class="text-blue-100/80 text-sm uppercase px-6 leading-relaxed">DETALLE COMPLETO DEL PROVEEDOR</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 bg-white/5 p-6 rounded-[2rem] border border-white/10 shadow-inner">
                        <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors">
                                <p class="text-mdfont-black text-blue-300/40 uppercase tracking-widest">Contacto Primario</p>
                                <p class="text-white font-bold uppercase text-sm">${proveedor.contacto || 'N/A'}</p>
                                <p class="text-md ">${proveedor.telefono || 'S/T'}</p>
                                <p class="text-md lowercase">${proveedor.email || ''}</p>
                            </div>
                            <div class="space-y-1 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors">
                                <p class="text-mdfont-black text-blue-300/40 uppercase tracking-widest">Contacto Secundario</p>
                                <p class="text-white font-bold uppercase text-sm">${proveedor.contacto_secundario || 'N/A'}</p>
                                <p class="text-md ">${proveedor.telefono_secundario || 'S/T'}</p>
                                <p class="text-md lowercase">${proveedor.email_secundario || ''}</p>
                            </div>
                        </div>
                        <div class="col-span-2 space-y-1 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors">
                            <p class="text-mdfont-black text-blue-300/40 uppercase tracking-widest">Marcas / Productos</p>
                            <p class="text-white font-bold uppercase text-md leading-relaxed">${proveedor.marcas_productos || 'N/A'}</p>
                        </div>
                        <div class="col-span-2 space-y-1 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors">
                            <p class="text-mdfont-black text-blue-300/40 uppercase tracking-widest">Dirección</p>
                            <p class="text-white font-bold uppercase text-md leading-relaxed">${proveedor.direccion || 'N/A'}</p>
                        </div>
                        <div class="col-span-2 space-y-1 p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-mdfont-black text-blue-300/40 uppercase tracking-widest mb-2">Observaciones</p>
                            <p class="text-white font-bold uppercase text-md leading-relaxed">${proveedor.observaciones || 'SIN COMENTARIOS ADICIONALES'}</p>
                        </div>
                    </div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'CERRAR',
            confirmButtonColor: '#3b82f6',
            background: 'rgba(15, 23, 42, 0.95)',
            color: '#fff',
            width: '600px',
            customClass: {
                popup: 'backdrop-blur-xl border border-white/20 rounded-[3rem] p-8',
                confirmButton: 'px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-md'
            }
        });
    }

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
