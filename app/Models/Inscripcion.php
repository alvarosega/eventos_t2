<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'externo_id',
        'evento_id',
    ];

    public function externo()
    {
        return $this->belongsTo(Externo::class, 'externo_id');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    // Relación con la cancelación (si existe)
    public function cancelacion()
    {
        return $this->hasOne(InscripcionCancelada::class, 'inscripcion_id');
    }
}
