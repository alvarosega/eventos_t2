<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'hora_inicio',
        'fecha_finalizacion',
        'hora_finalizacion',
        'descripcion',
        'estado',
        'ubicacion',
        'legajo',   // <--- Añadir
    ];
    
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'evento_id');
    }
    
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
