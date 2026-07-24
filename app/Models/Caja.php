<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caja extends Model
{
    protected $table = 'cajas';

    protected $fillable = [
        'user_id',
        'monto_apertura',
        'monto_cierre',
        'estado',
        'fecha_cierre'
    ];

    protected $casts = [
        'fecha_cierre' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene los movimientos ocurridos en el lapso de tiempo de esta caja
     */
    public function movimientos()
    {
        $inicio = $this->created_at;
        $fin = $this->fecha_cierre ?? now();

        return MovimientoCaja::where('user_id', $this->user_id)
            ->whereBetween('created_at', [$inicio, $fin]);
    }
}