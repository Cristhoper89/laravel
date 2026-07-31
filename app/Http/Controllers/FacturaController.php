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
     * Obtiene de forma flexible el porcentaje de IVA registrado en la BD (Soporta 'IVA', 'iva', 'tax')
     */
    private function obtenerIvaEmpresa(): float
    {
        $empresa = DB::table('company')->first();

        if ($empresa) {
            if (isset($empresa->IVA) && is_numeric($empresa->IVA)) {
                return (float) $empresa->IVA;
            }
            if (isset($empresa->iva) && is_numeric($empresa->iva)) {
                return (float) $empresa->iva;
            }
            if (isset($empresa->tax) && is_numeric($empresa->tax)) {
                return (float) $empresa->tax;
            }
        }

        return 19.0; // Fallback predeterminado
    }

    /**
     * Muestra el historial de facturas para el Administrador
     */
    public function index()
    {
        $facturas = Factura::with('reporte')->latest()->get();
        
        // Cargamos los movimientos con su reporte financiero asociado
        $movimientos = MovimientoCaja::with('reporte')->orderBy('created_at', 'desc')->get();

        return view('admin.facturas.index', compact('facturas', 'movimientos'));
    }

    /**
     * Muestra el detalle de una comanda específica
     */
    public function show($id)
    {
        // 1. Cargar factura con sus relaciones
        $factura = Factura::with(['detalles.producto', 'reporte'])->findOrFail($id);

        // 2. Obtener la caja que está actualmente ABIERTA
        $cajaActiva = \App\Models\Caja::where('estado', 'abierta')->first();

        // 3. Evaluar si la factura pertenece a la caja activa actual
        $cajaCerrada = !$cajaActiva || ($factura->created_at < $cajaActiva->created_at);

        return view('admin.facturas.show', compact('factura', 'cajaCerrada'));
    }

    /**
     * Procesar la compra y generar la factura
     */
    public function store(Request $request)
    {
        // Validar el payload de la solicitud
        $request->validate([
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'metodo_pago' => 'required|string'
        ]);

        // 🔑 Obtenemos el IVA real directamente de la BD (ej. 10.0 en lugar del 19.0 por defecto)
        $porcentajeIva = $this->obtenerIvaEmpresa();
        $tasaIva = $porcentajeIva / 100; // Convierte por ejemplo 10 a 0.10

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
                    'producto_instancia' => $producto 
                ];
            }

            $montoImpuesto = $subtotal * $tasaIva;
            $montoTotal = $subtotal + $montoImpuesto;

            // 2. Crear la Cabecera de la Factura
            $factura = Factura::create([
                'numero_factura' => 'FAC-' . strtoupper(uniqid()), 
                'user_id' => Auth::id(), 
                'cliente_nombre' => Auth::user()->name ?? 'Cliente General',
                'subtotal' => $subtotal,
                'impuesto' => $montoImpuesto, // 👈 Ahora sí guardará $1.00 en vez de $1.90 (para $10)
                'total' => $montoTotal,       // 👈 Ahora sí guardará $11.00 en vez de $11.90
                'metodo_pago' => $request->metodo_pago,
            ]);

            // 3. Crear automáticamente el Reporte/Movimiento de Caja asociado
            Report::create([
                'type' => 'entrance',       
                'status' => 'activo',       
                'id_factura' => $factura->id 
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

                // Restamos el stock del platillo de tu inventario
                $detalle['producto_instancia']->decrement('stock', $detalle['cantidad']);
            }

            DB::commit(); 
            session()->forget('carrito');
            return redirect()->route('dashboard')->with('success', '¡Pedido confirmado y enviado a cocina! 🍳');
        } catch (\Exception $e) {
            DB::rollBack(); 
            return back()->withErrors(['error' => 'Error crítico al procesar la factura: ' . $e->getMessage()]);
        }
    }

    /**
     * Permite corregir el método de pago si el cajero se equivocó
     */
    public function updatePago(Request $request, $id)
    {
        $factura = Factura::findOrFail($id);

        $cajaActiva = \App\Models\Caja::where('estado', 'abierta')->first();
        $cajaCerrada = !$cajaActiva || ($factura->created_at < $cajaActiva->created_at);

        if ($cajaCerrada) {
            return redirect()->back()->with('error', 'No se puede modificar el método de pago porque esta transacción no pertenece a una caja abierta.');
        }

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
        $reporte = Report::findOrFail($id);
        $cajaActiva = \App\Models\Caja::where('estado', 'abierta')->first();

        $perteneceACajaActiva = $cajaActiva && ($reporte->created_at >= $cajaActiva->created_at);

        if (!$perteneceACajaActiva) {
            return redirect()->back()->with('error', 'No se puede modificar este movimiento porque no pertenece a la caja abierta actualmente. Solo se permiten cambios en la caja activa.');
        }

        $nuevoEstado = ($reporte->status === 'activo') ? 'inactivo' : 'activo';

        $factura = Factura::find($reporte->id_factura);
        
        if ($factura) {
            $detalles = FacturaDetalle::where('factura_id', $factura->id)->get();

            foreach ($detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    if ($nuevoEstado === 'inactivo') {
                        $producto->increment('stock', $detalle->cantidad);
                    } else {
                        $producto->decrement('stock', $detalle->cantidad);
                    }
                }
            }
        }

        $reporte->update(['status' => $nuevoEstado]);

        $mensaje = ($nuevoEstado === 'inactivo') 
            ? "Movimiento desactivado con éxito de la caja actual. El stock fue devuelto al inventario." 
            : "Movimiento reactivado con éxito.";

        return redirect()->back()->with('success', $mensaje);
    }

    /**
     * Muestra el historial de compras exclusivo del cliente autenticado
     */
    public function historialCliente()
    {
        $facturas = Factura::with('detalles.producto')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $porcentajeIva = $this->obtenerIvaEmpresa();

        return view('cliente.compras_historial', compact('facturas', 'porcentajeIva'));
    }

    /**
     * Activa o desactiva un movimiento manual de caja (Gastos / Entradas Extras)
     */
    public function toggleMovimiento($id)
    {
        $movimiento = MovimientoCaja::findOrFail($id);
        $cajaActiva = \App\Models\Caja::where('estado', 'abierta')->first();

        $perteneceACajaActiva = $cajaActiva && ($movimiento->created_at >= $cajaActiva->created_at);

        if (!$perteneceACajaActiva) {
            return redirect()->back()->with('error', 'No se puede modificar este movimiento porque no pertenece a la caja abierta actualmente.');
        }

        $reporte = Report::where('movimiento_id', $movimiento->id)->first();

        if (!$reporte) {
            return redirect()->back()->with('error', 'No se encontró el reporte financiero asociado a este movimiento.');
        }

        $nuevoEstado = ($reporte->status === 'activo') ? 'inactivo' : 'activo';

        if ($movimiento->producto_id && $movimiento->cantidad_producto) {
            $producto = Producto::find($movimiento->producto_id);
            if ($producto) {
                if ($nuevoEstado === 'inactivo') {
                    $producto->decrement('stock', $movimiento->cantidad_producto);
                } else {
                    $producto->increment('stock', $movimiento->cantidad_producto);
                }
            }
        }

        $reporte->update(['status' => $nuevoEstado]);

        $mensaje = ($nuevoEstado === 'inactivo') 
            ? "Movimiento de caja desactivado con éxito." 
            : "Movimiento de caja reactivado con éxito.";

        return redirect()->back()->with('success', $mensaje);
    }
}