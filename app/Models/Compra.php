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
        'factura',
        'fecha_compra',
        'total'
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
