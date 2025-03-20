<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['latitud_evento', 'longitud_evento']);
        });
    }

    public function down()
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->decimal('latitud_evento', 10, 8)->nullable();
            $table->decimal('longitud_evento', 11, 8)->nullable();
        });
    }
};
