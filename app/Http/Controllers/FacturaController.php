<?php

namespace App\Http\Controllers;

use App\Models\MovimientoCaja;
use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Producto;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FacturaController extends Controller
{
    /**
     * Muestra el historial de facturas para el Administrador
     */
    public function index()
    {
        // ✨ Cargamos las facturas con su reporte de caja asociado para la tabla
        $facturas = Factura::with('reporte')->latest()->get();
        
        // 2. ✨ TRAE LOS MOVIMIENTOS DESDE LA BASE DE DATOS
        // Esto recupera los registros necesarios para que las nuevas pestañas no den error
        $movimientos = MovimientoCaja::orderBy('created_at', 'desc')->get();

        // 3. ✨ ENVÍA AMBAS VARIABLES A LA VISTA usando compact()
        return view('admin.facturas.index', compact('facturas', 'movimientos'));
    }

    /**
     * Muestra el detalle de una comanda específica
     */
    public function show($id)
    {
        // ✨ Cargamos la factura con sus detalles y los productos correspondientes
        $factura = Factura::with('detalles.producto')->findOrFail($id);

        // 🔑 CORREGIDO: Apunta a la subcarpeta 'admin'
        return view('admin.facturas.show', compact('factura'));
    }

    /**
     * Procesar la compra y generar la factura
     */
    public function store(Request $request)
    {
        // Asumiendo que recibes un array de productos desde el carrito de compras
        $request->validate([
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'metodo_pago' => 'required|string'
        ]);

        // Usamos una Transacción de Base de Datos por seguridad. 
        DB::beginTransaction();

        try {
            $subtotal = 0;
            $detallesParaInsertar = [];

            // 1. Calcular totales y verificar stock
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id']);

                if ($producto->stock < $item['cantidad']) {
                    return back()->withErrors(['error' => "Stock insuficiente para el platillo: {$producto->name}"]);
                }

                $totalLinea = $producto->price * $item['cantidad'];
                $subtotal += $totalLinea;

                // Guardamos la información estructural del detalle
                $detallesParaInsertar[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->price,
                    'total_linea' => $totalLinea,
                    'producto_instancia' => $producto // Lo guardamos temporalmente para restarle stock luego
                ];
            }

            // 2. Crear la Cabecera de la Factura
            $factura = Factura::create([
                'numero_factura' => 'FAC-' . strtoupper(uniqid()), // Genera un código único como FAC-6474F3B
                'user_id' => Auth::id(), // ID del usuario autenticado que hace la compra
                'cliente_nombre' => Auth::user()->name ?? 'Cliente General',
                'subtotal' => $subtotal,
                'impuesto' => $subtotal * 0.19, // Ejemplo con el 19% de IVA
                'total' => $subtotal * 1.19,
                'metodo_pago' => $request->metodo_pago,
            ]);

            // 3. Crear automáticamente el Reporte/Movimiento de Caja asociado
            Report::create([
                'type' => 'entrance',       // Marcamos como entrada de dinero al restaurante
                'status' => 'activo',       // Estado inicial activo
                'id_factura' => $factura->id // Vinculamos el ID de la factura
            ]);

            // 4. Registrar los detalles y restar el inventario de comida
            foreach ($detallesParaInsertar as $detalle) {
                FacturaDetalle::create([
                    'factura_id' => $factura->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'total_linea' => $detalle['total_linea'],
                ]);

                // Restamos el stock del platillo de tu inventario automáticamente
                $detalle['producto_instancia']->decrement('stock', $detalle['cantidad']);
            }

            DB::commit(); // Guardamos cambios en la DB
            session()->forget('carrito');
            return redirect()->route('dashboard')->with('success', '¡Pedido confirmado y enviado a cocina! 🍳');
        } catch (\Exception $e) {
            DB::rollBack(); // Deshacemos todo lo que se alcanzó a registrar si algo falla
            return back()->withErrors(['error' => 'Error crítico al procesar la factura: ' . $e->getMessage()]);
        }
    }

    /**
     * Permite corregir el método de pago si el cajero se equivocó
     */
    public function updatePago(Request $request, $id)
    {
        $factura = Factura::findOrFail($id);
        $factura->update([
            'metodo_pago' => $request->metodo_pago
        ]);

        return redirect()->back()->with('success', '¡Método de pago actualizado correctamente!');
    }

    /**
     * Apaga o enciende el reporte financiero según se requiera
     */
    public function toggleReporte($id)
{
    // 1. Buscamos el reporte
    $reporte = Report::findOrFail($id);

    // Definimos el nuevo estado
    $nuevoEstado = ($reporte->status === 'activo') ? 'inactivo' : 'activo';

    // 2. Buscamos la factura vinculada a este reporte (usando la columna id_factura)
    $factura = \App\Models\Factura::find($reporte->id_factura);

    if ($factura) {
        // Buscamos todos los productos comprados en esta factura desde la tabla 'factura_detalles'
        $detalles = \Illuminate\Support\Facades\DB::table('factura_detalles')
            ->where('factura_id', $factura->id)
            ->get();

        foreach ($detalles as $detalle) {
            $producto = \App\Models\Producto::find($detalle->producto_id);
            
            if ($producto) {
                if ($nuevoEstado === 'inactivo') {
                    // CASO A: Se está ANULANDO la factura -> Devolvemos los productos al stock
                    $producto->increment('stock', $detalle->cantidad);
                } else {
                    // CASO B: Se está REACTIVANDO la factura -> Volvemos a restar del stock
                    $producto->decrement('stock', $detalle->cantidad);
                }
            }
        }
    }

    // 3. Actualizamos el estado del reporte
    $reporte->update([
        'status' => $nuevoEstado
    ]);

    $mensaje = $nuevoEstado === 'inactivo' 
        ? "La venta fue anulada correctamente y los productos regresaron al stock." 
        : "La venta fue reactivada correctamente y los productos se descontaron del stock.";

    return redirect()->back()->with('success', $mensaje);
}
    /**
     * Muestra el historial de compras exclusivo del cliente autenticado
     */
    public function historialCliente()
    {
        // Traemos las facturas del usuario actual, de la más reciente a la más antigua
        $facturas = Factura::with('detalles.producto')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        // Retornamos la vista que crearemos en el siguiente paso
        return view('cliente.compras_historial', compact('facturas'));
    }
}
