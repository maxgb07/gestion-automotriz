<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class DetalleCompra extends Model
{
    use Auditable;
    use HasFactory;

    protected $table = 'detalles_compra';

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'precio_compra',
        'precio_venta_sugerido'
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
