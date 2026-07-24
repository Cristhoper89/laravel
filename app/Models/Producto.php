<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    // Nombre de la tabla en MySQL
    protected $table = 'productos'; 

    // Columnas permitidas para inserción masiva (Mass Assignment)
    protected $fillable = [
        'name',
        'barcode',
        'supplier_id',  // Cambiado
        'category_id',  // Agregado
        'unit_of_measurement',
        'image',
        'price',
        'stock',
        'state'
    ];
    public function proveedor()
{
    return $this->belongsTo(Proveedor::class, 'supplier_id');
}

    /**
     * Relación: Un producto pertenece a una Categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}