<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    /**
     * Tabla a la que pertenece el modelo (opcional si sigue convención).
     * protected $table = 'pedidos';
     */

    /**
     * Campos que se pueden asignar de manera masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'evento_id',
        'externo_id',
        'cantidad',
        'total',
        'estado',
        'foto_evidencia',
        'nombre',
        'precio',
    ];

    /**
     * Relación: un Pedido pertenece a un Evento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
    

    /**
     * Relación: un Pedido pertenece a un Externo (usuario externo).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function externo()
    {
        return $this->belongsTo(Externo::class, 'externo_id');
    }

    /**
     * Relación: un Pedido tiene muchos detalles (PedidoDetalle).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'pedido_id');
    }
}
