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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            
            // 1. Tipo de reporte
            $table->enum('type', ['entrance', 'exit', 'other']); 
            
            // 2. Estado (Cambiado de 'type' a 'status' para evitar el error)
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            
            // 3. ID de la factura (Mejorado a unsignedBigInteger si se va a relacionar)
            $table->unsignedBigInteger('id_factura');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};