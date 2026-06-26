<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model
{
    // Definimos la tabla exacta de la migración
    protected $table = 'factura_detalles';

    protected $fillable = [
        'factura_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'total_linea'
    ];

    // Relación: El detalle pertenece a una factura madre
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    // Relación: El detalle sabe qué platillo se vendió
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}