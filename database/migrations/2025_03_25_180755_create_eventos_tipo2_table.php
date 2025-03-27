<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('eventos_tipo2', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('evento');
            $table->string('encargado');
            $table->string('celular');
            $table->string('direccion');
            $table->string('ubicacion');
            $table->text('material');
            $table->time('hor_entrega');
            $table->boolean('recojo');
            $table->string('operador');
            $table->string('supervisor');
            $table->string('estado_evento');

            // Nueva columna: Clave foránea 'legajo' vinculada con 'empleados.legajo'
            $table->string('legajo')->nullable(); 
            $table->foreign('legajo')->references('legajo')->on('empleados')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos_tipo2', function (Blueprint $table) {
            $table->dropForeign(['legajo']); // Eliminar la clave foránea antes de eliminar la columna
            $table->dropColumn('legajo');
        });

        Schema::dropIfExists('eventos_tipo2');
    }
};
