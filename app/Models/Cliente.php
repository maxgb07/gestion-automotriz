<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

class Cliente extends Model
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'direccion',
        'codigo_postal',
        'rfc',
        'telefono',
        'celular',
        'email',
        'activo',
    ];

    /**
     * Get the vehicles for the client.
     */
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'cliente_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }

    public function ordenesServicio()
    {
        return $this->hasMany(OrdenServicio::class, 'cliente_id');
    }

    public function seguimientosCredito()
    {
        return $this->hasMany(SeguimientoCredito::class, 'cliente_id');
    }
}
