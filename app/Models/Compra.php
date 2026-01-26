<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Proveedor;
use App\Models\DetalleCompra;

class Compra extends Model
{
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
