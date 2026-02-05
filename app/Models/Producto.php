<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\Auditable;

class Producto extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'sku',
        'marca',
        'codigo_barras',
        'aplicacion',
        'imagen',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'observaciones'
    ];

    protected function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = mb_strtoupper($value);
    }

    protected function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = mb_strtoupper($value);
    }

    protected function setSkuAttribute($value)
    {
        $this->attributes['sku'] = mb_strtoupper($value);
    }

    protected function setCodigoBarrasAttribute($value)
    {
        $this->attributes['codigo_barras'] = mb_strtoupper($value);
    }

    protected function setMarcaAttribute($value)
    {
        $this->attributes['marca'] = mb_strtoupper($value);
    }

    protected function setAplicacionAttribute($value)
    {
        $this->attributes['aplicacion'] = mb_strtoupper($value);
    }

    protected function setObservacionesAttribute($value)
    {
        $this->attributes['observaciones'] = mb_strtoupper($value);
    }

    public function alertasStock()
    {
        return $this->hasMany(StockAlerta::class);
    }
}
