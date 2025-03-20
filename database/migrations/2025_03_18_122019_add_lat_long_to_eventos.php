<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->decimal('latitud_evento', 10, 7)->nullable()->after('descripcion');
            $table->decimal('longitud_evento', 10, 7)->nullable()->after('latitud_evento');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn('latitud_evento');
            $table->dropColumn('longitud_evento');
        });
    }
};
