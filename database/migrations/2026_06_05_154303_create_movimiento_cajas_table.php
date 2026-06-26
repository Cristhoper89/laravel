<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            // Controla si entra o sale dinero
            $table->enum('tipo', ['ingreso', 'egreso']); 
            
            // Conceptos claros y estandarizados
            $table->string('concepto'); // 'pago_proveedor', 'abono_empleado', 'servicios', 'otros'
            
            $table->decimal('monto', 10, 2);
            $table->text('descripcion')->nullable(); // Ej: "Abono Carlos préstamo" o "Compra stock extra"
            
            // Relaciones opcionales para automatizar los flujos especiales que mencionaste
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedors')->onDelete('set null');
            $table->foreignId('producto_id')->nullable()->constrained('productos')->onDelete('set null');
            $table->integer('cantidad_producto')->nullable(); // Por si sumas stock al gastar
            
            // ¿Quién hizo el registro? (Administrador / Cajero)
            $table->foreignId('user_id')->constrained('users'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};