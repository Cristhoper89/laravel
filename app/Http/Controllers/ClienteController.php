<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClienteController extends Controller
{
    /**
     * Muestra el panel del cliente con los productos disponibles.
     */
    public function index()
    {
        // 1. Capturamos al cliente logueado
        $cliente = auth()->user();

        // 2. Traemos los productos con stock
        $productos = Producto::where('stock', '>', 0)->get();

        // 3. Pasamos AMBAS variables a la vista dentro del compact
        return view('cliente.dashboard', compact('cliente', 'productos'));
    }

    /**
     * Procesa la compra de un producto.
     */
    public function comprar(Request $request, $id)
    {
        // 1. Buscar el producto
        $producto = Producto::findOrFail($id);

        // 2. Validar que haya stock suficiente
        if ($producto->stock < 1) {
            return redirect()->back()->with('error', 'Lo sentimos, este producto ya no está disponible.');
        }

        // 3. Usar una transacción de Base de Datos por seguridad
        DB::transaction(function () use ($producto) {
            // Restar 1 al stock del producto
            $producto->decrement('stock', 1);

            // Generar un número de factura simulado o registrarlo
            $numeroFactura = rand(1000, 9999);

            // Registrar la salida en la tabla 'reports' (que creamos antes)
            DB::table('reports')->insert([
                'type' => 'exit',         // Salida de comida / Venta
                'status' => 'activo',
                'id_factura' => $numeroFactura,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        });

        // 4. Redirigir con mensaje de éxito
        return redirect()->route('cliente.dashboard')->with('success', '¡Compra realizada con éxito! Disfruta tu comida.');
    }
}