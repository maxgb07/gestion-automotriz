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
        'observaciones',
        'requiere_factura',
        'folio_factura',
        'fecha_factura',
        'uuid_factura',
        'motivo_cancelacion',
        'cancelado_at'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_vencimiento' => 'date',
        'cancelado_at' => 'datetime',
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
