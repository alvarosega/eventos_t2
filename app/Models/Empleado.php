<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Empleado extends Authenticatable
{
    protected $guard = 'empleado';

    protected $fillable = [
        'nombre_completo',
        'legajo',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}