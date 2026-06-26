<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\User;
use App\Models\Producto; 
use App\Models\Report;   
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminVentaController extends Controller
{
    public function create()
    {
        // 1. Traemos los productos con stock disponible
        $productos = Producto::where('stock', '>', 0)->orderBy('name', 'asc')->get();
        
        // 2. Traemos SOLO a los usuarios registrados cuyo rol sea 'cliente'
        $clientes = User::where('role', 'cliente')->orderBy('name', 'asc')->get();

        // 3. Traemos los proveedores para el formulario de movimientos de La Cabaña
        $proveedores = Proveedor::all();

        // ✨ SOLUCIÓN: Agregamos 'proveedores' dentro del compact() para que viaje a la vista
        return view('admin.ventas_create', compact('productos', 'clientes', 'proveedores'));
    }

    public function store(Request $request)
    {
        // Validamos la información entrante
        $request->validate([
            'user_id'       => 'nullable|exists:users,id',
            'metodo_pago'   => 'required|string',
            'productos'     => 'required|array|min:1',
            'productos.*.id'=> 'required|exists:productos,id',
            'productos.*.cant' => 'required|integer|min:1',
        ], [
        'productos.required' => 'Debe agregar al menos un producto a la venta.',
        'productos.array' => 'Los productos enviados no son válidos.',
        'productos.min' => 'Debe seleccionar al menos un producto.',
    ]);

        try {
            DB::beginTransaction();

            $subtotalFactura = 0;
            $detallesPreparados = [];

            // Procesamos los productos elegidos utilizando la estructura de tu negocio
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id']);

                if ($producto->stock < $item['cant']) {
                    return back()->withErrors(['error' => "El producto '{$producto->name}' no cuenta con stock suficiente (Stock disponible: {$producto->stock})."])->withInput();
                }

                $totalLinea = $producto->price * $item['cant'];
                $subtotalFactura += $totalLinea;

                $detallesPreparados[] = [
                    'producto_id'     => $producto->id,
                    'cantidad'        => $item['cant'],
                    'precio_unitario' => $producto->price,
                    'total_linea'     => $totalLinea,
                    'instancia'       => $producto // Reservado para decrementar stock
                ];
            }

            // Calculamos impuestos basados en tu esquema (19% IVA)
            $impuesto = $subtotalFactura * 0.19;
            $totalFinal = $subtotalFactura + $impuesto;

            // Buscamos el nombre del cliente si fue asignado, si no, es 'Cliente General'
            $clienteNombre = 'Cliente General';
            if ($request->user_id) {
                $userAsociado = User::find($request->user_id);
                if ($userAsociado) {
                    $clienteNombre = $userAsociado->name;
                }
            }

            // 1. Crear la Cabecera de la Factura usando tus campos exactos
            $factura = Factura::create([
                'numero_factura' => 'FAC-' . strtoupper(uniqid()),
                'user_id'        => $request->user_id, // Puede ser null si es público general
                'cliente_nombre' => $clienteNombre,
                'subtotal'       => $subtotalFactura,
                'impuesto'       => $impuesto,
                'total'          => $totalFinal,
                'metodo_pago'    => $request->metodo_pago,
            ]);

            // 2. Crear automáticamente el Reporte/Movimiento de Caja asociado
            Report::create([
                'type'       => 'entrance',
                'status'     => 'activo',
                'id_factura' => $factura->id
            ]);

            // 3. Guardar los Detalles y descontar stock automáticamente
            foreach ($detallesPreparados as $detalle) {
                FacturaDetalle::create([
                    'factura_id'      => $factura->id,
                    'producto_id'     => $detalle['producto_id'],
                    'cantidad'        => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'total_linea'     => $detalle['total_linea'],
                ]);

                // Descontamos del inventario
                $detalle['instancia']->decrement('stock', $detalle['cantidad']);
            }

            DB::commit();

            return redirect()->route('facturas.index')->with('success', '¡Venta presencial en caja registrada y reportada con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error crítico al procesar la venta en caja: ' . $e->getMessage()])->withInput();
        }
    }
}