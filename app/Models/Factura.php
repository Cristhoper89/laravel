<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'numero_factura',
        'user_id',
        'cliente_nombre',
        'subtotal',
        'impuesto',
        'total',
        'metodo_pago'
    ];

    // Relación: Una factura pertenece a un Usuario (Cliente)
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function reporte()
    {
        return $this->hasOne(Report::class, 'id_factura');
    }
    // Relación: Una factura tiene muchos detalles (productos vendidos)
    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class);
    }
}