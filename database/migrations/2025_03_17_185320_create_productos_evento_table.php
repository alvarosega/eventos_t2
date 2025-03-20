<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos_evento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')
                  ->constrained('eventos')
                  ->onDelete('cascade');
            $table->string('nombre');
            $table->decimal('precio', 10, 2);
            $table->integer('stock_disponible');
            $table->string('imagen')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_evento');
    }
};
