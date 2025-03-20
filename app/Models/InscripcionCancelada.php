<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscripcionCancelada extends Model
{
    use HasFactory;

    protected $table = 'inscripciones_canceladas';

    protected $fillable = [
        'inscripcion_id',
        'motivo',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'inscripcion_id');
    }
}
