@extends('layouts.app')

@section('title', 'Catálogo de Servicios')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white uppercase">Catálogo de Servicios</h1>
            <p class="text-blue-200">Gestión de servicios y mantenimiento de taller</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('servicios.create') }}" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nuevo Servicio
            </a>
        </div>
    </div>

    <!-- Search Box -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 mb-8 shadow-xl">
        <form action="{{ route('servicios.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="BUSCAR POR NOMBRE O DESCRIPCIÓN..." class="block w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm uppercase">
            </div>
            <button type="submit" class="w-fit px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all">
                BUSCAR
            </button>
            @if(request('buscar'))
                <a href="{{ route('servicios.index') }}" class="w-fit px-5 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-200 font-semibold rounded-xl border border-red-500/30 transition-all text-center">
                    LIMPIAR
                </a>
            @endif
        </form>
    </div>

    <!-- Services Table -->
    <div class="bg-white/10 backdrop-blur-xl rounded-3xl overflow-hidden border border-white/20 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Nombre / Servicio</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Descripción</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Precio</th>
                        <th class="px-6 py-4 text-sm font-semibold text-blue-200 uppercase tracking-wider text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($servicios as $servicio)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-4">
                                    <!-- <div class="relative w-12 h-12 rounded-xl overflow-hidden bg-slate-800 border border-white/10 flex-shrink-0 group-hover:scale-105 transition-transform">
                                        @if($servicio->imagen)
                                            <img src="{{ Storage::url($servicio->imagen) }}" alt="{{ $servicio->nombre }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-blue-400 font-bold uppercase">
                                                {{ substr($servicio->nombre, 0, 1) }}
                                            </div>
                                        @endif
                                    </div> -->
                                    <div class="flex flex-col items-center">
                                        <span class="text-white font-bold uppercase group-hover:text-blue-300 transition-colors">{{ $servicio->nombre }}</span>
                                        <!-- <span class="text-[10px] text-blue-200/40 uppercase tracking-widest font-mono">SKU: {{ $servicio->sku }}</span> -->
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-blue-100 text-sm font-bold uppercase line-clamp-2 text-center">{{ $servicio->descripcion ?? 'SIN DESCRIPCIÓN' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-white font-bold text-sm">
                                    ${{ number_format($servicio->precio, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <button onclick='verServicio(@json($servicio))' class="p-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-xl transition-all" title="VER DETALLES">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('servicios.edit', $servicio) }}" class="p-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-xl transition-all" title="EDITAR">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="eliminarServicio({{ $servicio->id }}, '{{ $servicio->nombre }}')" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-xl transition-all" title="ELIMINAR">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $servicio->id }}" action="{{ route('servicios.destroy', $servicio) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-indigo-300/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xl font-medium text-blue-200 uppercase">No hay servicios registrados</p>
                                    <p class="text-sm text-blue-200/50 mt-2 uppercase text-center max-w-xs mx-auto">Comienza agregando los servicios que ofrece tu taller al catálogo.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($servicios->hasPages())
            <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                {{ $servicios->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

<script>
    function verServicio(servicio) {
        const imagenHtml = servicio.imagen 
            ? `<div class="flex justify-center mb-6">
                <img src="/storage/${servicio.imagen}" class="w-48 h-48 object-cover rounded-3xl border-2 border-white/20 shadow-2xl shadow-indigo-500/20">
               </div>`
            : `<div class="flex justify-center mb-6">
                <div class="w-48 h-48 bg-white/5 rounded-3xl border-2 border-dashed border-white/10 flex items-center justify-center text-indigo-200/20">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                </div>
               </div>`;

        Swal.fire({
            html: `
                <div class="text-left">
                    ${imagenHtml}
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-black text-white uppercase mb-2 tracking-tighter">${servicio.nombre}</h3>
                        <div class="h-px w-20 bg-gradient-to-r from-transparent via-indigo-500/50 to-transparent mx-auto mb-4"></div>
                        <p class="text-indigo-100/80 text-sm uppercase px-6 leading-relaxed">${servicio.descripcion || 'SIN DESCRIPCIÓN'}</p>
                    </div>

                    <div class="bg-white/5 p-6 rounded-[2rem] border border-white/10 shadow-inner">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="p-5 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-center group">
                                <p class="text-[10px] font-black text-indigo-400/40 uppercase tracking-widest mb-1">Precio del Servicio</p>
                                <p class="text-green-400 font-black text-3xl group-hover:scale-110 transition-transform origin-center">$${new Intl.NumberFormat().format(servicio.precio)}</p>
                            </div>
                            <div class="p-5 rounded-2xl bg-white/5 border border-white/10">
                                <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-widest mb-2">Observaciones Internas</p>
                                <p class="text-white/70 text-xs uppercase italic leading-relaxed">${servicio.observaciones || 'SIN COMENTARIOS ADICIONALES'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'CERRAR VENTANA',
            confirmButtonColor: '#6366f1',
            background: 'rgba(15, 23, 42, 0.95)',
            color: '#fff',
            width: '550px',
            customClass: {
                popup: 'backdrop-blur-xl border border-white/20 rounded-[3rem] p-8',
                confirmButton: 'px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-xs'
            }
        });
    }

    function eliminarServicio(id, nombre) {
        Swal.fire({
            title: '¿ELIMINAR SERVICIO?',
            text: `ESTÁS A PUNTO DE ELIMINAR EL SERVICIO "${nombre}". ESTA ACCIÓN NO SE PUEDE DESHACER.`,
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
