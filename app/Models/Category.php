<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Definimos el nombre real de la tabla porque Laravel por defecto busca "categories" en plural
    protected $table = 'category';

    protected $fillable = [
        'name',
        'type'
    ];
}

