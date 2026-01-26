@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
    <div class="max-w-4xl mx-auto py-4">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('clientes.show', $cliente) }}" class="inline-flex items-center text-blue-200 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Cancelar edición
            </a>
            <h1 class="text-3xl font-bold text-white uppercase tracking-tight">Editar Cliente</h1>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl">
            <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                @csrf
                @method('PUT')
                
                @include('clientes._formulario')

                <div class="flex items-center justify-center gap-6 py-12 mt-10 border-t border-white/5">
                    <button type="submit" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-black leading-5 rounded-base text-sm px-10 py-4 focus:outline-none inline-flex items-center min-w-[220px] justify-center uppercase tracking-widest">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('clientes.show', $cliente) }}" class="inline-flex items-center justify-center px-10 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg border border-white/20 transition-all min-w-[200px] text-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
