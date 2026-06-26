<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura')->unique(); // Ej: FAC-0001
            
            // Llave foránea hacia la tabla users (nullable por si es venta rápida sin registrar cliente)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null'); // Si se borra el usuario, la factura no se pierde

            $table->string('cliente_nombre')->nullable(); // Para escribir el nombre si es un cliente invitado
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->string('metodo_pago')->default('Efectivo'); // Efectivo, Tarjeta, Transferencia
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};