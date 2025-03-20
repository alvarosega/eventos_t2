<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // 1) Elimina o vuelve opcional la columna 'cliente'
            //    En este ejemplo, la eliminamos para usar 'externo_id'
            $table->dropColumn('cliente');

            // 2) Agregamos la columna 'externo_id'
            $table->foreignId('externo_id')
                  ->after('evento_id')
                  ->constrained('externos')
                  ->onDelete('cascade');

            // 3) Cambiamos el enum de estado para reflejar el nuevo flujo
            $table->enum('estado', ['pendiente', 'en_preparacion', 'enviado', 'entregado'])
                  ->default('pendiente')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Restaurar 'cliente'
            $table->string('cliente')->nullable();

            // Restaurar estado anterior
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado'])
                  ->default('pendiente')
                  ->change();

            // Eliminar 'externo_id'
            $table->dropForeign(['externo_id']);
            $table->dropColumn('externo_id');
        });
    }
};
