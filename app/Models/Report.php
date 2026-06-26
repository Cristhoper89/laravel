<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    // Definimos el nombre exacto de tu tabla si no sigue el plural en inglés
    protected $table = 'reports'; 

    protected $fillable = [
        'type',
        'status',
        'id_factura'
    ];

    // Relación: Un reporte pertenece a una factura
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura');
    }
}