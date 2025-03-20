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
            // Relación con la inscripción
            $table->foreignId('inscripcion_id')
                  ->constrained('inscripciones')
                  ->onDelete('cascade');
            // Texto libre (o enum/JSON si lo deseas)
            $table->text('motivo')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones_canceladas');
    }
};
