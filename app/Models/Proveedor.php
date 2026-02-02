<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\Auditable;

class Proveedor extends Model
{
    use Auditable;
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'contacto',
        'contacto_secundario',
        'telefono',
        'telefono_secundario',
        'email',
        'email_secundario',
        'marcas_productos',
        'direccion',
        'observaciones'
    ];

    protected function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = mb_strtoupper($value);
    }

    protected function setContactoAttribute($value)
    {
        $this->attributes['contacto'] = mb_strtoupper($value);
    }

    protected function setContactoSecundarioAttribute($value)
    {
        $this->attributes['contacto_secundario'] = mb_strtoupper($value);
    }

    protected function setMarcasProductosAttribute($value)
    {
        $this->attributes['marcas_productos'] = mb_strtoupper($value);
    }

    protected function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = mb_strtoupper($value);
    }

    protected function setObservacionesAttribute($value)
    {
        $this->attributes['observaciones'] = mb_strtoupper($value);
    }
}
