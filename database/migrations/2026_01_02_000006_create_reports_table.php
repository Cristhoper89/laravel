<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('entrance');
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->unsignedBigInteger('id_factura')->nullable();
            $table->foreignId('movimiento_id')->nullable()->constrained('movimientos_caja')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};