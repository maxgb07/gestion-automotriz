<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaCreditoProveedor extends Model
{
    protected $fillable = [
        'proveedor_id',
        'folio',
        'monto_original',
        'saldo_disponible',
        'fecha',
        'estado',
        'observaciones'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoCompra::class, 'nota_credito_id');
    }
}
