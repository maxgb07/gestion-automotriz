<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCompra extends Model
{
    protected $fillable = [
        'compra_id',
        'monto',
        'fecha_pago',
        'forma_pago',
        'referencia',
        'tipo',
        'nota_credito_id',
        'observaciones',
        'grupo_pago_id',
        'estado_documentos',
        'complemento_folio',
        'complemento_fecha',
        'complemento_monto',
        'ncs_informativas'
    ];

    protected $casts = [
        'ncs_informativas' => 'array',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function notaCredito()
    {
        return $this->belongsTo(NotaCreditoProveedor::class, 'nota_credito_id');
    }
}
