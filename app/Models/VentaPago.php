<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class VentaPago extends Model
{
    use Auditable;
    protected $fillable = [
        'venta_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'referencia'
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
