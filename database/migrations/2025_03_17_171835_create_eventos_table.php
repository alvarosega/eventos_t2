<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->date('fecha_inicio');
            $table->time('hora_inicio');
            $table->date('fecha_finalizacion');
            $table->time('hora_finalizacion');
            $table->text('descripcion')->nullable();
            $table->string('ubicacion')->nullable(); // Guardamos coordenadas "lat,lng"
            $table->enum('estado', ['activo', 'en espera', 'finalizado'])->default('en espera');
        
            // ✅ NUEVO: columna para legajo del usuario que crea el evento
            $table->string('legajo');
        
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
