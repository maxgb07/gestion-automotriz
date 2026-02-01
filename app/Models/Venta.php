<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Venta extends Model
{
    use Auditable;
    protected $fillable = [
        'cliente_id',
        'folio',
        'fecha',
        'total',
        'descuento',
        'saldo_pendiente',
        'metodo_pago',
        'estado',
        'fecha_vencimiento',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_vencimiento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(VentaPago::class);
    }
}
