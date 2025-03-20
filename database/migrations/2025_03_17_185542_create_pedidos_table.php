<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')
                  ->constrained('eventos')
                  ->onDelete('cascade');
            $table->string('cliente');
            $table->integer('cantidad');
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
