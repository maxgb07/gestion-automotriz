<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($request->has('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('contacto', 'like', "%{$buscar}%")
                  ->orWhere('contacto_secundario', 'like', "%{$buscar}%")
                  ->orWhere('telefono', 'like', "%{$buscar}%")
                  ->orWhere('telefono_secundario', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%")
                  ->orWhere('email_secundario', 'like', "%{$buscar}%")
                  ->orWhere('marcas_productos', 'like', "%{$buscar}%");
            });
        }

        $proveedores = $query->latest()->paginate(15)->withQueryString();

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'contacto_secundario' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'telefono_secundario' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'email_secundario' => 'nullable|email|max:255',
            'marcas_productos' => 'nullable|string',
            'direccion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        Proveedor::create($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.editar', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'contacto_secundario' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'telefono_secundario' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'email_secundario' => 'nullable|email|max:255',
            'marcas_productos' => 'nullable|string',
            'direccion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $proveedor->update($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado exitosamente');
    }
}
