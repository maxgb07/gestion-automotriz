@extends('layouts.app')

@section('title', 'Inventario Físico - ' . $marca)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('productos.index') }}" class="p-2 bg-white/5 hover:bg-white/10 rounded-lg text-blue-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-white uppercase tracking-tighter">Inventario Físico</h1>
            </div>
            <p class="text-blue-200 uppercase font-medium">Marca: <span class="text-white font-bold ml-1">{{ $marca }}</span></p>
        </div>
        
        <div class="flex items-center gap-4 bg-yellow-500/10 border border-yellow-500/20 px-4 py-3 rounded-xl">
            <svg class="w-6 h-6 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-xs text-yellow-100 uppercase leading-snug">
                <b>Nota Importante:</b> Si dejas el campo "Físico" vacío, el stock NO se modificará. Solo se actualizará si ingresas un número (incluyendo 0).
            </p>
        </div>
    </div>

    <!-- Inventory Form -->
    <form action="{{ route('productos.inventario.update') }}" method="POST" id="inventory-form" class="bg-white/10 backdrop-blur-xl rounded-3xl overflow-hidden border border-white/20 shadow-2xl">
        @csrf
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/10 sticky top-0 z-10 backdrop-blur-md">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-blue-300 uppercase tracking-widest text-left w-32">SKU / Clave</th>
                        <th class="px-6 py-4 text-xs font-black text-blue-300 uppercase tracking-widest text-left">Producto</th>
                        <th class="px-6 py-4 text-xs font-black text-blue-300 uppercase tracking-widest text-center w-32">Sistema</th>
                        <th class="px-6 py-4 text-xs font-black text-blue-300 uppercase tracking-widest text-center w-40 bg-indigo-600/20">Físico</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($productos as $producto)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 align-middle">
                                <span class="text-white font-bold uppercase transition-colors">{{ $producto->nombre }}</span> <!-- Clave/SKU Real -->
                            </td>
                            <td class="px-6 py-4 align-middle">
                                <div class="flex flex-col gap-1">
                                    <span class="text-white font-bold uppercase group-hover:text-blue-300 transition-colors">{{ $producto->descripcion }}</span>
                                    <span class="text-white font-bold uppercase">{{ $producto->aplicacion }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                <span class="text-sm font-bold text-white/50">{{ $producto->stock }}</span>
                            </td>
                            <td class="px-6 py-4 text-center align-middle bg-indigo-600/5 group-hover:bg-indigo-600/10 transition-colors">
                                <input type="number" 
                                       name="stocks[{{ $producto->id }}]" 
                                       min="0" 
                                       step="1" 
                                       placeholder="-" 
                                       class="inventory-input w-24 px-3 py-2 bg-white text-black font-black text-center rounded-xl border border-white/20 focus:outline-none focus:ring-4 focus:ring-green-500/50 placeholder-slate-400 text-lg shadow-inner"
                                       onwheel="this.blur()">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <p class="text-xl font-medium text-blue-200 uppercase">No hay productos registrados para esta marca.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productos->hasPages())
            <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                {{ $productos->links('vendor.pagination.custom') }}
            </div>
        @endif

        <div class="px-6 py-6 bg-white/5 border-t border-white/10 flex justify-end gap-4 sticky bottom-0 backdrop-blur-xl z-20">
            <a href="{{ route('productos.index') }}" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all uppercase tracking-wider text-sm flex items-center gap-2 shadow-lg shadow-red-900/20" style="background-color: #dc2626;">
                Cancelar
            </a>
            <button type="submit" id="btn-save" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-black rounded-xl shadow-lg shadow-green-900/40 transition-all uppercase tracking-tighter text-sm flex items-center gap-2 transform hover:scale-105 active:scale-95" style="background-color: #16a34a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Guardar Inventario
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let hasChanges = false;
    let isSubmitting = false;

    // Detect changes in inputs
    document.querySelectorAll('.inventory-input').forEach(input => {
        input.addEventListener('input', () => {
            hasChanges = true;
        });
    });

    // Handle form submit to disable the warning
    document.getElementById('inventory-form').addEventListener('submit', () => {
        isSubmitting = true;
    });

    // Validacion para enlaces internos con SweetAlert (simulando ventas.crear)
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && !isSubmitting && hasChanges && !link.hasAttribute('download') && link.target !== '_blank') {
            const href = link.href;
            if (href && href.startsWith(window.location.origin) && !href.includes('#')) {
                e.preventDefault();
                Swal.fire({
                    title: '¿CAMBIOS SIN GUARDAR?',
                    text: "SI SALES AHORA, PERDERÁS LOS DATOS CAPTURADOS EN ESTA PÁGINA.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'SÍ, SALIR',
                    cancelButtonText: 'QUEDARME',
                    background: '#1e293b',
                    color: '#fff',
                    customClass: {
                        popup: 'rounded-3xl border border-white/20 shadow-2xl',
                        title: 'text-xl font-black uppercase tracking-tighter'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        isSubmitting = true; 
                        window.location.href = href;
                    }
                });
            }
        }
    });

    // Warn user before leaving if there are changes (Native Browser Fallback)
    window.addEventListener('beforeunload', (e) => {
        if (!isSubmitting && hasChanges) {
            e.preventDefault();
            e.returnValue = ''; // Standard for Chrome
        }
    });
</script>
@endpush
@endsection
