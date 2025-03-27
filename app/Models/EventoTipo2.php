<?php
// app/Models/EventoTipo2.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoTipo2 extends Model
{
    protected $table = 'eventos_tipo2'; // Exactamente este nombre
    
    protected $fillable = [
        'fecha', 'evento', 'encargado', 'celular',
        'direccion', 'ubicacion', 'material',
        'hor_entrega', 'recojo', 'operador',
        'supervisor', 'estado_evento'
    ];
}