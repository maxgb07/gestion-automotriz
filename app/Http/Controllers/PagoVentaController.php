<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Venta;
use App\Models\VentaPago;
use Illuminate\Support\Facades\DB;

class PagoVentaController extends Controller
{
    public function store(Request $request, Venta $venta)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $venta->saldo_pendiente,
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:EFECTIVO,TARJETA,TRANSFERENCIA',
            'referencia' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            VentaPago::create([
                'venta_id' => $venta->id,
                'monto' => $request->monto,
                'fecha_pago' => $request->fecha_pago,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
            ]);

            $venta->saldo_pendiente -= $request->monto;
            if ($venta->saldo_pendiente <= 0) {
                $venta->estado = 'PAGADA';
            }
            $venta->save();

            DB::commit();
            return back()->with('success', 'Pago registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }
}
