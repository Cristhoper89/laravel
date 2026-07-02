<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiometricCredential extends Model
{
    // Si tus tablas usan prefijo como login_roles_ lo especificas aquí, 
    // de lo contrario Laravel buscará "biometric_credentials" por defecto.
    protected $table = 'biometric_credentials'; 

    protected $fillable = [
        'user_id',
        'credential_id',
        'public_key',
        'counter'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}