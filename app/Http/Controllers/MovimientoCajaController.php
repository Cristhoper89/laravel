<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovimientoCaja;
use App\Models\Producto;
use App\Models\Report; // Asegúrate de que apunte a tu modelo original 'Report'
use Illuminate\Support\Facades\DB;

class MovimientoCajaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'concepto' => 'required|string',
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'required|string|max:255',
            'producto_id' => 'nullable|exists:productos,id',
            'proveedor_id' => 'nullable|exists:proveedors,id',
            'cantidad_producto' => 'nullable|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Crear el Movimiento Base de Caja
            $movimiento = MovimientoCaja::create([
                'tipo' => $request->tipo,
                'concepto' => $request->concepto,
                'monto' => $request->monto,
                'descripcion' => $request->descripcion,
                'producto_id' => $request->producto_id,
                'proveedor_id' => $request->proveedor_id,
                'cantidad_producto' => $request->cantidad_producto,
                'user_id' => auth()->id(),
            ]);

            // 2. Automatización: Si es pago a proveedor e incluyó producto, sumamos inventario
            if ($request->concepto === 'pago_proveedor' && $request->producto_id && $request->cantidad_producto) {
                $producto = Producto::findOrFail($request->producto_id);
                $producto->increment('stock', $request->cantidad_producto);
            }

            // 3. ✨ REPORTE FINANCIERO: Mapeamos el flujo directo a tu tabla 'reports'
            // Traducimos 'ingreso' -> 'entrance' y 'egreso' -> 'expense' (o el término que uses)
            $tipoReporte = ($request->tipo === 'ingreso') ? 'entrance' : 'expense';

            DB::table('reports')->insert([
                'type' => $tipoReporte,
                'status' => 'activo',
                'id_factura' => null, // Queda null porque es un movimiento de caja manual
                'movimiento_id' => $movimiento->id, // Mapeamos la nueva columna de enlace
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', '¡Movimiento de caja y reporte financiero procesados correctamente! ✨');
    }
}