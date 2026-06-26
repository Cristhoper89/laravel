<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // 1. Hacemos que id_factura sea nullable por si es un gasto manual
            $table->unsignedBigInteger('id_factura')->nullable()->change();

            // 2. Agregamos la relación con la nueva tabla de movimientos
            $table->foreignId('movimiento_id')
                  ->nullable()
                  ->after('id_factura')
                  ->constrained('movimientos_caja')
                  ->onDelete('cascade');
                  
            // 3. Cambiamos el tipo para que acepte 'expense' (gasto) además de 'entrance'
            // Nota: Si en tu migración original era un string, no hay problema. Si era enum, lo expandimos:
            $table->string('type')->default('entrance')->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['movimiento_id']);
            $table->dropColumn('movimiento_id');
            $table->unsignedBigInteger('id_factura')->nullable(false)->change();
        });
    }
};
