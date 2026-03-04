<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoCredito extends Model
{
    protected $table = 'seguimientos_credito';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'comentario',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
