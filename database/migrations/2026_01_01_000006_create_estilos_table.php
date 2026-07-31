<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estilos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 25);
            $table->boolean('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estilos');
    }
};