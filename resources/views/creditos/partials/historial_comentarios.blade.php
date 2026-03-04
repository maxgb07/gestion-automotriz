@forelse($comentarios as $comentario)
    <div class="bg-white/5 border border-white/5 rounded-2xl p-4 transition-all duration-300 hover:border-white/10 group">
        <div class="flex justify-between items-start mb-2">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-blue-500/10 flex items-center justify-center text-[10px] font-black text-blue-300">
                    {{ substr($comentario->user->name, 0, 1) }}
                </div>
                <span class="text-[10px] font-black text-white/60 uppercase tracking-widest">{{ $comentario->user->name }}</span>
            </div>
            <span class="text-[9px] font-bold text-blue-300/30 uppercase tracking-widest">{{ $comentario->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <p class="text-sm text-blue-100 font-medium leading-relaxed">{{ $comentario->comentario }}</p>
    </div>
@empty
    <div class="text-center py-12 opacity-20 flex flex-col items-center gap-3">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <span class="text-[10px] font-black uppercase tracking-[0.3em]">No hay comentarios registrados</span>
    </div>
@endforelse
