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
    /**
     * Obtiene el valor dinámico del IVA directamente desde la BD.
     */
    private function obtenerIvaEmpresa(): float
{
    $empresa = DB::table('company')->first();

    if ($empresa) {
        // 1. Verificamos la columna en Mayúsculas (como está en tu phpMyAdmin 'IVA')
        if (isset($empresa->IVA) && is_numeric($empresa->IVA)) {
            return (float) $empresa->IVA;
        }

        // 2. Verificamos por si acaso en minúsculas ('iva')
        if (isset($empresa->iva) && is_numeric($empresa->iva)) {
            return (float) $empresa->iva;
        }

        // 3. Verificamos si existe bajo otros nombres comunes ('tax' o 'impuesto')
        if (isset($empresa->tax) && is_numeric($empresa->tax)) {
            return (float) $empresa->tax;
        }
    }

    // Backup final si la tabla estuviera vacía
    return 19.0;
}

    public function create()
    {
        $porcentajeIva = $this->obtenerIvaEmpresa();

        // 1. Productos activos con stock
        $productos = Producto::where('stock', '>', 0)->where('state', true)->orderBy('name', 'asc')->get();
        
        // 2. Clientes
        $clientes = User::where('role', 'cliente')->orderBy('name', 'asc')->get();

        // 3. Proveedores
        $proveedores = Proveedor::all();

        return view('admin.ventas_create', compact('productos', 'clientes', 'proveedores', 'porcentajeIva'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'nullable|exists:users,id',
            'metodo_pago'      => 'required|string',
            'productos'        => 'required|array|min:1',
            'productos.*.id'   => 'required|exists:productos,id',
            'productos.*.cant' => 'required|integer|min:1',
        ], [
            'productos.required' => 'Debe agregar al menos un producto a la venta.',
            'productos.array'    => 'Los productos enviados no son válidos.',
            'productos.min'      => 'Debe seleccionar al menos un producto.',
        ]);

        // Obtenemos el porcentaje directamente de la BD
        $porcentajeIva = $this->obtenerIvaEmpresa();
        $tasaIva = $porcentajeIva / 100;

        try {
            DB::beginTransaction();

            $subtotalFactura = 0;
            $detallesPreparados = [];

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
                    'instancia'       => $producto
                ];
            }

            // Cálculo dinámico de impuestos
            $impuesto = $subtotalFactura * $tasaIva;
            $totalFinal = $subtotalFactura + $impuesto;

            $clienteNombre = 'Cliente General';
            if ($request->user_id) {
                $userAsociado = User::find($request->user_id);
                if ($userAsociado) {
                    $clienteNombre = $userAsociado->name;
                }
            }

            // 1. Crear Cabecera de la Factura
            $factura = Factura::create([
                'numero_factura' => 'FAC-' . strtoupper(uniqid()),
                'user_id'        => $request->user_id,
                'cliente_nombre' => $clienteNombre,
                'subtotal'       => $subtotalFactura,
                'impuesto'       => $impuesto,
                'total'          => $totalFinal,
                'metodo_pago'    => $request->metodo_pago,
            ]);

            // 2. Crear Movimiento / Reporte
            Report::create([
                'type'       => 'entrance',
                'status'     => 'activo',
                'id_factura' => $factura->id
            ]);

            // 3. Guardar Detalles y Descontar Inventario
            foreach ($detallesPreparados as $detalle) {
                FacturaDetalle::create([
                    'factura_id'      => $factura->id,
                    'producto_id'     => $detalle['producto_id'],
                    'cantidad'        => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'total_linea'     => $detalle['total_linea'],
                ]);

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