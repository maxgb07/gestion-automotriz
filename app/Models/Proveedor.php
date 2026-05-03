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
        'rfc',
        'contacto',
        'contacto_secundario',
        'telefono',
        'telefono_secundario',
        'email',
        'email_secundario',
        'marcas_productos',
        'direccion',
        'observaciones',
        'dias_credito',
        'porcentaje_descuento_global',
        'porcentaje_descuento_extra',
        'banco',
        'clabe_interbancaria',
        'cuenta_bancaria',
        'titular_cuenta',
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

    protected function setBancoAttribute($value)
    {
        $this->attributes['banco'] = mb_strtoupper($value);
    }

    protected function setTitularCuentaAttribute($value)
    {
        $this->attributes['titular_cuenta'] = mb_strtoupper($value);
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'proveedor_id');
    }
}
