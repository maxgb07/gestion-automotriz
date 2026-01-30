@if(isset($eomAlert))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<span class="text-xl font-bold uppercase tracking-tight text-white">¡Cierre de Mes Próximo!</span>',
                html: `
                    <div class="text-left py-2">
                        <p class="text-white text-md mb-4 leading-relaxed">
                            Tienes <span class="text-white font-black text-lg">{{ $eomAlert['count'] }}</span> órdenes en estado de <span class="text-white font-bold tracking-widest">REPARACIÓN.</span>
                        </p>
                        <div class="bg-slate-800/80 p-4 rounded-xl border border-white/10">
                            <p class="text-[14px] font-black text-white uppercase tracking-[0.2em] mb-2">Folios Pendientes</p>
                            <p class="text-white font-mono text-md break-words">
                                ({{ $eomAlert['folios'] }})
                            </p>
                        </div>
                        <p class="text-white text-[14px] mt-4 uppercase font-black tracking-widest text-center">
                            AVISO: Estas órdenes deben cerrarse antes de finalizar el mes.
                        </p>
                    </div>
                `,
                icon: 'info',
                iconColor: '#3b82f6',
                background: 'rgba(15, 23, 42, 0.95)',
                backdrop: 'rgba(0,0,0,0.4) blur(4px)',
                confirmButtonText: 'ENTENDIDO',
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-[2rem] border border-white/10 shadow-2xl backdrop-blur-2xl',
                    confirmButton: 'rounded-xl font-black uppercase tracking-widest px-10 py-3 shadow-lg shadow-blue-500/20'
                }
            });
        });
    </script>
@endif
