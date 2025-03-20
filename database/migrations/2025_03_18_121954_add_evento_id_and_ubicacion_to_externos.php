<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('externos', function (Blueprint $table) {
            // Relacionar con eventos
            // Si el evento se borra, se setea a null
            $table->foreignId('evento_id')
                  ->nullable()
                  ->after('password')
                  ->constrained('eventos')
                  ->onDelete('set null');

            // Guardar lat y long como string "lat,long"
            $table->string('ubicacion')->nullable()->after('evento_id');
        });
    }

    public function down(): void
    {
        Schema::table('externos', function (Blueprint $table) {
            $table->dropForeign(['evento_id']);
            $table->dropColumn('evento_id');
            $table->dropColumn('ubicacion');
        });
    }
};
