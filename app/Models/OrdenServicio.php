<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

class OrdenServicio extends Model
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'ordenes_servicio';

    protected $fillable = [
        'folio',
        'cliente_id',
        'vehiculo_id',
        'fecha_entrada',
        'kilometraje_entrada',
        'falla_reportada',
        'placas',
        'numero_serie',
        'kilometraje_entrega',
        'fecha_entrega',
        'total',
        'saldo_pendiente',
        'estado',
        'mecanico',
        'observaciones',
        'observaciones_post_reparacion',
        'requiere_factura',
        'folio_factura',
        'fecha_factura',
        'uuid_factura',
    ];

    protected $casts = [
        'fecha_entrada' => 'datetime',
        'fecha_entrega' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function detalles()
    {
        return $this->hasMany(OrdenServicioDetalle::class, 'orden_servicio_id');
    }

    public function pagos()
    {
        return $this->hasMany(OrdenServicioPago::class, 'orden_servicio_id');
    }

    public function imagenes()
    {
        return $this->hasMany(OrdenServicioImagen::class, 'orden_servicio_id');
    }
}
