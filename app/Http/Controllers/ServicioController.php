<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $query = Servicio::query();

        if ($request->has('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('sku', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $servicios = $query->latest()->paginate(15)->withQueryString();

        return view('servicios.index', compact('servicios'));
    }

    public function create()
    {
        return view('servicios.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'precio' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('servicios', 'public');
            $data['imagen'] = $path;
        }

        $servicio = Servicio::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Servicio registrado exitosamente',
                'data' => $servicio
            ]);
        }

        return redirect()->route('servicios.index')->with('success', 'Servicio registrado exitosamente');
    }

    public function edit(Servicio $servicio)
    {
        return view('servicios.editar', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'precio' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen')) {
            if ($servicio->imagen) {
                Storage::disk('public')->delete($servicio->imagen);
            }
            $path = $request->file('imagen')->store('servicios', 'public');
            $data['imagen'] = $path;
        }

        $servicio->update($data);

        return redirect()->route('servicios.index')->with('success', 'Servicio actualizado exitosamente');
    }

    public function destroy(Servicio $servicio)
    {
        if ($servicio->imagen) {
            Storage::disk('public')->delete($servicio->imagen);
        }
        $servicio->delete();
        return redirect()->route('servicios.index')->with('success', 'Servicio eliminado exitosamente');
    }
}
