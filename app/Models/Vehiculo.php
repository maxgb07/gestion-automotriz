<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

class Vehiculo extends Model
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'vehiculos';

    protected $fillable = [
        'cliente_id',
        'marca',
        'modelo',
        'anio',
        'placas',
        'numero_serie',
        'kilometraje',
        'observaciones',
    ];

    /**
     * Get the client that owns the vehicle.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
