<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'nombre',
        'sku',
        'descripcion',
        'precio',
        'imagen',
        'observaciones'
    ];

    protected function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = mb_strtoupper($value);
        // Sincronizar SKU con el nombre automáticamente
        $this->attributes['sku'] = mb_strtoupper($value);
    }

    protected function setSkuAttribute($value)
    {
        // Forzar SKU a ser el nombre en mayúsculas
        $this->attributes['sku'] = mb_strtoupper($this->attributes['nombre'] ?? $value);
    }

    protected function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = $value ? mb_strtoupper($value) : null;
    }

    protected function setObservacionesAttribute($value)
    {
        $this->attributes['observaciones'] = $value ? mb_strtoupper($value) : null;
    }
}
