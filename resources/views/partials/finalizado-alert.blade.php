@if(isset($finishedOrdersAlert))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<span class="text-xl font-bold uppercase tracking-tight text-white">¡Órdenes Finalizadas Pendientes!</span>',
                html: `
                    <div class="text-left py-2">
                        <p class="text-white text-md mb-4 leading-relaxed">
                            Tienes <span class="text-white font-black text-lg">{{ $finishedOrdersAlert['count'] }}</span> órdenes en estado <span class="text-white font-bold tracking-widest">FINALIZADO</span> de meses anteriores que aún no han sido gestionadas.
                        </p>
                        <div class="bg-rose-500/10 p-4 rounded-xl border border-rose-500/20">
                            <p class="text-[14px] font-black text-rose-300 uppercase tracking-[0.2em] mb-2">Folios Pendientes</p>
                            <p class="text-white font-mono text-md break-words">
                                ({{ $finishedOrdersAlert['folios'] }})
                            </p>
                        </div>
                        <p class="text-white text-[14px] mt-4 uppercase font-black tracking-widest text-center">
                            RECOMENDACIÓN: Define si estas órdenes ya fueron cobradas o deben pasar a Pendiente de Pago.
                        </p>
                    </div>
                `,
                icon: 'warning',
                iconColor: '#f43f5e',
                background: 'rgba(15, 23, 42, 0.95)',
                backdrop: 'rgba(0,0,0,0.4) blur(4px)',
                showCancelButton: true,
                confirmButtonText: 'VER ÓRDENES',
                cancelButtonText: 'AHORA NO',
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#475569',
                customClass: {
                    popup: 'rounded-[2rem] border border-white/10 shadow-2xl backdrop-blur-2xl',
                    confirmButton: 'rounded-xl font-black uppercase tracking-widest px-10 py-3 shadow-lg shadow-rose-500/20',
                    cancelButton: 'rounded-xl font-black uppercase tracking-widest px-10 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ $finishedOrdersAlert['url'] }}';
                }
            });
        });
    </script>
@endif
