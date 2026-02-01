@if(isset($prevMonthAlert))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<span class="text-xl font-bold uppercase tracking-tight text-white">¡Órdenes Pendientes del Mes Anterior!</span>',
                html: `
                    <div class="text-left py-2">
                        <p class="text-white text-md mb-4 leading-relaxed">
                            Tienes <span class="text-white font-black text-lg">{{ $prevMonthAlert['count'] }}</span> órdenes en <span class="text-white font-bold tracking-widest uppercase">Reparación</span> registradas en meses pasados.
                        </p>
                        <div class="bg-amber-500/10 p-4 rounded-xl border border-amber-500/20">
                            <p class="text-[14px] font-black text-amber-300 uppercase tracking-[0.2em] mb-2">Detalle de Folios</p>
                            <p class="text-white font-mono text-md break-words">
                                ({{ $prevMonthAlert['folios'] }})
                            </p>
                        </div>
                        <p class="text-white text-[14px] mt-4 uppercase font-black tracking-widest text-center">
                            RECOMENDACIÓN: Prioriza el cierre de estas unidades para limpiar tu inventario de taller.
                        </p>
                    </div>
                `,
                icon: 'warning',
                iconColor: '#f59e0b',
                background: 'rgba(15, 23, 42, 0.95)',
                backdrop: 'rgba(0,0,0,0.4) blur(4px)',
                confirmButtonText: 'ENTENDIDO',
                confirmButtonColor: '#f59e0b',
                customClass: {
                    popup: 'rounded-[2rem] border border-white/10 shadow-2xl backdrop-blur-2xl',
                    confirmButton: 'rounded-xl font-black uppercase tracking-widest px-10 py-3 shadow-lg shadow-amber-500/20'
                }
            });
        });
    </script>
@endif
