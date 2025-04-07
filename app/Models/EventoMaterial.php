<?php
// app/Models/EventoMaterial.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoMaterial extends Model
{
    protected $table = 'evento_materials';

    protected $fillable = [
        'evento_tipo2_id',
        'material',
        'cantidad',
        'foto_entregado',
        'foto_recibido',
        'legajo',
    ];

    /**
     * Relación: Cada material pertenece a un EventoTipo2.
     */
    public function eventoTipo2()
    {
        return $this->belongsTo(EventoTipo2::class, 'evento_tipo2_id');
    }
}
