<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Externo extends Authenticatable
{
    protected $guard = 'externo';

    protected $fillable = [
        'nombre',
        'numero_telefono',
        'password',
        'foto_referencia',
        'rol',
        'ubicacion'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}