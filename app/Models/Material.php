<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materiales'; // Asegúrate que coincida con tu migración
    protected $fillable = ['nombre', 'stock_total', 'detalles'];
}