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
        'supervisor', 'estado_evento', 'legajo'
    ];
    public function materiales() {
        return $this->belongsToMany(Material::class, 'evento_material')
            ->withPivot(['cantidad', 'fecha_entrega', 'fecha_devolucion_estimada', 'estado', 'foto_entrega', 'foto_devolucion', 'notas_devolucion']);
    }
}
