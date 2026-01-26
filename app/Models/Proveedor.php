<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
        'direccion'
    ];

    protected function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = mb_strtoupper($value);
    }

    protected function setContactoAttribute($value)
    {
        $this->attributes['contacto'] = mb_strtoupper($value);
    }

    protected function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = mb_strtoupper($value);
    }
}
