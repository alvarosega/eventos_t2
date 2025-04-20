<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('eventos_tipo2', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('evento');
            $table->string('encargado');
            $table->string('celular', 8);
            $table->string('direccion');
            $table->string('ubicacion');
            $table->time('hor_entrega');
            $table->dateTime('recojo');
            $table->string('operador');
            $table->string('supervisor');
            $table->string('estado_evento');
            $table->string('legajo')->nullable();
            $table->timestamps();
    
            // Clave foránea explícita
            $table->foreign('legajo')
                  ->references('legajo')
                  ->on('empleados')
                  ->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('eventos_tipo2');
    }
};
