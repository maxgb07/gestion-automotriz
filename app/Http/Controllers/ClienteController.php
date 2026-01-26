<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Rules\ValidadorRfc;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'activos');
        $query = Cliente::query();

        if ($status === 'inactivos') {
            $query->onlyTrashed();
        }

        if ($request->has('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('rfc', 'like', "%{$buscar}%")
                  ->orWhere('celular', 'like', "%{$buscar}%");
            });
        }

        $clientes = $query->latest()->paginate(10)->withQueryString();

        return view('clientes.index', compact('clientes', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.crear');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:10',
            'rfc' => ['nullable', 'string', new ValidadorRfc],
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $data = $request->all();
        // Convertir a mayúsculas excepto email
        foreach ($data as $key => $value) {
            if ($key !== 'email' && is_string($value)) {
                $data[$key] = mb_strtoupper($value, 'UTF-8');
            }
        }

        Cliente::create($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Cliente $cliente)
    {
        $vStatus = $request->get('v_status', 'activos');
        
        $vehiculosQuery = $cliente->vehiculos();
        if ($vStatus === 'inactivos') {
            $vehiculosQuery->onlyTrashed();
        }
        
        $vehiculos = $vehiculosQuery->latest()->get();
        
        return view('clientes.ver', compact('cliente', 'vehiculos', 'vStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.editar', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:10',
            'rfc' => ['nullable', 'string', new ValidadorRfc],
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'activo' => 'nullable|boolean',
        ]);

        $data = $request->all();
        // Convertir a mayúsculas excepto email
        foreach ($data as $key => $value) {
            if ($key !== 'email' && is_string($value)) {
                $data[$key] = mb_strtoupper($value, 'UTF-8');
            }
        }

        $cliente->update($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Información del cliente actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente desactivado exitosamente.');
    }

    public function restore($id)
    {
        $cliente = Cliente::withTrashed()->findOrFail($id);
        $cliente->restore();

        return redirect()->route('clientes.index')->with('success', 'Cliente reactivado exitosamente.');
    }

    public function buscar(Request $request)
    {
        $buscar = $request->get('q');
        $clientes = Cliente::where('nombre', 'like', "%{$buscar}%")
            ->orWhere('rfc', 'like', "%{$buscar}%")
            ->orWhere('celular', 'like', "%{$buscar}%")
            ->get(['id', 'nombre', 'celular', 'rfc']);

        return response()->json($clientes);
    }
}
