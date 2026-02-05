<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlerta extends Model
{
    use HasFactory;

    protected $table = 'stock_alertas';

    protected $fillable = [
        'producto_id',
        'user_id',
        'stock_previo',
        'cantidad_solicitada',
        'referencia_tipo',
        'referencia_id',
        'fecha',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
