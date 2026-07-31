<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports'; 

    protected $fillable = [
        'type',
        'status',
        'id_factura',
        'movimiento_id'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura');
    }

    public function movimiento()
    {
        return $this->belongsTo(MovimientoCaja::class, 'movimiento_id');
    }
}