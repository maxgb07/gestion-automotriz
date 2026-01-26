<div class="space-y-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Nombre -->
        <div>
            <label for="nombre" class="block text-sm font-medium text-blue-100 mb-2">Nombre / Razón Social *</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $cliente->nombre ?? '') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
        </div>

        <!-- RFC -->
        <div>
            <label for="rfc" class="block text-sm font-medium text-blue-100 mb-2">RFC (México)</label>
            <input type="text" name="rfc" id="rfc" value="{{ old('rfc', $cliente->rfc ?? '') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm font-mono" placeholder="ABC123456XYZ">
            <p class="mt-1 text-xs text-blue-200/50">13 caracteres para personas físicas, 12 para morales.</p>
        </div>

        <!-- Dirección -->
        <div class="md:col-span-2">
            <label for="direccion" class="block text-sm font-medium text-blue-100 mb-2">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="{{ old('direccion', $cliente->direccion ?? '') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
        </div>

        <!-- CP -->
        <div>
            <label for="codigo_postal" class="block text-sm font-medium text-blue-100 mb-2">Código Postal</label>
            <input type="text" name="codigo_postal" id="codigo_postal" value="{{ old('codigo_postal', $cliente->codigo_postal ?? '') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-blue-100 mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $cliente->email ?? '') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
        </div>

        <!-- Teléfono Fijo -->
        <div>
            <label for="telefono" class="block text-sm font-medium text-blue-100 mb-2">Teléfono Fijo</label>
            <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $cliente->telefono ?? '') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
        </div>

        <!-- Celular -->
        <div>
            <label for="celular" class="block text-sm font-medium text-blue-100 mb-2">Celular *</label>
            <input type="text" name="celular" id="celular" value="{{ old('celular', $cliente->celular ?? '') }}" class="block w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-200/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all backdrop-blur-sm">
        </div>
    </div>
</div>
