<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class OrdenServicioDetalle extends Model
{
    use Auditable;
    use HasFactory;

    protected $table = 'orden_servicio_detalles';

    protected $fillable = [
        'orden_servicio_id',
        'producto_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal',
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_servicio_id');
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
