<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class OrdenServicioImagen extends Model
{
    use Auditable;
    use HasFactory;

    protected $table = 'orden_servicio_imagenes';

    protected $fillable = [
        'orden_servicio_id',
        'ruta',
        'descripcion',
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_servicio_id');
    }
}
