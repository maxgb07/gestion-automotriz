<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class VentaDetalle extends Model
{
    use Auditable;
    protected $fillable = [
        'venta_id',
        'producto_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
}
