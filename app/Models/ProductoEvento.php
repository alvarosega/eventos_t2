<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoEvento extends Model
{
    use HasFactory;

    /**
     * Sobrescribir el nombre de la tabla
     * para que use 'productos_evento' en lugar de 'producto_eventos'.
     */
    protected $table = 'productos_evento';

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'evento_id',
        'nombre',
        'precio',
        'stock_disponible',
        'imagen',
        'descripcion',
    ];

    /**
     * Relación con el modelo Evento
     */
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
