<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedors'; // 👈 El nombre real con el que se creó
    
    protected $fillable = [
        'company_name',
        'nit',
        'contact_name',
        'phone',
        'email',
        'address',
        'image'
    ];
}
