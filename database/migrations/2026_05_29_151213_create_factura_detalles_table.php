<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura_detalles', function (Blueprint $table) {
            $table->id();
            
            // Relación con la factura
            $table->foreignId('factura_id')->constrained('facturas')->onDelete('cascade');
            
            // Relación con tu tabla de productos (Platillos)
            // NOTA: Si tu tabla de la imagen 1 se llama 'productos', déjalo así. 
            // Si se llama 'productos_tab' o similar, cambia el nombre dentro de constrained()
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2); // Guardamos el precio del momento de la venta
            $table->decimal('total_linea', 10, 2); // cantidad * precio_unitario
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_detalles');
  
        }
};