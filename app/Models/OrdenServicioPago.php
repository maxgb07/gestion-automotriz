<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenServicioPago extends Model
{
    use HasFactory;

    protected $table = 'orden_servicio_pagos';

    protected $fillable = [
        'orden_servicio_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'referencia',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_servicio_id');
    }
}
