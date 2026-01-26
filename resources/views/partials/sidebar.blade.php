<aside class="fixed left-4 top-24 bottom-4 w-64 bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl z-40 overflow-y-auto hidden lg:block shadow-2xl transition-all duration-300">
    <div class="p-6 space-y-8">
        <!-- Main Navigation -->
        <div>
            <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-[0.2em] mb-4 px-4">Menu Principal</p>
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500/80 to-purple-600/80 text-white shadow-lg shadow-blue-500/20 font-bold' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('clientes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('clientes.*') ? 'bg-gradient-to-r from-blue-500/80 to-purple-600/80 text-white shadow-lg shadow-blue-500/20 font-bold' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Clientes</span>
                </a>

                <a href="{{ route('ventas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('ventas.*') ? 'bg-gradient-to-r from-blue-500/80 to-purple-600/80 text-white shadow-lg shadow-blue-500/20 font-bold' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span>Ventas</span>
                </a>

                <a href="{{ route('ordenes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('ordenes.*') ? 'bg-gradient-to-r from-blue-500/80 to-purple-600/80 text-white shadow-lg shadow-blue-500/20 font-bold' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Órdenes de Servicio</span>
                </a>

                <a href="{{ route('productos.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('productos.*') ? 'bg-gradient-to-r from-blue-500/80 to-purple-600/80 text-white shadow-lg shadow-blue-500/20 font-bold' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Inventario</span>
                </a>

                <a href="{{ route('servicios.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('servicios.*') ? 'bg-gradient-to-r from-indigo-500/80 to-blue-600/80 text-white shadow-lg shadow-indigo-500/20 font-bold' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Servicios</span>
                </a>

                <a href="{{ route('proveedores.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('proveedores.*') ? 'bg-gradient-to-r from-blue-500/80 to-purple-600/80 text-white shadow-lg shadow-blue-500/20 font-bold' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Proveedores</span>
                </a>

                <a href="{{ route('compras.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('compras.*') ? 'bg-gradient-to-r from-blue-500/80 to-purple-600/80 text-white shadow-lg shadow-blue-500/20 font-bold' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Compras</span>
                </a>

                <a href="{{ route('reportes.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('reportes.*') ? 'bg-gradient-to-r from-blue-500/80 to-purple-600/80 text-white shadow-lg shadow-blue-500/20 font-bold' : 'text-blue-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m32 4v-2a4 4 0 00-4-4h-5a4 4 0 00-4 4v2m12-9a4 4 0 11-8 0 4 4 0 018 0zM10 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Reportes</span>
                </a>
            </nav>
        </div>

    </div>
</aside>

<!-- Mobile Content Spacer (Mobile Navbar can be added here if needed) -->
<div class="lg:hidden h-16"></div>
