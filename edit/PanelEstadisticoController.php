<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Producto;
use App\Models\User;
use App\Models\MovimientoCaja; // 🔥 Importamos tu nuevo modelo
use Illuminate\Http\Request;

class PanelEstadisticoController extends Controller
{
   public function index()
    {
        // =========================================================================
        // 1. SEGMENTACIÓN DE INGRESOS
        // =========================================================================
        // Ingresos directos por restaurante (Ventas de productos)
        $ingresosVentas = Factura::whereHas('reporte', function ($query) {
            $query->where('status', 'activo');
        })->sum('total');

        // Ingresos externos/extras registrados manualmente en la nueva tabla
        $ingresosExtras = \App\Models\MovimientoCaja::where('tipo', 'ingreso')->sum('monto');

        // Gran total combinado
        $totalIngresos = $ingresosVentas + $ingresosExtras;

        // Egresos manuales/Gastos
        $totalEgresos = \App\Models\MovimientoCaja::where('tipo', 'egreso')->sum('monto');


       // =========================================================================
        // 2. DETECCIÓN DE PLATOS MÁS VENDIDOS (TOP 3) - Solución Directa con Joins
        // =========================================================================
        $platosMasVendidos = Producto::select('productos.id', 'productos.name', 'productos.image', 'productos.price')
            ->join('factura_detalles', 'productos.id', '=', 'factura_detalles.producto_id')
            ->join('facturas', 'facturas.id', '=', 'factura_detalles.factura_id')
            ->join('reports', 'facturas.id', '=', 'reports.id_factura') // Conectamos con la tabla vieja de reportes
            ->where('reports.status', '=', 'activo') // Filtramos que la comanda/factura esté activa
            ->selectRaw('SUM(factura_detalles.cantidad) as total_unidades')
            ->groupBy('productos.id', 'productos.name', 'productos.image', 'productos.price')
            ->orderByDesc('total_unidades')
            ->take(3)
            ->get();


        // =========================================================================
        // 3. MÉTRICAS BASE Y GRÁFICOS
        // =========================================================================
        $totalPlatillos   = Producto::count();
        $platillosCriticos = Producto::where('stock', '<=', 5)->count();
        $totalClientes   = User::where('role', 'cliente')->count();

        $ultimasFacturas = Factura::with('reporte')->latest()->take(5)->get();

        $metodosPago = Factura::selectRaw('metodo_pago, count(*) as total')
            ->groupBy('metodo_pago')
            ->get();

        $chartLabels = $metodosPago->pluck('metodo_pago');
        $chartData   = $metodosPago->pluck('total');

        return view('admin.estadisticas', compact(
            'totalIngresos',
            'ingresosVentas',
            'ingresosExtras',
            'totalEgresos',
            'totalPlatillos',
            'platillosCriticos',
            'totalClientes',
            'ultimasFacturas',
            'chartLabels',
            'chartData',
            'platosMasVendidos' // Enviamos los platos más vendidos
        ));
    }
    public function imprimirFactura($id)
    {
        // Cargamos la factura con sus relaciones
        $factura = \App\Models\Factura::with('detalles.producto', 'reporte')->findOrFail($id);

        // Si la factura no tiene reporte o su reporte está inactivo, abortamos con un error 403
        if (!$factura->reporte || $factura->reporte->status !== 'activo') {
            abort(403, 'No se puede generar el comprobante de una factura anulada.');
        }

        return view('admin.facturas.ticket', compact('factura'));
    }
}