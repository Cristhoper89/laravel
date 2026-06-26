<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('proveedors', function (Blueprint $table) {
        $table->id();
        $table->string('company_name'); 
        $table->string('nit'); // Cambiado a NIT obligatorio
        $table->string('contact_name'); 
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->string('address')->nullable();
        $table->string('image')->nullable(); // Campo para guardar la ruta del logo/imagen
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedors');
    }
};
