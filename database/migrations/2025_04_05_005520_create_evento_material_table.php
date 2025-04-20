<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/2025_04_05_005520_create_evento_material_table.php
    public function up()
    {
        // Verifica que las tablas referenciadas existan
        if (!Schema::hasTable('materiales')) {
            throw new RuntimeException('Primero debe crear la tabla materiales');
        }

        if (!Schema::hasTable('eventos_tipo2')) {
            throw new RuntimeException('Primero debe crear la tabla eventos_tipo2');
        }

        Schema::create('evento_material', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('evento_tipo2_id')
                ->constrained('eventos_tipo2')
                ->onDelete('cascade');
                
            $table->foreignId('material_id')
                ->constrained('materiales')  // Nombre exacto de tabla
                ->onDelete('cascade');
            
            // Resto de campos...
            $table->integer('cantidad')->unsigned();
            $table->date('fecha_entrega');
            $table->date('fecha_devolucion_estimada');
            $table->date('fecha_devolucion_real')->nullable();
            $table->enum('estado', ['reservado', 'en_uso', 'devuelto', 'dañado'])->default('reservado');
            $table->string('foto_entrega');
            $table->string('foto_devolucion')->nullable();
            $table->text('notas_devolucion')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('evento_material'); // Nombre corregido (singular)
    }
};
