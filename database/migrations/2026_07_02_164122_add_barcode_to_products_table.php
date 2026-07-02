<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Añadimos el código de barras. Puede ser nullable por si tienes productos sin código.
            // Lo hacemos 'unique' para que no haya duplicados.
            $table->string('barcode')->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }
};