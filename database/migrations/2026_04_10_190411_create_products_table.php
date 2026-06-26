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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // 🔗 Llaves Foráneas corregidas según tu phpMyAdmin:
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('category')->onDelete('cascade');

            $table->string('unit_of_measurement');
            $table->string('image')->nullable(); // Soporta la ruta de la foto (ej: food/Frt...)
            $table->decimal('price', 10, 2);     // Cambiado a 10 para soportar precios mayores con precisión
            $table->integer('stock');            // Cantidad disponible en cocina
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};