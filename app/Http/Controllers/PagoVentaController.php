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
            'monto' => 'required|numeric|min:0|max:' . $venta->saldo_pendiente,
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
            'referencia' => 'nullable|string|max:100',
            'requiere_factura' => 'nullable|string|in:SI,NO',
        ]);
        
        try {
            DB::beginTransaction();

            $metodo = $request->metodo_pago;
            $monto = floatval($request->monto);

            if ($metodo === 'CRÉDITO 15 DÍAS') {
                $monto = 0;
            }

            if ($monto > 0) {
                VentaPago::create([
                    'venta_id' => $venta->id,
                    'monto' => $monto,
                    'fecha_pago' => $request->fecha_pago,
                    'metodo_pago' => $metodo,
                    'referencia' => mb_strtoupper($request->referencia, 'UTF-8'),
                ]);

                $venta->saldo_pendiente -= $monto;
            }

            if ($venta->saldo_pendiente <= 0 && $metodo !== 'CRÉDITO 15 DÍAS') {
                $venta->estado = 'PAGADA';
            } else {
                $venta->estado = 'PENDIENTE';
            }
            
            if ($request->has('requiere_factura')) {
                $venta->requiere_factura = $request->requiere_factura;
            }

            $venta->save();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pago registrado correctamente.',
                    'pdf_url' => route('ventas.pdf', $venta)
                ]);
            }

            return back()->with('success', 'Pago registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }
}
