<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    /**
     * Muestra la vista del carrito con los platos agregados
     */
    public function index()
    {
        $carrito = session()->get('carrito', []);
        return view('carrito.index', compact('carrito'));
    }

    /**
     * Agrega un platillo al carrito o incrementa su cantidad si ya existe
     */
    public function add($id)
    {
        $producto = Producto::findOrFail($id);
        $carrito = session()->get('carrito', []);

        // Si ya está en el carrito, sumamos cantidad
        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad']++; // ✨ Busca 'cantidad'
        } else {
            // Si es nuevo, lo estructuramos con los datos necesarios
            $carrito[$id] = [
                "id" => $producto->id,
                "name" => $producto->name,
                "cantidad" => 1, // ✨ Cambiado de 'quantity' a 'cantidad'
                "price" => $producto->price,
                "image" => $producto->image,
                "unit" => $producto->unit_of_measurement
            ];
        }

        session()->put('carrito', $carrito);
        return redirect()->back()->with('success', '¡Platillo agregado al carrito!');
    }

    /**
     * Actualiza la cantidad directamente desde la vista del carrito
     */
    public function update(Request $request)
    {
        if ($request->id && $request->cantidad) {
            $carrito = session()->get('carrito', []);
            
            // Validamos contra el stock real en base de datos
            $producto = Producto::find($request->id);
            if ($producto && $request->cantidad > $producto->stock) {
                return redirect()->back()->with('error', "No hay suficiente stock. Máximo disponible: {$producto->stock}");
            }

            $carrito[$request->id]["cantidad"] = $request->cantidad;
            session()->put('carrito', $carrito);
        }
        
        return redirect()->back()->with('success', 'Carrito actualizado correctamente.');
    }

    /**
     * Elimina un plato específico del carrito
     */
    public function remove(Request $request)
    {
        if ($request->id) {
            $carrito = session()->get('carrito', []);
            if (isset($carrito[$request->id])) {
                unset($carrito[$request->id]);
                session()->put('carrito', $carrito);
            }
        }
        return redirect()->back()->with('success', 'Platillo removido del carrito.');
    }
}