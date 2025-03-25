<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones_canceladas', function (Blueprint $table) {
            $table->id();
            // Relación con la tabla 'externos' en lugar de 'inscripciones'
            $table->foreignId('inscripcion_id')
                  ->constrained('externos') 
                  ->onDelete('cascade');

            // Nuevas columnas para almacenar datos del usuario externo
            $table->string('nombre'); // Guardará el nombre del usuario externo
            $table->string('numero_telefono'); // Guardará el número de teléfono
            
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones_canceladas');
    }
};
