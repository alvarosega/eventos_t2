<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'producto_evento_id',
        'cantidad',
        'precio_unitario',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function productoEvento()
    {
        return $this->belongsTo(ProductoEvento::class, 'producto_evento_id');
    }
}
