<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'tipo',
        'concepto',
        'monto',
        'descripcion',
        'proveedor_id',
        'producto_id',
        'cantidad_producto',
        'user_id',
    ];

    // Relación con el administrador que lo creó
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación por si el gasto involucró a un proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    // Relación por si el gasto incrementó el stock de un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
    
    public function caja()
{
    return $this->belongsTo(Caja::class, 'caja_id');
}
}