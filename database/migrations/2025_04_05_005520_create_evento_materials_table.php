<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evento_materials', function (Blueprint $table) {
            $table->id();
            
            // Relación con el evento tipo2
            $table->unsignedBigInteger('evento_tipo2_id');
            $table->foreign('evento_tipo2_id')
                  ->references('id')
                  ->on('eventos_tipo2')
                  ->onDelete('cascade');

            // Registro del material seleccionado
            $table->string('material'); // Ej.: "sillas", "kiosko", etc.
            $table->integer('cantidad')->default(0);
            $table->string('foto_entregado')->nullable();
            $table->string('foto_recibido')->nullable();
            $table->string('legajo'); // Legajo del usuario que crea el registro

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_materials');
    }
};
