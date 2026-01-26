<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaPago extends Model
{
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
