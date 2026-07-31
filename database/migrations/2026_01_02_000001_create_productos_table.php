<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('barcode')->nullable()->unique();
            $table->foreignId('supplier_id');
            $table->foreignId('category_id');
            $table->string('unit_of_measurement');
            $table->string('image')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('stock');
            $table->boolean('state');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};