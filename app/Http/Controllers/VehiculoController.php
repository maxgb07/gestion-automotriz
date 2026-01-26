<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Cliente $cliente)
    {
        return view('vehiculos.crear', compact('cliente'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Cliente $cliente)
    {
        $request->validate([
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'placas' => 'nullable|string|max:20',
            'numero_serie' => 'nullable|string|max:50',
            'kilometraje' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string',
        ]);

        $data = $request->all();
        // Convertir a mayúsculas
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = mb_strtoupper($value, 'UTF-8');
            }
        }

        $cliente->vehiculos()->create($data);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Vehículo registrado correctamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehiculo $vehiculo)
    {
        return view('vehiculos.editar', compact('vehiculo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $request->validate([
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'placas' => 'nullable|string|max:20',
            'numero_serie' => 'nullable|string|max:50',
            'kilometraje' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string',
        ]);

        $data = $request->all();
        // Convertir a mayúsculas
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = mb_strtoupper($value, 'UTF-8');
            }
        }

        $vehiculo->update($data);

        return redirect()->route('clientes.show', $vehiculo->cliente_id)
            ->with('success', 'Información del vehículo actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehiculo $vehiculo)
    {
        $clienteId = $vehiculo->cliente_id;
        $vehiculo->delete();

        return redirect()->route('clientes.show', $clienteId)->with('success', 'Vehículo desactivado exitosamente.');
    }

    public function restore($id)
    {
        $vehiculo = Vehiculo::withTrashed()->findOrFail($id);
        $vehiculo->restore();

        return redirect()->route('clientes.show', $vehiculo->cliente_id)->with('success', 'Vehículo reactivado exitosamente.');
    }

    public function buscar(Request $request)
    {
        $buscar = $request->get('q');
        $clienteId = $request->get('cliente_id');
        
        $query = Vehiculo::query();
        
        if ($clienteId) {
            $query->where('cliente_id', $clienteId);
        }
        
        $vehiculos = $query->where(function($q) use ($buscar) {
                $q->where('marca', 'like', "%{$buscar}%")
                  ->orWhere('modelo', 'like', "%{$buscar}%")
                  ->orWhere('placas', 'like', "%{$buscar}%");
            })
            ->get(['id', 'marca', 'modelo', 'anio', 'placas']);

        return response()->json($vehiculos);
    }
}
