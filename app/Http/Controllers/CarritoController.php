<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarritoController extends Controller
{
    /**
     * Obtiene el valor dinámico del IVA directamente desde la BD.
     */
    private function obtenerIvaEmpresa(): float
    {
        $empresa = DB::table('company')->first();

        if ($empresa) {
            // 1. Verificamos la columna en Mayúsculas ('IVA')
            if (isset($empresa->IVA) && is_numeric($empresa->IVA)) {
                return (float) $empresa->IVA;
            }

            // 2. Verificamos en minúsculas ('iva')
            if (isset($empresa->iva) && is_numeric($empresa->iva)) {
                return (float) $empresa->iva;
            }

            // 3. Verificamos bajo otros nombres comunes ('tax')
            if (isset($empresa->tax) && is_numeric($empresa->tax)) {
                return (float) $empresa->tax;
            }
        }

        return 19.0; // Fallback
    }

    /**
     * Muestra la vista del carrito con los platos agregados y cálculos de IVA
     */
    public function index()
    {
        $carrito = session()->get('carrito', []);
        $porcentajeIva = $this->obtenerIvaEmpresa();

        // Calculamos los totales en servidor
        $subtotal = 0;
        foreach ($carrito as $item) {
            $subtotal += $item['price'] * $item['cantidad'];
        }

        $montoIva = $subtotal * ($porcentajeIva / 100);
        $total = $subtotal + $montoIva;

        return view('carrito.index', compact('carrito', 'porcentajeIva', 'subtotal', 'montoIva', 'total'));
    }

    /**
     * Agrega un platillo al carrito o incrementa su cantidad si ya existe
     */
    public function add($id)
    {
        $producto = Producto::findOrFail($id);
        $carrito = session()->get('carrito', []);

        // Validamos si supera el stock disponible antes de incrementar
        $cantidadActual = isset($carrito[$id]) ? $carrito[$id]['cantidad'] : 0;
        if (($cantidadActual + 1) > $producto->stock) {
            return redirect()->back()->with('error', "No hay suficiente stock. Máximo disponible: {$producto->stock}");
        }

        // Si ya está en el carrito, sumamos cantidad
        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad']++;
        } else {
            // Si es nuevo, lo estructuramos con los datos necesarios
            $carrito[$id] = [
                "id"       => $producto->id,
                "name"     => $producto->name,
                "cantidad" => 1,
                "price"    => $producto->price,
                "image"    => $producto->image,
                "unit"     => $producto->unit_of_measurement
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