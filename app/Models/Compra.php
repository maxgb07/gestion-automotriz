<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Proveedor;
use App\Models\DetalleCompra;

use App\Traits\Auditable;

class Compra extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'folio',
        'factura',
        'fecha_compra',
        'subtotal',
        'porcentaje_descuento',
        'monto_descuento',
        'porcentaje_descuento_extra',
        'monto_descuento_extra',
        'monto_descuento_interno',
        'monto_maniobra',
        'aplica_descuento_maniobra',
        'monto_seguro',
        'aplica_descuento_seguro',
        'iva',
        'total',
        'fecha_vencimiento',
        'saldo_pendiente',
        'estado_pago',
        'estado_complemento'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }
}
